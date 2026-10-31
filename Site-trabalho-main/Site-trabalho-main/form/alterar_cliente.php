
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

// Recebe o ID do cliente pela URL
$id = (int) $_GET["id"];

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recebe os novos dados
    $nome = mysqli_real_escape_string($conn, $_POST["nome"]);
    $endereco = mysqli_real_escape_string($conn, $_POST["endereco"]);
    $telefone = mysqli_real_escape_string($conn, $_POST["telefone"]);

    // Atualiza os dados do cliente
    $sql = "UPDATE CLIENTES
            SET nome_Clientes='$nome',
                endereco_Clientes='$endereco',
                telefone_Clientes='$telefone'
            WHERE idCLIENTES=$id";

    // Executa o comando
    if (mysqli_query($conn, $sql)) {
        header("Location: ../pages/painel_admin.php?cliente=alterado");
    } else {
        header("Location: ../pages/painel_admin.php?cliente=erro");
    }

    exit;
}

// Busca os dados atuais do cliente
$sql = "SELECT * FROM CLIENTES WHERE idCLIENTES=$id";
$resultado = mysqli_query($conn, $sql);
$cliente = mysqli_fetch_assoc($resultado);

// Verifica se o cliente existe
if (!$cliente) {
    header("Location: ../pages/painel_admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Alterar Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5">

    <h2>Alterar Cliente</h2>

    <form method="POST">

        Nome:<br>
        <input type="text" name="nome" class="form-control"
               value="<?= htmlspecialchars($cliente['nome_Clientes']) ?>" required>

        <br>

        Endereço:<br>
        <input type="text" name="endereco" class="form-control"
               value="<?= htmlspecialchars($cliente['endereco_Clientes']) ?>" required>

        <br>

        Telefone:<br>
        <input type="text" name="telefone" class="form-control"
               value="<?= htmlspecialchars($cliente['telefone_Clientes']) ?>" required>

        <br>

        <button type="submit" class="btn btn-dark">Atualizar</button>

    </form>

    <br>

    <a href="../pages/painel_admin.php">Voltar</a>

</div>

</body>
</html>

