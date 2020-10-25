<?php 

namespace Src\Controllers;

require 'vendor/autoload.php';

use Src\Classes\Item;

class StockController {
	private $db;
	private $method;
	private $itemId;

	private $item;

	public function __construct($db, $requestMethod, $itemId) {
		$this->db = $db;
		$this->method = $requestMethod;
		$this->itemId = $itemId;

		$this->item = new Item($db);
	}

	public function processRequest()
	{ 		
		$response = null;
		switch ($this->method) {
			case 'GET':
				if ($this->itemId) {
					$response = $this->getItem($this->itemId);
				} else {
					$response = $this->getAllItem();
				};
				break;
		}

		if($response)
			return $response;
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

	private function notFoundResponse()
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