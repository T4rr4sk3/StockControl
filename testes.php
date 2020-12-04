<?php 

include_once 'DBC.php';

function multi_bind_param(mysqli_stmt $stmt, string $types, array $array){

	$val = array_values($array);

	switch(strlen($types)){
		case 1:
			if( !( $stmt->bind_param($types,$val[0]) ) )
				echo "Parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			break;

		case 2:
			if( !( $stmt->bind_param($types,$val[0],$val[1]) ) )
				echo "Parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			break;

		case 3:
			if( !( $stmt->bind_param($types,$val[0],$val[1],$val[2]) ) )
				echo "Parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			break;

		case 4:
			if( !( $stmt->bind_param($types,$val[0],$val[1],$val[2],$val[3]) ) )
				echo "Parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			break;

		case 5:
			if( !( $stmt->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4]) ) )
				echo "Parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			break;

		case 6:
			if( !( $stmt->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5]) ) )
				echo "Parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			break;
		
		case 7:
			if( !( $stmt->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6]) ) )
				echo "Parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			break;
		
		case 8:
			if( !( $stmt->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7]) ) )
				echo "Parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			break;

		default:
			echo "Tamanho de parametros invalido.";
			return false;
	}

	return true;
}

function insert_array_db($dbName, $dbTable, $array_args) {

	$con = OpenCon($dbName);

	$q = 'insert into '.$dbTable.' (';

	$count = count($array_args);

	$keys = array_keys($array_args);

	for($i = 0; $i < $count; $i++){
		
		$q = $q.$keys[$i];

		if($i != $count - 1)
			$q = $q.',';
		
	}

	$q = $q.') values (';

	for($i = 0; $i < $count; $i++){
		$q = $q.'?';

		if($i != $count - 1)
			$q = $q.',';
	}

	$params = '';
	foreach($array_args as $value){
		if(is_numeric($value))
			$params = $params.'i';
		else
			$params = $params.'s';
	}

	//botar a parte da retirada aqui e usar o params e o array no bind_params

	if( !( $p = $con->prepare($q.')') ) )

		echo "Prepare failed: (" . $con->errno . ") " . $con->error;
		
	//substituir por funcao
	if( !multi_bind_param($p, $params, $array_args))

		echo "Bind failed. (" . $p->errno . ") " . $p->error;

    if( !( $p->execute() ) )

        echo "Execute failed: (" . $p->errno . ") " . $p->error;
    else

        echo "Operação efetuada com sucesso.";


	return true;
}

$arrayTest = array('nome' => 'Dell');

insert_array_db("estoque","marca",$arrayTest); // ($banco_de_dados, $tabela, $array_associativo);

