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

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=IBM+Plex+Sans:wght@300;400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet" />

  <!-- CSS propios -->
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="../css/panel.css" />
  <link rel="stylesheet" href="../css/dashboard.css" />
  <link rel="stylesheet" href="../css/admin.css" />

  <style>
    :root {
      --udenar-green: #2d5a27;
      --udenar-green-dark: #1e3d1a;
    }

    body {
      font-family: 'IBM Plex Sans', sans-serif;
      background-color: #f4f6f3;
    }

    /* HEADER */
    .panel-header {
      background: var(--udenar-green);
      color: #fff;
      padding: .6rem 1rem;
    }

    .brand-seal img {
      width: 38px;
      height: 38px;
      object-fit: contain;
    }

    .panel-brand-text {
      font-family: 'Libre Baskerville', serif;
      font-size: .95rem;
      color: #fff;
    }

    .role-badge {
      font-family: 'IBM Plex Mono', monospace;
      font-size: .65rem;
      font-weight: 600;
      letter-spacing: .08em;
      text-transform: uppercase;
      padding: .2rem .55rem;
      border-radius: 4px;
    }

    .role-badge.admin {
      background: rgba(255, 220, 100, .25);
      color: #ffe680;
      border: 1px solid rgba(255, 220, 100, .4);
    }

    .user-name {
      font-size: .85rem;
      color: rgba(255, 255, 255, .9);
    }

    .btn-logout {
      font-size: .78rem;
      color: rgba(255, 255, 255, .8);
      text-decoration: none;
      border: 1px solid rgba(255, 255, 255, .35);
      padding: .2rem .65rem;
      border-radius: 4px;
      transition: background .2s;
    }

    .btn-logout:hover {
      background: rgba(255, 255, 255, .15);
      color: #fff;
    }

    /* SIDEBAR NAV */
    .nav-item-dash {
      display: flex;
      align-items: center;
      gap: .55rem;
      width: 100%;
      background: none;
      border: none;
      text-align: left;
      font-family: 'IBM Plex Sans', sans-serif;
      font-size: .88rem;
      font-weight: 500;
      color: #3a3a3a;
      padding: .7rem 1rem;
      border-radius: 8px;
      transition: background .18s, color .18s;
      cursor: pointer;
      position: relative;
    }

    .nav-item-dash:hover,
    .nav-item-dash.active {
      background: var(--udenar-green);
      color: #fff;
    }

    /* BADGE contador */
    .nav-badge {
      margin-left: auto;
      background: #e74c3c;
      color: #fff;
      font-size: .65rem;
      font-weight: 700;
      border-radius: 10px;
      padding: .1rem .45rem;
      min-width: 1.2rem;
      text-align: center;
      display: none;
    }

    .nav-badge.visible {
      display: inline-block;
    }

    /* SECTION HEADER */
    .section-header h2 {
      font-family: 'Libre Baskerville', serif;
      font-size: 1.5rem;
      font-weight: 700;
      color: #1a2e18;
      margin-bottom: .2rem;
    }

    .section-header p {
      font-size: .88rem;
      color: #666;
    }

    /* TAB PANEL */
    .tab-panel {
      display: none;
    }

    .tab-panel.active {
      display: block;
    }

    /* MODAL */
    .modal-box {
      background: #fff;
      border-radius: 14px;
      padding: 2rem;
      box-shadow: 0 8px 40px rgba(0,0,0,.18);
      position: relative;
    }

    /* Sidebar desktop */
    @media (min-width: 768px) {
      .sidebar-col {
        width: 210px;
        flex-shrink: 0;
      }
    }
  </style>
</head>

