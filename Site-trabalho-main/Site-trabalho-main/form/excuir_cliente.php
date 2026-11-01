<?php

session_start();

// Verifica se o administrador está logado
if (!isset($_SESSION["usuario_id"]) || $_SESSION["usuario_tipo"] != "admin") {
    header("Location: ../pages/login.php");
    exit;
}

// Inclui a conexão com o banco
include("avaliacoes.php");

$conn = conectar();

// Recebe o ID do cliente pela URL
$id = (int) $_GET["id"];

// Verifica se o cliente possui pedidos
$sql = "SELECT idPEDIDOS FROM PEDIDOS
        WHERE CLIENTES_idCLIENTES = $id";

$resultado = mysqli_query($conn, $sql);

// Se possuir pedidos, não permite excluir
if (mysqli_num_rows($resultado) > 0) {
    mysqli_close($conn);
    header("Location: ../pages/painel_admin.php?cliente=possui_pedidos");
    exit;
}

// Exclui o cliente
$sql = "DELETE FROM CLIENTES WHERE idCLIENTES = $id";

// Executa o comando
mysqli_query($conn, $sql);

// Fecha a conexão
mysqli_close($conn);

// Volta para o painel
header("Location: ../pages/painel_admin.php");

exit;

?>
