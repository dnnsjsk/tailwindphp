<?php

declare(strict_types=1);

namespace TailwindPHP\DefaultTheme;

use TailwindPHP\Theme;

/**
 * Default Theme - Standard Tailwind CSS theme values.
 *
 * Port of: packages/tailwindcss/theme.css
 *
 * @port-deviation:source TypeScript version loads theme.css as CSS.
 * PHP provides the same values as PHP code for programmatic use.
 *
 * @port-deviation:format TypeScript parses CSS @theme blocks at runtime.
 * PHP uses explicit static values added via Theme::add() for simplicity.
 *
 * This provides the default theme values that Tailwind ships with.
 */
class DefaultTheme
{
    /**
     * Create a Theme instance with all default Tailwind values.
     */
    public static function create(): Theme
    {
        $theme = new Theme();

        self::addFonts($theme);
        self::addColors($theme);
        self::addSpacing($theme);
        self::addBreakpoints($theme);
        self::addContainers($theme);
        self::addTextSizes($theme);
        self::addFontWeights($theme);
        self::addTracking($theme);
        self::addLeading($theme);
        self::addRadius($theme);
        self::addShadows($theme);
        self::addInsetShadows($theme);
        self::addDropShadows($theme);
        self::addTextShadows($theme);
        self::addEasing($theme);
        self::addAnimations($theme);
        self::addBlur($theme);
        self::addPerspective($theme);
        self::addAspect($theme);
        self::addDefaults($theme);
        self::addDeprecated($theme);

        return $theme;
    }

    private static function addFonts(Theme $theme): void
    {
        $theme->add('--font-sans', "ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji'");
        $theme->add('--font-serif', "ui-serif, Georgia, Cambria, 'Times New Roman', Times, serif");
        $theme->add('--font-mono', "ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace");
    }

