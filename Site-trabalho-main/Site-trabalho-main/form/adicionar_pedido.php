<?php

session_start();

if (!isset($_SESSION["usuario_id"]) || $_SESSION["usuario_tipo"] != "admin") {
    header("Location: ../pages/login.php");
    exit;
}

include("avaliacoes.php");

$conn = conectar();

// Receber os dados do formulário

$nomeCliente = mysqli_real_escape_string($conn, trim($_POST["nome_cliente"]));
$data = mysqli_real_escape_string($conn, $_POST["data"]);
$retirada = mysqli_real_escape_string($conn, $_POST["retirada"]);

$produtos = $_POST["produto"] ?? [];
$quantidades = $_POST["quantidade"] ?? [];

// Verificar se existe pelo menos um produto válido

$temProduto = false;

for ($i = 0; $i < count($produtos); $i++) {

    $idProduto = (int) $produtos[$i];
    $quantidade = (int) ($quantidades[$i] ?? 0);

    if ($idProduto > 0 && $quantidade > 0) {
        $temProduto = true;
    }
}

if ($nomeCliente == "" || !$temProduto) {
    mysqli_close($conn);
    header("Location: ../pages/painel_admin.php?pedido=erro");
    exit;
}

// Procurar o cliente pelo nome

$sql = "SELECT idCLIENTES
        FROM CLIENTES
        WHERE nome_Clientes = '$nomeCliente'
        LIMIT 1";

$resultado = mysqli_query($conn, $sql);

if (mysqli_num_rows($resultado) > 0) {

    // Cliente já cadastrado

    $cliente = mysqli_fetch_assoc($resultado);
    $idCliente = (int) $cliente['idCLIENTES'];

} else {

    // Cliente ainda não cadastrado

    $sql = "INSERT INTO CLIENTES (nome_Clientes)
            VALUES ('$nomeCliente')";

    if (mysqli_query($conn, $sql)) {

        $idCliente = mysqli_insert_id($conn);

    } else {

        mysqli_close($conn);
        header("Location: ../pages/painel_admin.php?pedido=erro");
        exit;
    }
}

// Criar o pedido

$sql = "INSERT INTO PEDIDOS
        (CLIENTES_idCLIENTES, data_PEDIDOS, status_PEDIDOS, retirada_PEDIDOS)
        VALUES
        ($idCliente, '$data', 'Aguardando confirmação', '$retirada')";

if (mysqli_query($conn, $sql)) {

    $idPedido = mysqli_insert_id($conn);

    // Adicionar os produtos ao pedido

    for ($i = 0; $i < count($produtos); $i++) {

        $idProduto = (int) $produtos[$i];
        $quantidade = (int) ($quantidades[$i] ?? 0);

        if ($idProduto > 0 && $quantidade > 0) {

            // Verificar se o produto já está no pedido

            $sql = "SELECT quantidade_PRODUTOS
                    FROM ITENS_PEDIDOS
                    WHERE PEDIDOS_idPEDIDOS = $idPedido
                    AND PRODUTOS_idPRODUTOS = $idProduto
                    AND PEDIDOS_CLIENTES_idCLIENTES = $idCliente";

            $resultado = mysqli_query($conn, $sql);

            if (mysqli_num_rows($resultado) > 0) {

                // Somar a quantidade

                $sql = "UPDATE ITENS_PEDIDOS
                        SET quantidade_PRODUTOS = quantidade_PRODUTOS + $quantidade
                        WHERE PEDIDOS_idPEDIDOS = $idPedido
                        AND PRODUTOS_idPRODUTOS = $idProduto
                        AND PEDIDOS_CLIENTES_idCLIENTES = $idCliente";

            } else {

                // Inserir produto novo no pedido

                $sql = "INSERT INTO ITENS_PEDIDOS
                        (PEDIDOS_idPEDIDOS, PRODUTOS_idPRODUTOS,
                         PEDIDOS_CLIENTES_idCLIENTES, quantidade_PRODUTOS)
                        VALUES
                        ($idPedido, $idProduto, $idCliente, $quantidade)";
            }

            if (!mysqli_query($conn, $sql)) {

                mysqli_query($conn, "DELETE FROM PEDIDOS
                                     WHERE idPEDIDOS = $idPedido
                                     AND CLIENTES_idCLIENTES = $idCliente");

                mysqli_close($conn);
                header("Location: ../pages/painel_admin.php?pedido=erro");
                exit;
            }
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
