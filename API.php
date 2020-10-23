<?php

include_once 'DBC.php';

class Item implements JsonSerializable{
//valores: id, tipo, marca, modelo, estado, qtde
	private $id;
	private $tipo;
	private $marca;
	private $modelo;
	private $estado;
	private $qtde;

	function __construct(){
		
	}

	function listarTipo($tipo){
		$con = OpenCon("estoque");

		$q = 'SELECT id, t.nome, m.nome, modelo, estado, qtde FROM controle INNER JOIN tipo t on id_tipo = t.id INNER JOIN marca m on id_marca = m.id where estado in ("Novo","Usado")';

		$con->query($q);		
	}

	function setId($n){
		$this->id = $n;
	}

	//decodificar de JSON para Item
	function decodeJson($json_str){

		$obj = json_decode($json_str);
		$this->id = $obj->id;
		$this->tipo = $obj->tipo;
		$this->marca = $obj->marca;
		$this->modelo = $obj->modelo;
		$this->estado = $obj->estado;
		$this->qtde = $obj->qtde;
	}

	//codificar de Item para JSON string
	function toJson(){

		return json_encode($this);
	}

	//funcao que substitui o jsonSerialize normal, que nao associa
	function jsonSerialize(){

		$array = [
			'id' => $this->id,
			'tipo' => $this->tipo,
			'marca' => $this->marca,
			'modelo' => $this->modelo,
			'estado' => $this->estado,
			'qtde' => $this->qtde
		];

		return $array;
	}
}
$item = new Item();
$item->setId(2);
$item_str = $item->toJson();//json_encode($array, JSON_PARTIAL_OUTPUT_ON_ERROR);
$item->setId(1);
echo 'O item em Json ficou: '.$item_str;

$item3 = new Item();
$item3->decodeJson($item_str);

echo '<br>';
var_dump($item3);
echo '<br>';
var_dump($item);