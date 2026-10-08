<?php
require_once __DIR__ . '/../form/avaliacoes.php';
require_once __DIR__ . '/../form/auth.php';
iniciarSessao();

$mysqli = conectar();
$avaliacoesRecentes = listarAvaliacoesRecentes($mysqli, 10);
$mysqli->close();
?>
<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Avaliações - Fábrica Encantada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/styleavaliacao.css">
    <script>
      function validarFormulario(event) {
        event.preventDefault();

        let nome = document.forms["Formulario"]["nome"].value;
        let caixaComentario = document.forms["Formulario"]["caixaComentario"].value;
        let rating = document.querySelector('input[name="rating"]:checked');

        if (nome === "" || caixaComentario === "" || !rating) {
          alert("Por favor, preencha todos os campos.");
          return false;
        }

        alert("Obrigado pela sua avaliação!");
        event.target.reset();
      }
    </script>
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
            <li class="nav-item"><a class="nav-link active" href="avaliacao.php">Avaliações</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= usuarioLogado() ? 'logout.php' : 'login.php' ?>"><?= usuarioLogado() ? 'Sair' : 'Login' ?></a></li>
          </ul>
        </div>
      </div>
    </nav>
    <br/>

    <?php if (count($avaliacoesRecentes) > 0): ?>
    <div id="carouselExampleInterval" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <?php foreach ($avaliacoesRecentes as $i => $av): ?>
          <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>" data-bs-interval="7000">
            <div class="card mx-auto" style="width: 18rem;">
              <img src="../img/review.jpg" class="card-img" alt="...">
              <div class="card-body">
                <h5 class="card-title">
                  <?= htmlspecialchars($av['nome_Clientes']) ?>
                  <?= str_repeat('★', (int) $av['nota_AVALIACOES']) ?>
                </h5>
                <p class="card-text"><?= htmlspecialchars($av['comentario_AVALIACOES']) ?></p>
                <small class="text-muted">Sobre: <?= htmlspecialchars($av['nome_PRODUTOS']) ?></small>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleInterval" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleInterval" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
    <?php else: ?>
      <p class="text-center">Ainda não há avaliações.</p>
    <?php endif; ?>

    <br/><br/>

    <div class="card col-12 col-md-8 offset-md-2">
      <div class="card-body">
        <div>
          <h5 class="card-title">Deixe sua avaliação!</h5>
        </div>
        <br/>
        <form name="Formulario" onsubmit="return validarFormulario(event)">
          <label for="nome">Nome:</label><br>
          <input type="text" id="nome" name="nome" rows="3"><br>
          <br/>
          <label for="caixaComentario" class="form-label">Nos conte a sua experiência!:</label>
          <textarea class="form-control" id="caixaComentario" name="caixaComentario" rows="3"></textarea>
          <div class="rating" id="stars">
            <input type="radio" id="star5" name="rating" value="5" />
              <label for="star5">5 stars</label>
            <input type="radio" id="star4" name="rating" value="4" />
              <label for="star4">4 stars</label>
            <input type="radio" id="star3" name="rating" value="3" />
              <label for="star3">3 stars</label>
            <input type="radio" id="star2" name="rating" value="2" />
              <label for="star2">2 stars</label>
            <input type="radio" id="star1" name="rating" value="1" />
              <label for="star1">1 star</label>
          </div>
          <br/><br/><br/>
          <div>
            <button type="submit" class="btn btn-primary">Enviar</button>
          </div>
        </form>
      </div>
    </div>

    <br/><br/>

    <footer class="footer-premium p-4 mt-5">
      <h2>📞 Contato</h2>
      <p>Telefone: (11) 99999-9999</p>
      <p>Email: fabricaencantada@gmail.com</p>
      <p>Localização: Chafic Faccury 4109</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>