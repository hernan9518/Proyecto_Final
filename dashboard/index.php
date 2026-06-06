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
  <link
    href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=IBM+Plex+Sans:wght@300;400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="../css/panel.css" />
  <link rel="stylesheet" href="../css/dashboard.css" />

  <style>
    /* ── IMAGEN LATERAL DEL FORMULARIO ───────────────── */
    .reporte-form-wrap {
      display: grid;
      grid-template-columns: 1fr 320px;
      gap: 2rem;
      align-items: start;
    }

    .reporte-form {
      max-width: 100%;
      display: flex;
      flex-direction: column;
      gap: 1.25rem;
    }

    .reporte-form-img {
      position: sticky;
      top: 2rem;
      border-radius: 14px;
      overflow: hidden;
      box-shadow: 0 6px 28px rgba(0, 0, 0, 0.13);
      border: 1px solid var(--border);
    }

    .reporte-form-img img {
      width: 100%;
      height: 220px;
      object-fit: cover;
      display: block;
    }

    .reporte-form-img .img-caption {
      background: var(--udenar-green);
      color: #fff;
      font-family: 'IBM Plex Sans', sans-serif;
      font-size: .78rem;
      font-weight: 500;
      padding: .65rem 1rem;
      text-align: center;
      letter-spacing: .04em;
    }

    .reporte-form-img .img-info {
      padding: 1rem 1.1rem;
      background: #fff;
      display: flex;
      flex-direction: column;
      gap: .55rem;
    }

    .img-info-item {
      display: flex;
      align-items: flex-start;
      gap: .6rem;
      font-family: 'IBM Plex Sans', sans-serif;
      font-size: .8rem;
      color: var(--text-muted);
      line-height: 1.4;
    }

    .img-info-item .info-icon {
      font-size: 1rem;
      flex-shrink: 0;
      margin-top: .05rem;
    }

    .img-info-item strong {
      display: block;
      color: var(--text);
      font-size: .78rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .05em;
      margin-bottom: .1rem;
    }

    /* Responsive */
    @media (max-width: 860px) {
      .reporte-form-wrap {
        grid-template-columns: 1fr;
      }

      .reporte-form-img {
        position: static;
        order: -1;
      }

      .reporte-form-img img {
        height: 180px;
      }
    }
  </style>
</head>

<body class="panel-body">

  <header class="panel-header">
    <div class="panel-brand">
      <div class="brand-seal">
        <img src="../img/logo.png" alt="Logo Universidad de Nariño" />
      </div>
      <span>Sistema de Reportes</span>
    </div>
    <div class="panel-user">
      <span class="role-badge usuario">Estudiante</span>
      <span class="user-name"><?= htmlspecialchars($user["nombres"] . " " . $user["apellidos"]) ?></span>
      <a href="../php/logout.php" class="btn-logout">Cerrar sesión</a>
    </div>
  </header>

  <div class="dash-layout">

    <nav class="dash-sidebar">
      <button class="nav-item active" data-tab="nuevo">
        <span class="nav-icon">📝</span><span>Nuevo Reporte</span>
      </button>
      <button class="nav-item" data-tab="historial">
        <span class="nav-icon">📋</span><span>Mis Reportes</span>
      </button>
    </nav>

    <main class="dash-content">

      <!-- TAB: NUEVO REPORTE -->
      <section id="tab-nuevo" class="tab-panel active">
        <div class="section-header">
          <h2>Registrar Hallazgo</h2>
          <p>Describe la falla encontrada en el campus</p>
        </div>

        <div id="formMsg" class="msg"></div>

        <div class="reporte-form-wrap">

          <!-- FORMULARIO -->
          <form id="reporteForm" class="reporte-form">

            <div class="form-row">
              <div class="field">
                <label>Tipo de falla</label>
                <select name="tipo_falla" required>
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
              <div class="field">
                <label>Bloque</label>
                <select name="bloque" required>
                  <option value="">— Selecciona —</option>
                  <option value="A">Bloque A</option>
                  <option value="B">Bloque B</option>
                  <option value="C">Bloque C</option>
                </select>
              </div>
            </div>

            <div class="field">
              <label>Ubicación específica</label>
              <input type="text" name="ubicacion_detalle" placeholder="Ej: Salón 203, Laboratorio de Sistemas 1"
                required maxlength="150" />
            </div>

            <div class="field">
              <label>Descripción del hallazgo</label>
              <textarea name="descripcion" rows="5"
                placeholder="Describe con detalle la falla: qué observaste, desde cuándo, posible causa..." required
                maxlength="1000"></textarea>
            </div>

            <div class="field">
              <label>Foto de evidencia <span class="optional">(opcional)</span></label>
              <div class="file-drop" id="fileDrop">
                <input type="file" name="foto" id="fotoInput" accept="image/*" style="display:none" />
                <div class="file-drop-inner" id="fileDropInner">
                  <span class="file-icon">📷</span>
                  <span class="file-label">Arrastra una imagen o <u>haz clic aquí</u></span>
                  <span class="file-hint">JPG, PNG o WEBP · Máx 5 MB</span>
                </div>
                <div class="file-preview" id="filePreview" style="display:none">
                  <img id="previewImg" src="" alt="preview" />
                  <button type="button" class="file-remove" id="fileRemove">✕ Quitar</button>
                </div>
              </div>
            </div>

            <button type="submit" class="btn btn-primary btn-full" id="btnEnviar">
              Enviar Reporte
            </button>

          </form>

          <!-- IMAGEN LATERAL -->
          <aside class="reporte-form-img">
            <img src="../img/udenar.jpg" alt="Campus Universidad de Nariño" />
            <div class="img-caption">📍 Campus Torobajo · Universidad de Nariño</div>
            <div class="img-info">
              <div class="img-info-item">
                <span class="info-icon">📌</span>
                <div>
                  <strong>¿Dónde reportar?</strong>
                  Indica el bloque y la ubicación exacta para que el equipo técnico pueda llegar rápidamente.
                </div>
              </div>
              <div class="img-info-item">
                <span class="info-icon">📷</span>
                <div>
                  <strong>Adjunta evidencia</strong>
                  Una foto ayuda a evaluar la gravedad del hallazgo y agiliza la atención.
                </div>
              </div>
              <div class="img-info-item">
                <span class="info-icon">⏱️</span>
                <div>
                  <strong>Seguimiento</strong>
                  Consulta el estado de tus reportes en la sección <em>Mis Reportes</em>.
                </div>
              </div>
            </div>
          </aside>

        </div><!-- /.reporte-form-wrap -->
      </section>

      <!-- TAB: HISTORIAL -->
      <section id="tab-historial" class="tab-panel">
        <div class="section-header">
          <h2>Mis Reportes</h2>
          <p>Seguimiento de todos tus hallazgos enviados</p>
        </div>
        <div id="historialContent">
          <div class="loading-state">Cargando reportes…</div>
        </div>
      </section>

    </main>
  </div>

  <script src="../js/dashboard.js"></script>
</body>

</html>