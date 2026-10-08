<?php
/**
 * Conexão e operações sobre a tabela AVALIACOES.
 * Ajuste host, usuário e senha conforme seu ambiente.
 */

function conectar(): mysqli
{
    $host = 'localhost';
    $db   = 'CONFEITARIA';
    $user = 'root';
    $pass = '';

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $mysqli = new mysqli($host, $user, $pass, $db);
    $mysqli->set_charset('utf8mb4');

    return $mysqli;
}

/**
 * Insere uma nova avaliação para um item de pedido específico.
 * Retorna o id da avaliação criada.
 * Lança mysqli_sql_exception em caso de erro (ex.: item de pedido
 * inexistente, ou avaliação duplicada — códigos 1452 e 1062).
 */
function inserirAvaliacao(
    mysqli $mysqli,
    int $idPedido,
    int $idClienteDoPedido,
    int $idProduto,
    int $nota,
    ?string $comentario = null
): int {
    if ($nota < 1 || $nota > 5) {
        throw new InvalidArgumentException('Nota deve estar entre 1 e 5.');
    }

    $sql = "INSERT INTO AVALIACOES (
                ITENS_PEDIDOS_PEDIDOS_idPEDIDOS,
                ITENS_PEDIDOS_PEDIDOS_CLIENTES_idCLIENTES,
                ITENS_PEDIDOS_PRODUTOS_idPRODUTOS,
                nota_AVALIACOES,
                comentario_AVALIACOES
            ) VALUES (?, ?, ?, ?, ?)";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param(
        'iiiis',
        $idPedido,
        $idClienteDoPedido,
        $idProduto,
        $nota,
        $comentario
    );
    $stmt->execute();

    $id = $stmt->insert_id;
    $stmt->close();

    return $id;
}

/**
 * Retorna todas as avaliações de um produto, com nome do produto
 * e id do cliente que avaliou, ordenadas das mais recentes primeiro.
 */
function listarAvaliacoesPorProduto(mysqli $mysqli, int $idProduto): array
{
    $sql = "SELECT
                a.idAVALIACOES,
                a.ITENS_PEDIDOS_PEDIDOS_CLIENTES_idCLIENTES AS idCliente,
                a.nota_AVALIACOES,
                a.comentario_AVALIACOES,
                a.data_AVALIACOES,
                p.nome_PRODUTOS
            FROM AVALIACOES a
            JOIN PRODUTOS p
                ON p.idPRODUTOS = a.ITENS_PEDIDOS_PRODUTOS_idPRODUTOS
            WHERE a.ITENS_PEDIDOS_PRODUTOS_idPRODUTOS = ?
            ORDER BY a.data_AVALIACOES DESC";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $idProduto);
    $stmt->execute();

    $resultado = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $resultado;
}

/**
 * Retorna a nota média e o total de avaliações de um produto.
 */
function mediaAvaliacoesPorProduto(mysqli $mysqli, int $idProduto): array
{
    $sql = "SELECT
                COUNT(*) AS total_avaliacoes,
                ROUND(AVG(nota_AVALIACOES), 2) AS media_nota
            FROM AVALIACOES
            WHERE ITENS_PEDIDOS_PRODUTOS_idPRODUTOS = ?";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $idProduto);
    $stmt->execute();

    $resultado = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $resultado;
}

/**
 * Retorna os itens de pedido de um cliente que ainda NÃO foram avaliados.
 * Usado para popular o <select> da página de avaliação, já que uma
 * avaliação sempre precisa se referir a um item de pedido existente.
 */
function listarItensParaAvaliar(mysqli $mysqli, int $idCliente): array
{
    $sql = "SELECT
                ip.PEDIDOS_idPEDIDOS AS idPedido,
                ip.PRODUTOS_idPRODUTOS AS idProduto,
                pr.nome_PRODUTOS,
                pe.data_PEDIDOS
            FROM ITENS_PEDIDOS ip
            JOIN PEDIDOS pe
                ON pe.idPEDIDOS = ip.PEDIDOS_idPEDIDOS
               AND pe.CLIENTES_idCLIENTES = ip.PEDIDOS_CLIENTES_idCLIENTES
            JOIN PRODUTOS pr
                ON pr.idPRODUTOS = ip.PRODUTOS_idPRODUTOS
            LEFT JOIN AVALIACOES a
                ON a.ITENS_PEDIDOS_PEDIDOS_idPEDIDOS = ip.PEDIDOS_idPEDIDOS
               AND a.ITENS_PEDIDOS_PEDIDOS_CLIENTES_idCLIENTES = ip.PEDIDOS_CLIENTES_idCLIENTES
               AND a.ITENS_PEDIDOS_PRODUTOS_idPRODUTOS = ip.PRODUTOS_idPRODUTOS
            WHERE ip.PEDIDOS_CLIENTES_idCLIENTES = ?
              AND a.idAVALIACOES IS NULL
            ORDER BY pe.data_PEDIDOS DESC";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $idCliente);
    $stmt->execute();

    $resultado = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $resultado;
}

/**
 * Retorna as avaliações mais recentes de todo o site (qualquer produto),
 * com nome do cliente e do produto — para exibir num carrossel geral.
 */
function listarAvaliacoesRecentes(mysqli $mysqli, int $limite = 10): array
{
    $sql = "SELECT
                a.idAVALIACOES,
                a.nota_AVALIACOES,
                a.comentario_AVALIACOES,
                a.data_AVALIACOES,
                c.nome_Clientes,
                pr.nome_PRODUTOS
            FROM AVALIACOES a
            JOIN CLIENTES c
                ON c.idCLIENTES = a.ITENS_PEDIDOS_PEDIDOS_CLIENTES_idCLIENTES
            JOIN PRODUTOS pr
                ON pr.idPRODUTOS = a.ITENS_PEDIDOS_PRODUTOS_idPRODUTOS
            ORDER BY a.data_AVALIACOES DESC
            LIMIT ?";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $limite);
    $stmt->execute();

    $resultado = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $resultado;
}

// ---------------------------------------------------------------
// Exemplo de uso
// ---------------------------------------------------------------
/*
try {
    $mysqli = conectar();

    $idAvaliacao = inserirAvaliacao(
        $mysqli,
        idPedido: 10,
        idClienteDoPedido: 3,
        idProduto: 7,
        nota: 5,
        comentario: 'Bolo maravilhoso, chegou no horário!'
    );

    echo "Avaliação criada com id $idAvaliacao\n";

    $avaliacoes = listarAvaliacoesPorProduto($mysqli, 7);
    $resumo = mediaAvaliacoesPorProduto($mysqli, 7);

    print_r($avaliacoes);
    print_r($resumo);

    $mysqli->close();

} catch (mysqli_sql_exception $e) {
    // Código 1452 = FK inexistente (item de pedido não encontrado)
    // Código 1062 = entrada duplicada (avaliação já existe para este item)
    echo 'Erro ao acessar o banco: ' . $e->getMessage() . ' (código ' . $e->getCode() . ')';
} catch (InvalidArgumentException $e) {
    echo 'Dados inválidos: ' . $e->getMessage();
}
*/