    private static function addColors(Theme $theme): void
    {
        // Red
        $theme->add('--color-red-50', 'oklch(97.1% 0.013 17.38)');
        $theme->add('--color-red-100', 'oklch(93.6% 0.032 17.717)');
        $theme->add('--color-red-200', 'oklch(88.5% 0.062 18.334)');
        $theme->add('--color-red-300', 'oklch(80.8% 0.114 19.571)');
        $theme->add('--color-red-400', 'oklch(70.4% 0.191 22.216)');
        $theme->add('--color-red-500', 'oklch(63.7% 0.237 25.331)');
        $theme->add('--color-red-600', 'oklch(57.7% 0.245 27.325)');
        $theme->add('--color-red-700', 'oklch(50.5% 0.213 27.518)');
        $theme->add('--color-red-800', 'oklch(44.4% 0.177 26.899)');
        $theme->add('--color-red-900', 'oklch(39.6% 0.141 25.723)');
        $theme->add('--color-red-950', 'oklch(25.8% 0.092 26.042)');

        // Orange
        $theme->add('--color-orange-50', 'oklch(98% 0.016 73.684)');
        $theme->add('--color-orange-100', 'oklch(95.4% 0.038 75.164)');
        $theme->add('--color-orange-200', 'oklch(90.1% 0.076 70.697)');
        $theme->add('--color-orange-300', 'oklch(83.7% 0.128 66.29)');
        $theme->add('--color-orange-400', 'oklch(75% 0.183 55.934)');
        $theme->add('--color-orange-500', 'oklch(70.5% 0.213 47.604)');
        $theme->add('--color-orange-600', 'oklch(64.6% 0.222 41.116)');
        $theme->add('--color-orange-700', 'oklch(55.3% 0.195 38.402)');
        $theme->add('--color-orange-800', 'oklch(47% 0.157 37.304)');
        $theme->add('--color-orange-900', 'oklch(40.8% 0.123 38.172)');
        $theme->add('--color-orange-950', 'oklch(26.6% 0.079 36.259)');

        // Amber
        $theme->add('--color-amber-50', 'oklch(98.7% 0.022 95.277)');
        $theme->add('--color-amber-100', 'oklch(96.2% 0.059 95.617)');
        $theme->add('--color-amber-200', 'oklch(92.4% 0.12 95.746)');
        $theme->add('--color-amber-300', 'oklch(87.9% 0.169 91.605)');
        $theme->add('--color-amber-400', 'oklch(82.8% 0.189 84.429)');
        $theme->add('--color-amber-500', 'oklch(76.9% 0.188 70.08)');
        $theme->add('--color-amber-600', 'oklch(66.6% 0.179 58.318)');
        $theme->add('--color-amber-700', 'oklch(55.5% 0.163 48.998)');
        $theme->add('--color-amber-800', 'oklch(47.3% 0.137 46.201)');
        $theme->add('--color-amber-900', 'oklch(41.4% 0.112 45.904)');
        $theme->add('--color-amber-950', 'oklch(27.9% 0.077 45.635)');

        // Yellow
        $theme->add('--color-yellow-50', 'oklch(98.7% 0.026 102.212)');
        $theme->add('--color-yellow-100', 'oklch(97.3% 0.071 103.193)');
        $theme->add('--color-yellow-200', 'oklch(94.5% 0.129 101.54)');
        $theme->add('--color-yellow-300', 'oklch(90.5% 0.182 98.111)');
        $theme->add('--color-yellow-400', 'oklch(85.2% 0.199 91.936)');
        $theme->add('--color-yellow-500', 'oklch(79.5% 0.184 86.047)');
        $theme->add('--color-yellow-600', 'oklch(68.1% 0.162 75.834)');
        $theme->add('--color-yellow-700', 'oklch(55.4% 0.135 66.442)');
        $theme->add('--color-yellow-800', 'oklch(47.6% 0.114 61.907)');
        $theme->add('--color-yellow-900', 'oklch(42.1% 0.095 57.708)');
        $theme->add('--color-yellow-950', 'oklch(28.6% 0.066 53.813)');

        // Lime
        $theme->add('--color-lime-50', 'oklch(98.6% 0.031 120.757)');
        $theme->add('--color-lime-100', 'oklch(96.7% 0.067 122.328)');
        $theme->add('--color-lime-200', 'oklch(93.8% 0.127 124.321)');
        $theme->add('--color-lime-300', 'oklch(89.7% 0.196 126.665)');
        $theme->add('--color-lime-400', 'oklch(84.1% 0.238 128.85)');
        $theme->add('--color-lime-500', 'oklch(76.8% 0.233 130.85)');
        $theme->add('--color-lime-600', 'oklch(64.8% 0.2 131.684)');
        $theme->add('--color-lime-700', 'oklch(53.2% 0.157 131.589)');
        $theme->add('--color-lime-800', 'oklch(45.3% 0.124 130.933)');
        $theme->add('--color-lime-900', 'oklch(40.5% 0.101 131.063)');
        $theme->add('--color-lime-950', 'oklch(27.4% 0.072 132.109)');

        // Green
        $theme->add('--color-green-50', 'oklch(98.2% 0.018 155.826)');
        $theme->add('--color-green-100', 'oklch(96.2% 0.044 156.743)');
        $theme->add('--color-green-200', 'oklch(92.5% 0.084 155.995)');
        $theme->add('--color-green-300', 'oklch(87.1% 0.15 154.449)');
        $theme->add('--color-green-400', 'oklch(79.2% 0.209 151.711)');
        $theme->add('--color-green-500', 'oklch(72.3% 0.219 149.579)');
        $theme->add('--color-green-600', 'oklch(62.7% 0.194 149.214)');
        $theme->add('--color-green-700', 'oklch(52.7% 0.154 150.069)');
        $theme->add('--color-green-800', 'oklch(44.8% 0.119 151.328)');
        $theme->add('--color-green-900', 'oklch(39.3% 0.095 152.535)');
        $theme->add('--color-green-950', 'oklch(26.6% 0.065 152.934)');

        // Emerald
        $theme->add('--color-emerald-50', 'oklch(97.9% 0.021 166.113)');
        $theme->add('--color-emerald-100', 'oklch(95% 0.052 163.051)');
        $theme->add('--color-emerald-200', 'oklch(90.5% 0.093 164.15)');
        $theme->add('--color-emerald-300', 'oklch(84.5% 0.143 164.978)');
        $theme->add('--color-emerald-400', 'oklch(76.5% 0.177 163.223)');
        $theme->add('--color-emerald-500', 'oklch(69.6% 0.17 162.48)');
        $theme->add('--color-emerald-600', 'oklch(59.6% 0.145 163.225)');
        $theme->add('--color-emerald-700', 'oklch(50.8% 0.118 165.612)');
        $theme->add('--color-emerald-800', 'oklch(43.2% 0.095 166.913)');
        $theme->add('--color-emerald-900', 'oklch(37.8% 0.077 168.94)');
        $theme->add('--color-emerald-950', 'oklch(26.2% 0.051 172.552)');

        // Teal
        $theme->add('--color-teal-50', 'oklch(98.4% 0.014 180.72)');
        $theme->add('--color-teal-100', 'oklch(95.3% 0.051 180.801)');
        $theme->add('--color-teal-200', 'oklch(91% 0.096 180.426)');
        $theme->add('--color-teal-300', 'oklch(85.5% 0.138 181.071)');
        $theme->add('--color-teal-400', 'oklch(77.7% 0.152 181.912)');
        $theme->add('--color-teal-500', 'oklch(70.4% 0.14 182.503)');
        $theme->add('--color-teal-600', 'oklch(60% 0.118 184.704)');
        $theme->add('--color-teal-700', 'oklch(51.1% 0.096 186.391)');
        $theme->add('--color-teal-800', 'oklch(43.7% 0.078 188.216)');
        $theme->add('--color-teal-900', 'oklch(38.6% 0.063 188.416)');
        $theme->add('--color-teal-950', 'oklch(27.7% 0.046 192.524)');

        // Cyan
        $theme->add('--color-cyan-50', 'oklch(98.4% 0.019 200.873)');
        $theme->add('--color-cyan-100', 'oklch(95.6% 0.045 203.388)');
        $theme->add('--color-cyan-200', 'oklch(91.7% 0.08 205.041)');
        $theme->add('--color-cyan-300', 'oklch(86.5% 0.127 207.078)');
        $theme->add('--color-cyan-400', 'oklch(78.9% 0.154 211.53)');
        $theme->add('--color-cyan-500', 'oklch(71.5% 0.143 215.221)');
        $theme->add('--color-cyan-600', 'oklch(60.9% 0.126 221.723)');
        $theme->add('--color-cyan-700', 'oklch(52% 0.105 223.128)');
        $theme->add('--color-cyan-800', 'oklch(45% 0.085 224.283)');
        $theme->add('--color-cyan-900', 'oklch(39.8% 0.07 227.392)');
        $theme->add('--color-cyan-950', 'oklch(30.2% 0.056 229.695)');

        // Sky
        $theme->add('--color-sky-50', 'oklch(97.7% 0.013 236.62)');
        $theme->add('--color-sky-100', 'oklch(95.1% 0.026 236.824)');
        $theme->add('--color-sky-200', 'oklch(90.1% 0.058 230.902)');
        $theme->add('--color-sky-300', 'oklch(82.8% 0.111 230.318)');
        $theme->add('--color-sky-400', 'oklch(74.6% 0.16 232.661)');
        $theme->add('--color-sky-500', 'oklch(68.5% 0.169 237.323)');
        $theme->add('--color-sky-600', 'oklch(58.8% 0.158 241.966)');
        $theme->add('--color-sky-700', 'oklch(50% 0.134 242.749)');
        $theme->add('--color-sky-800', 'oklch(44.3% 0.11 240.79)');
        $theme->add('--color-sky-900', 'oklch(39.1% 0.09 240.876)');
        $theme->add('--color-sky-950', 'oklch(29.3% 0.066 243.157)');

        // Blue
        $theme->add('--color-blue-50', 'oklch(97% 0.014 254.604)');
        $theme->add('--color-blue-100', 'oklch(93.2% 0.032 255.585)');
        $theme->add('--color-blue-200', 'oklch(88.2% 0.059 254.128)');
        $theme->add('--color-blue-300', 'oklch(80.9% 0.105 251.813)');
        $theme->add('--color-blue-400', 'oklch(70.7% 0.165 254.624)');
        $theme->add('--color-blue-500', 'oklch(62.3% 0.214 259.815)');
        $theme->add('--color-blue-600', 'oklch(54.6% 0.245 262.881)');
        $theme->add('--color-blue-700', 'oklch(48.8% 0.243 264.376)');
        $theme->add('--color-blue-800', 'oklch(42.4% 0.199 265.638)');
        $theme->add('--color-blue-900', 'oklch(37.9% 0.146 265.522)');
        $theme->add('--color-blue-950', 'oklch(28.2% 0.091 267.935)');

        // Indigo
        $theme->add('--color-indigo-50', 'oklch(96.2% 0.018 272.314)');
        $theme->add('--color-indigo-100', 'oklch(93% 0.034 272.788)');
        $theme->add('--color-indigo-200', 'oklch(87% 0.065 274.039)');
        $theme->add('--color-indigo-300', 'oklch(78.5% 0.115 274.713)');
        $theme->add('--color-indigo-400', 'oklch(67.3% 0.182 276.935)');
        $theme->add('--color-indigo-500', 'oklch(58.5% 0.233 277.117)');
        $theme->add('--color-indigo-600', 'oklch(51.1% 0.262 276.966)');
        $theme->add('--color-indigo-700', 'oklch(45.7% 0.24 277.023)');
        $theme->add('--color-indigo-800', 'oklch(39.8% 0.195 277.366)');
        $theme->add('--color-indigo-900', 'oklch(35.9% 0.144 278.697)');
        $theme->add('--color-indigo-950', 'oklch(25.7% 0.09 281.288)');

        // Violet
        $theme->add('--color-violet-50', 'oklch(96.9% 0.016 293.756)');
        $theme->add('--color-violet-100', 'oklch(94.3% 0.029 294.588)');
        $theme->add('--color-violet-200', 'oklch(89.4% 0.057 293.283)');
        $theme->add('--color-violet-300', 'oklch(81.1% 0.111 293.571)');
        $theme->add('--color-violet-400', 'oklch(70.2% 0.183 293.541)');
        $theme->add('--color-violet-500', 'oklch(60.6% 0.25 292.717)');
        $theme->add('--color-violet-600', 'oklch(54.1% 0.281 293.009)');
        $theme->add('--color-violet-700', 'oklch(49.1% 0.27 292.581)');
        $theme->add('--color-violet-800', 'oklch(43.2% 0.232 292.759)');
        $theme->add('--color-violet-900', 'oklch(38% 0.189 293.745)');
        $theme->add('--color-violet-950', 'oklch(28.3% 0.141 291.089)');

        // Purple
        $theme->add('--color-purple-50', 'oklch(97.7% 0.014 308.299)');
        $theme->add('--color-purple-100', 'oklch(94.6% 0.033 307.174)');
        $theme->add('--color-purple-200', 'oklch(90.2% 0.063 306.703)');
        $theme->add('--color-purple-300', 'oklch(82.7% 0.119 306.383)');
        $theme->add('--color-purple-400', 'oklch(71.4% 0.203 305.504)');
        $theme->add('--color-purple-500', 'oklch(62.7% 0.265 303.9)');
        $theme->add('--color-purple-600', 'oklch(55.8% 0.288 302.321)');
        $theme->add('--color-purple-700', 'oklch(49.6% 0.265 301.924)');
        $theme->add('--color-purple-800', 'oklch(43.8% 0.218 303.724)');
        $theme->add('--color-purple-900', 'oklch(38.1% 0.176 304.987)');
        $theme->add('--color-purple-950', 'oklch(29.1% 0.149 302.717)');

        // Fuchsia
        $theme->add('--color-fuchsia-50', 'oklch(97.7% 0.017 320.058)');
        $theme->add('--color-fuchsia-100', 'oklch(95.2% 0.037 318.852)');
        $theme->add('--color-fuchsia-200', 'oklch(90.3% 0.076 319.62)');
        $theme->add('--color-fuchsia-300', 'oklch(83.3% 0.145 321.434)');
        $theme->add('--color-fuchsia-400', 'oklch(74% 0.238 322.16)');
        $theme->add('--color-fuchsia-500', 'oklch(66.7% 0.295 322.15)');
        $theme->add('--color-fuchsia-600', 'oklch(59.1% 0.293 322.896)');
        $theme->add('--color-fuchsia-700', 'oklch(51.8% 0.253 323.949)');
        $theme->add('--color-fuchsia-800', 'oklch(45.2% 0.211 324.591)');
        $theme->add('--color-fuchsia-900', 'oklch(40.1% 0.17 325.612)');
        $theme->add('--color-fuchsia-950', 'oklch(29.3% 0.136 325.661)');

        // Pink
        $theme->add('--color-pink-50', 'oklch(97.1% 0.014 343.198)');
        $theme->add('--color-pink-100', 'oklch(94.8% 0.028 342.258)');
        $theme->add('--color-pink-200', 'oklch(89.9% 0.061 343.231)');
        $theme->add('--color-pink-300', 'oklch(82.3% 0.12 346.018)');
        $theme->add('--color-pink-400', 'oklch(71.8% 0.202 349.761)');
        $theme->add('--color-pink-500', 'oklch(65.6% 0.241 354.308)');
        $theme->add('--color-pink-600', 'oklch(59.2% 0.249 0.584)');
        $theme->add('--color-pink-700', 'oklch(52.5% 0.223 3.958)');
        $theme->add('--color-pink-800', 'oklch(45.9% 0.187 3.815)');
        $theme->add('--color-pink-900', 'oklch(40.8% 0.153 2.432)');
        $theme->add('--color-pink-950', 'oklch(28.4% 0.109 3.907)');

        // Rose
        $theme->add('--color-rose-50', 'oklch(96.9% 0.015 12.422)');
        $theme->add('--color-rose-100', 'oklch(94.1% 0.03 12.58)');
        $theme->add('--color-rose-200', 'oklch(89.2% 0.058 10.001)');
        $theme->add('--color-rose-300', 'oklch(81% 0.117 11.638)');
        $theme->add('--color-rose-400', 'oklch(71.2% 0.194 13.428)');
        $theme->add('--color-rose-500', 'oklch(64.5% 0.246 16.439)');
        $theme->add('--color-rose-600', 'oklch(58.6% 0.253 17.585)');
        $theme->add('--color-rose-700', 'oklch(51.4% 0.222 16.935)');
        $theme->add('--color-rose-800', 'oklch(45.5% 0.188 13.697)');
        $theme->add('--color-rose-900', 'oklch(41% 0.159 10.272)');
        $theme->add('--color-rose-950', 'oklch(27.1% 0.105 12.094)');

        // Slate
        $theme->add('--color-slate-50', 'oklch(98.4% 0.003 247.858)');
        $theme->add('--color-slate-100', 'oklch(96.8% 0.007 247.896)');
        $theme->add('--color-slate-200', 'oklch(92.9% 0.013 255.508)');
        $theme->add('--color-slate-300', 'oklch(86.9% 0.022 252.894)');
        $theme->add('--color-slate-400', 'oklch(70.4% 0.04 256.788)');
        $theme->add('--color-slate-500', 'oklch(55.4% 0.046 257.417)');
        $theme->add('--color-slate-600', 'oklch(44.6% 0.043 257.281)');
        $theme->add('--color-slate-700', 'oklch(37.2% 0.044 257.287)');
        $theme->add('--color-slate-800', 'oklch(27.9% 0.041 260.031)');
        $theme->add('--color-slate-900', 'oklch(20.8% 0.042 265.755)');
        $theme->add('--color-slate-950', 'oklch(12.9% 0.042 264.695)');

        // Gray
        $theme->add('--color-gray-50', 'oklch(98.5% 0.002 247.839)');
        $theme->add('--color-gray-100', 'oklch(96.7% 0.003 264.542)');
        $theme->add('--color-gray-200', 'oklch(92.8% 0.006 264.531)');
        $theme->add('--color-gray-300', 'oklch(87.2% 0.01 258.338)');
        $theme->add('--color-gray-400', 'oklch(70.7% 0.022 261.325)');
        $theme->add('--color-gray-500', 'oklch(55.1% 0.027 264.364)');
        $theme->add('--color-gray-600', 'oklch(44.6% 0.03 256.802)');
        $theme->add('--color-gray-700', 'oklch(37.3% 0.034 259.733)');
        $theme->add('--color-gray-800', 'oklch(27.8% 0.033 256.848)');
        $theme->add('--color-gray-900', 'oklch(21% 0.034 264.665)');
        $theme->add('--color-gray-950', 'oklch(13% 0.028 261.692)');

        // Zinc
        $theme->add('--color-zinc-50', 'oklch(98.5% 0 0)');
        $theme->add('--color-zinc-100', 'oklch(96.7% 0.001 286.375)');
        $theme->add('--color-zinc-200', 'oklch(92% 0.004 286.32)');
        $theme->add('--color-zinc-300', 'oklch(87.1% 0.006 286.286)');
        $theme->add('--color-zinc-400', 'oklch(70.5% 0.015 286.067)');
        $theme->add('--color-zinc-500', 'oklch(55.2% 0.016 285.938)');
        $theme->add('--color-zinc-600', 'oklch(44.2% 0.017 285.786)');
        $theme->add('--color-zinc-700', 'oklch(37% 0.013 285.805)');
        $theme->add('--color-zinc-800', 'oklch(27.4% 0.006 286.033)');
        $theme->add('--color-zinc-900', 'oklch(21% 0.006 285.885)');
        $theme->add('--color-zinc-950', 'oklch(14.1% 0.005 285.823)');

        // Neutral
        $theme->add('--color-neutral-50', 'oklch(98.5% 0 0)');
        $theme->add('--color-neutral-100', 'oklch(97% 0 0)');
        $theme->add('--color-neutral-200', 'oklch(92.2% 0 0)');
        $theme->add('--color-neutral-300', 'oklch(87% 0 0)');
        $theme->add('--color-neutral-400', 'oklch(70.8% 0 0)');
        $theme->add('--color-neutral-500', 'oklch(55.6% 0 0)');
        $theme->add('--color-neutral-600', 'oklch(43.9% 0 0)');
        $theme->add('--color-neutral-700', 'oklch(37.1% 0 0)');
        $theme->add('--color-neutral-800', 'oklch(26.9% 0 0)');
        $theme->add('--color-neutral-900', 'oklch(20.5% 0 0)');
        $theme->add('--color-neutral-950', 'oklch(14.5% 0 0)');

        // Stone
        $theme->add('--color-stone-50', 'oklch(98.5% 0.001 106.423)');
        $theme->add('--color-stone-100', 'oklch(97% 0.001 106.424)');
        $theme->add('--color-stone-200', 'oklch(92.3% 0.003 48.717)');
        $theme->add('--color-stone-300', 'oklch(86.9% 0.005 56.366)');
        $theme->add('--color-stone-400', 'oklch(70.9% 0.01 56.259)');
        $theme->add('--color-stone-500', 'oklch(55.3% 0.013 58.071)');
        $theme->add('--color-stone-600', 'oklch(44.4% 0.011 73.639)');
        $theme->add('--color-stone-700', 'oklch(37.4% 0.01 67.558)');
        $theme->add('--color-stone-800', 'oklch(26.8% 0.007 34.298)');
        $theme->add('--color-stone-900', 'oklch(21.6% 0.006 56.043)');
        $theme->add('--color-stone-950', 'oklch(14.7% 0.004 49.25)');

        // Black and white
        $theme->add('--color-black', '#000');
        $theme->add('--color-white', '#fff');

        // Utility colors (for backwards compatibility)
        $theme->add('--color-transparent', 'transparent');
        $theme->add('--color-current', 'currentColor');
        $theme->add('--color-inherit', 'inherit');
    }

