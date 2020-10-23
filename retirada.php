<?php 

    include_once 'DBC.php';

    session_start();

    //variaveis pegas pelo POST.
    $modelo = explode(" - ",$_POST['modelo'])[0];
    $marca = explode(" - ",$_POST['modelo'])[1];
    $n_r = $_POST['retirada'];
    $opt = $_POST['opcao'];
    $tipo = $_POST['tipo'];
    $estado = $_POST['estado'];

    $con = OpenCon("estoque");

    $q = 'SELECT * FROM controle WHERE id_tipo = (SELECT id FROM tipo where nome = ?) and id_marca = (SELECT id FROM marca WHERE nome = ?) and estado = ? and modelo = ?';

    if( !( $p = $con->prepare($q) ) )

        echo "Prepare failed: (" . $con->errno . ") " . $con->error;

    if( !( $p->bind_param("ssss",$tipo,$marca,$estado,$modelo) ) )

        echo "Parameters failed: (" . $p->errno . ") " . $p->error;

    if( !( $p->execute() ) )

        echo "Execute failed: (" . $p->errno . ") " . $p->error;
    else

        echo "Operação efetuada com sucesso.";

    //variaveis necessárias para operação no banco de dados.
    $r = $p->get_result()->fetch_assoc();
    $qtde = $r['qtde'];  
    $id_marca = $r['id_marca'];
    $id_tipo = $r['id_tipo'];

    
   if( $opt == "retirar" ) {
        
        if( ( $qtde >= $n_r ) && ( $n_r > 0 ) ) {

            echo "Quantidade menor ou igual a qtde a retirar.";

            $qtde -= $n_r; //já retira a qtde do estoque disponivel para depois atualizar o banco de dados

            $q = 'select * from controle where id_tipo = '.$id_tipo.' and id_marca = '.$id_marca.' and modelo = "'.$modelo.'" and estado = "Em Uso"';     

            $p = $con->query($q);
            
            if( $p->num_rows == 1 ) { // se tem uma linha já com Em Uso, só pega o atual, soma com a retirada e da update
                
                //$q = 'UPDATE controle set qtde = '..'';
                $r = $p->fetch_assoc();

                $x = $r['qtde'] + $n_r;

                $q = 'UPDATE controle set qtde = ? where id_tipo = ? and id_marca = ? and modelo = ? and estado = "Em Uso"';

                if( !( $p = $con->prepare($q) ) )

                    echo "Prepare failed: (" . $con->errno . ") " . $con->error;
            
                    if( !( $p->bind_param("iiis", $x, $id_tipo, $id_marca, $modelo) ) )

                        echo "Parameters failed: (" . $p->errno . ") " . $p->error;
            
                        if( !( $p->execute() ) )

                            echo "Execute failed: (" . $p->errno . ") " . $p->error;
                        else

                            echo "Operação efetuada com sucesso.";
                    
            } elseif ( $p->num_rows == 0 ) { // se não retornar nada, da insert no controle, só que em uso

                $q = 'insert into controle (qtde , id_marca, id_tipo, modelo, estado) values (?,?,?,?,"Em Uso")';

                if( !( $p = $con->prepare($q) ) )

                    echo "Prepare failed: (" . $con->errno . ") " . $con->error;
        
                    if( !( $p->bind_param("iiis", $n_r, $id_marca, $id_tipo, $modelo) ) )

                        echo "Parameters failed: (" . $p->errno . ") " . $p->error;
        
                        if(!($p->execute()))

                            echo "Execute failed: (" . $p->errno . ") " . $p->error;
                        else

                            echo "Operação efetuada com sucesso.";

            }

                //ai aqui vc poe a parte para dar update na linha que está disponível.
                $q = 'UPDATE controle set qtde = '.$qtde.' where id_marca = ? and id_tipo = ? and modelo = ? and estado = ?';

                if( !( $p = $con->prepare($q) ) )
                echo "Prepare failed: (" . $con->errno . ") " . $con->error;
        
                    if( !( $p->bind_param("iiss", $id_marca, $id_tipo, $modelo,$estado) ) )

                        echo "Parameters failed: (" . $p->errno . ") " . $p->error;
        
                        if( !($p->execute() ) )

                            echo "Execute failed: (" . $p->errno . ") " . $p->error;
                        else

                            echo "Operação efetuada com sucesso.";


        } else {

            echo "Quantidade inválida. A quantidade deve ser maior que zero e menor que a quantidade no estoque.";

        }

   } elseif( $opt == "repor" ) {
        
            if( $n_r > 0 ) {
                
                // se for repor um cara q está em uso, é considero aumentar o estoque do item usado(Pois ta repondo o estoque com algo q estava em uso)
                if($estado == "Em Uso"){
                    
                    $opt = "tirar em uso e repor usado";
                    
                    $qtde -= $n_r; //retirando já do que tá em uso pra dar update depois

                    $q = 'select * from controle where id_tipo = '.$id_tipo.' and id_marca = '.$id_marca.' and modelo = "'.$modelo.'" and estado = "Usado"'; 

                    $p = $con->query($q);

                    if($p->num_rows == 1) {
                        
                        $r = $p->fetch_assoc();

                        $x = $n_r + $r['qtde']; //qtde do usado mais o numero de retirada dos 'Em uso' = total usado

                        $q = 'UPDATE controle set qtde = ? where id_tipo = ? and id_marca = ? and modelo = ? and estado = "Usado"';

                        if( !( $p = $con->prepare($q) ) )

                            echo "Prepare failed: (" . $con->errno . ") " . $con->error;
            
                            if( !( $p->bind_param("iiis", $x, $id_tipo, $id_marca, $modelo) ) )

                                echo "Parameters failed: (" . $p->errno . ") " . $p->error;
            
                                if( !( $p->execute() ) )

                                    echo "Execute failed: (" . $p->errno . ") " . $p->error;
                                else

                                    echo "Operação efetuada com sucesso.";

                    } elseif ($p->num_rows == 0) {

                        $q = 'insert into controle (qtde , id_marca, id_tipo, modelo, estado) values (?,?,?,?,"Usado")';

                        if( !( $p = $con->prepare($q) ) )

                            echo "Prepare failed: (" . $con->errno . ") " . $con->error;
        
                            if( !( $p->bind_param("iiis", $n_r, $id_marca, $id_tipo, $modelo) ) )

                                echo "Parameters failed: (" . $p->errno . ") " . $p->error;
        
                                if(!($p->execute()))

                                    echo "Execute failed: (" . $p->errno . ") " . $p->error;
                                else

                                    echo "Operação efetuada com sucesso.";

                        }

                        $q = 'UPDATE controle set qtde = '.$qtde.' where id_marca = ? and id_tipo = ? and modelo = ? and estado = ?';

                        if( !( $p = $con->prepare($q) ) ) 

                            echo "Prepare failed: (" . $con->errno . ") " . $con->error;
        
                            if( !( $p->bind_param("iiss", $id_marca, $id_tipo, $modelo,$estado) ) )

                                echo "Parameters failed: (" . $p->errno . ") " . $p->error;
        
                                if( !( $p->execute() ) )

                                    echo "Execute failed: (" . $p->errno . ") " . $p->error;
                                else

                                    echo "Operação efetuada com sucesso.";

                    

                } else {
                    
                    $opt = "repor ".$estado;

                    $qtde += $n_r;
                    
                    $q = 'UPDATE controle set qtde = '.$qtde.' where id_marca = ? and id_tipo = ? and modelo = ? and estado = ?';

                    if( !( $p = $con->prepare($q) ) ) 

                        echo "Prepare failed: (" . $con->errno . ") " . $con->error;
        
                        if( !( $p->bind_param("iiss", $id_marca, $id_tipo, $modelo,$estado) ) )

                            echo "Parameters failed: (" . $p->errno . ") " . $p->error;
        
                            if( !( $p->execute() ) )

                                echo "Execute failed: (" . $p->errno . ") " . $p->error;
                            else

                                echo "Operação efetuada com sucesso.";
                }
            }

   } elseif( $opt == "descartar" ) {
        
        $qtde -= $n_r;

        $q = 'UPDATE controle set qtde = '.$qtde.' where id_marca = ? and id_tipo = ? and modelo = ? and estado = "Em Uso"';

        if( !( $p = $con->prepare($q) ) )

            echo "Prepare failed: (" . $con->errno . ") " . $con->error;
        
            if( !( $p->bind_param("iis", $id_marca, $id_tipo, $modelo) ) )

                echo "Parameters failed: (" . $p->errno . ") " . $p->error;
        
                if( !( $p->execute() ) )

                    echo "Execute failed: (" . $p->errno . ") " . $p->error;
                else

                    echo "Operação efetuada com sucesso.";
    
   } else {
        $_SESSION['exib_dialg'] = true;
        $_SESSION['dialg'] = "Opção Inválida.";
        redirect('adminControl.php',303);
    }

    //parte de inclusao no historico
    try{
       if( ( isset($_POST['opcao']) ) && ($n_r > 0) && ( isset($_SESSION['usuario']) ) ) {                        

            $q = 'INSERT into historico (usuario, operacao, qtde_op, qtde_dps, id_tipo, id_marca, modelo, estado, dataehora) values (?,?,?,?,?,?,?,?,?)';
    
            $dataehora = new DateTime("now",new DateTimeZone("America/Sao_Paulo"));
            $str = $dataehora->format("Y-m-d H:i:s");
            $opt = strtoupper($opt);
            echo $opt;

            if( !( $p = $con->prepare($q) ) )

                echo "Prepare failed: (" . $con->errno . ") " . $con->error;
    
                if( !( $p->bind_param("ssiiiisss", $_SESSION['usuario'], $opt, $n_r, $qtde, $id_tipo, $id_marca, $modelo, $estado, $str) ) )

                    echo "Parameters failed: (" . $p->errno . ") " . $p->error;
    
                    if( !( $p->execute() ) )

                        echo "Execute failed: (" . $p->errno . ") " . $p->error;
                    else

                        echo "Operação efetuada com sucesso.";

            }
    } catch(ErroException $e){

        echo $e->getMessage;
    
    }

   $_SESSION['exib_dialg'] = true;
   $_SESSION['dialg'] = "Operação efetuada com sucesso.";

redirect('adminControl.php',303);

?>