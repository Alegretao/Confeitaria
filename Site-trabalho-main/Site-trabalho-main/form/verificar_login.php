<?php

session_start();

header('Content-Type : application/json; charset=utf-8');

echo json_encode([
      'logado'=> isset($_SESSION['usuario_id'], $_SESSION['usuario.php']),
      'nome' => $_SESSION['usuario_nome'] ?? '',
      'tipo' => $_SESSION['usuario_tipo'] ?? ''
]);
?>
