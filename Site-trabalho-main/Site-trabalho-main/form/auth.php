<?php

function iniciarSessao(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function usuarioLogado(): bool
{
    iniciarSessao();
    return isset($_SESSION['usuario_id'], $_SESSION['usuario_tipo']);
}

function ehAdministrador(): bool
{
    iniciarSessao();
    return ($_SESSION['usuario_tipo'] ?? null) === 'admin';
}

function ehCliente(): bool
{
    iniciarSessao();
    return ($_SESSION['usuario_tipo'] ?? null) === 'cliente';
}

function exigirAdministrador(): void
{
    if (!ehAdministrador()) {
        header('Location: login.php?erro=restrito');
        exit;
    }
}

function exigirCliente(): void
{
    if (!ehCliente()) {
        header('Location: login.php?erro=cliente');
        exit;
    }
}
