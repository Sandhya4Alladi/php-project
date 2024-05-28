<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(

	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Dbox Test',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	//import all files here
	'import'=>array(
        'application.models.*',
        'application.components.*',
		'application.components.helpers.*',
        'ext.YiiMongoDbSuite.*',
    ),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		'streambox',
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'123',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),


	),

	// application components
	'components'=>array(

		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		'ivsBroadcastClient' => array(
			'class' => 'application.components.IvsBroadcastClient', // Replace with the actual class path
			// Add any other configuration options here
		),

		// 

		// 'jwtAuth' => array(
		// 	'class' => 'application.components.JWT',
		// 	// You can configure other properties of your JWT authentication component here
		// ),

		'session' => array(
            'class' => 'application.components.RedisSessionManager',
            'autoStart' => true,
           // 'cookieMode' => 'none', //set php.ini to session.use_cookies = 0, session.use_only_cookies = 0
            'useTransparentSessionID' => true, //set php.ini to session.use_trans_sid = 1
            'sessionName' => 'session',
            'saveHandler' => 'redis',
            'savePath' => 'tcp://172.18.0.1:6379?database=0&prefix=session::',
            'timeout' => 28800,
			'cookieParams' => array(
                'lifetime' => 3600, // Lifetime of the session cookie in seconds
                'path' => '/', // Path on the domain where the cookie will work
                'domain' => '.darwinboxlocal.com', // Domain for which the cookie is valid
            ),
        ),
 
        'cache' => array(
            'class' => 'CRedisCache',
            'hostname' => '172.18.0.1',
            'port' => 6379,
            'database' => 0,
            'options' => STREAM_CLIENT_CONNECT,
        ),

		'mongodb' => array(
            'class' => 'EMongoDB',
            'connectionString' => 'mongodb+srv://afrinmahammad7403:7403@cluster0.9fggrwa.mongodb.net/',
            'dbName' => 'php-s',
            'fsyncFlag' => true,
            'safeFlag' => true,
        ),

		// uncomment the following to enable URLs in path-format

		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),


		// database settings are configured in database.php
		'db'=>require(dirname(__FILE__).'/database.php'),

		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>YII_DEBUG ? null : 'site/error',
		),

		// 'log'=>array(
		// 	'class'=>'CLogRouter',
		// 	'routes'=>array(
		// 		array(
		// 			'class'=>'CFileLogRoute',
		// 			'levels'=>'error, warning',
		// 		),
		// 		// uncomment the following to show log messages on web pages

		// 		array(
		// 			'class'=>'CWebLogRoute',
		// 		),

		// 	),
		// ),

	),

	

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
	),
);
