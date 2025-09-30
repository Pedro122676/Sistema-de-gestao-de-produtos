<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
 
    if (!isset($_SESSION['usuario_id'])) {
        die("Você precisa estar logado para criar uma cesta.");
    }
    
  
    $pdo = Database::getConnection(); 
    
   
    if (empty($_POST['produtos']) || !is_array($_POST['produtos'])) {
      
        $_SESSION['mensagem_cesta'] = "Erro: Selecione pelo menos um produto para a cesta.";
        header("Location: produtos.php"); 
        exit();
    }
    
    $usuario_id = $_SESSION['usuario_id'];
    $produtos_validos = [];

 
    foreach ($_POST['produtos'] as $produto_id) {
        $id = filter_var($produto_id, FILTER_VALIDATE_INT);
        if ($id !== false) {
            $produtos_validos[] = $id;
        }
    }
    
    if (empty($produtos_validos)) {
         $_SESSION['mensagem_cesta'] = "Erro: IDs de produtos inválidos detectados. Por favor, tente novamente.";
         header("Location: produtos.php"); 
         exit();
    }

   
  
    $pdo->beginTransaction(); 

    try {
     
        // Cria a Cesta Principal
        $stmt = $pdo->prepare("INSERT INTO cesta (usuario_id) VALUES (?)");
        $stmt->execute([$usuario_id]);
        $cesta_id = $pdo->lastInsertId();

       
     
        $sql_parts = [];
        $params = [];
        
        foreach ($produtos_validos as $produto_id) {
            $sql_parts[] = '(?, ?)';
            $params[] = $cesta_id;
            $params[] = $produto_id;
        }
        
        $sql = "INSERT INTO cesta_produtos (cesta_id, produto_id) VALUES " . implode(', ', $sql_parts);
        $stmt_produtos = $pdo->prepare($sql);
        $stmt_produtos->execute($params);
        
        $pdo->commit(); 
        
        $_SESSION['mensagem_cesta'] = "Cesta criada com sucesso com " . count($produtos_validos) . " produtos.";
        header("Location: visualizar_cesta.php");
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['mensagem_cesta'] = "Erro ao criar a cesta: " . $e->getMessage();
        header("Location: produtos.php");
        exit();
    }
}

header("Location: produtos.php"); 
exit();
?>