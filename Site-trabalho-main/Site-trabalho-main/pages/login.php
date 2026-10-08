<?php
require_once __DIR__ . '/../form/auth.php';
iniciarSessao();

if (ehAdministrador()) {
    header('Location: painel_admin.php');
    exit;
}

$erro = $_GET['erro'] ?? '';
$mensagens = [
    'credenciais' => 'Dados de acesso incorretos.',
    'campos' => 'Preencha todos os campos para entrar.',
    'restrito' => 'Faça login como administrador para acessar o painel.',
    'cliente' => 'Faça login como cliente para continuar.'
];
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Fábrica Encantada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/stylehome.css">
    <link rel="stylesheet" href="../css/stylelogin.css">
</head>
<body>
<nav class="navbar navbar-expand-lg nav-premium">
    <div class="container">
        <a class="navbar-brand nome-empresa" href="home.html">Fábrica Encantada</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="home.html">A Fábrica</a></li>
                <li class="nav-item"><a class="nav-link" href="bolos.html">Bolos</a></li>
                <li class="nav-item"><a class="nav-link" href="sobre.html">Sobre</a></li>
                <li class="nav-item"><a class="nav-link" href="avaliacao.php">Avaliações</a></li>
                <li class="nav-item"><a class="nav-link active" href="login.php"><i class="bi bi-person-circle me-1"></i>Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<main class="login-area container py-5">
    <div class="row justify-content-center align-items-center g-4">
        <div class="col-lg-5 d-none d-lg-block">
            <div class="login-apresentacao">
                <span class="login-mini">Confeitaria Artesanal</span>
                <h1>Um cantinho feito com carinho.</h1>
                <p>Entre para acessar sua conta, avaliar seus pedidos ou administrar a Fábrica Encantada.</p>
                <div class="login-detalhe"><i class="bi bi-cake2"></i></div>
            </div>
        </div>

        <div class="col-12 col-md-8 col-lg-5">
            <div class="login-card">
                <div class="text-center mb-4">
                    <div class="login-icone"><i class="bi bi-person-heart"></i></div>
                    <h2>Bem-vindo</h2>
                    <p class="text-muted mb-0">Acesse sua conta</p>
                </div>

                <?php if (isset($mensagens[$erro])): ?>
                    <div class="alert alert-danger py-2"><?= htmlspecialchars($mensagens[$erro]) ?></div>
                <?php endif; ?>

                <?php if (isset($_GET['logout'])): ?>
                    <div class="alert alert-success py-2">Você saiu da conta com sucesso.</div>
                <?php endif; ?>

                <form action="../form/processar_login.php" method="post">
                    <label class="form-label">Tipo de acesso</label>
                    <div class="tipo-acesso mb-3">
                        <input class="btn-check" type="radio" name="tipo" id="tipoCliente" value="cliente" checked>
                        <label class="btn" for="tipoCliente"><i class="bi bi-person"></i> Cliente</label>

                        <input class="btn-check" type="radio" name="tipo" id="tipoAdmin" value="admin">
                        <label class="btn" for="tipoAdmin"><i class="bi bi-shield-lock"></i> Administrador</label>
                    </div>

                    <div class="mb-3">
                        <label for="identificador" class="form-label" id="labelIdentificador">Telefone</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="text" class="form-control" id="identificador" name="identificador" placeholder="11988887777" autocomplete="username" required>
                        </div>
                        <div class="form-text" id="ajudaIdentificador">Use o telefone cadastrado no banco.</div>
                    </div>

                    <div class="mb-4">
                        <label for="senha" class="form-label">Senha</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="senha" name="senha" placeholder="Sua senha" autocomplete="current-password" required>
                            <button class="btn btn-outline-secondary" type="button" id="mostrarSenha" aria-label="Mostrar senha"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>

                    <button class="btn btn-login w-100" type="submit">Entrar</button>
                </form>

                <a class="voltar-home" href="home.html"><i class="bi bi-arrow-left"></i> Voltar para a página inicial</a>
            </div>
        </div>
    </div>
</main>

<footer class="footer-premium p-4 mt-5">
    <h2>📞 Contato</h2>
    <p>Telefone: (11) 99999-9999</p>
    <p>Email: fabricaencantada@gmail.com</p>
    <p>Localização: Chafic Faccury 4109</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script>
const cliente = document.getElementById('tipoCliente');
const admin = document.getElementById('tipoAdmin');
const label = document.getElementById('labelIdentificador');
const input = document.getElementById('identificador');
const ajuda = document.getElementById('ajudaIdentificador');
const icone = input.parentElement.querySelector('.bi');

function atualizarTipo() {
    if (admin.checked) {
        label.textContent = 'E-mail';
        input.type = 'email';
        input.placeholder = 'admin@fabricaencantada.com';
        ajuda.textContent = 'Use o e-mail cadastrado como administrador.';
        icone.className = 'bi bi-envelope';
    } else {
        label.textContent = 'Telefone';
        input.type = 'text';
        input.placeholder = '11988887777';
        ajuda.textContent = 'Use o telefone cadastrado no banco.';
        icone.className = 'bi bi-telephone';
    }
}
cliente.addEventListener('change', atualizarTipo);
admin.addEventListener('change', atualizarTipo);

document.getElementById('mostrarSenha').addEventListener('click', function () {
    const senha = document.getElementById('senha');
    const icon = this.querySelector('i');
    const mostrar = senha.type === 'password';
    senha.type = mostrar ? 'text' : 'password';
    icon.className = mostrar ? 'bi bi-eye-slash' : 'bi bi-eye';
});
</script>
</body>
</html>
