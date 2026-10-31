<?php
require_once __DIR__ .'/avaliacoes.php';
require_once __DIR__ . '/auth.php';

exigirAdministrador();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['sucesso' => false]);
    exit;
}

$idPedido = (int) ($_POST['idPEDIDOS'] ?? 0);
$idCliente = (int) ($_POST['CLIENTES_idCLIENTES'] ?? 0);
$status = $_POST['status_PEDIDOS'] ?? '';

$statusPermitidos = [
    'Aguardando confirmação',
    'Confirmado',
    'Em preparo',
    'Pronto para retirada',
    'Entregue',
    'Retirado',
    'Cancelado'
];

if ($idPedido <= 0 || $idCliente <= 0 || !in_array($status, $statusPermitidos, true)) {
    echo json_encode(['sucesso' => false]);
    exit;
}

$mysqli = conectar();

$sql = "UPDATE PEDIDOS
        SET status_PEDIDOS = ?
        WHERE idPEDIDOS = ? AND CLIENTES_idCLIENTES = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param('sii', $status, $idPedido, $idCliente);

$sucesso = $stmt->execute();

$stmt->close();
$mysqli->close();

echo json_encode(['sucesso' => $sucesso]);
