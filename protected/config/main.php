<?php
/**
 * Organizzy
 * Copyright (C) 2014 Organizzy Team
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return [
	'basePath' => __DIR__ .'/..',
	'name'=>'Organizzy',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),


	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
            'loginUrl' => array('/user/login'),
            'returnUrl' => array('/activity/index'),
		),

		'urlManager'=>array(
			'urlFormat'=>'path',
            'showScriptName'=>false,
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<id:\d+>/<action:\w+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),


		'db'=>array(
			'emulatePrepare' => true,
			'charset' => 'utf8',
            'schemaCachingDuration'=>7200,
		),

		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),

		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error,trace,info,warning',
                    'categories'=>'system.db.CDbCommand',
                    'logFile'=>'sql.log',
                ),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),

        'request' => [
            'enableCookieValidation' => true,
        ],

        'session' => [
            'sessionName' => 'sid',
            'cookieParams' => [
                'lifetime' => '2592000', // 30 days
            ]
        ],

        'mail' => [
            'class' => 'Mailer'
        ]
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		'domainName' => 'm.organizzy.org',
        'adminEmail' => 'admin@organizzy.org',
	),
];

