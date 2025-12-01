<?php

declare(strict_types=1);

namespace TailwindPHP;

/**
 * CSS Parser - Character-by-character CSS tokenizer.
 *
 * Port of: packages/tailwindcss/src/css-parser.ts
 */

const BACKSLASH_CSS = 0x5c;
const SLASH_CSS = 0x2f;
const ASTERISK_CSS = 0x2a;
const DOUBLE_QUOTE_CSS = 0x22;
const SINGLE_QUOTE_CSS = 0x27;
const COLON_CSS = 0x3a;
const SEMICOLON_CSS = 0x3b;
const LINE_BREAK_CSS = 0x0a;
const CARRIAGE_RETURN_CSS = 0x0d;
const SPACE_CSS = 0x20;
const TAB_CSS = 0x09;
const OPEN_CURLY_CSS = 0x7b;
const CLOSE_CURLY_CSS = 0x7d;
const OPEN_PAREN_CSS = 0x28;
const CLOSE_PAREN_CSS = 0x29;
const OPEN_BRACKET_CSS = 0x5b;
const CLOSE_BRACKET_CSS = 0x5d;
const DASH_CSS = 0x2d;
const AT_SIGN_CSS = 0x40;
const EXCLAMATION_MARK_CSS = 0x21;

/**
 * CSS syntax error with source location information.
 */
class CssSyntaxError extends \Exception
{
    public ?array $loc;

    public function __construct(string $message, ?array $loc = null)
    {
        parent::__construct($message);
        $this->loc = $loc;
    }
}

/**
 * Parse CSS string into AST.
 *
 * @param string $input
 * @return array<array>
 * @throws CssSyntaxError
 */
