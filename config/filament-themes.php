<?php

return [
    /**
     * Disable the theme manager completely.
     * If the theme manager is disabled, the provider won't register the
     * theme and the default theme color should be
     * managed by the filament/filament package.
     */
    'auto_register' => true,

    /**
     * You need to change this value if in the webpack the
     * tailwind resources is different to: `public/css/app.css`
     */
    'theme_public_path' => 'assets/css/extend.css',

    /**
     * The color used by the theme.
     * Available colors based on the default tailwind named colors:
     *   - slate: slate.css
     *   - gray: gray.css
     *   - zinc: zinc.css
     *   - neutral neutral.css
     *   - stone: stone.css
     *   - red: red.css
     *   - orange: orange.css
     *   - amber: amber.css
     *   - yellow: yellow.css
     *   - lime: lime.css
     *   - green: green.css
     *   - emerald: emerald.css
     *   - teal: teal.css
     *   - cyan: cyan.css
     *   - sky: sky.css
     *   - blue: blue.css
     *   - indigo: indigo.css
     *   - violet: violet.css
     *   - purple: purple.css
     *   - fuchsia: fuchsia.css
     *   - pink: pink.css
     *   - rose: rose.css
     */
    'color_public_path' => 'assets/css/red-theme.css',
];
