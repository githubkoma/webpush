<?php
/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\WebPush\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'routes' => [
	   //['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
	   ['name' => 'page#do_echo', 'url' => '/echo', 'verb' => 'POST'],
       ['name' => 'personal_settings#subscribe', 'url' => '/subscribe', 'verb' => 'POST'],
       ['name' => 'admin_settings#generate_vapid_keys', 'url' => '/generateVapidkeys', 'verb' => 'GET'],
       ['name' => 'push_api#push_me', 'url' => '/api/v0/pushMe/{myself}/{userApiKey}/{message}', 'verbs' => ['GET', 'POST']],
    ]
];