    private static function addSpacing(Theme $theme): void
    {
        $theme->add('--spacing', '0.25rem');
    }

    private static function addBreakpoints(Theme $theme): void
    {
        $theme->add('--breakpoint-sm', '40rem');
        $theme->add('--breakpoint-md', '48rem');
        $theme->add('--breakpoint-lg', '64rem');
        $theme->add('--breakpoint-xl', '80rem');
        $theme->add('--breakpoint-2xl', '96rem');
    }

    private static function addContainers(Theme $theme): void
    {
        $theme->add('--container-3xs', '16rem');
        $theme->add('--container-2xs', '18rem');
        $theme->add('--container-xs', '20rem');
        $theme->add('--container-sm', '24rem');
        $theme->add('--container-md', '28rem');
        $theme->add('--container-lg', '32rem');
        $theme->add('--container-xl', '36rem');
        $theme->add('--container-2xl', '42rem');
        $theme->add('--container-3xl', '48rem');
        $theme->add('--container-4xl', '56rem');
        $theme->add('--container-5xl', '64rem');
        $theme->add('--container-6xl', '72rem');
        $theme->add('--container-7xl', '80rem');
    }

    private static function addTextSizes(Theme $theme): void
    {
        $theme->add('--text-xs', '0.75rem');
        $theme->add('--text-xs--line-height', 'calc(1 / 0.75)');
        $theme->add('--text-sm', '0.875rem');
        $theme->add('--text-sm--line-height', 'calc(1.25 / 0.875)');
        $theme->add('--text-base', '1rem');
        $theme->add('--text-base--line-height', 'calc(1.5 / 1)');
        $theme->add('--text-lg', '1.125rem');
        $theme->add('--text-lg--line-height', 'calc(1.75 / 1.125)');
        $theme->add('--text-xl', '1.25rem');
        $theme->add('--text-xl--line-height', 'calc(1.75 / 1.25)');
        $theme->add('--text-2xl', '1.5rem');
        $theme->add('--text-2xl--line-height', 'calc(2 / 1.5)');
        $theme->add('--text-3xl', '1.875rem');
        $theme->add('--text-3xl--line-height', 'calc(2.25 / 1.875)');
        $theme->add('--text-4xl', '2.25rem');
        $theme->add('--text-4xl--line-height', 'calc(2.5 / 2.25)');
        $theme->add('--text-5xl', '3rem');
        $theme->add('--text-5xl--line-height', '1');
        $theme->add('--text-6xl', '3.75rem');
        $theme->add('--text-6xl--line-height', '1');
        $theme->add('--text-7xl', '4.5rem');
        $theme->add('--text-7xl--line-height', '1');
        $theme->add('--text-8xl', '6rem');
        $theme->add('--text-8xl--line-height', '1');
        $theme->add('--text-9xl', '8rem');
        $theme->add('--text-9xl--line-height', '1');
    }

