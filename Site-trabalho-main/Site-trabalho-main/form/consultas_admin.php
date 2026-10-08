<?php

/**
 * Consultas usadas pelo painel do administrador (painel_admin.html).
 * Depende de conectar() definida em avaliacoes.php, que deve retornar
 * uma instância mysqli já conectada ao banco CONFEITARIA.
 */

function listarPedidos(mysqli $mysqli): array
{
    $sql = "SELECT
                p.idPEDIDOS,
                p.CLIENTES_idCLIENTES,
                p.data_PEDIDOS,
                p.status_PEDIDOS,
                p.retirada_PEDIDOS,
                c.nome_Clientes
            FROM PEDIDOS p
            JOIN CLIENTES c ON c.idCLIENTES = p.CLIENTES_idCLIENTES
            ORDER BY p.data_PEDIDOS DESC, p.idPEDIDOS DESC";

    $res = $mysqli->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

function listarItensDoPedido(mysqli $mysqli, int $idPedido, int $idCliente): array
{
    $sql = "SELECT
                ip.PRODUTOS_idPRODUTOS,
                ip.quantidade_PRODUTOS,
                pr.nome_PRODUTOS,
                pr.preco_PRODUTOS
            FROM ITENS_PEDIDOS ip
            JOIN PRODUTOS pr ON pr.idPRODUTOS = ip.PRODUTOS_idPRODUTOS
            WHERE ip.PEDIDOS_idPEDIDOS = ?
              AND ip.PEDIDOS_CLIENTES_idCLIENTES = ?";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ii', $idPedido, $idCliente);
    $stmt->execute();
    $res = $stmt->get_result();
    $itens = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();

    return $itens;
}

function listarProdutos(mysqli $mysqli): array
{
    $sql = "SELECT
                pr.idPRODUTOS,
                pr.nome_PRODUTOS,
                pr.descricao_PRODUTOS,
                pr.preco_PRODUTOS,
                pr.status_PRODUTOS,
                i.url_IMAGENS
            FROM PRODUTOS pr
            JOIN IMAGENS i ON i.idIMAGENS = pr.IMAGENS_idIMAGENS
            ORDER BY pr.nome_PRODUTOS";

    $res = $mysqli->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

function listarClientes(mysqli $mysqli): array
{
    $sql = "SELECT
                idCLIENTES,
                nome_Clientes,
                endereco_Clientes,
                telefone_Clientes
            FROM CLIENTES
            ORDER BY nome_Clientes";

    $res = $mysqli->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}
