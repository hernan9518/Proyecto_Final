<?php
// dashboard/index.php — Dashboard del estudiante

session_start();
if (empty($_SESSION["user"])) {
  header("Location: ../index.php");
  exit;
}
if ($_SESSION["user"]["rol"] === "admin") {
  header("Location: ../admin/panel.php");
  exit;
}
$user = $_SESSION["user"];
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>UDENAR · Mi Dashboard</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=IBM+Plex+Sans:wght@300;400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet" />

  <!-- CSS propios (mantener los existentes para variables y estilos base) -->
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="../css/panel.css" />
  <link rel="stylesheet" href="../css/dashboard.css" />

  <style>
    /* ── Overrides Bootstrap para mantener identidad visual UDENAR ─── */
    :root {
      --udenar-green: #2d5a27;
      --udenar-green-dark: #1e3d1a;
      --bs-primary: var(--udenar-green);
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

    .role-badge.usuario {
      background: rgba(255, 255, 255, .18);
      color: #fff;
      border: 1px solid rgba(255, 255, 255, .35);
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

    /* SIDEBAR (offcanvas en móvil, columna en desktop) */
    .nav-item {
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
    }

    .nav-item:hover,
    .nav-item.active {
      background: var(--udenar-green);
      color: #fff;
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

    /* FORM */
    .form-label {
      font-size: .82rem;
      font-weight: 600;
      color: #333;
      text-transform: uppercase;
      letter-spacing: .04em;
      margin-bottom: .3rem;
    }

    .form-control,
    .form-select {
      font-family: 'IBM Plex Sans', sans-serif;
      font-size: .88rem;
      border-radius: 8px;
      border: 1.5px solid #d0d8cc;
      transition: border-color .2s, box-shadow .2s;
    }

    .form-control:focus,
    .form-select:focus {
      border-color: var(--udenar-green);
      box-shadow: 0 0 0 3px rgba(45, 90, 39, .15);
    }

    /* FILE DROP */
    .file-drop {
      border: 2px dashed #b5c4b0;
      border-radius: 12px;
      background: #f8faf7;
      cursor: pointer;
      transition: border-color .2s, background .2s;
    }

    .file-drop:hover {
      border-color: var(--udenar-green);
      background: #eef4ec;
    }

    .file-drop-inner {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 1.8rem 1rem;
      gap: .4rem;
    }

    .file-icon {
      font-size: 2rem;
    }

    .file-label {
      font-size: .85rem;
      color: #555;
    }

    .file-hint {
      font-size: .75rem;
      color: #999;
    }

    .file-preview img {
      width: 100%;
      max-height: 200px;
      object-fit: cover;
      border-radius: 10px;
    }

    .file-remove {
      display: block;
      width: 100%;
      margin-top: .5rem;
      font-size: .8rem;
      color: #c0392b;
      background: none;
      border: 1px solid #c0392b;
      border-radius: 6px;
      padding: .25rem .5rem;
      cursor: pointer;
    }

    /* BTN ENVIAR */
    .btn-udenar {
      background: var(--udenar-green);
      color: #fff;
      font-family: 'IBM Plex Sans', sans-serif;
      font-weight: 600;
      font-size: .9rem;
      letter-spacing: .04em;
      border: none;
      border-radius: 9px;
      padding: .75rem 1.5rem;
      transition: background .2s, transform .1s;
    }

    .btn-udenar:hover {
      background: var(--udenar-green-dark);
      color: #fff;
      transform: translateY(-1px);
    }

    /* IMAGEN LATERAL */
    .reporte-aside {
      border-radius: 14px;
      overflow: hidden;
      border: 1px solid #dde5d9;
      box-shadow: 0 4px 20px rgba(0, 0, 0, .09);
    }

    .reporte-aside img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      display: block;
    }

    .aside-caption {
      background: var(--udenar-green);
      color: #fff;
      font-size: .75rem;
      font-weight: 600;
      letter-spacing: .05em;
      text-align: center;
      padding: .55rem 1rem;
    }

    .aside-info {
      padding: 1rem 1.1rem;
      background: #fff;
      display: flex;
      flex-direction: column;
      gap: .7rem;
    }

    .aside-info-item {
      display: flex;
      gap: .55rem;
      font-size: .8rem;
      color: #666;
      line-height: 1.4;
    }

    .aside-info-item strong {
      display: block;
      font-size: .75rem;
      font-weight: 600;
      color: #222;
      text-transform: uppercase;
      letter-spacing: .05em;
      margin-bottom: .1rem;
    }

    /* MSG */
    #formMsg:not(:empty) {
      margin-bottom: 1rem;
    }

    /* Sidebar desktop */
    @media (min-width: 768px) {
      .sidebar-col {
        width: 200px;
        flex-shrink: 0;
      }
    }
  </style>
</head>

<body>

  <!-- ═══════════════ HEADER ═══════════════ -->
  <header class="panel-header" style="display:flex!important;align-items:center!important;justify-content:space-between!important;flex-wrap:wrap;gap:.5rem;padding:.6rem 1rem;">
    <!-- Izquierda -->
    <div class="d-flex align-items-center gap-2">
      <button class="btn btn-sm d-md-none p-1"
              style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);color:#fff;"
              type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-label="Menú">
        <i class="bi bi-list fs-5"></i>
      </button>
      <div class="brand-seal">
        <img src="../img/logo.png" alt="Logo Universidad de Nariño" />
      </div>
      <span class="panel-brand-text">Sistema de Reportes</span>
    </div>
    <!-- Derecha -->
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <span class="role-badge usuario">Estudiante</span>
      <span class="user-name d-none d-sm-inline">
        <?= htmlspecialchars($user["nombres"] . " " . $user["apellidos"]) ?>
      </span>
      <a href="../php/logout.php" class="btn-logout">Cerrar sesión</a>
    </div>
  </header>

  <!-- ═══════════════ OFFCANVAS SIDEBAR (móvil) ═══════════════ -->
  <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarOffcanvas" style="width:220px;">
    <div class="offcanvas-header" style="background:var(--udenar-green);color:#fff;">
      <h6 class="offcanvas-title mb-0" style="font-family:'Libre Baskerville',serif;">Menú</h6>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-2">
      <div class="d-flex flex-column gap-1">
        <button class="nav-item active" data-tab="nuevo">
          <i class="bi bi-pencil-square"></i> Nuevo Reporte
        </button>
        <button class="nav-item" data-tab="historial">
          <i class="bi bi-clipboard-data"></i> Mis Reportes
        </button>
      </div>
    </div>
  </div>

  <!-- ═══════════════ LAYOUT PRINCIPAL ═══════════════ -->
  <div class="d-flex" style="min-height:calc(100vh - 56px);">

    <!-- SIDEBAR desktop (oculto en móvil) -->
    <aside class="d-none d-md-flex flex-column gap-1 p-3 sidebar-col"
           style="background:#fff;border-right:1px solid #dde5d9;">
      <button class="nav-item active" data-tab="nuevo">
        <i class="bi bi-pencil-square"></i> Nuevo Reporte
      </button>
      <button class="nav-item" data-tab="historial">
        <i class="bi bi-clipboard-data"></i> Mis Reportes
      </button>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="flex-grow-1 p-3 p-md-4" style="overflow-y:auto;">

      <!-- ── TAB: NUEVO REPORTE ── -->
      <section id="tab-nuevo" class="tab-panel active">
        <div class="section-header mb-4">
          <h2>Registrar Hallazgo</h2>
          <p>Describe la falla encontrada en el campus</p>
        </div>

        <div id="formMsg" class="msg"></div>

        <!-- Grid: formulario + aside (apilados en móvil, lado a lado en lg+) -->
        <div class="row g-4 align-items-start">

          <!-- FORMULARIO -->
          <div class="col-12 col-lg-7 col-xl-8">
            <form id="reporteForm">

              <div class="row g-3 mb-3">
                <div class="col-12 col-sm-6">
                  <label class="form-label">Tipo de falla</label>
                  <select name="tipo_falla" class="form-select" required>
                    <option value="">— Selecciona —</option>
                    <option value="estructural">🏗️ Estructural</option>
                    <option value="electrica">⚡ Eléctrica</option>
                    <option value="iluminacion">💡 Iluminación</option>
                    <option value="red">🌐 Red / Conectividad</option>
                    <option value="sistema">💻 Sistema / PC</option>
                    <option value="mobiliario">🪑 Mobiliario</option>
                    <option value="otro">📌 Otro</option>
                  </select>
                </div>
                <div class="col-12 col-sm-6">
                  <label class="form-label">Bloque</label>
                  <select name="bloque" class="form-select" required>
                    <option value="">— Selecciona —</option>
                    <option value="A">Bloque A</option>
                    <option value="B">Bloque B</option>
                    <option value="C">Bloque C</option>
                  </select>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Ubicación específica</label>
                <input type="text" name="ubicacion_detalle" class="form-control"
                       placeholder="Ej: Salón 203, Laboratorio de Sistemas 1"
                       required maxlength="150" />
              </div>

              <div class="mb-3">
                <label class="form-label">Descripción del hallazgo</label>
                <textarea name="descripcion" class="form-control" rows="5"
                          placeholder="Describe con detalle la falla: qué observaste, desde cuándo, posible causa…"
                          required maxlength="1000"></textarea>
              </div>

              <div class="mb-4">
                <label class="form-label">
                  Foto de evidencia <span class="text-muted fw-normal" style="text-transform:none;letter-spacing:0;">(opcional)</span>
                </label>
                <div class="file-drop" id="fileDrop">
                  <input type="file" name="foto" id="fotoInput" accept="image/*" style="display:none" />
                  <div class="file-drop-inner" id="fileDropInner">
                    <span class="file-icon">📷</span>
                    <span class="file-label">Arrastra una imagen o <u>haz clic aquí</u></span>
                    <span class="file-hint">JPG, PNG o WEBP · Máx 5 MB</span>
                  </div>
                  <div class="file-preview p-2" id="filePreview" style="display:none">
                    <img id="previewImg" src="" alt="preview" />
                    <button type="button" class="file-remove" id="fileRemove">✕ Quitar</button>
                  </div>
                </div>
              </div>

              <button type="submit" class="btn btn-udenar w-100" id="btnEnviar">
                <i class="bi bi-send me-2"></i>Enviar Reporte
              </button>

            </form>
          </div>

          <!-- IMAGEN LATERAL -->
          <div class="col-12 col-lg-5 col-xl-4">
            <aside class="reporte-aside">
              <img src="../img/udenar.jpg" alt="Campus Universidad de Nariño" />
              <div class="aside-caption">📍 Campus Torobajo · Universidad de Nariño</div>
              <div class="aside-info">
                <div class="aside-info-item">
                  <span style="font-size:1rem;flex-shrink:0;">📌</span>
                  <div>
                    <strong>¿Dónde reportar?</strong>
                    Indica el bloque y la ubicación exacta para que el equipo técnico llegue rápido.
                  </div>
                </div>
                <div class="aside-info-item">
                  <span style="font-size:1rem;flex-shrink:0;">📷</span>
                  <div>
                    <strong>Adjunta evidencia</strong>
                    Una foto ayuda a evaluar la gravedad y agiliza la atención.
                  </div>
                </div>
                <div class="aside-info-item">
                  <span style="font-size:1rem;flex-shrink:0;">⏱️</span>
                  <div>
                    <strong>Seguimiento</strong>
                    Consulta el estado en la sección <em>Mis Reportes</em>.
                  </div>
                </div>
              </div>
            </aside>
          </div>

        </div><!-- /.row -->
      </section>

      <!-- ── TAB: HISTORIAL ── -->
      <section id="tab-historial" class="tab-panel">
        <div class="section-header mb-4">
          <h2>Mis Reportes</h2>
          <p>Seguimiento de todos tus hallazgos enviados</p>
        </div>
        <div id="historialContent">
          <div class="text-center text-muted py-5">
            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
            Cargando reportes…
          </div>
        </div>
      </section>

    </main>
  </div>

  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Cierra el offcanvas al cambiar de tab (complementa dashboard.js)
    document.querySelectorAll('.nav-item').forEach(btn => {
      btn.addEventListener('click', () => {
        const oc = bootstrap.Offcanvas.getInstance(document.getElementById('sidebarOffcanvas'));
        if (oc) oc.hide();
      });
    });
  </script>

  <script src="../js/dashboard.js"></script>
</body>

</html>
