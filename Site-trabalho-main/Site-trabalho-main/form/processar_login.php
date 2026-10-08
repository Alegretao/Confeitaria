<?php
require_once __DIR__ . '/avaliacoes.php';
require_once __DIR__ . '/auth.php';

iniciarSessao();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/login.php');
    exit;
}

$tipo = $_POST['tipo'] ?? '';
$identificador = trim($_POST['identificador'] ?? '');
$senha = $_POST['senha'] ?? '';

if ($identificador === '' || $senha === '' || !in_array($tipo, ['cliente', 'admin'], true)) {
    header('Location: ../pages/login.php?erro=campos');
    exit;
}

try {
    $mysqli = conectar();

    if ($tipo === 'admin') {
        $stmt = $mysqli->prepare(
            'SELECT idADMINISTRADOR, nome_ADMINISTRADOR, email_ADMINISTRADOR, senha_ADMINISTRADOR
             FROM ADMINISTRADOR
             WHERE email_ADMINISTRADOR = ?
             LIMIT 1'
        );
    } else {
        $stmt = $mysqli->prepare(
            'SELECT idCLIENTES, nome_Clientes, telefone_Clientes, senha_Clientes
             FROM CLIENTES
             WHERE telefone_Clientes = ?
             LIMIT 1'
        );
    }

    $stmt->bind_param('s', $identificador);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$usuario) {
        throw new RuntimeException('credenciais');
    }

    $hashArmazenado = $tipo === 'admin'
        ? (string) $usuario['senha_ADMINISTRADOR']
        : (string) $usuario['senha_Clientes'];

    $senhaCorreta = password_verify($senha, $hashArmazenado);

    // Compatibilidade com dados antigos em texto puro. Ao entrar, atualiza para hash.
    if (!$senhaCorreta && hash_equals($hashArmazenado, $senha)) {
        $senhaCorreta = true;
        $novoHash = password_hash($senha, PASSWORD_DEFAULT);

        if ($tipo === 'admin') {
            $id = (int) $usuario['idADMINISTRADOR'];
            $up = $mysqli->prepare('UPDATE ADMINISTRADOR SET senha_ADMINISTRADOR = ? WHERE idADMINISTRADOR = ?');
        } else {
            $id = (int) $usuario['idCLIENTES'];
            $up = $mysqli->prepare('UPDATE CLIENTES SET senha_Clientes = ? WHERE idCLIENTES = ?');
        }
        $up->bind_param('si', $novoHash, $id);
        $up->execute();
        $up->close();
    }

    if (!$senhaCorreta) {
        throw new RuntimeException('credenciais');
    }

    session_regenerate_id(true);

    if ($tipo === 'admin') {
        $_SESSION['usuario_id'] = (int) $usuario['idADMINISTRADOR'];
        $_SESSION['usuario_nome'] = $usuario['nome_ADMINISTRADOR'] ?: 'Administrador';
        $_SESSION['usuario_tipo'] = 'admin';
        $_SESSION['usuario_email'] = $usuario['email_ADMINISTRADOR'];
        $destino = '../pages/painel_admin.php';
    } else {
        $_SESSION['usuario_id'] = (int) $usuario['idCLIENTES'];
        $_SESSION['usuario_nome'] = $usuario['nome_Clientes'];
        $_SESSION['usuario_tipo'] = 'cliente';
        $_SESSION['usuario_telefone'] = $usuario['telefone_Clientes'];
        $destino = '../pages/avaliacao.php';
    }

    $mysqli->close();
    header('Location: ' . $destino);
    exit;

} catch (Throwable $e) {
    if (isset($mysqli) && $mysqli instanceof mysqli) {
        $mysqli->close();
    }
    header('Location: ../pages/login.php?erro=credenciais');
    exit;
}
