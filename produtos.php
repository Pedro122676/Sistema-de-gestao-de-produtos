<?php
require 'db.php';
session_start(); 

// 1. Checagem de Autenticação
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// CORREÇÃO: Inicializa a variável $pdo chamando o método estático da classe Database
$pdo = Database::getConnection();
$mensagem = '';
$mensagem_erro = '';

// 2. Lógica de Inserção (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $preco = filter_var($_POST['preco'], FILTER_VALIDATE_FLOAT); 
    $fornecedor_id = filter_var($_POST['fornecedor_id'], FILTER_VALIDATE_INT); 
   
    if (empty($nome) || $preco === false || $fornecedor_id === false) {
        $mensagem_erro = "Dados do produto inválidos ou incompletos.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, fornecedor_id) VALUES (?, ?, ?)");
            $stmt->execute([$nome, $preco, $fornecedor_id]);
            $mensagem = "Produto cadastrado com sucesso!";
            
        } catch (PDOException $e) {
            $mensagem_erro = "Erro ao cadastrar produto: " . $e->getMessage();
        }
    }
}

// 3. Lógica de Exibição (GET)
// Busca todos os produtos
$produtos = [];
// CORREÇÃO: A linha 36 (ou próxima) agora usa o objeto $pdo corretamente inicializado
try {
    $stmt_produtos = $pdo->query("SELECT p.id, p.nome, p.preco, f.nome as fornecedor_nome FROM produtos p JOIN fornecedores f ON p.fornecedor_id = f.id ORDER BY p.nome");
    $produtos = $stmt_produtos->fetchAll();
} catch (PDOException $e) {
    $mensagem_erro .= ($mensagem_erro ? '<br>' : '') . "Erro ao buscar produtos: " . $e->getMessage();
}

// Busca todos os fornecedores para o <select>
$fornecedores = [];
try {
    $stmt_fornecedores = $pdo->query("SELECT id, nome FROM fornecedores ORDER BY nome");
    $fornecedores = $stmt_fornecedores->fetchAll();
} catch (PDOException $e) {
    $mensagem_erro .= ($mensagem_erro ? '<br>' : '') . "Erro ao buscar fornecedores para o formulário: " . $e->getMessage();
}

// 4. HTML
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Produtos - Gestão de Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'dashboard.php'; // Inclui o menu de navegação ?> 
    
    <div class="container mt-4">
        <h3 class="mb-4">Gestão de Produtos</h3>

        <?php if (isset($mensagem) && $mensagem): ?>
            <div class="alert alert-success"><?php echo $mensagem; ?></div>
        <?php endif; ?>
        <?php if (isset($mensagem_erro) && $mensagem_erro): ?>
            <div class="alert alert-danger"><?php echo $mensagem_erro; ?></div>
        <?php endif; ?>


        <div class="card mb-4">
            <div class="card-header">Cadastrar Novo Produto</div>
            <div class="card-body">
                <form action="produtos.php" method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="nome" class="form-label">Nome do Produto</label>
                            <input type="text" name="nome" id="nome" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="preco" class="form-label">Preço</label>
                            <input type="number" step="0.01" name="preco" id="preco" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="fornecedor_id" class="form-label">Fornecedor</label>
                            <select name="fornecedor_id" id="fornecedor_id" class="form-select" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($fornecedores as $forn): ?>
                                    <option value="<?php echo htmlspecialchars($forn['id']); ?>">
                                        <?php echo htmlspecialchars($forn['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <h4 class="mt-5 mb-3">Produtos Cadastrados (Selecione para Cesta)</h4>
        <form action="cesta.php" method="POST">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Preço</th>
                            <th>Fornecedor</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($produtos)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Nenhum produto cadastrado.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($produtos as $p): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($p['id']); ?></td>
                                    <td><?php echo htmlspecialchars($p['nome']); ?></td>
                                    <td>R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($p['fornecedor_nome']); ?></td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="produtos[]" value="<?php echo htmlspecialchars($p['id']); ?>" id="produto_<?php echo htmlspecialchars($p['id']); ?>">
                                            <label class="form-check-label" for="produto_<?php echo htmlspecialchars($p['id']); ?>">Adicionar</label>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn btn-success mt-3" <?php echo empty($produtos) ? 'disabled' : ''; ?>>Adicionar Selecionados à Cesta</button>
        </form>
    </div>
</body>
</html>