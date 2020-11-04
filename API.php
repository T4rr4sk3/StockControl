<?php

require 'vendor/autoload.php';

use Src\Controllers\StockController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url ($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$uri = explode('/', $uri);

$itemId = null;

// ..API.php/
if($uri[count($uri)-2] == 'item') //se URL for /item/x

    $itemId = (int) $uri[count($uri)-1];

elseif($uri[count($uri)-1] !== 'item') { //se URL nao tiver final /item

    header('HTTP/1.1 404 Not Found');
	exit();
}

$requestMethod = $_SERVER['REQUEST_METHOD'];

$controller = new StockController("estoque", $requestMethod, $itemId);
return $controller->processRequest();