    private static function addFontWeights(Theme $theme): void
    {
        $theme->add('--font-weight-thin', '100');
        $theme->add('--font-weight-extralight', '200');
        $theme->add('--font-weight-light', '300');
        $theme->add('--font-weight-normal', '400');
        $theme->add('--font-weight-medium', '500');
        $theme->add('--font-weight-semibold', '600');
        $theme->add('--font-weight-bold', '700');
        $theme->add('--font-weight-extrabold', '800');
        $theme->add('--font-weight-black', '900');
    }

    private static function addTracking(Theme $theme): void
    {
        $theme->add('--tracking-tighter', '-0.05em');
        $theme->add('--tracking-tight', '-0.025em');
        $theme->add('--tracking-normal', '0em');
        $theme->add('--tracking-wide', '0.025em');
        $theme->add('--tracking-wider', '0.05em');
        $theme->add('--tracking-widest', '0.1em');
    }

    private static function addLeading(Theme $theme): void
    {
        $theme->add('--leading-tight', '1.25');
        $theme->add('--leading-snug', '1.375');
        $theme->add('--leading-normal', '1.5');
        $theme->add('--leading-relaxed', '1.625');
        $theme->add('--leading-loose', '2');
    }

    private static function addRadius(Theme $theme): void
    {
        $theme->add('--radius-xs', '0.125rem');
        $theme->add('--radius-sm', '0.25rem');
        $theme->add('--radius-md', '0.375rem');
        $theme->add('--radius-lg', '0.5rem');
        $theme->add('--radius-xl', '0.75rem');
        $theme->add('--radius-2xl', '1rem');
        $theme->add('--radius-3xl', '1.5rem');
        $theme->add('--radius-4xl', '2rem');
    }

