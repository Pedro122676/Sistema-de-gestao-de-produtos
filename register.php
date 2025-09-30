<?php
require 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha']; // Senha bruta

  
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT); 
    
 
    $pdo = Database::getConnection();

    try {
    
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash) VALUES (?, ?, ?)");
        $stmt->execute([$nome, $email, $senha_hash]);
        
   
        echo "<!DOCTYPE html><html lang='pt-br'><head><meta charset='UTF-8'><title>Cadastro Sucesso</title><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'></head><body class='bg-light'><div class='container mt-5'><div class='alert alert-success mx-auto' style='max-width: 400px;'>Usuário **" . htmlspecialchars($nome) . "** cadastrado com sucesso! <br><a href='index.html'>Clique aqui para fazer login</a></div></div></body></html>";

    } catch (PDOException $e) {
   
        if ($e->getCode() == '23000') {
            $erro = "Erro: O e-mail informado já está em uso. <a href='register.html'>Tente outro e-mail.</a>";
        } else {
            $erro = "Erro ao cadastrar usuário: " . $e->getMessage();
        }
        
     
        echo "<!DOCTYPE html><html lang='pt-br'><head><meta charset='UTF-8'><title>Erro</title><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'></head><body class='bg-light'><div class='container mt-5'><div class='alert alert-danger mx-auto' style='max-width: 400px;'>$erro</div></div></body></html>";
    }
} else {

    header("Location: register.html");
    exit();
}
?>