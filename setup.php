<?php

$host = 'localhost';
$db   = 'gestao_produtos'; 
$user = 'root'; 
$pass = '';     

try {
    
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $db");
    $pdo->exec("USE $db");

    
    $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100),
        email VARCHAR(100) UNIQUE,
        senha_hash VARCHAR(255) 

   
    $pdo->exec("CREATE TABLE IF NOT EXISTS fornecedores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100),
        cnpj VARCHAR(20) UNIQUE
    )");

    
    $pdo->exec("CREATE TABLE IF NOT EXISTS produtos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100),
        preco DECIMAL(10,2),
        fornecedor_id INT,
        FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id)
    )");

  
    $pdo->exec("CREATE TABLE IF NOT EXISTS cesta (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT,
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    )");

   
    $pdo->exec("CREATE TABLE IF NOT EXISTS cesta_produtos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cesta_id INT,
        produto_id INT,
        FOREIGN KEY (cesta_id) REFERENCES cesta(id),
        FOREIGN KEY (produto_id) REFERENCES produtos(id),
        UNIQUE KEY unique_cesta_produto (cesta_id, produto_id)
    )");

    echo "Banco de dados e tabelas criados/atualizados com sucesso!";

} catch (PDOException $e) {
    die("Erro ao configurar o banco de dados: " . $e->getMessage());
}
?>