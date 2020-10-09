<?php 

include_once 'DBC.php';

    $user = $_POST['username'];
    $pass = $_POST['pass'];

    //echo '<script>alert("Tá setado.")</script>';

    $con = OpenCon("estoque");

    $q = "SELECT nome,nivel from usuario where username like ? and pass like ?";

    $p = $con->prepare($q);
        
    $p->bind_param('ss',$user,$pass);

    $p->execute();

    $r = $p->get_result();

    if($r->num_rows > 0){
        if($r->num_rows == 1){
            
            session_start();
            
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