//parte de autenticacao
/*require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

$alg_hash = 'fnv1a32';

$c = curl_init("http://localhost/StockControl/API.php/item/1");

            //curl_setopt($c,CURLOPT_URL,"http://localhost/api/api1.php/teste");
            curl_setopt($c,CURLOPT_BINARYTRANSFER,1);
            curl_setopt($c,CURLOPT_FAILONERROR,1);
            //curl_setopt($c,CURLOPT_POST,1);
            //curl_setopt($c,CURLOPT_HEADER,1);
            curl_exec($c);

			curl_close($c);
			

//teste de autenticacao no server.
$auth_basic = false;

$realm = 'teste';

$users = array('dfpelajo' => '123');

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if($auth_basic){
	//unset($_SERVER['PHP_AUTH_USER']);
	if(!isset($_SERVER['PHP_AUTH_USER'])) {
		header('WWW-Authenticate: Basic realm="Teste"');
		header('HTTP/1.0 401 Unauthorized');
		echo 'Texto enviado caso seja cancelado o login.';
		exit();
	} else {
		$_SERVER['PHP_AUTH_USER'] = hash($alg_hash, $_SERVER['PHP_AUTH_USER']);
		$_SERVER['PHP_AUTH_PW'] = hash($alg_hash, $_SERVER['PHP_AUTH_PW']);
		echo "<p>Ola, {$_SERVER['PHP_AUTH_USER']}.</p>";
		echo "<p>Sua senha: {$_SERVER['PHP_AUTH_PW']}</p>";
	}
} else{
	if(empty($_SERVER['PHP_AUTH_DIGEST'])){
		header('HTTP/1.0 401 Unauthorized');
		header('WWW-Authenticate: Digest realm="'.$realm.'", qop="auth",nonce="'.hash($alg_hash,uniqid()).'",opaque="'.hash($alg_hash,$realm).'"');
		echo 'Texto enviado caso seja cancelado o login.';
		die();
	}

	$pos = strpos($_SERVER['PHP_AUTH_DIGEST'],"nonce="); //pegar a primeira posicao no 'nonce' na string
	$nonce = substr($_SERVER['PHP_AUTH_DIGEST'],$pos+7,8); // adicionar mais sete para avancar a pos para a primeira aspa e pegar o nonce de 8 dig
	//echo "nonce=".$nonce."<br>";

	$substr = trim($_SERVER['PHP_AUTH_DIGEST'],'username="'); //pega a string tirando o 'username="'
	$username = substr($substr, 0, strpos($substr,'"')); // pega o nome ate encontrar o fecha aspas.
	//echo "username=".$username;

	$pos = strpos($_SERVER['PHP_AUTH_DIGEST'],"nc=");
	$nc = substr($_SERVER['PHP_AUTH_DIGEST'],$pos+3,8);
	//echo "nc=".$nc;

	$pos = strpos($_SERVER['PHP_AUTH_DIGEST'],"cnonce=");
	$cnonce = substr($_SERVER['PHP_AUTH_DIGEST'],$pos+8,16);
	//echo "cnonce=".$cnonce;

	$uri = $_SERVER['REQUEST_URI'];
	//echo "uri=".$uri;

	$a1 = md5($username.":".$realm.":".$users[$username]);
	$a2 = md5($_SERVER['REQUEST_METHOD'].":".$uri);
	$resp_valida = md5($a1.':'.$nonce.':'.$nc.':'.$cnonce.':'.'auth'.':'.$a2);

	//echo "Olha so o falso: ".$a1.':'.$nonce.':'.$nc.':'.$cnonce.':'.'auth'.':'.$a2."<br>";

	$pos = strpos($_SERVER['PHP_AUTH_DIGEST'],'response=');
	$response = substr($_SERVER['PHP_AUTH_DIGEST'],$pos+10,32);

	echo $resp_valida.'<br>';

	echo $response;

	//if(!($data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])))

	function http_digest_parse($txt)
	{
		// proteção contra dados incompletos
		$needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'opaque'=>1, 'username'=>1, 'uri'=>1, 'response'=>1, 'realm'=>1, 'qop'=>1);
		$data = array();
		$keys = implode('|', array_keys($needed_parts));

		preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

		foreach ($matches as $m) {
			$data[$m[1]] = $m[3] ? $m[3] : $m[4];
			unset($needed_parts[$m[1]]);
		}

		return $needed_parts ? false : $data;
	}
	// analisar a variável PHP_AUTH_DIGEST
	if (!($data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) ||
	!isset($users[$data['username']])){
		unset($_SERVER['PHP_AUTH_DIGEST']);
		die('Credenciais inválidas!');
	}

	//verificar o porque do seu ter dado errado.

	// gerar a resposta válida
	$A1 = md5($data['username'] . ':' . $realm . ':' . $users[$data['username']]);
	$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
	$valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);

	if ($data['response'] != $resp_valida)
		die('Credenciais inválidas!');

	// ok, nome de usuário e senha válidos
	echo 'Você está logado como: ' . $username;

	//echo "<br>".$_SERVER['PHP_AUTH_DIGEST']."<br><br>";
	// $data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST']);
	// echo $data['cnonce'];
	
	// if(hash_equals(hash($alg_hash,"test"),$data['opaque']))
	// 	echo "True";

	
}


/*$dev = false; 
if($dev) {

	$filename = "teste.xls";

	header("Content-Type: application/vnd.ms-excel; charset=iso-8859-1");
	header("Content-Disposition: attachment; filename=\"$filename\"");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);

	$con = OpenCon("estoque");

	$q = 'SELECT t.nome as tipo, m.nome as marca, modelo, estado, qtde as quantidade from controle inner join marca m on id_marca = m.id inner join tipo t on id_tipo = t.id where estado in ("Novo","Usado")';

	$p = $con->query($q);

	$heading = false;

	while($r = $p->fetch_assoc()) {
		if(!$heading){
			$str = "";
			foreach(array_keys($r) as $key){
				$str = $str.strtoupper($key)."\t";
			}
			echo $str."\n";
			$heading = true;
		}
		echo implode("\t",array_values($r))."\n";
	}

	exit();
} else {

//criando planilha

$s = new Spreadsheet();

$sheet = $s->getActiveSheet(); //planilha atual

$sheet->setTitle("Estoque");

//query no BD e pegando os resultados e botando na planilha

$con = OpenCon("estoque");

$q = 'SELECT t.nome as tipo, m.nome as marca, modelo, estado, qtde from controle inner join marca m on id_marca = m.id inner join tipo t on id_tipo = t.id where estado in ("Novo","Usado")';

$p = $con->query($q);

$r = $p->fetch_assoc();

$i = 1;

//primeira linha, cabecalho

foreach(array_keys($r) as $head){	
	$sheet->setCellValueByColumnAndRow($i, 1, strtoupper($head));
	$i++;
}

//segunda linha, corpo

$sheet->fromArray(array_values($r), NULL, "A2");

$sheet->getRowDimension("2")->setRowHeight(18);

//terceira linha em diante do corpo

$lin = 3;

while($r = $p->fetch_assoc()){	
	$col = 1;
		foreach(array_values($r) as $v){
			$sheet->setCellValueByColumnAndRow($col,$lin,$v);
			$col++;
		}
	$sheet->getRowDimension((string) $lin)->setRowHeight(18);
	$lin++;
}

//botando o tamanho de cada coisinha(colunas e linha 1)

$sheet->getColumnDimension("A")->setWidth(14);
$sheet->getColumnDimension("B")->setWidth(18);
$sheet->getColumnDimension("C")->setWidth(26);
$sheet->getColumnDimension("D")->setWidth(12);
$sheet->getColumnDimension("E")->setWidth(10);
$sheet->getRowDimension("1")->setRowHeight(20);

//estilo header = cabecalho

$styleHeader = [
'font' => [
    'bold' => true,
    'italic' => false,
    'underline' => false,
    'strikethrough' => false,
    'color' => ['argb' => 'FF000000'],
    'name' => "Calibri",
    'size' => 14
  ],

 'alignment' => [
	  'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
	  'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
 ],

'borders' => [
    'outline' => [
      'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
      'color' => ['argb' => '00000000']
    ]
  ],

  'fill' => [
		'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
    'color' => ['argb' => 'FF8E8E8E']
  ]
];

$style = $sheet->getStyle('A1:E1');

$style->applyFromArray($styleHeader);

//estilo especial para a coluna QTDE da primeira linha

$style = $sheet->getStyle('E1');

$styleQTDE = [
'alignment' => [
	  'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
	  'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
	  ]
 ];

$style->applyFromArray($styleQTDE);

//estilo do corpo da planilha abaixo do cabecalho

$styleBody = [
'font' => [
    'bold' => false,
    'italic' => false,
    'underline' => false,
    'strikethrough' => false,
    'color' => ['argb' => 'FF000000'],
    'name' => "Calibri",
    'size' => 12
  ],

 'alignment' => [
	  'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
	  'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
 ],

'borders' => [
    'top' => [
      'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
      'color' => ['argb' => '00000000']
    ],
    'bottom' => [
      'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
      'color' => ['argb' => '00000000']
    ],
    'right' => [
      'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
      'color' => ['argb' => '00000000']
    ]
  ],

'fill' => [
		'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
    'color' => ['argb' => 'FFFFFFFF']
    ]
];

$lastCell = (string) $sheet->getHighestColumn().$sheet->getHighestRow();

$style = $sheet->getStyle("A2:".$lastCell); // da segunda linha primeira coluna ate ultima coluna e linha

$style->applyFromArray($styleBody);

//botar estilo de cada linha separada

$styleLines = [
  'borders' => [
	'bottom' => [
		'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
		'color' => [ 'argb' => 'FF000000']
	]
  ]
];

$ultLin = $sheet->getHighestRow();

for($i = 2; $i < $ultLin; $i++){
	$style = $sheet->getStyle("A".$i.":E".$i);

	$style->applyFromArray($styleLines);
}

//estilo da coluna qtde "E"

$styleQTDE = [
  'alignment' => [
	'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
	'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
 ]
];

$style = $sheet->getStyle("E2:E".$ultLin);

$style->applyFromArray($styleQTDE);

//criando writer e salvando em arquivo e forcando download. apos isso, sair(exit).

$w = new Xlsx($s);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="testando.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: cache, must-revalidate');
header('pragma: public');
$w->save('php://output');

exit();
}*/