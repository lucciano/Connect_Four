<?php
require_once __DIR__.'/../vendor/autoload.php'; 

$app = new Silex\Application(); 
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app->get('/', function() use($app) { 
    return $app['twig']->render('wellcome.html.twig');
}); 

$app->get('/games/{user}', function($user) use($app) { 
    if(!in_array($user, array('1','2'))){
	throw new \Exception;
    }
    return 'Wellcome  User '.$user;
}); 

$app->error(function (\Exception $e, $code) use ($app) {

    if ($code == 404) {

        return "Page not found";
    }

    return 'We are sorry, but something went terribly wrong - code: '. $code;

});

$app->run(); 
