<?php

session_start();

if (!isset($_SESSION["usuario_id"]) || $_SESSION["usuario_tipo"] != "admin") {
    header("Location: ../pages/login.php");
    exit;
}

include("avaliacoes.php");

$conn = conectar();

$idPedido = (int) $_POST["idPEDIDOS"];
$idCliente = (int) $_POST["idCLIENTES"];
$status = mysqli_real_escape_string($conn, $_POST["status"]);

$statusPermitidos = [
    "Aguardando confirmação",
    "Confirmado",
    "Em preparo",
    "Pronto para retirada",
    "Entregue",
    "Retirado",
    "Cancelado"
];

if (!in_array($status, $statusPermitidos, true)) {
    mysqli_close($conn);
    header("Location: ../pages/painel_admin.php?pedido=erro");
    exit;
}

$sql = "UPDATE PEDIDOS
        SET status_PEDIDOS = '$status'
        WHERE idPEDIDOS = $idPedido
        AND CLIENTES_idCLIENTES = $idCliente";

if (mysqli_query($conn, $sql)) {
    mysqli_close($conn);
    header("Location: ../pages/painel_admin.php?pedido=status_sucesso");
} else {
    mysqli_close($conn);
    header("Location: ../pages/painel_admin.php?pedido=erro");
}

exit;

?>
