<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>UDENAR · Acceso al Sistema</title>

  <link
    href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=IBM+Plex+Sans:wght@300;400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap"
    rel="stylesheet" />

  <link rel="stylesheet" href="css/style.css">
</head>

<body>

  <div id="loginView" class="view active">

    <div class="auth-shell">

      <div class="auth-brand">

        <div class="brand-seal">
          <img src="img/logo.png" alt="Logo Universidad de Nariño" />
        </div>

        <div class="brand-text">
          <span class="brand-name">Universidad de Nariño</span>
          <span class="brand-sub">Sistema de Reportes de Infraestructura</span>
        </div>

      </div>

      <div class="auth-card">

        <h1 class="auth-title">Iniciar sesión</h1>
        <p class="auth-hint">Ingresa con tu código estudiantil</p>

        <div id="loginMsg" class="msg"></div>

        <div class="field">
          <label for="codigo">Código estudiantil</label>
          <input type="text" id="codigo" placeholder="202412345" autocomplete="username" />
        </div>

        <div class="field">
          <label for="password">Contraseña (cédula)</label>
          <input type="password" id="password" placeholder="••••••••" autocomplete="current-password" />
        </div>

        <button class="btn btn-primary" id="btnLogin">
          Ingresar al sistema
        </button>

      </div>

      <footer class="auth-footer">
        Facultad de Ingeniería · Ingeniería de Sistemas · 2026
      </footer>

    </div>

  </div>

  <!-- Script principal de autenticación -->
  <script src="js/index.js"></script>

</body>

</html>