    private static function addShadows(Theme $theme): void
    {
        $theme->add('--shadow-2xs', '0 1px rgb(0 0 0 / 0.05)');
        $theme->add('--shadow-xs', '0 1px 2px 0 rgb(0 0 0 / 0.05)');
        $theme->add('--shadow-sm', '0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)');
        $theme->add('--shadow-md', '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)');
        $theme->add('--shadow-lg', '0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)');
        $theme->add('--shadow-xl', '0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)');
        $theme->add('--shadow-2xl', '0 25px 50px -12px rgb(0 0 0 / 0.25)');
    }

    private static function addInsetShadows(Theme $theme): void
    {
        $theme->add('--inset-shadow-2xs', 'inset 0 1px rgb(0 0 0 / 0.05)');
        $theme->add('--inset-shadow-xs', 'inset 0 1px 1px rgb(0 0 0 / 0.05)');
        $theme->add('--inset-shadow-sm', 'inset 0 2px 4px rgb(0 0 0 / 0.05)');
        $theme->add('--inset-shadow', 'inset 0 2px 4px rgb(0 0 0 / 0.05)');
    }

    private static function addDropShadows(Theme $theme): void
    {
        $theme->add('--drop-shadow-xs', '0 1px 1px rgb(0 0 0 / 0.05)');
        $theme->add('--drop-shadow-sm', '0 1px 2px rgb(0 0 0 / 0.15)');
        $theme->add('--drop-shadow-md', '0 3px 3px rgb(0 0 0 / 0.12)');
        $theme->add('--drop-shadow-lg', '0 4px 4px rgb(0 0 0 / 0.15)');
        $theme->add('--drop-shadow-xl', '0 9px 7px rgb(0 0 0 / 0.1)');
        $theme->add('--drop-shadow-2xl', '0 25px 25px rgb(0 0 0 / 0.15)');
    }

