
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

// Conecta ao banco
$conn = conectar();

// Recebe os dados do formulário
$nome = $_POST["nome"];
$endereco = $_POST["endereco"];
$telefone = $_POST["telefone"];

// Cria o comando para cadastrar o cliente
$sql = "INSERT INTO CLIENTES
        (nome_Clientes, endereco_Clientes, telefone_Clientes)
        VALUES ('$nome', '$endereco', '$telefone')";

// Executa o comando
if (mysqli_query($conn, $sql)) {

    header("Location: ../pages/painel_admin.php?cliente=sucesso");

} else {

    header("Location: ../pages/painel_admin.php?cliente=erro");

}

// Fecha a conexão
mysqli_close($conn);

exit;
?>
