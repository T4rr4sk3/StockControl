<?php 

include_once 'DBC.php';


session_start();

    	if( !(isset($_SESSION['nivel']))) {
 
        	redirect("index.php",303);

        } else {

            $nivel = $_SESSION['nivel'];
        }
        
        if ($nivel < 4)
            redirect("adminControl.php",303);

$con = OpenCon("estoque");


if(isset($_POST['tabela'])) {
    
    $t = $_POST['tabela'];

    $q = "";

    switch ($t) {
        case 'tipo' :
        
        case 'marca' :

            $q = "INSERT INTO ".$t."(nome) VALUES (?)";
        break;

        case 'controle' :

            $q = "INSERT INTO ".$t."(qtde,id_marca,id_tipo,modelo) VALUES (?,?,?,?)";
        break;
    }


    if(($t == 'tipo') or ($t == 'marca')){
        if(isset($_POST['data']))
            $d = $_POST['data']; 
        
        if(!($p = $con->prepare($q)))
            echo "Prepare failed: (" . $con->errno . ") " . $con->error;;
        
            if(!($p->bind_param("s",$d)))
                echo "Parameters failed: (" . $p->errno . ") " . $p->error;
            

            $q = 'select nome from '.$t.' where nome = "'.$d.'"';

            if(!($s = $con->query($q)))
                echo "Verification failed: (" . $con->errno .") " . $con->error;
            else {

            if($s->num_rows > 0)
                echo "Já existente!";
            else
                if(!($p->execute()))
                    echo "Execute failed: (" . $p->errno . ") " . $p->error;
                else
                    echo "Operação efetuada com sucesso.";
                
        }

    } elseif($t == 'controle') {

        $m = $_POST['marca'];
        $t = $_POST['tipo'];

        $s = $con->query('select id from marca where nome like "'.$m.'"');

        if(!($id_m = $s->fetch_row()))
            echo "Check id_marca failed";
        
        $s = $con->query('select id from tipo where nome like "'.$t.'"');

        if(!($id_t = $s->fetch_row())) {
            echo "Check id_tipo failed";
        }

        if($_POST['modelo']=="" or $_POST['qtde'] == 0)
            echo "Campo modelo e/ou quantidade não podem estar vazios!";
        else {
            
            $model = $_POST['modelo'];
            $qtde = $_POST['qtde'];
            
            $p = $con->prepare($q);

            if(!($p->bind_param("iiis",$qtde,$id_m[0],$id_t[0],$model)))
                echo "Parameters failed: (" . $p->errno . ") " . $p->error;
            
            $q = 'select * from controle where id_marca = '.$id_m[0].' and id_tipo = '.$id_t[0].' and modelo like "'.$model.'"';

            if(!($s = $con->query($q)))
                echo "Verification failed: (" . $con->errno .") " . $con->error;
            else
                if($s->num_rows > 0)
                    echo "Já existente!";
                else
                    if(!($p->execute()))
                        echo "Execute failed: (" . $p->errno . ") " . $p->error;
                    else
                        echo "Operação efetuada com sucesso.";
                
        }
    }

} 

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
		<nav class="ml-3 nav">
			<h2 class="text-light">Stocks</h2>
			<a class="ml-3 nav-link texto" href="adminControl.php">Início</a>
		</nav>
	</div>
    
    <div class="my-5 d-flex justify-content-center">
        <div class="flex-column text-center">
            <h3 class="mb-4">Criar Tipo ou Marca</h3>        

            <form method="POST" class="form-inline my-4" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                   
                <label class="mr-2">Criar:</label>

                <div class="input-group mr-2">
                    <div class="input-group-prepend">
                        <select class="form-control" id="tabela" name="tabela" onchange="changePh(this)">
                            <option value="tipo">Tipo</option>
                            <option value="marca">Marca</option>
                        </select>
                    </div>
                </div>

                <div class="input-group mr-2">
                    <div class="input-group-append">
                        <input id="tipo_marca" class="form-control" type="text" name="data" placeholder="Nome do Tipo...">
                    </div>
                </div>

                <input class="btn btn-primary" type="submit" value="Criar">
            </form>
        </div>
    </div>

    <div class="my-5 d-flex justify-content-center">
        <div class="flex-column text-center">
            <h3 class="mb-4">Criar Controle</h3>

            <form method="POST" class="form-inline my-4" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                
                <label class="mr-2">Controle: 
                    <input name="tabela" value="controle" style="display: none;">
                </label>
                    
                <div class="input-group">
                    <div class="input-group-prepend">
                        <select class="form-control" style="border-bottom-right-radius:0; border-top-right-radius:0;" name="tipo"><?php $q = $con->query("select nome from tipo");
                                while($s = $q->fetch_row()) {?>
                            <option value="<?php echo $s[0];?>"><?php echo $s[0];?></option>
                                <?php }?>
                        </select>
                    </div>

                    
                        <select class="form-control" name="marca">
                        <?php $q = $con->query("select nome from marca");
                                while($s = $q->fetch_row()) {?>
                            <option value="<?php echo $s[0];?>"><?php echo $s[0];?></option>
                        <?php }
                        $q->free_result();?>                                
                        </select>
                    

                    
                        <input class="form-control" type="text" name="modelo" placeholder="Modelo...">
                    

                    <div class="input-group-append">
                        <input class="form-control" type="number" style="border-bottom-left-radius:0; border-top-left-radius:0; width:65px;" name="qtde" min=0 max=100 value=0>
                    </div>
                </div>
                    
                    <input class="ml-4 btn btn-primary" type="submit" value="Criar Controle">                    
                
            </form>
        </div>
    </div>

    <form class="d-flex justify-content-center mt-5" target="_SELF">
        <input class="btn btn-primary" type="reset" value="Reset">
    </form>

</body>

<script>
    function changePh(x){
        input = document.getElementById("tipo_marca");
        
        if(x.value == "tipo")
            input.placeholder = "Nome do Tipo...";
        else
            input.placeholder = "Nome da Marca...";
    }
</script>

</html>