    private static function addTextShadows(Theme $theme): void
    {
        $theme->add('--text-shadow-2xs', '0px 1px 0px rgb(0 0 0 / 0.15)');
        $theme->add('--text-shadow-xs', '0px 1px 1px rgb(0 0 0 / 0.2)');
        $theme->add('--text-shadow-sm', '0px 1px 0px rgb(0 0 0 / 0.075), 0px 1px 1px rgb(0 0 0 / 0.075), 0px 2px 2px rgb(0 0 0 / 0.075)');
        $theme->add('--text-shadow-md', '0px 1px 1px rgb(0 0 0 / 0.1), 0px 1px 2px rgb(0 0 0 / 0.1), 0px 2px 4px rgb(0 0 0 / 0.1)');
        $theme->add('--text-shadow-lg', '0px 1px 2px rgb(0 0 0 / 0.1), 0px 3px 2px rgb(0 0 0 / 0.1), 0px 4px 8px rgb(0 0 0 / 0.1)');
    }

    private static function addEasing(Theme $theme): void
    {
        $theme->add('--ease-in', 'cubic-bezier(0.4, 0, 1, 1)');
        $theme->add('--ease-out', 'cubic-bezier(0, 0, 0.2, 1)');
        $theme->add('--ease-in-out', 'cubic-bezier(0.4, 0, 0.2, 1)');
    }

