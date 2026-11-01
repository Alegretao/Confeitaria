<?php
require_once __DIR__ . '/../form/avaliacoes.php';
require_once __DIR__ . '/../form/consultas_admin.php';
require_once __DIR__ . '/../form/auth.php';

exigirAdministrador();

$mysqli = conectar();

$pedidos = listarPedidos($mysqli);
$produtos = listarProdutos($mysqli);
$clientes = listarClientes($mysqli);

$pedidosAtivos = [];
$pedidosHistorico = [];



foreach ($pedidos as $pedido) {
    $status = strtolower($pedido['status_PEDIDOS'] ?? '');

    if (in_array($status, ['entregue', 'retirado', 'cancelado'], true)) {
        $pedidosHistorico[] = $pedido;
    } else {
        $pedidosAtivos[] = $pedido;
    }
}
?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Painel do Administrador - Fábrica Encantada</title>

   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
      rel="stylesheet">

    <link rel="stylesheet" href="../css/styleadmin.css">
</head>

<body>

<nav class="navbar navbar-expand-lg nav-premium">
    <div class="container">

        <a class="navbar-brand nome-empresa" href="home.html">
            Fábrica Encantada — Admin
        </a>

        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="text-dark">
                Olá, <?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Administrador') ?>
            </span>

            <a class="btn btn-sm btn-outline-dark" href="logout.php">
                Sair
            </a>
        </div>

    </div>
</nav>

<br>

<div class="container">

    <!-- PEDIDOS -->

    <h3>Pedidos</h3>

    <?php if (isset($_GET['pedido']) && $_GET['pedido'] === 'sucesso'): ?>
        <div class="alert alert-success">
            Pedido cadastrado com sucesso!
        </div>
    <?php elseif (isset($_GET['pedido']) && $_GET['pedido'] === 'erro'): ?>
        <div class="alert alert-danger">
            Não foi possível cadastrar o pedido.
        </div>
    <?php endif; ?>

    <form action="../form/adicionar_pedido.php" method="POST" class="mb-4">

        <h5>Adicionar pedido</h5>

        <label>Nome do cliente:</label>

        <input type="text" name="nome_cliente" class="form-control"
               maxlength="50" placeholder="Digite o nome do cliente" required>

        <br>

        <label>Data do pedido:</label>

        <input type="date" name="data" class="form-control" required>

        <br>

        <label>Retirada:</label>

        <input type="text" name="retirada" class="form-control"
               placeholder="Ex: Retirada na loja" maxlength="45" required>

        <br>

        <h5>Produtos do pedido</h5>

        <?php for ($i = 0; $i < 10; $i++): ?>

            <div class="row mb-2">

                <div class="col-md-6">

                    <select name="produto[]" class="form-control">
                        <option value="">Selecione um produto</option>

                        <?php foreach ($produtos as $produto): ?>
                            <option value="<?= (int) $produto['idPRODUTOS'] ?>">
                                <?= htmlspecialchars($produto['nome_PRODUTOS']) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="col-md-3">

                    <input type="number" name="quantidade[]"
                           class="form-control"
                           placeholder="Quantidade" min="1">

                </div>

            </div>

        <?php endfor; ?>

        <button type="submit" class="btn btn-success mt-3">
            Cadastrar pedido
        </button>

    </form>

   