<body>

  <!-- ═══════════════ HEADER ═══════════════ -->
  <header class="panel-header">
    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">

      <!-- Izquierda -->
      <div class="d-flex align-items-center gap-2">
        <!-- Toggle sidebar móvil -->
        <button class="btn btn-sm d-md-none me-1 p-1"
                style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);color:#fff;"
                type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-label="Menú">
          <i class="bi bi-list fs-5"></i>
        </button>
        <div class="brand-seal">
          <img src="../img/logo.png" alt="Logo Universidad de Nariño" />
        </div>
        <span class="panel-brand-text">Administración · Reportes</span>
      </div>

      <!-- Derecha -->
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <span class="role-badge admin">Admin</span>
        <span class="user-name d-none d-sm-inline">
          <?= htmlspecialchars($user["nombres"] . " " . $user["apellidos"]) ?>
        </span>
        <a href="../php/logout.php" class="btn-logout">Cerrar sesión</a>
      </div>

    </div>
  </header>

  <!-- ═══════════════ OFFCANVAS SIDEBAR (móvil) ═══════════════ -->
  <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarOffcanvas" style="width:220px;">
    <div class="offcanvas-header" style="background:var(--udenar-green);color:#fff;">
      <h6 class="offcanvas-title mb-0" style="font-family:'Libre Baskerville',serif;">Panel Admin</h6>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-2">
      <div class="d-flex flex-column gap-1">
        <button class="nav-item-dash active" data-tab="pendientes">
          <i class="bi bi-bell"></i> Pendientes
          <span class="nav-badge" id="badgePendientesOC"></span>
        </button>
        <button class="nav-item-dash" data-tab="en_proceso">
          <i class="bi bi-wrench"></i> En Proceso
          <span class="nav-badge" id="badgeProcesoOC"></span>
        </button>
        <button class="nav-item-dash" data-tab="completados">
          <i class="bi bi-check2-circle"></i> Completados
        </button>
      </div>
    </div>
  </div>

  <!-- ═══════════════ LAYOUT PRINCIPAL ═══════════════ -->
  <div class="d-flex" style="min-height:calc(100vh - 56px);">

    <!-- SIDEBAR desktop -->
    <aside class="d-none d-md-flex flex-column gap-1 p-3 sidebar-col"
           style="background:#fff;border-right:1px solid #dde5d9;">
      <button class="nav-item-dash active" data-tab="pendientes">
        <i class="bi bi-bell"></i> Pendientes
        <span class="nav-badge" id="badgePendientes"></span>
      </button>
      <button class="nav-item-dash" data-tab="en_proceso">
        <i class="bi bi-wrench"></i> En Proceso
        <span class="nav-badge" id="badgeProceso"></span>
      </button>
      <button class="nav-item-dash" data-tab="completados">
        <i class="bi bi-check2-circle"></i> Completados
      </button>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="flex-grow-1 p-3 p-md-4" style="overflow-y:auto;">

      <!-- ── PENDIENTES ── -->
      <section id="tab-pendientes" class="tab-panel active">
        <div class="section-header mb-4">
          <h2>Reportes Pendientes</h2>
          <p>Nuevos hallazgos sin revisar</p>
        </div>
        <div id="listaPendientes">
          <div class="text-center text-muted py-5">
            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
            Cargando…
          </div>
        </div>
      </section>

      <!-- ── EN PROCESO ── -->
      <section id="tab-en_proceso" class="tab-panel">
        <div class="section-header mb-4">
          <h2>En Proceso</h2>
          <p>Reportes siendo atendidos</p>
        </div>
        <div id="listaProceso">
          <div class="text-center text-muted py-5">
            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
            Cargando…
          </div>
        </div>
      </section>

      <!-- ── COMPLETADOS ── -->
      <section id="tab-completados" class="tab-panel">
        <div class="section-header mb-4">
          <h2>Historial de Completados</h2>
          <p>Reportes resueltos</p>
        </div>
        <div id="listaCompletados">
          <div class="text-center text-muted py-5">
            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
            Cargando…
          </div>
        </div>
      </section>

    </main>
  </div>

  <!-- ═══════════════ MODAL ═══════════════ -->
  <div id="modalOverlay" class="modal-overlay" style="display:none;
       position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:1050;
       display:none;align-items:center;justify-content:center;padding:1rem;">
    <div class="modal-box" style="width:100%;max-width:560px;max-height:90vh;overflow-y:auto;">
      <button class="btn btn-sm btn-outline-secondary position-absolute top-0 end-0 m-2"
              id="modalClose">✕</button>
      <div id="modalContent"></div>
    </div>
  </div>

  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // ── Tab switching ─────────────────────────────────────────
    function switchTab(tabName) {
      document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
      document.querySelectorAll('.nav-item-dash').forEach(b => b.classList.remove('active'));

      const panel = document.getElementById('tab-' + tabName);
      if (panel) panel.classList.add('active');

      document.querySelectorAll('[data-tab="' + tabName + '"]').forEach(b => b.classList.add('active'));

      const oc = bootstrap.Offcanvas.getInstance(document.getElementById('sidebarOffcanvas'));
      if (oc) oc.hide();
    }

    document.querySelectorAll('.nav-item-dash').forEach(btn => {
      btn.addEventListener('click', () => switchTab(btn.dataset.tab));
    });

    // ── Modal helpers ─────────────────────────────────────────
    const overlay = document.getElementById('modalOverlay');
    document.getElementById('modalClose').addEventListener('click', () => {
      overlay.style.display = 'none';
    });
    overlay.addEventListener('click', e => {
      if (e.target === overlay) overlay.style.display = 'none';
    });
  </script>

  <script src="../js/admin.js"></script>
</body>

</html>
