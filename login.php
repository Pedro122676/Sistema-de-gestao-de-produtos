<?php
session_start();
require 'db.php';


if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    
  
    $_SESSION = array();

  
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

 
    session_destroy();
    
   
    header("Location: index.html"); 
    exit();
}


if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $email = trim($_POST['email']);
    $senha = $_POST['senha']; 

    $pdo = Database::getConnection();

    // Busca o usuário pelo e-mail
    $stmt = $pdo->prepare("SELECT id, senha_hash FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

   
    if ($usuario && $senha == $usuario['senha_hash']) {
       
        
        session_regenerate_id(true); 
        $_SESSION['usuario_id'] = $usuario['id'];
        
        header("Location: dashboard.php");
        exit();
    } else {
  
        echo "Email ou senha incorreta.";
    }
}
?>