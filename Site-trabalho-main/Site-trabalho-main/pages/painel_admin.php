<?php
require_once __DIR__ . '/../form/avaliacoes.php';      // função conectar()
require_once __DIR__ . '/../form/consultas_admin.php';
require_once __DIR__ . '/../form/auth.php';

exigirAdministrador(); // consultas de pedidos e produtos

$mysqli = conectar();

$pedidos  = listarPedidos($mysqli);
$produtos = listarProdutos($mysqli);
$clientes = listarClientes($mysqli);
?>
<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Painel do Administrador - Fábrica Encantada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/stylehome.css">
  </head>
  <body>
    <nav class="navbar navbar-expand-lg nav-premium">
      <div class="container">
        <a class="navbar-brand nome-empresa" href="home.html">Fábrica Encantada — Admin</a>
        <div class="ms-auto d-flex align-items-center gap-3">
          <span class="text-dark">Olá, <?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Administrador') ?></span>
          <a class="btn btn-sm btn-outline-dark" href="logout.php">Sair</a>
        </div>
      </div>
    </nav>
    <br/>

    <div class="container">

      <h3>Pedidos</h3>
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>#Pedido</th>
            <th>Cliente</th>
            <th>Data</th>
            <th>Status</th>
            <th>Retirada</th>
            <th>Itens</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($pedidos) === 0): ?>
            <tr><td colspan="6" class="text-center">Nenhum pedido cadastrado.</td></tr>
          <?php else: ?>
            <?php foreach ($pedidos as $pedido): ?>
              <?php $itens = listarItensDoPedido($mysqli, (int) $pedido['idPEDIDOS'], (int) $pedido['CLIENTES_idCLIENTES']); ?>
              <tr>
                <td><?= (int) $pedido['idPEDIDOS'] ?></td>
                <td><?= htmlspecialchars($pedido['nome_Clientes']) ?></td>
                <td><?= htmlspecialchars($pedido['data_PEDIDOS']) ?></td>
                <td><?= htmlspecialchars($pedido['status_PEDIDOS']) ?></td>
                <td><?= htmlspecialchars($pedido['retirada_PEDIDOS']) ?></td>
                <td>
                  <ul class="mb-0">
                    <?php foreach ($itens as $item): ?>
                      <li>
                        <?= htmlspecialchars($item['nome_PRODUTOS']) ?>
                        (x<?= (int) $item['quantidade_PRODUTOS'] ?>)
                        — R$ <?= number_format((float) $item['preco_PRODUTOS'], 2, ',', '.') ?>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>

      <br/>

      <h3>Produtos</h3>
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>Imagem</th>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Preço</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($produtos) === 0): ?>
            <tr><td colspan="5" class="text-center">Nenhum produto cadastrado.</td></tr>
          <?php else: ?>
            <?php foreach ($produtos as $produto): ?>
              <tr>
                <td><?php $img = basename((string) $produto['url_IMAGENS']); ?><img src="../img/<?= htmlspecialchars($img) ?>" alt="" style="max-width:80px;"></td>
                <td><?= htmlspecialchars($produto['nome_PRODUTOS']) ?></td>
                <td><?= htmlspecialchars($produto['descricao_PRODUTOS']) ?></td>
                <td>R$ <?= number_format((float) $produto['preco_PRODUTOS'], 2, ',', '.') ?></td>
                <td><?= htmlspecialchars($produto['status_PRODUTOS']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>

      <br/>

      <h3>Clientes</h3>
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>#Cliente</th>
            <th>Nome</th>
            <th>Endereço</th>
            <th>Telefone</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($clientes) === 0): ?>
            <tr><td colspan="4" class="text-center">Nenhum cliente cadastrado.</td></tr>
          <?php else: ?>
            <?php foreach ($clientes as $cliente): ?>
              <tr>
                <td><?= (int) $cliente['idCLIENTES'] ?></td>
                <td><?= htmlspecialchars($cliente['nome_Clientes']) ?></td>
                <td><?= htmlspecialchars($cliente['endereco_Clientes'] ?? '') ?></td>
                <td><?= htmlspecialchars($cliente['telefone_Clientes'] ?? '') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>

    </div>

    <br/><br/>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>