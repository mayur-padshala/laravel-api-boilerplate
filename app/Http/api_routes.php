<?php
/**
 * @var $api \Dingo\Api\Routing\Router
 */
// API VERSION 1 ROUTES. Can have multiple versions here.
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function (\Dingo\Api\Routing\Router $api) {

	require_once app_path('Api/V1/routes.php');

});
