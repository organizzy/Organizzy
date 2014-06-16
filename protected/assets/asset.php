<?php
/**
 * Organizzy
 * copyright (c) 2014 abie
 *
 * @author abie
 * @date 6/16/14 1:21 PM
 */

return [
    'inputBase' => __DIR__,
    'outputBase' => __DIR__ . '/../../web/assets',

    'assets' => [
        [
            'out' => 'app.js',
            'in' => ['scripts/global.js', 'scripts/ajax-page.js'],
            'type' => 'js',
        ],

        [
            'out' => 'style.css',
            'in' => ['stylesheets/style.scss'],
            'watch' => [
                'stylesheets/_event.scss',
                'stylesheets/_home.scss',
                'stylesheets/_ratchet_theme.scss',
                'stylesheets/_shared.scss',
                'stylesheets/_user.scss',
            ],
            'type' => 'scss',
        ],

    ],

    'processors' => [
        'js' => [ClosureAssetProcessor::getClass(), JsMinAssetProcessor::getClass()],
        'scss' => [ScssPhpAssetProcessor::getClass()],
    ],
];