function parse(string $input): array
{
    // Handle BOM (UTF-8 BOM is 3 bytes: EF BB BF)
    if (strlen($input) >= 3 && substr($input, 0, 3) === "\xEF\xBB\xBF") {
        $input = ' ' . substr($input, 3);
    }

    $ast = [];
    $licenseComments = [];
    $stack = [];
    $parent = null;
    $node = null;
    $buffer = '';
    $closingBracketStack = '';
    $bufferStart = 0;
    $len = strlen($input);

    for ($i = 0; $i < $len; $i++) {
        $currentChar = ord($input[$i]);

        // Skip over the CR in CRLF
        if ($currentChar === CARRIAGE_RETURN_CSS) {
            if ($i + 1 < $len && ord($input[$i + 1]) === LINE_BREAK_CSS) {
                continue;
            }
        }

        // Backslash - escape next character
        if ($currentChar === BACKSLASH_CSS) {
            if ($buffer === '') {
                $bufferStart = $i;
            }
            $buffer .= substr($input, $i, 2);
            $i += 1;
            continue;
        }

        // Start of a comment
        if ($currentChar === SLASH_CSS && $i + 1 < $len && ord($input[$i + 1]) === ASTERISK_CSS) {
            $start = $i;

            for ($j = $i + 2; $j < $len; $j++) {
                $peekChar = ord($input[$j]);

                // Escaped character
                if ($peekChar === BACKSLASH_CSS) {
                    $j += 1;
                    continue;
                }

                // End of comment
                if ($peekChar === ASTERISK_CSS && $j + 1 < $len && ord($input[$j + 1]) === SLASH_CSS) {
                    $i = $j + 1;
                    break;
                }
            }

            $commentString = substr($input, $start, $i - $start + 1);

            // License comments (/*! ... */)
            if (strlen($commentString) > 2 && ord($commentString[2]) === EXCLAMATION_MARK_CSS) {
                $licenseComments[] = comment(substr($commentString, 2, -2));
            }
            continue;
        }

        // Start of a string
        if ($currentChar === SINGLE_QUOTE_CSS || $currentChar === DOUBLE_QUOTE_CSS) {
            $end = parseString($input, $i, $currentChar);
            $buffer .= substr($input, $i, $end - $i + 1);
            $i = $end;
            continue;
        }

        // Skip consecutive whitespace
        if (($currentChar === SPACE_CSS || $currentChar === LINE_BREAK_CSS || $currentChar === TAB_CSS) && $i + 1 < $len) {
            $peekChar = ord($input[$i + 1]);
            if ($peekChar === SPACE_CSS || $peekChar === LINE_BREAK_CSS || $peekChar === TAB_CSS ||
                ($peekChar === CARRIAGE_RETURN_CSS && $i + 2 < $len && ord($input[$i + 2]) === LINE_BREAK_CSS)) {
                continue;
            }
        }

        // Replace newlines with spaces
        if ($currentChar === LINE_BREAK_CSS) {
            if (strlen($buffer) === 0) {
                continue;
            }
            $lastChar = ord($buffer[strlen($buffer) - 1]);
            if ($lastChar !== SPACE_CSS && $lastChar !== LINE_BREAK_CSS && $lastChar !== TAB_CSS) {
                $buffer .= ' ';
            }
            continue;
        }

        // Custom property (starts with --)
        if ($currentChar === DASH_CSS && $i + 1 < $len && ord($input[$i + 1]) === DASH_CSS && strlen($buffer) === 0) {
            $customPropStack = '';
            $start = $i;
            $colonIdx = -1;

            for ($j = $i + 2; $j < $len; $j++) {
                $peekChar = ord($input[$j]);

                // Escaped character
                if ($peekChar === BACKSLASH_CSS) {
                    $j += 1;
                    continue;
                }

                // String
                if ($peekChar === SINGLE_QUOTE_CSS || $peekChar === DOUBLE_QUOTE_CSS) {
                    $j = parseString($input, $j, $peekChar);
                    continue;
                }

                // Comment
                if ($peekChar === SLASH_CSS && $j + 1 < $len && ord($input[$j + 1]) === ASTERISK_CSS) {
                    for ($k = $j + 2; $k < $len; $k++) {
                        $pk = ord($input[$k]);
                        if ($pk === BACKSLASH_CSS) {
                            $k += 1;
                            continue;
                        }
                        if ($pk === ASTERISK_CSS && $k + 1 < $len && ord($input[$k + 1]) === SLASH_CSS) {
                            $j = $k + 1;
                            break;
                        }
                    }
                    continue;
                }

                // Colon (end of property name)
                if ($colonIdx === -1 && $peekChar === COLON_CSS) {
                    $colonIdx = strlen($buffer) + $j - $start;
                    continue;
                }

                // Semicolon (end of custom property)
                if ($peekChar === SEMICOLON_CSS && strlen($customPropStack) === 0) {
                    $buffer .= substr($input, $start, $j - $start);
                    $i = $j;
                    break;
                }

                // Opening brackets
                if ($peekChar === OPEN_PAREN_CSS) {
                    $customPropStack .= ')';
                } elseif ($peekChar === OPEN_BRACKET_CSS) {
                    $customPropStack .= ']';
                } elseif ($peekChar === OPEN_CURLY_CSS) {
                    $customPropStack .= '}';
                }

                // End without semicolon
                if (($peekChar === CLOSE_CURLY_CSS || $j === $len - 1) && strlen($customPropStack) === 0) {
                    if ($peekChar === CLOSE_CURLY_CSS) {
                        $i = $j - 1;
                        $buffer .= substr($input, $start, $j - $start);
                    } else {
                        // End of input - include the last character
                        $i = $j;
                        $buffer .= substr($input, $start, $j - $start + 1);
                    }
                    break;
                }

                // Closing brackets
                if ($peekChar === CLOSE_PAREN_CSS || $peekChar === CLOSE_BRACKET_CSS || $peekChar === CLOSE_CURLY_CSS) {
                    if (strlen($customPropStack) > 0 && $input[$j] === $customPropStack[strlen($customPropStack) - 1]) {
                        $customPropStack = substr($customPropStack, 0, -1);
                    }
                }
            }

            $declaration = parseDeclaration($buffer, $colonIdx);
            if (!$declaration) {
                throw new CssSyntaxError("Invalid custom property, expected a value");
            }

            if ($parent !== null) {
                $parent['nodes'][] = $declaration;
            } else {
                $ast[] = $declaration;
            }

            $buffer = '';
            continue;
        }

        // End of body-less at-rule
        if ($currentChar === SEMICOLON_CSS && strlen($buffer) > 0 && ord($buffer[0]) === AT_SIGN_CSS) {
            $node = parseAtRule($buffer);

            if ($parent !== null) {
                $parent['nodes'][] = $node;
            } else {
                $ast[] = $node;
            }

            $buffer = '';
            $node = null;
            continue;
        }

        // End of declaration
        if ($currentChar === SEMICOLON_CSS && (strlen($closingBracketStack) === 0 || $closingBracketStack[strlen($closingBracketStack) - 1] !== ')')) {
            $declaration = parseDeclaration($buffer);
            if (!$declaration) {
                if (strlen($buffer) === 0) {
                    continue;
                }
                throw new CssSyntaxError("Invalid declaration: `" . trim($buffer) . "`");
            }

            if ($parent !== null) {
                $parent['nodes'][] = $declaration;
            } else {
                $ast[] = $declaration;
            }

            $buffer = '';
            continue;
        }

        // Start of a block
        if ($currentChar === OPEN_CURLY_CSS && (strlen($closingBracketStack) === 0 || $closingBracketStack[strlen($closingBracketStack) - 1] !== ')')) {
            $closingBracketStack .= '}';

            $node = rule(trim($buffer));

            // Push current parent to stack along with the index where this node will be placed
            if ($parent !== null) {
                $nodeIndex = count($parent['nodes']);
                $parent['nodes'][$nodeIndex] = $node;
                $stack[] = ['parent' => $parent, 'index' => $nodeIndex];
                $parent = &$parent['nodes'][$nodeIndex];
            } else {
                $stack[] = ['parent' => null, 'index' => -1];
                $parent = $node;
            }

            $buffer = '';
            continue;
        }

        // End of a block
        if ($currentChar === CLOSE_CURLY_CSS && (strlen($closingBracketStack) === 0 || $closingBracketStack[strlen($closingBracketStack) - 1] !== ')')) {
            if ($closingBracketStack === '') {
                throw new CssSyntaxError('Missing opening {');
            }

            $closingBracketStack = substr($closingBracketStack, 0, -1);

            // Handle leftover buffer
            if (strlen($buffer) > 0) {
                if (ord($buffer[0]) === AT_SIGN_CSS) {
                    $node = parseAtRule($buffer);
                    $parent['nodes'][] = $node;
                    $buffer = '';
                    $node = null;
                } else {
                    // Attach the declaration to the parent.
                    $colonIdx = strpos($buffer, ':');
                    $decl = parseDeclaration($buffer, $colonIdx !== false ? $colonIdx : -1);
                    if (!$decl) {
                        throw new CssSyntaxError("Invalid declaration: `" . trim($buffer) . "`");
                    }
                    $parent['nodes'][] = $decl;
                }
            }

            $stackItem = array_pop($stack);
            $grandParent = $stackItem['parent'];

            if ($grandParent === null) {
                $ast[] = $parent;
                $parent = null;
            } else {
                // Update the node in the grandparent since we've been modifying a copy
                $grandParent['nodes'][$stackItem['index']] = $parent;
                $parent = $grandParent;
            }

            $buffer = '';
            continue;
        }

        // Open paren
        if ($currentChar === OPEN_PAREN_CSS) {
            $closingBracketStack .= ')';
            $buffer .= '(';
            continue;
        }

        // Close paren
        if ($currentChar === CLOSE_PAREN_CSS) {
            if (strlen($closingBracketStack) > 0 && $closingBracketStack[strlen($closingBracketStack) - 1] !== ')') {
                throw new CssSyntaxError('Missing opening (');
            }
            $closingBracketStack = substr($closingBracketStack, 0, -1);
            $buffer .= ')';
            continue;
        }

        // Skip leading whitespace
        if (strlen($buffer) === 0 && ($currentChar === SPACE_CSS || $currentChar === LINE_BREAK_CSS || $currentChar === TAB_CSS)) {
            continue;
        }

        if ($buffer === '') {
            $bufferStart = $i;
        }

        $buffer .= chr($currentChar);
    }

    // Handle leftover at-rule at end of input
    if (strlen($buffer) > 0 && ord($buffer[0]) === AT_SIGN_CSS) {
        $ast[] = parseAtRule($buffer);
    }

    // Check for unterminated blocks
    if (strlen($closingBracketStack) > 0 && $parent !== null) {
        if ($parent['kind'] === 'rule') {
            throw new CssSyntaxError("Missing closing } at {$parent['selector']}");
        }
        if ($parent['kind'] === 'at-rule') {
            throw new CssSyntaxError("Missing closing } at {$parent['name']} {$parent['params']}");
        }
    }

    if (count($licenseComments) > 0) {
        return array_merge($licenseComments, $ast);
    }

    return $ast;
}

