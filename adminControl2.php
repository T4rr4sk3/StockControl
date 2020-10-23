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
		<nav class="ml-3 nav">
			<h2 class="text-light">Stocks</h2>
			<a class="ml-3 nav-link texto" href="adminControl.php">Início</a>
            <a class="ml-3 nav-link texto" href="history.php">Histórico</a>
            <a class="ml-3 nav-link texto" href="creator.php">Criar</a>
            <a class="ml-3 nav-link texto" href="export-control-excel.php" target="_blank">Teste</a>
            <a class="ml-3 nav-link texto" href="exit.php">Sair</a>
		</nav>
	</div>

    <div class="pesquisa bg-dark d-flex d-flex-justify-center p-2 flex-nowrap">
		<form class="form-inline mx-auto" action="search.php" method="GET">
			<div class="input-group">

        <div class="input-group-prepend">
          <label class="input-group-text" for="search">Pesquisar</label>
        </div>

        <select class="custom-select" name="escolha" for="search">
          <option selected>Tipo</option>
            <option>Marca</option>
          <option>Modelo</option>
        </select>
          <div class="input-group-append">
            <input id="search" name="pesquisa" type="search" placeholder="Digite aqui..." class="form-control" style="border-radius: 0;"/>
          </div>   
			  <button type="submit" class="btn btn-outline-white" style="border-radius: 0 .25rem .25rem 0"><img src="baseline_search_black_24dp.png" /></button>

      </div>
    </form>
  </div>

  <div class="d-flex justify-content-center pt-2" onchange="Filtrar(this)">
        
    <label class="mr-3">Exibir:</label>

        <label class="mr-2">
            <input type="checkbox" value="novo" checked>
                <span class=" mr-1">Novo</span>
        </label>
        <label class="mr-2">
            <input type="checkbox" value="usado" checked>
                <span class="mr-1">Usado</span>
        </label>
        <!--<label class="mr-2">
            <input type="checkbox" value="em uso">
                <span class=" mr-1">Em Uso</span>
        </label>-->        
     </div>          

     <!--Tabela e botao de exportar em cima-->
    <div class="blank-box table-responsive">        
        <form action="export-excel.php" target="_blank" method="POST">    
            <button class="btn btn-sm btn-dark mb-1 float-right" type="submit">Exportar Excel</button>
        </form>

        <table class="table table-hover mx-auto table-striped table-bordered" id="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Tipo</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Quantidade</th>
                    <th scope="col">Controle</th>
                </tr>
            </thead>
                <tbody id="tabela">
            <?php                             
                // $n = 0; //variavel contadora de linha, util pra array gerada de itens.(depois eu vou melhorar este processo.)
                $q = 'SELECT t.nome as tipo,estado,sum(qtde) FROM controle inner join tipo t on t.id = id_tipo where estado in ("Novo") group by t.nome';
                $p = $con->query($q);
                if($p->num_rows > 0) { 
                    // $_SESSION['tipo_r'] = $_SESSION['marca_r'] = $_SESSION['estado_r'] = $_SESSION['modelo_r'] = array();
             while($r = $p->fetch_row()) { ?>
                <tr>
                    <td><?php echo implode("</td><td>",$r);?></td>
                    <td>
                        <button type="button" class="btn btn-primary sm-btn mx-2" data-toggle="modal" data-target="#dialogo1" onclick="Retirar(this,Opcoes.RETIRAR)">
                            Modificar
                        </button>                             
                    </td>
             <?php  
                }
            } 
            ?>


            <?php // tive de separar a consulta para novo e usado para agrupar todos por tipo.
                // $n = 0; //variavel contadora de linha, util pra array gerada de itens.(depois eu vou melhorar este processo.)
                $q = 'SELECT t.nome as tipo,estado,sum(qtde) FROM controle inner join tipo t on t.id = id_tipo where estado in ("Usado") group by t.nome';
                $p = $con->query($q);
                if($p->num_rows > 0) { 
                    // $_SESSION['tipo_r'] = $_SESSION['marca_r'] = $_SESSION['estado_r'] = $_SESSION['modelo_r'] = array();
             while($r = $p->fetch_row()) { ?>
                <tr>
                    <td><?php echo implode("</td><td>",$r);?></td>
                    <td>
                        <button type="button" class="btn btn-primary sm-btn mx-2" data-toggle="modal" data-target="#dialogo1" onclick="Retirar(this,Opcoes.RETIRAR)">
                            Modificar
                        </button>                             
                    </td>
             <?php  
                }
            } 
            ?>
                </tr>
                
    <?php
    /*
                $q = 'SELECT t.nome as tipo,estado,sum(qtde) FROM controle inner join tipo t on t.id = id_tipo where estado in ("Em Uso") group by t.nome';
                $p = $con->query($q);
                if($p->num_rows > 0) {
                while($r = $p->fetch_row()) {?>
                <tr>    
                    <td><?php echo implode("</td><td>",$r); ?></td>
                    <td>
                        <button type="button" class="btn btn-primary sm-btn mx-2" data-toggle="modal" data-target="#dialogo1" onclick="Retirar(this,Opcoes.DESCARTAR)">
                            Modificar
                        </button>
                    </td>

                <?php
                        } 
                    }*/?>

                <!--</tr>-->

            </tbody>
        </table>
    </div>
        
    <!-- Modal Form-->
    <div class="modal" id="dialogo1" tabindex="-1" role="dialog" aria-labelledby="Dialogo" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

          <div class="modal-header">

              <h5 class="modal-title" id="titulo">Nada ainda</h5>

            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="Dispose()" id="close-btn">
              <span aria-hidden="true">&times;</span>
            </button>

          </div>
      
          <div class="modal-body">

              <form class="form" action="retirada.php" id="myform" method="POST">
              <div class="container">
                  <div class="row mb-3">
                      <div class="col">
                          <label for="tipo" class="d-flex justify-content-center font-weight-bold">Tipo</label>
                            <label id="tipo" class="d-flex justify-content-center"></label>
                          <input type="hidden" value="" id="tipo_f" name="tipo" />
                      </div>
              
                      <div class="col">
                          <label for="estado" class="d-flex justify-content-center font-weight-bold">Estado</label>
                            <label id="estado" class="d-flex justify-content-center"></label>
                          <input type="hidden" value="" id="estado_f" name="estado" />
                      </div>
                          
                      <div class="col-6">
                          <label for="modelo" class="d-flex justify-content-center font-weight-bold">Modelo</label>
                            <select id="modelo" style="width:100%" name="modelo" onchange="Mudar()"></select>
                      </div>

                    </div> 
              

                    <div class="row justify-content-between">

                        <div class="col-md-auto">
                          <label for="qtde" class="font-weight-bold">Quantidade Disp.:&nbsp;</label>
                            <label id="qtde"></label>
                        </div>

                        <div class="col-md-auto">

                        	<div class="d-inline-flex" id="rad">
	                        	<label class="mr-2"><input type="radio" name="opcao" value="retirar" id="retirar" checked />
	                        		Retirar
	                        	</label>

	                        	<label class="mr-3"><input type="radio" name="opcao" value="repor" id="repor" />
	                        		Repor
	                        	</label>
	                        </div>
                
                            <input type="number" style="width:45px" min=0 name="retirada" value=0 id="retirada" />

                        </div>
                    </div>
              </div>
              
          </form>

          </div>

          <div class="modal-footer">

            <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="Dispose()">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="Verificar()">Efetuar</button>
            <button type="submit" class="d-none" form="myform" id="enviar"></button>
          </div>
        </div>
      </div>
    </div>

	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script>

        function Filtrar(x) {
            Novo = x.getElementsByTagName("input")[0];
            Usado = x.getElementsByTagName("input")[1];
            Em_uso = x.getElementsByTagName("input")[2];
            tr = document.getElementById("tabela").getElementsByTagName("tr");

            for (i=0; i<tr.length; i++){
                td = tr[i].getElementsByTagName("td")[1];
                estado = td.innerText || td.textContent;
                
                switch(estado){
                    case "Novo":
                        if(Novo.checked)
                            tr[i].style.display = "";
                        else
                            tr[i].style.display = "none";
                            
                        break;

                    case "Em Uso":
                        if(Em_uso.checked)
                            tr[i].style.display = "";
                        else
                            tr[i].style.display = "none";
                         
                        break;

                    case "Usado":
                        if(Usado.checked)
                            tr[i].style.display = "";
                        else
                            tr[i].style.display = "none";
                         
                        break;

                }
            }
        }

        document.getElementById('myform').addEventListener('keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                Verificar();
                return false;
                
            }
        });

        document.getElementById('dialogo1').addEventListener('keydown',function(e){
            var keyCode = e.keyCode || e.which;
            if (keyCode === 27) {
                Dispose();
                return false;
          
            }
        });

        var a = [{
        <?php 
            $q = 'select  t.nome as Tipo, c.modelo as Modelo, m.nome as Marca, c.estado as Estado, c.qtde as Quantidade from controle c inner join tipo t on c.id_tipo = t.id inner join marca m on c.id_marca = m.id';
            if($nivel < 5)
                $q = $q.' where estado in ("Novo","Usado")';
            $p = $con->query($q);
            if($p->num_rows > 0) {
                while($r = $p->fetch_row()) {
                                       
                    print('Tipo : "'.$r[0].'", ');
                    print('Modelo : "'.$r[1].'", ');
                    print('Marca : "'.$r[2].'", ');
                    print('Estado : "'.$r[3].'", ');
                    print('Qtde : "'.$r[4].'"} , {');

				}     
			}
        ?> Tipo : "", Modelo : "", Marca : "", Estado : "", Qtde : ""}];
		
		const Opcoes = {
			RETIRAR : 'Retirar',
            DESCARTAR : 'Descartar'
		};

        var index = [{id : "", n : -1}];

        function Mudar() { //muda a qtde sempre que muda a opção de modelo
            var qtde = document.getElementById('qtde');
            var valor = document.getElementById('modelo').value;
            
            for (i = 1; i < index.length; i++) {
                if (index[i].id == valor)
                    qtde.innerText = a[index[i].n].Qtde;
            }
        }

        //verifica se o número a retirar ou repor é valido de acordo com a opção,
        function Verificar() { // evita chamar a outra pagina desnecessariamente.
            var qtde = parseInt(document.getElementById('qtde').innerText);
            var num = document.getElementById('retirada').value;
            var retirar = document.getElementById('retirar');
            var repor = document.getElementById('repor');
            var descartar = document.getElementById('descartar');

            if (num > 0) {            	
                if(retirar != null){
                    
                    if (repor.checked){
                    	if (confirm("Deseja repor " + num + " deste item?"))
                        	document.getElementById('enviar').click();

            		} else if (num > qtde)

						alert("A quantidade a retirar não pode ser maior que a Disponível!");

                	else if (retirar.checked)
                        if (confirm("Deseja retirar " + num + " deste item?"))
                            document.getElementById('enviar').click();

                } else {
                    
                    if (repor.checked){
                    	    if (confirm("Deseja repor " + num + " deste item?"))
                        	    document.getElementById('enviar').click();

            		    } else if (num > qtde)

						    alert("A quantidade a descartar não pode ser maior que a Disponível!");

                	    else if (descartar.checked)
                            if (confirm("Deseja descartar " + num + " deste item?"))
                                document.getElementById('enviar').click();

                    }   
                                                 
            } else 
                alert("A quantidade não pode ser zero!");
            
        }

        function Dispose() { //desfazer algumas coisas para não ficar acumulando nem ocupando espaço
            document.getElementById('modelo').innerHTML = "";
            document.getElementById('estado_f').value = "";
            document.getElementById('tipo_f').value = "";
            document.getElementById('retirada').value = 0;
            index = [{ id: "", n: -1 }];
        }

		function Retirar(x, opcao) {

			var tr = x.parentElement.parentElement; //o parentNode deste x(botao) é o td, e o parentNode do td é o tr, que contem cells(td)
            var tipo = document.getElementById('tipo');
            var estado = document.getElementById('estado');
            var qtde = document.getElementById('qtde');
            var modelo = document.getElementById('modelo'); //modelo + marca
            var rad = document.getElementById('rad');

            var t = tr.cells[0].innerText;
            var e = tr.cells[1].innerText;

            var o1 = document.getElementById('retirar');
            var o2 = document.getElementById('repor');
            var o3 = document.getElementById('descartar');

            document.getElementById('titulo').innerText = opcao;
            document.getElementById('tipo_f').value = t;
            document.getElementById('estado_f').value = e;

            tipo.innerText = t;
            estado.innerText = e;

            var first = true;

                    //mudar modal para retirada e mudar tipo e estado e fazer dropdown com marca." - ".modelo, evento onChange no qtde e enviar form com essas informações
                    for (i = 0; i < a.length; i++) {                  
                        if ((a[i].Tipo == t) && (a[i].Estado == e)) {
                            var str = a[i].Modelo + " - " + a[i].Marca;
                            modelo.innerHTML += '<option value="' + str + '">' + str + '</option>';

                            index.push({ id: str, n: parseInt(i,10) });

                            if (first) {
                                qtde.innerText = a[i].Qtde;
                                first = false;
                            }
                        }
                    }
            //aqui muda pra retirar ou descartar no modal
            if(opcao == Opcoes.RETIRAR) {            	
            	            	
            	rad.innerHTML = '<label class="mr-2"><input type="radio" name="opcao" value="retirar" id="retirar" checked>Retirar</label>';
                rad.innerHTML += '<label class="mr-3"><input type="radio" name="opcao" value="repor" id="repor">Repor</label>';

            } else if(opcao == Opcoes.DESCARTAR) {

            	rad.innerHTML = '<label class="mr-2"><input type="radio" name="opcao" value="descartar" id="descartar" checked>Descartar</label>';
                rad.innerHTML += '<label class="mr-3"><input type="radio" name="opcao" value="repor" id="repor">Realocar</label>';
            }

            

		}

 <?php
    
    if(isset($_SESSION['exib_dialg'])){
        $exibir = $_SESSION['exib_dialg'];
        $msg = $_SESSION['dialg'];

        if($exibir) {				
            echo 'alert("'.$msg.'");';
            $_SESSION['exib_dialg'] = false;
	    }
    }
 ?>
    </script>
</body>
</html>