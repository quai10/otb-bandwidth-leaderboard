<?php

use OtbBandwidthLeaderboard\OtbApi;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/config.php';

$app = new \Slim\App();

$container = $app->getContainer();
$container['view'] = function ($c) {
    $view = new \Slim\Views\Smarty(__DIR__.'/templates/');
    $smartyPlugins = new \Slim\Views\SmartyPlugins($c['router'], $c['request']->getUri());
    $view->registerPlugin('function', 'path_for', [$smartyPlugins, 'pathFor']);
    $view->registerPlugin('function', 'base_url', [$smartyPlugins, 'baseUrl']);

    return $view;
};

$app->get('/', function (Request $request, Response $response) use ($container) {
    $api = new OtbApi(OTB_URL, OTB_USER, OTB_PASS);
    $hosts = $api->getTrafficData();
    $container->view->render(
        $response,
        'index.tpl',
        [
            'hosts' => $hosts,
        ]
    );
    /*
     * @todo Refresh with AJAX
     */
    return $response->withHeader('Refresh', '5; url='.$request->getUri());
});
$app->run();
