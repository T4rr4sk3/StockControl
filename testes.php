<?php 

include_once 'DBC.php';

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

$dev = false;
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
}