<table class="table table-striped table-bordered">

    <thead>
        <tr>
            <th>#Pedido</th>
            <th>Cliente</th>
            <th>Data</th>
            <th>Status</th>
            <th>Retirada</th>
            <th>Produto</th>
            <th>Quantidade</th>
            <th>Atualizar</th>
        </tr>
    </thead>

    <?php if (count($pedidosAtivos) === 0): ?>

        <tbody>
            <tr>
                <td colspan="8" class="text-center">
                    Nenhum pedido ativo.
                </td>
            </tr>
        </tbody>

    <?php else: ?>

        <?php foreach ($pedidosAtivos as $pedido): ?>

            <?php
            $itens = listarItensDoPedido(
                $mysqli,
                (int) $pedido['idPEDIDOS'],
                (int) $pedido['CLIENTES_idCLIENTES']
            );

            $totalLinhas = max(count($itens), 1);
            ?>

            <tbody>

                <?php if (count($itens) > 0): ?>

                    <?php foreach ($itens as $indice => $item): ?>

                        <tr>

                            <?php if ($indice === 0): ?>

                                <td rowspan="<?= $totalLinhas ?>">
                                    <?= (int) $pedido['idPEDIDOS'] ?>
                                </td>

                                <td rowspan="<?= $totalLinhas ?>">
                                    <?= htmlspecialchars($pedido['nome_Clientes']) ?>
                                    <br>
                                    <small>ID: <?= (int) $pedido['CLIENTES_idCLIENTES'] ?></small>
                                </td>

                                <td rowspan="<?= $totalLinhas ?>">
                                    <?= htmlspecialchars($pedido['data_PEDIDOS']) ?>
                                </td>

                                <td rowspan="<?= $totalLinhas ?>">
                                    <?= htmlspecialchars($pedido['status_PEDIDOS']) ?>
                                </td>

                                <td rowspan="<?= $totalLinhas ?>">
                                    <?= htmlspecialchars($pedido['retirada_PEDIDOS']) ?>
                                </td>

                            <?php endif; ?>

                            <td>
                                <?= htmlspecialchars($item['nome_PRODUTOS']) ?>
                            </td>

                            <td>
                                <?= (int) $item['quantidade_PRODUTOS'] ?>
                            </td>

                            <?php if ($indice === 0): ?>

                                <td rowspan="<?= $totalLinhas ?>">

                                    <form action="../form/atualizar_status_pedido.php" method="POST">

                                        <input type="hidden" name="idPEDIDOS"
                                               value="<?= (int) $pedido['idPEDIDOS'] ?>">

                                        <input type="hidden" name="idCLIENTES"
                                               value="<?= (int) $pedido['CLIENTES_idCLIENTES'] ?>">

                                        <select name="status" class="form-select mb-2">

                                            <option value="Aguardando confirmação">Aguardando confirmação</option>
                                            <option value="Confirmado">Confirmado</option>
                                            <option value="Em preparo">Em preparo</option>
                                            <option value="Pronto para retirada">Pronto para retirada</option>
                                            <option value="Entregue">Entregue</option>
                                            <option value="Retirado">Retirado</option>
                                            <option value="Cancelado">Cancelado</option>

                                        </select>

                                        <button type="submit" class="btn btn-primary btn-sm">
                                            Salvar
                                        </button>

                                    </form>

                                </td>

                            <?php endif; ?>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td><?= (int) $pedido['idPEDIDOS'] ?></td>
                        <td><?= htmlspecialchars($pedido['nome_Clientes']) ?></td>
                        <td><?= htmlspecialchars($pedido['data_PEDIDOS']) ?></td>
                        <td><?= htmlspecialchars($pedido['status_PEDIDOS']) ?></td>
                        <td><?= htmlspecialchars($pedido['retirada_PEDIDOS']) ?></td>
                        <td colspan="2">Nenhum item cadastrado</td>
                        <td>
                            <!-- Nenhum item para atualizar -->
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        <?php endforeach; ?>

    <?php endif; ?>

</table>


    <br>

    <!-- PRODUTOS -->

    <h3>Produtos</h3>

    <?php if (isset($_GET['produto']) && $_GET['produto'] === 'status_sucesso'): ?>

        <div class="alert alert-success">
            Disponibilidade do produto atualizada com sucesso!
        </div>

    <?php elseif (isset($_GET['produto']) && $_GET['produto'] === 'sucesso'): ?>

        <div class="alert alert-success">
            Produto cadastrado com sucesso!
        </div>

    <?php elseif (isset($_GET['produto']) && $_GET['produto'] === 'erro'): ?>

        <div class="alert alert-danger">
            Não foi possível realizar a operação com o produto.
        </div>

    <?php endif; ?>

    <div class="card p-3 mb-4">

        <h5>Adicionar novo bolo</h5>

        <form action="../form/adicionar_produto.php"
              method="POST" enctype="multipart/form-data">

            <div class="mb-3">

                <label class="form-label">Nome do bolo</label>

                <input type="text" name="nome" class="form-control"
                       maxlength="100" required>

            </div>

            <div class="mb-3">

                <label class="form-label">Descrição</label>

                <textarea name="descricao" class="form-control"
                          maxlength="255" required></textarea>

            </div>

            <div class="mb-3">

                <label class="form-label">Foto do bolo</label>

                <input type="file" name="foto" class="form-control"
                       accept="image/png,image/jpeg,image/webp" required>

            </div>

            <button type="submit" class="btn btn-dark">
                Cadastrar produto
            </button>

        </form>

    </div>


