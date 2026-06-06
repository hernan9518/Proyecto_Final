<?php
session_start();
if (empty($_SESSION["user"]) || $_SESSION["user"]["rol"] !== "admin") {
  header("Location: ../index.php");
  exit;
}
$user = $_SESSION["user"];
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>UDENAR · Panel Admin</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=IBM+Plex+Sans:wght@300;400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="../css/panel.css" />
  <link rel="stylesheet" href="../css/dashboard.css" />
  <link rel="stylesheet" href="../css/admin.css" />
</head>

<body class="panel-body">

  <header class="panel-header">
    <div class="panel-brand">
      <div class="brand-seal">
        <img src="../img/logo.png" alt="Logo Universidad de Nariño" />
      </div>
      <span>Administración · Reportes</span>
    </div>
    <div class="panel-user">
      <span class="role-badge admin">Admin</span>
      <span class="user-name"><?= htmlspecialchars($user["nombres"] . " " . $user["apellidos"]) ?></span>
      <a href="../php/logout.php" class="btn-logout">Cerrar sesión</a>
    </div>
  </header>

  <div class="dash-layout">
    <nav class="dash-sidebar">
      <button class="nav-item active" data-tab="pendientes">
        <span class="nav-icon">🔔</span><span>Pendientes</span>
        <span class="badge" id="badgePendientes"></span>
      </button>
      <button class="nav-item" data-tab="en_proceso">
        <span class="nav-icon">🔧</span><span>En Proceso</span>
        <span class="badge" id="badgeProceso"></span>
      </button>
      <button class="nav-item" data-tab="completados">
        <span class="nav-icon">✅</span><span>Completados</span>
      </button>
    </nav>

    <main class="dash-content">
      <section id="tab-pendientes" class="tab-panel active">
        <div class="section-header">
          <h2>Reportes Pendientes</h2>
          <p>Nuevos hallazgos sin revisar</p>
        </div>
        <div id="listaPendientes">
          <div class="loading-state">Cargando…</div>
        </div>
      </section>

      <section id="tab-en_proceso" class="tab-panel">
        <div class="section-header">
          <h2>En Proceso</h2>
          <p>Reportes siendo atendidos</p>
        </div>
        <div id="listaProceso">
          <div class="loading-state">Cargando…</div>
        </div>
      </section>

      <section id="tab-completados" class="tab-panel">
        <div class="section-header">
          <h2>Historial de Completados</h2>
          <p>Reportes resueltos</p>
        </div>
        <div id="listaCompletados">
          <div class="loading-state">Cargando…</div>
        </div>
      </section>
    </main>
  </div>

  <!-- MODAL -->
  <div id="modalOverlay" class="modal-overlay" style="display:none">
    <div class="modal-box">
      <button class="modal-close" id="modalClose">✕</button>
      <div id="modalContent"></div>
    </div>
  </div>

  <script src="../js/admin.js"></script>
</body>

</html>