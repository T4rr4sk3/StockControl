<?php 

include_once 'DBC.php';

    $user = $_POST['username'];
    $pass = $_POST['pass'];

    session_start();

    

    try {
    
        $con = OpenCon("estoque");        

        $q = "SELECT nome,nivel from usuario where username like ? and pass like ?";

        $p = $con->prepare($q);
        
        $p->bind_param('ss',$user,$pass);

        $p->execute();

        $r = $p->get_result();

        if($r->num_rows > 0){
            if($r->num_rows == 1){
            
                $s = $r->fetch_row();
            
                $_SESSION['usuario'] = $s[0];

                $_SESSION['nivel'] = $s[1];
            
                redirect("adminControl.php",303);
            }
        } else{

            $_SESSION['erro'] = true;
            $_SESSION['msg'] = "Usuário ou senha incorretos.";
            redirect("index.php",303);

        }

    } catch (Exception $e) {

        $_SESSION['erro'] = true;
        $msg = "Não foi possível contactar banco de dados interno. ".$e->getFile()." Linha ".$e->getLine();
        $_SESSION['msg'] = str_replace("\\","\\\\",$msg);
        redirect("index.php",303);
        //echo $_SESSION['msg'];
    
    }