<table class="table table-striped table-bordered">

    <thead>
        <tr>
            <th>Imagem</th>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody>

        <?php if (count($produtos) === 0): ?>

            <tr>
                <td colspan="4" class="text-center">
                    Nenhum produto cadastrado.
                </td>
            </tr>

        <?php else: ?>

            <?php foreach ($produtos as $produto): ?>

                <tr>

                    <td>
                        <?php $img = basename((string) $produto['url_IMAGENS']); ?>

                        <img src="../img/<?= htmlspecialchars($img) ?>"
                             alt="" style="max-width:80px;">
                    </td>

                    <td>
                        <?= htmlspecialchars($produto['nome_PRODUTOS']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($produto['descricao_PRODUTOS']) ?>
                    </td>

                    <td>

                        <?php if ($produto['status_PRODUTOS'] === 'Disponível'): ?>

                            <span class="badge bg-success">Disponível</span>

                        <?php else: ?>

                            <span class="badge bg-danger">Indisponível</span>

                        <?php endif; ?>

                        <form action="../form/atualizar_status_produto.php"
                              method="POST" class="mt-2">

                            <input type="hidden" name="idPRODUTOS"
                                   value="<?= (int) $produto['idPRODUTOS'] ?>">

                            <button type="submit" name="status"
                                    value="Disponível"
                                    class="btn btn-success btn-sm mb-1">
                                Tornar disponível
                            </button>

                            <button type="submit" name="status"
                                    value="Indisponível"
                                    class="btn btn-danger btn-sm mb-1">
                                Tornar indisponível
                            </button>

                        </form>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php endif; ?>

    </tbody>

</table>

    <br>

    <!-- CLIENTES -->

    <h3>Clientes</h3>

    <form action="../form/adicionar_cliente.php"
          method="POST" class="mb-4">

        <h5>Adicionar cliente</h5>

        <label>Nome:</label>

        <input type="text" name="nome" class="form-control"
               maxlength="50" required>

        <label>Endereço:</label>

        <input type="text" name="endereco" class="form-control"
               maxlength="45" required>

        <label>Telefone:</label>

        <input type="tel" name="telefone" class="form-control"
               maxlength="11" required>

        <button type="submit" class="btn btn-success mt-3">
            Adicionar cliente
        </button>

    </form>

    <table class="table table-striped table-bordered">

        <thead>
            <tr>
                <th>#Cliente</th>
                <th>Nome</th>
                <th>Endereço</th>
                <th>Telefone</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>

            <?php if (count($clientes) === 0): ?>

                <tr>
                    <td colspan="5" class="text-center">
                        Nenhum cliente cadastrado.
                    </td>
                </tr>

            <?php else: ?>

                <?php foreach ($clientes as $cliente): ?>

                    <tr>

                        <td>
                            <?= (int) $cliente['idCLIENTES'] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($cliente['nome_Clientes']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($cliente['endereco_Clientes'] ?? '') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($cliente['telefone_Clientes'] ?? '') ?>
                        </td>

                        <td>

                            <a href="../form/alterar_cliente.php?id=<?= (int) $cliente['idCLIENTES'] ?>"
                               class="btn btn-warning btn-sm">
                                Alterar
                            </a>
                            <a href="../form/excluir_clientes.php?id=<?= (int) $cliente['idCLIENTES'] ?>"
                                class="btn btn-danger btn-sm"
                                   onclick="return confirm('Tem certeza que deseja excluir este cliente?')">
                                Excluir
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

        </tbody>

    </table>

</div>

<br><br>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
