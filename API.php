<?php

require 'vendor/autoload.php';

use Src\Controllers\StockController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url ($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$uri = explode('/', $uri);

if($uri[count($uri)-1] !== 'item') {
	header('HTTP/1.1 404 Not Found');
	exit();
}

$itemId = null;

if (isset($uri[2])) {
	$userId = (int) $uri[2];
}

$requestMethod = $_SERVER['REQUEST_METHOD'];

$controller = new StockController("estoque", $requestMethod, $itemId);
echo $controller->processRequest();