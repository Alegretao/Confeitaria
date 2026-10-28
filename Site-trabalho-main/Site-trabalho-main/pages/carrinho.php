<?php
session_start();

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = array();
    
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bolo'])) {
    $bolo = $_POST['bolo'];

    if (isset($_SESSION['carrinho'][$bolo])) {
        $_SESSION['carrinho'][$bolo] =
            $_SESSION['carrinho'][$bolo] + 1;
    } else {
        $_SESSION['carrinho'][$bolo] = 1;
    }
}


if (isset($_GET['remover'])) {
    $bolo = $_GET['remover'];

    if (isset($_SESSION['carrinho'][$bolo])) {
        unset($_SESSION['carrinho'][$bolo]);
    }
}


if (isset($_POST['alterar']) && isset($_POST['quantidade'])) {
    $bolo = $_POST['alterar'];
    $quantidade = (int) $_POST['quantidade'];

    if (isset($_SESSION['carrinho'][$bolo])) {
        if ($quantidade > 0 && $quantidade <= 99) {
            $_SESSION['carrinho'][$bolo] = $quantidade;
        } elseif ($quantidade == 0) {
            unset($_SESSION['carrinho'][$bolo]);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Meu Carrinho - Fábrica Encantada</title>

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
          rel="stylesheet">
    <link rel="stylesheet" href="../css/carrinho.css">
</head>

<body class="bg-light">

<div class="container py-5">

    <h1 class="mb-4">
        <i class="bi bi-cart3"></i>
        Meu carrinho
    </h1>

    <?php
    if (count($_SESSION['carrinho']) == 0) {
        echo "<p>Seu carrinho está vazio.</p>";
    } else {

        $nomes = array_keys($_SESSION['carrinho']);
        $totalItens = 0;

        for ($i = 0; $i < count($nomes); $i++) {
            $bolo = $nomes[$i];
            $quantidade = $_SESSION['carrinho'][$bolo];
            $totalItens = $totalItens + $quantidade;

            echo "<div class='card mb-3'>";
            echo "<div class='card-body'>";
            echo "<h5 class='card-title'>"
                . htmlspecialchars($bolo) . "</h5>";

            echo "<form action='carrinho.php' method='POST'>";
            echo "<input type='hidden' name='alterar' value='"
                . htmlspecialchars($bolo, ENT_QUOTES, 'UTF-8') . "'>";

            echo "<label class='form-label'>Quantidade:</label>";
            echo "<input class='form-control mb-2' type='number' "
                . "name='quantidade' min='0' max='99' value='"
                . $quantidade . "' required>";

            echo "<button class='btn btn-primary btn-sm' type='submit'>";
            echo "Atualizar quantidade";
            echo "</button>";

            echo "</form>";

            echo "<a class='btn btn-danger btn-sm mt-2' href='carrinho.php?remover="
                . urlencode($bolo) . "'>Remover bolo</a>";

            echo "</div>";
            echo "</div>";
        }

        echo "<p><strong>Total de unidades: "
            . $totalItens . "</strong></p>";

        echo "<p>Consulte os preços e finalize seu pedido pelo WhatsApp.</p>";

        echo "<form action='finalizar_pedido.php' method='POST'>";
        echo "<button type='submit' class='btn btn-success'>";
        echo "<i class='bi bi-whatsapp'></i> Consultar preços pelo WhatsApp";
        echo "</button>";
        echo "</form>";
    }
    ?>

    <a href="bolos.html" class="btn btn-secondary mt-3">
        <i class="bi bi-arrow-left"></i>
        Voltar aos bolos
    </a>

</div>

</body>
</html>

