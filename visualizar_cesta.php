<?php
require 'db.php';
session_start();


if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$pdo = Database::getConnection();


$stmt_cesta = $pdo->prepare("SELECT id FROM cesta WHERE usuario_id = ? ORDER BY data_criacao DESC LIMIT 1");
$stmt_cesta->execute([$_SESSION['usuario_id']]);
$cesta_ativa = $stmt_cesta->fetch();

$produtos = [];
$total = 0;

if ($cesta_ativa) {
  
    $stmt = $pdo->prepare("SELECT cp.*, p.nome, p.preco 
                           FROM cesta_produtos cp
                           JOIN produtos p ON cp.produto_id = p.id
                           WHERE cp.cesta_id = ?");
    $stmt->execute([$cesta_ativa['id']]);
    $produtos = $stmt->fetchAll();

    foreach ($produtos as $p) {
        $total += $p['preco'];
    }
}



$mensagem = '';
if (isset($_SESSION['mensagem_cesta'])) {
    $mensagem = $_SESSION['mensagem_cesta'];
    unset($_SESSION['mensagem_cesta']); 
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Visualizar Cesta - Gestão de Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'dashboard.php'; ?> 
    
    <div class="container mt-4">
        <h3 class="mb-4">Sua Cesta de Compras</h3>

        <?php if ($mensagem): ?>
            <div class="alert alert-<?php echo strpos($mensagem, 'sucesso') !== false ? 'success' : 'danger'; ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($produtos)): ?>
            <div class="alert alert-info">A sua cesta está vazia. Adicione produtos na página de <a href="produtos.php">Produtos</a>.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Preço</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['nome']); ?></td>
                                <td>R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-dark">
                            <th>Total</th>
                            <th>R$ <?php echo number_format($total, 2, ',', '.'); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <form action="esvaziar_cesta.php" method="POST" onsubmit="return confirm('Tem certeza que deseja esvaziar a cesta? Esta ação é irreversível.');">
                <button type="submit" class="btn btn-danger mt-3">
                    <i class="fas fa-trash"></i> Esvaziar Cesta
                </button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>