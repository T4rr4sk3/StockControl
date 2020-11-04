<?php

namespace Src\Classes;

include_once 'DBC.php';

class Item implements \JsonSerializable{
//valores: id, tipo, marca, modelo, estado, qtde
	private $id;
	private $tipo;
	private $marca;
	private $modelo;
	private $estado;
	private $qtde;
	private $db;

	function __construct($nome_db)
	{
		$this->db = $nome_db;
	}

	function getItemJson($itemId)
	{
		$con = OpenCon($this->db);

		$q = 'SELECT controle.id, t.nome as tipo, m.nome as marca, modelo, estado, qtde FROM controle INNER JOIN tipo t on id_tipo = t.id INNER JOIN marca m on id_marca = m.id where controle.id = ?';

		if( !( $p = $con->prepare($q) ) )

            echo "Prepare failed: (" . $con->errno . ") " . $con->error;

        if( !( $p->bind_param("i",$itemId) ) )

            echo "Parameters failed: (" . $p->errno . ") " . $p->error;

        if( !( $p->execute() ) )

            echo "Execute failed: (" . $p->errno . ") " . $p->error;

		$result = $p->get_result();

		if($result->num_rows == 1) {
			$r = $result->fetch_assoc();

			return $this->toJson($r);
		}
		else

			return null;


	}

	function listarTodosJson()
	{
        $con = OpenCon($this->db);

		$q = 'select nome from tipo';

		$p = $con->query($q);

		if($p->num_rows < 1)
            return "Erro, nenhum dado retornado do banco!";

		CloseCon($con);

		$array = array();
		
		while ($r = $p->fetch_assoc()){
            //array_push($array,$this->listarPorTipo($r['nome']));
			$array[$r['nome']] =  $this->listarPorTipo($r['nome']);
        }

		array_pop($array); //tira o ultimo elemento, que no caso é o vazio(ultima linha do fetch_assoc é vazia).

		return json_encode($array);

    }

	function listarPorTipoJson($tipo)
	{
        return $this->toJson($this->listarPorTipo($tipo));
    }

	private function listarPorTipo($tipo)
    {
		$con = OpenCon($this->db);

		$q = 'SELECT controle.id, t.nome as tipo, m.nome as marca, modelo, estado, qtde FROM controle INNER JOIN tipo t on id_tipo = t.id INNER JOIN marca m on id_marca = m.id where UPPER(t.nome) like UPPER(?) and estado in ("Novo","Usado")';

		if( !( $p = $con->prepare($q) ) )

            echo "Prepare failed: (" . $con->errno . ") " . $con->error;

        if( !( $p->bind_param("s",$tipo) ) )

            echo "Parameters failed: (" . $p->errno . ") " . $p->error;

        if( !( $p->execute() ) )

            echo "Execute failed: (" . $p->errno . ") " . $p->error;

		$result = $p->get_result();

		$arrayItem = array();

		while($r = $result->fetch_assoc()){
			$db = $this->db;
            $item = new Item($db);
			$item->decodeJson( htmlentities( json_encode( $r, JSON_UNESCAPED_UNICODE), 0, 'UTF-8'));
			array_push($arrayItem,$item);
			//linha do decode pega a codificação em Json com letras acentuadas codificadas para UTF-8 com htmlentities
        }

		CloseCon($con);

		if(count($arrayItem) > 0)

			return $arrayItem;

		else

			return null;

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
	function toJson($array = NULL){
		if(isset($array))
			return json_encode($array);

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
//$item = new Item();
//$item->setId(2);
//$item_str = $item->toJson();//json_encode($array, JSON_PARTIAL_OUTPUT_ON_ERROR);
//$item->setId(1);
//echo 'O item em Json ficou: '.$item_str;

//$item3 = new Item();
//$item3->decodeJson($item_str);

//echo '<br>';
//var_dump($item3);
//echo '<br>';
//var_dump($item);