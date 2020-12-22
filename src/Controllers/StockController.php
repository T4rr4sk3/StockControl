<?php 

namespace Src\Controllers;

require 'vendor/autoload.php';

use Src\Classes\Item;

class StockController {
	private $db;
	private $method;
	private $itemId;
	private $qtde;
	private $op;
	private $username;

	private $item;

	public function __construct($db, $requestMethod, $itemId, $qtde = NULL, $operacao = NULL, $username = NULL) {
		$this->db = $db;
		$this->method = $requestMethod;
		$this->itemId = $itemId;
		$this->qtde = $qtde;
		$this->op = $operacao;
		$this->username = $username;

		$this->item = new Item($db, $username);
	}

	public function processRequest()
	{ 		
		$response = null;

		switch ($this->method) {
			case 'GET':
				if ($this->itemId)
					$response = $this->item->getItemJson($this->itemId);
				
				else
					$response = $this->item->listarTodosJson();
				
				break;

            case 'POST':
				if (isset($this->itemId) and isset($this->qtde) and isset($this->op)) 

					$response = $this->item->alteraQtde($this->itemId, $this->qtde, $this->op, $this->username);
					
			}

		if($response){
			$this->OK();
			return $response;
        }
		else
			$this->notFoundResponse();
	}

	private function getAllItem() {
		$result = $this->item->listarTodosJson();

		if(! $result)
			return $this->notFoundResponse();

		$this->OK();
		return $result;
	}

	private function getItem($id)
	{
		$result = $this->item->getItemJson($id);

		if(! $result)
			return $this->notFoundResponse();
		
		$this->OK();
		return $result;
	}

	private function removeQtde($id, $qtde){
        if($this->item->alterarQtde($id,OP_SUB))
			$result = "Sucess";

		if(! $result)
			return $this->notFoundResponse();
    }

	public function notFoundResponse()
	{
		$sapi_type = php_sapi_name();

		if (substr($sapi_type, 0, 3) == 'cgi')
		    header("Status: 404 Not Found");
		else
		    header("HTTP/1.1 404 Not Found");
	}

	private function OK()
	{
		$sapi_type = php_sapi_name();

		if (substr($sapi_type, 0, 3) == 'cgi')
		    header("Status: 200 OK");
		else
		    header("HTTP/1.1 200 OK");
	}
}