    private static function addAnimations(Theme $theme): void
    {
        $theme->add('--animate-spin', 'spin 1s linear infinite');
        $theme->add('--animate-ping', 'ping 1s cubic-bezier(0, 0, 0.2, 1) infinite');
        $theme->add('--animate-pulse', 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite');
        $theme->add('--animate-bounce', 'bounce 1s infinite');

        // Note: Keyframes are registered separately in the theme system
        // The keyframe definitions are:
        // @keyframes spin { to { transform: rotate(360deg); } }
        // @keyframes ping { 75%, 100% { transform: scale(2); opacity: 0; } }
        // @keyframes pulse { 50% { opacity: 0.5; } }
        // @keyframes bounce { 0%, 100% { transform: translateY(-25%); animation-timing-function: cubic-bezier(0.8, 0, 1, 1); } 50% { transform: none; animation-timing-function: cubic-bezier(0, 0, 0.2, 1); } }
    }

    private static function addBlur(Theme $theme): void
    {
        $theme->add('--blur-xs', '4px');
        $theme->add('--blur-sm', '8px');
        $theme->add('--blur-md', '12px');
        $theme->add('--blur-lg', '16px');
        $theme->add('--blur-xl', '24px');
        $theme->add('--blur-2xl', '40px');
        $theme->add('--blur-3xl', '64px');
    }

    private static function addPerspective(Theme $theme): void
    {
        $theme->add('--perspective-dramatic', '100px');
        $theme->add('--perspective-near', '300px');
        $theme->add('--perspective-normal', '500px');
        $theme->add('--perspective-midrange', '800px');
        $theme->add('--perspective-distant', '1200px');
    }

    private static function addAspect(Theme $theme): void
    {
        $theme->add('--aspect-video', '16 / 9');
    }

    private static function addDefaults(Theme $theme): void
    {
        $theme->add('--default-transition-duration', '150ms');
        $theme->add('--default-transition-timing-function', 'cubic-bezier(0.4, 0, 0.2, 1)');
        $theme->add('--default-font-family', '--theme(--font-sans, initial)');
        $theme->add('--default-font-feature-settings', '--theme(--font-sans--font-feature-settings, initial)');
        $theme->add('--default-font-variation-settings', '--theme(--font-sans--font-variation-settings, initial)');
        $theme->add('--default-mono-font-family', '--theme(--font-mono, initial)');
        $theme->add('--default-mono-font-feature-settings', '--theme(--font-mono--font-feature-settings, initial)');
        $theme->add('--default-mono-font-variation-settings', '--theme(--font-mono--font-variation-settings, initial)');
    }

    private static function addDeprecated(Theme $theme): void
    {
        // Deprecated values (for backwards compatibility)
        $theme->add('--blur', '8px');
        $theme->add('--shadow', '0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)');
        $theme->add('--shadow-inner', 'inset 0 2px 4px 0 rgb(0 0 0 / 0.05)');
        $theme->add('--drop-shadow', '0 1px 2px rgb(0 0 0 / 0.1), 0 1px 1px rgb(0 0 0 / 0.06)');
        $theme->add('--radius', '0.25rem');
        $theme->add('--max-width-prose', '65ch');
    }
}