/**
 * Parse a declaration from buffer.
 *
 * @param string $buffer
 * @param int $colonIdx
 * @return array|null
 */
function parseDeclaration(string $buffer, int $colonIdx = -1): ?array
{
    if ($colonIdx === -1) {
        $colonIdx = strpos($buffer, ':');
    }

    if ($colonIdx === false) {
        return null;
    }

    $importantIdx = strpos($buffer, '!important', $colonIdx + 1);

    return decl(
        trim(substr($buffer, 0, $colonIdx)),
        trim(substr($buffer, $colonIdx + 1, $importantIdx === false ? null : $importantIdx - $colonIdx - 1)),
        $importantIdx !== false
    );
}

/**
 * Parse a string (single or double quoted).
 *
 * @param string $input
 * @param int $startIdx
 * @param int $quoteChar
 * @return int End index of the string
 * @throws CssSyntaxError
 */
function parseString(string $input, int $startIdx, int $quoteChar): int
{
    $len = strlen($input);

    for ($i = $startIdx + 1; $i < $len; $i++) {
        $peekChar = ord($input[$i]);

        // Escaped character
        if ($peekChar === BACKSLASH_CSS) {
            $i += 1;
            continue;
        }

        // End of string
        if ($peekChar === $quoteChar) {
            return $i;
        }

        // Unterminated string with semicolon
        if ($peekChar === SEMICOLON_CSS) {
            $nextChar = $i + 1 < $len ? ord($input[$i + 1]) : 0;
            if ($nextChar === LINE_BREAK_CSS ||
                ($nextChar === CARRIAGE_RETURN_CSS && $i + 2 < $len && ord($input[$i + 2]) === LINE_BREAK_CSS)) {
                throw new CssSyntaxError(
                    "Unterminated string: " . substr($input, $startIdx, $i - $startIdx + 1) . chr($quoteChar)
                );
            }
        }

        // Unterminated string at newline
        if ($peekChar === LINE_BREAK_CSS ||
            ($peekChar === CARRIAGE_RETURN_CSS && $i + 1 < $len && ord($input[$i + 1]) === LINE_BREAK_CSS)) {
            throw new CssSyntaxError(
                "Unterminated string: " . substr($input, $startIdx, $i - $startIdx) . chr($quoteChar)
            );
        }
    }

    return $startIdx;
}
