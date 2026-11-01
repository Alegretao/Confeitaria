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
$cliente = (int) $_POST["cliente"];
$data = mysqli_real_escape_string($conn, $_POST["data"]);
$retirada = mysqli_real_escape_string($conn, $_POST["retirada"]);

$produtos = $_POST["produto"];
$quantidades = $_POST["quantidade"];

// Verifica se existe pelo menos um produto
$temProduto = false;

foreach ($produtos as $produto) {
    if ($produto != "") {
        $temProduto = true;
    }
}

if (!$temProduto) {
    header("Location: ../pages/painel_admin.php?pedido=sem_produto");
    exit;
}

// Cadastra o pedido
$sql = "INSERT INTO PEDIDOS
        (CLIENTES_idCLIENTES, data_PEDIDOS, status_PEDIDOS, retirada_PEDIDOS)
        VALUES
        ($cliente, '$data', 'Aguardando confirmação', '$retirada')";

if (mysqli_query($conn, $sql)) {

    // Pega o número do pedido criado
    $idPedido = mysqli_insert_id($conn);

    // Percorre os produtos selecionados
    for ($i = 0; $i < count($produtos); $i++) {

        $idProduto = (int) $produtos[$i];
        $quantidade = (int) $quantidades[$i];

        // Ignora os espaços que não foram preenchidos
        if ($idProduto > 0 && $quantidade > 0) {

            // Verifica se esse produto já foi adicionado ao pedido
            $sql = "SELECT quantidade_PRODUTOS
                    FROM ITENS_PEDIDOS
                    WHERE PEDIDOS_idPEDIDOS = $idPedido
                    AND PRODUTOS_idPRODUTOS = $idProduto
                    AND PEDIDOS_CLIENTES_idCLIENTES = $cliente";

            $resultado = mysqli_query($conn, $sql);

            if (mysqli_num_rows($resultado) > 0) {

                // Soma a quantidade caso o produto já exista
                $sql = "UPDATE ITENS_PEDIDOS
                        SET quantidade_PRODUTOS = quantidade_PRODUTOS + $quantidade
                        WHERE PEDIDOS_idPEDIDOS = $idPedido
                        AND PRODUTOS_idPRODUTOS = $idProduto
                        AND PEDIDOS_CLIENTES_idCLIENTES = $cliente";

            } else {

                // Cadastra o produto no pedido
                $sql = "INSERT INTO ITENS_PEDIDOS
                        (PEDIDOS_idPEDIDOS, PRODUTOS_idPRODUTOS,
                         PEDIDOS_CLIENTES_idCLIENTES, quantidade_PRODUTOS)
                        VALUES
                        ($idPedido, $idProduto, $cliente, $quantidade)";
            }

            mysqli_query($conn, $sql);
        }
    }

    mysqli_close($conn);

    header("Location: ../pages/painel_admin.php?pedido=sucesso");
    exit;

} else {

    mysqli_close($conn);

    header("Location: ../pages/painel_admin.php?pedido=erro");
    exit;
}

?>
