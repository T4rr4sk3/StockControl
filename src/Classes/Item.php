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
	private $user;

	function __construct($nome_db, $username = NULL)
	{
		$this->db = $nome_db;
		$this->user = $username;
	}

	function recuperarItem($itemId) {

		$con = OpenCon($this->db);

		if( !($p = $con->prepare("SELECT c.id, c.qtde, c.modelo, c.estado, m.nome AS marca, t.nome AS tipo FROM controle c INNER JOIN marca m ON m.id = c.id_marca INNER JOIN tipo t ON t.id = c.id_tipo WHERE c.id = ?") ) )
		
			echo "Prepare failed: (" . $con->errno . ") " . $con->error;

        if( !( $p->bind_param("i",$itemId) ) )

            echo "Parameters failed: (" . $p->errno . ") " . $p->error;

        if( !( $p->execute() ) )

			echo "Execute failed: (" . $p->errno . ") " . $p->error;

		$r = $p->get_result()->fetch_assoc();

		$this->decodeJson($this->toJson($r));

	}

	private function salvaHistorico($itemId, $qtdeOp, $tipoOp, $username){
		
		$this->recuperarItem($itemId);

		$con = OpenCon($this->db);

		//recuperar id do tipo e marca.

		$q = 'SELECT id FROM marca where nome LIKE "'. $this->marca .'"';

		$p = $con->query($q);

		$id_marca = $p->fetch_row()[0];

		$q = 'SELECT id FROM tipo where nome LIKE "'. $this->tipo .'"';

		$p = $con->query($q);

		$id_tipo = $p->fetch_row()[0];

		$q = 'INSERT INTO historico (usuario, operacao, qtde_op, qtde_dps, id_tipo, id_marca, modelo, estado, dataehora) VALUES (?,?,?,?,?,?,?,?,?)';

		$dataehora = new \DateTime("now", new \DateTimeZone("America/Sao_Paulo"));
		$str = $dataehora->format("Y-m-d H:i:s");
		$tipoOp = strtoupper($tipoOp);


		if( !( $p = $con->prepare($q) ) )

			echo "Prepare failed: (" . $con->errno . ") " . $con->error;

        if( !( $p->bind_param("ssiiiisss", $username, '(API) '.$tipoOp, $qtdeOp, $this->qtde, $id_tipo, $id_marca, $this->modelo, $this->estado, $str) ) )

            echo "Parameters failed: (" . $p->errno . ") " . $p->error;

        if( !( $p->execute() ) )

			echo "Execute failed: (" . $p->errno . ") " . $p->error;

		CloseCon($con);

	}

	function alteraQtde($itemId, $qtde, $op, $user) 
	{

		try {
			$num_retirada = (int) $qtde;
		} catch (Exception $e) {
			return "Failure: ".$e->message;
		}

		$con = OpenCon($this->db);

		if( !($p = $con->prepare("select qtde from controle where id = ?") ) )
		
			echo "Prepare failed: (" . $con->errno . ") " . $con->error;

        if( !( $p->bind_param("i",$itemId) ) )

            echo "Parameters failed: (" . $p->errno . ") " . $p->error;

        if( !( $p->execute() ) )

			echo "Execute failed: (" . $p->errno . ") " . $p->error;

		$result = $p->get_result();
		
		if($result->num_rows == 1)
			$qtde = $result->fetch_row()[0];
		
		else
			return '{"id":-1,"msg":"Operacao falhou. Item nao encontrado."}';

		switch($op){
			case 'remove' :
				if($num_retirada > $qtde)

					return '{"id":-1,"msg":"Operacao falhou. Numero a retirar maior que quantidade do item."}';

				$qtde = $qtde - $num_retirada;
			break;

			case 'add' :
				$qtde = $qtde + $num_retirada;

			break;

			/*case 'alter' :

			break;*/

			default : 
				return '{"id":-1,"msg":"Operacao invalida."}';
		}

		$q = 'UPDATE controle SET qtde = ? WHERE id = ?';

		if( !( $p = $con->prepare($q) ) )

			echo "Prepare failed: (" . $con->errno . ") " . $con->error;

        if( !( $p->bind_param("ii",$qtde,$itemId) ) )

            echo "Parameters failed: (" . $p->errno . ") " . $p->error;

        if( !( $p->execute() ) )

			echo "Execute failed: (" . $p->errno . ") " . $p->error;

		//$q = 'INSERT INTO historico VALUES ()'; Parte para salvar no historico tem que ser Funcao
		$this->salvaHistorico($itemId, $num_retirada, $op, $user);
		
		return '{"id":0,"msg":"Sucess"}';
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

			if($r['estado'] == "Em Uso")
				return '{ "id" : -1, "msg" : "Operacao nao permitida. Estado do item apenas para visualizacao do Admin." }';

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