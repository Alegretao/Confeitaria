
<?php
// Inicia a sessão
session_start();

// Verifica se o administrador está logado
if (!isset($_SESSION["usuario_id"]) || $_SESSION["usuario_tipo"] != "admin") {
    header("Location: ../pages/login.php");
    exit;
}

// Inclui a conexão com o banco
include("avaliacoes.php");
$mysqli = conectar();

// Recebe o ID do cliente
$id = (int) $_POST["idCLIENTES"];

// Exclui as avaliações do cliente
$sql = "DELETE FROM AVALIACOES
        WHERE ITENS_PEDIDOS_PEDIDOS_CLIENTES_idCLIENTES = $id";
mysqli_query($mysqli, $sql);

// Exclui os itens dos pedidos
$sql = "DELETE FROM ITENS_PEDIDOS
        WHERE PEDIDOS_CLIENTES_idCLIENTES = $id";
mysqli_query($mysqli, $sql);

// Exclui os pedidos
$sql = "DELETE FROM PEDIDOS
        WHERE CLIENTES_idCLIENTES = $id";
mysqli_query($mysqli, $sql);

// Exclui o cliente
$sql = "DELETE FROM CLIENTES
        WHERE idCLIENTES = $id";
mysqli_query($mysqli, $sql);

// Volta para o painel
header("Location: ../pages/painel_admin.php");
exit;
?>
