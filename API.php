<?php

require 'vendor/autoload.php';

use Src\Controllers\StockController;

$alg_hash = "fnv1a32"; //forma de criptografia

$users = array('dfpelajo'=>'daniel37571537','guest'=>'guest');

$realm = 'estoque';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if(empty($_SERVER['PHP_AUTH_DIGEST'])) {
    header('HTTP/1.0 401 Unauthorized');
	header('WWW-Authenticate: Digest realm="'.$realm.'", qop="auth",nonce="'.hash($alg_hash,uniqid()).'",opaque="'.hash($alg_hash,$realm).'"');
	die();
}   

    $str_digest = $_SERVER['PHP_AUTH_DIGEST'];

    $pos = strpos($str_digest, "nonce="); //pegar a primeira posicao no 'nonce' na string
    $nonce = substr($str_digest, $pos+7, 8); // adicionar mais sete para avancar a pos para a primeira aspa e pegar o nonce de 8 dig

    $substr = trim($str_digest, 'username="'); //pega a string tirando o 'username="'
	$username = substr($substr, 0, strpos($substr, '"')); // pega o nome ate encontrar o fecha aspas.

    $pos = strpos($str_digest, "nc="); // pega a posicao do nc=    
	$nc = substr($str_digest, $pos+3, 8); // com essa pos, pega todo o nc

    $pos = strpos($str_digest, "cnonce="); // pega a posicao do cnonce    
	$cnonce = substr($str_digest, $pos+8, 16); // com essa pos, pega o cnonce

    $uri = $_SERVER['REQUEST_URI']; // pega a URI

    //echo $_SERVER['PHP_AUTH_DIGEST'];

	$a1 = md5($username.':'.$realm.':'.$users[$username]);
	$a2 = md5($_SERVER['REQUEST_METHOD'].':'.$uri);
	$resp_valida = md5($a1.':'.$nonce.':'.$nc.':'.$cnonce.':'.'auth'.':'.$a2); //junta as informacoes e gera a resp a ser validada com o response
    
    $pos = strpos($_SERVER['PHP_AUTH_DIGEST'],'response=');
	$response = substr($_SERVER['PHP_AUTH_DIGEST'],$pos+10,32);

    if(! ($response == $resp_valida)){
        unset($_SERVER['PHP_AUTH_DIGEST']);
        die("Credencial invalida");
    }

    //if(!isset($_SESSION['USER_NONCE'])){

    //    $_SESSION['USER_NONCE'] = $nonce;
    //    $_SESSION['USERNAME'] = $username;

    //} elseif(!hash_equals($_SESSION['USER_NONCE'], $nonce)) {
    //        echo "autenticacao invalida.";
    //        die();
    //}

$uri = parse_url ($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$uri = explode('/', $uri);

$itemId = null;

$qtde = null;

$operacao = null;
// ..API.php/

if(($uri[count($uri)-3] == 'item') && ($uri[count($uri)-2] != 0)) { //se URL for /item/x/modo e x diferente de 0

    $itemId = (int) $uri[count($uri)-2];
    $operacao = $uri[count($uri)-1];

}
elseif(($uri[count($uri)-2] == 'item') && ($uri[count($uri)-1] != 0)) //se URL for /item/x e x diferente de 0

    $itemId = (int) $uri[count($uri)-1];

elseif($uri[count($uri)-1] !== 'item') { //se URL nao tiver final /item

    header('HTTP/1.1 404 Not Found');
    exit();
    
}

$requestMethod = $_SERVER['REQUEST_METHOD']; //pega o metodo do request

if (isset($_POST['qtde']))
    $qtde = $_POST['qtde'];

$controller = new StockController("estoque", $requestMethod, $itemId, $qtde, $operacao, $username);
echo $controller->processRequest();
return;