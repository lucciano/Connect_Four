<?php
require_once __DIR__.'/../vendor/autoload.php'; 

$app = new Silex\Application(); 
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->get('/', function() use($app) { 
    return $app['twig']->render('wellcome.html.twig');
}); 

$app->get('/games/{user}', function($user) use($app) { 
    $style_map = array('1' => 'player1', '2' => 'player2');
    if(!in_array($user, array('1','2'))){
	throw new \Exception;
    }
    return $app['twig']->render('user.html.twig', array('user' => (int) $user, 'css_class' => $style_map[$user]));
})->bind('user'); 

$app->get('/reset/{user}', function($user) use($app) { 
	$game = new FourInLine\Status();
	$game->newGame();
	return $app->redirect('/games/'.$user);
})->bind('reset');

$app->post('/turn/{user}/{move}',  function($user, $move) use ($app) {
	$game = new FourInLine\Status();
	list($x, $y)= preg_split("|_|", $move);
	$game->move($user, $x, $y);
	return $app->json($game->export());
})->bind('turn');

$app->get('/game-status', function() use ($app) {
	$game = new FourInLine\Status();
	return $app->json($game->export());
})->bind('game-status');

$app->error(function (\Exception $e, $code) use ($app) {

    if ($code == 404) {

        return "Page not found";
    }

    return 'We are sorry, but something went terribly wrong - code: '. $code ."\n". $e->getTraceAsString();

});

$app->run(); 
