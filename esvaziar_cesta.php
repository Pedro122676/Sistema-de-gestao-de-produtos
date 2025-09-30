<?php
require 'db.php';
session_start();


if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$pdo = Database::getConnection();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->beginTransaction();

    try {

        $stmt_cesta = $pdo->prepare("SELECT id FROM cesta WHERE usuario_id = ? ORDER BY data_criacao DESC LIMIT 1");
        $stmt_cesta->execute([$usuario_id]);
        $cesta = $stmt_cesta->fetch();

        if ($cesta) {
            $cesta_id = $cesta['id'];

         
            $stmt_del_produtos = $pdo->prepare("DELETE FROM cesta_produtos WHERE cesta_id = ?");
            $stmt_del_produtos->execute([$cesta_id]);

       
            $stmt_del_cesta = $pdo->prepare("DELETE FROM cesta WHERE id = ?");
            $stmt_del_cesta->execute([$cesta_id]);
            
            $pdo->commit();
            
            $_SESSION['mensagem_cesta'] = "Cesta esvaziada com sucesso!";
            header("Location: visualizar_cesta.php");
            exit();

        } else {
         
            $_SESSION['mensagem_cesta'] = "Nenhuma cesta ativa para esvaziar.";
            header("Location: visualizar_cesta.php");
            exit();
        }

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['mensagem_cesta'] = "Erro ao esvaziar a cesta: " . $e->getMessage();
        header("Location: visualizar_cesta.php");
        exit();
    }
} else {
   
    header("Location: visualizar_cesta.php");
    exit();
}
?>