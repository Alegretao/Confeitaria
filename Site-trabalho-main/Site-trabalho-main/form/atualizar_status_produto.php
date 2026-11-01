<?php

session_start();

if (!isset($_SESSION["usuario_id"]) || $_SESSION["usuario_tipo"] != "admin") {
    header("Location: ../pages/login.php");
    exit;
}

include("avaliacoes.php");

$conn = conectar();

$idProduto = (int) $_POST["idPRODUTOS"];
$status = $_POST["status"];

if ($status != "Disponível" && $status != "Indisponível") {
    mysqli_close($conn);
    header("Location: ../pages/painel_admin.php?produto=erro");
    exit;
}

$sql = "UPDATE PRODUTOS
        SET status_PRODUTOS = '$status'
        WHERE idPRODUTOS = $idProduto";

if (mysqli_query($conn, $sql)) {
    mysqli_close($conn);
    header("Location: ../pages/painel_admin.php?produto=sucesso");
} else {
    mysqli_close($conn);
    header("Location: ../pages/painel_admin.php?produto=erro");
}

exit;

?>
