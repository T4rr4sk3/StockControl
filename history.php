<?php

include_once 'DBC.php';


session_start();

    	if( !(isset($_SESSION['nivel']))) {
 
        	redirect("index.php",303);

        } else {

            $nivel = $_SESSION['nivel'];
        }
        


$con = OpenCon("estoque");

?>
<!DOCTYPE html>
<html>
<head>

    <title>Controle Estoque</title>

    <meta charset="utf-8">

    <meta http-equiv="Content-Language" content="pt-br">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <link rel="stylesheet" href="css/mainpage.css">

    <style>
        #tabela {
            font-size:14px;
        }
    </style>
</head>

<body onload="dateConvert()">

<div class="bg-dark">
		<nav class="ml-3 nav d-flex">
			<div class="d-flex justify-content-between">
				<h2 class="text-light">Stocks</h2>
				<a class="ml-3 nav-link texto" href="adminControl.php">Início</a>
				<a class="ml-3 nav-link texto" href="history.php">Histórico</a>
                <a class="ml-3 nav-link texto" href="creator.php">Criar</a>
                <a class="ml-3 nav-link texto" href="exit.php">Sair</a>
				<!-- <p class="text-light"><?php // echo $_SESSION['usuario']; ?></p> -->
			</div>
		</nav>
	</div>

<div class="blank-box table-responsive">
        <table class="table table-hover mx-auto table-striped table-bordered" id="tabela">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Operação</th>
                    <th scope="col" style="width:20%">Item</th>
                    <th scope="col">Qtde</th>
                    <th scope="col">Estado do Item</th>
                    <th scope="col">Data e Hora</th>
                    <th scope="col">Feito por</th>
                    <th scope="col">Chamado</th>
                    <th scope="col">Requerente</th>
                </tr>
            </thead>
                <tbody>
                	<?php 
                		$q = "select operacao, qtde_op, t.nome, m.nome, modelo, qtde_dps, estado, dataehora, usuario, chamado, requerente from historico inner join tipo t on t.id = id_tipo inner join marca m on m.id = id_marca order by dataehora desc limit 20";
                		$p = $con->query($q);
                        
                		if($p->num_rows > 0)
                			while($r = $p->fetch_row()) {?>
                				<tr>
	                				<td> <?php echo strtoupper($r[0])." deste ".$r[1]." un."; ?></td>
	                				<td> <?php echo $r[2]." ".$r[3]." ".$r[4]; ?></td>
	                				<td> <?php
                                        switch ($r[0]){
                                            case 'REPOR USADO':
                                            case 'REPOR NOVO':
                                                echo ($r[5]-$r[1]).' -> '.$r[5];
                                            break;

                                            case 'RETIRAR':
                                            case 'DESCARTAR':
                                            case 'TIRAR EM USO E REPOR':
                                                echo ($r[5]+$r[1]).' -> '.$r[5];
                                            break;

                                        }?>
                                    </td>
	                				<td> <?php echo $r[6]; ?></td>
	                				<td> <?php echo $r[7]; ?></td>
	                				<td> <?php echo $r[8]; ?></td>
                                    <td> <?php echo ($r[9]==0)? "Não tem" : $r[9]; ?></td>
                                    <td> <?php echo $r[10]; ?></td>
	                			</tr>
                			<?php
                			}
                		CloseCon($con);
                	?>


                </tbody>
            </table>
        </div>

	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>	

    <script>

        function dateConvert(){
            table = document.getElementById("tabela");
            tr = table.getElementsByTagName("tr");

            for(i = 1; i< tr.length; i++){
                
                td = tr[i].getElementsByTagName("td")[4];
                
                if(td){
                    txt = td.textContent || td.innerText;
                    array1 = txt.split(" "); // split in ['2020-xx-xx' , 'horas' ];
                    array2 = array1[1].split("-");// split 2020, xx, xx;
                    td.innerText = array2[2] + "/" + array2[1] + "/" + array2[0] + "   -   " + array1[2];

                }
            }

        }
    </script>

</body>
</html>