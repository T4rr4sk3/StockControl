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
</head>

<body>

<div class="bg-dark">
		<nav class="ml-3 nav d-flex">
			<div class="d-flex justify-content-between">
				<h2 class="text-light">Stocks</h2>
				<a class="ml-3 nav-link texto" href="adminControl.php">Início</a>
				<a class="ml-3 nav-link texto" href="history.php">Histórico</a>
				<!-- <p class="text-light"><?php // echo $_SESSION['usuario']; ?></p> -->
			</div>
		</nav>
	</div>
	
<div class="blank-box table-responsive">
        <table class="table table-hover mx-auto table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Operação</th>
                    <th scope="col" style="width:25%">Item</th>
                    <th scope="col">Quantidade do Item Pós-Op.</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Data e Hora</th>
                    <th scope="col">Feito por</th>
                </tr>
            </thead>
                <tbody>
                	<?php 
                		$q = "select operacao, qtde_op, t.nome, m.nome, modelo, qtde_dps, estado, dataehora, usuario from historico inner join tipo t on t.id = id_tipo inner join marca m on m.id = id_marca limit 20";
                		$p = $con->query($q);
                        
                		if($p->num_rows > 0)
                			while($r = $p->fetch_row()) {?>
                				<tr>
	                				<td> <?php echo strtoupper($r[0])." deste item ".$r[1]." un."; ?></td>
	                				<td> <?php echo $r[2]." ".$r[3]." ".$r[4]; ?></td>
	                				<td> <?php echo $r[5]; ?></td>
	                				<td> <?php echo $r[6]; ?></td>
	                				<td> <?php echo $r[7]; ?></td>
	                				<td> <?php echo $r[8]; ?></td>
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

</body>
</html>