<?php 

include_once 'DBC.php';

session_start();
/*if(session_start())
	if(isset($_SESSION['usuario'])) {
		$user = $_SESSION['usuario'];

		$con = OpenCon("estoque");

		$q = "SELECT nome from usuario where nome like ?";

		$p = $con->prepare($q);
			
		$p->bind_param('s',$user);

		$p->execute();

		$r = $p->get_result();

		if($r->num_rows > 0)
			if($r->num_rows == 1)
				redirect("adminControl.php",303);
			

	}*/
	
//redirect("testes.php",303);



?>
<!DOCTYPE html>
<html>
	<head>
		<title>Stocks</title>
			<meta name="viewport" content="width=device-width, user-scalable=no">
		<meta charset="utf-8">

		<style>

			*{
				box-sizing: border-box;
			}

			body{
				background-image:linear-gradient(to right, gray, darkgray , lightgray) ;
				max-width: 100%;
			}

			.blank-box{
				width:300px;
				height: auto;
				padding:1.5%;
				margin-left: auto;
				margin-right: auto;
				margin-top: 186px;
				background-color: white;
				border-radius: 5px;
				box-shadow: 2px 2px black;
			}

			.input-box{
				width:100%;
				padding: 5px 4px 5px 4px;
				border-radius: 4px;
			}

			.input-label{
				padding: 10px 0px;
			}

			.text-login{
				font-family: Verdana, Geneva, Tahoma, sans-serif;
			}

			.submit-btn{
				margin-top: 7%;
				border: none;
				border-radius: 3px;
				background-color:gray;
				color:white;
				text-align:center;
				padding: 3%;
			}

		</style>
	</head>
<body>

	<div class="blank-box">
		<form method="POST" action="validate.php">
			<h2 style="text-align: center;">Stocks - Login</h2>
			<div class="input-label">
				<label class="text-login">Usuário</label>
			</div>
				<input type="text" class="input-box" name="username" placeholder="Usuário..." autocomplete="off" autofocus="on">
			<div class="input-label">
				<label class="text-login">Senha</label>
			</div>
				<input type="password" class="input-box" name="pass" placeholder="Senha...">
				
			<input type="submit" class="submit-btn" value="Entrar">
		</form>
	</div>

	<?php
		if(isset($_SESSION['erro'])) {
			$exibir = $_SESSION['erro'];
			$msg = $_SESSION['msg'];

				if($_SESSION['erro']) {
					echo '<script>alert("'.$_SESSION['msg'].'")</script>';
					$_SESSION['erro'] = false;
				}
		} ?>

</body>
</html>