
<?php
session_start();

if (!isset($_SESSION['carrinho']) || count($_SESSION['carrinho']) == 0) {
    header("Location: carrinho.php");
    exit;
}

$mensagem = "Olá! Gostaria de consultar os preços destes bolos:%0A";

$nomes = array_keys($_SESSION['carrinho']);

for ($i = 0; $i < count($nomes); $i++) {
    $bolo = $nomes[$i];
    $quantidade = $_SESSION['carrinho'][$bolo];

    $mensagem .= "%0A- " . urlencode($bolo);
    $mensagem .= " (Quantidade: " . $quantidade . ")";
}

$telefone = "5511999999999";

$link = "https://wa.me/" . $telefone . "?text=" . $mensagem;

header("Location: " . $link);
exit;
?>

