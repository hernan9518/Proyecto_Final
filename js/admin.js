// js/admin.js — Lógica del panel de administración

document.addEventListener("DOMContentLoaded", () => {

  // ── TABS ─────────────────────────────────────────────────
  const navItems = document.querySelectorAll(".nav-item");
  const tabs     = document.querySelectorAll(".tab-panel");

  const tabLoaders = {
    pendientes:  () => cargarLista("pendiente",   "listaPendientes",  "badgePendientes"),
    en_proceso:  () => cargarLista("en_proceso",  "listaProceso",     "badgeProceso"),
    completados: () => cargarLista("completado",  "listaCompletados", null),
  };

  navItems.forEach(btn => {
    btn.addEventListener("click", () => {
      navItems.forEach(b => b.classList.remove("active"));
      tabs.forEach(t => t.classList.remove("active"));
      btn.classList.add("active");
      const key = btn.dataset.tab;
      document.getElementById("tab-" + key)?.classList.add("active");
      tabLoaders[key]?.();
    });
  });

  // Cargar pendientes al inicio
  tabLoaders.pendientes();
  tabLoaders.en_proceso();   // para el badge

  // ── CARGAR LISTA ──────────────────────────────────────────
  async function cargarLista(estado, containerId, badgeId) {
    const cont = document.getElementById(containerId);
    if (!cont) return;
    cont.innerHTML = '<div class="loading-state">Cargando…</div>';

    try {
      const res  = await fetch(`../php/admin_reportes.php?estado=${estado}`);
      const data = await res.json();

      if (badgeId) {
        const badge = document.getElementById(badgeId);
        if (badge) badge.textContent = data.reportes?.length || 0;
      }

      if (!data.ok) { cont.innerHTML = `<div class="empty-state">${data.msg}</div>`; return; }
      if (data.reportes.length === 0) {
        cont.innerHTML = `<div class="empty-state">No hay reportes en este estado.</div>`; return;
      }

      cont.innerHTML = `
        <div class="tabla-wrapper">
          <table class="reportes-tabla">
            <thead>
              <tr>
                <th>#</th><th>Estudiante</th><th>Tipo</th><th>Bloque</th>
                <th>Ubicación</th><th>Amenaza</th><th>Fecha</th><th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              ${data.reportes.map(r => filaTabla(r)).join("")}
            </tbody>
          </table>
        </div>`;

      // Vincular botones
      cont.querySelectorAll(".btn-detalle").forEach(btn => {
        btn.addEventListener("click", () => abrirModal(JSON.parse(btn.dataset.reporte)));
      });

    } catch (err) {
      cont.innerHTML = '<div class="empty-state">Error al cargar datos.</div>';
    }
  }

  function filaTabla(r) {
    const iconTipo = { estructural:"🏗️", electrica:"⚡", iluminacion:"💡", red:"🌐", sistema:"💻", mobiliario:"🪑", otro:"📌" };
    const fecha    = new Date(r.creado).toLocaleDateString("es-CO", { day:"2-digit", month:"short", year:"numeric" });
    const amenaza  = r.nivel_amenaza
      ? `<span class="amenaza-badge amenaza-${r.nivel_amenaza}">${r.nivel_amenaza}</span>`
      : `<span class="text-muted">—</span>`;

    return `
      <tr>
        <td class="mono">#${r.id}</td>
        <td>
          <strong>${escHtml(r.nombres)} ${escHtml(r.apellidos)}</strong><br/>
          <small class="mono">${escHtml(r.codigo)}</small>
        </td>
        <td>${iconTipo[r.tipo_falla] || "📌"} ${r.tipo_falla}</td>
        <td><span class="bloque-tag">Bloque ${r.bloque}</span></td>
        <td class="ubicacion-td">${escHtml(r.ubicacion_detalle)}</td>
        <td>${amenaza}</td>
        <td><small>${fecha}</small></td>
        <td>
          <button class="btn btn-primary btn-sm btn-detalle"
            data-reporte='${JSON.stringify(r).replace(/'/g,"&#39;")}'>
            Ver detalle
          </button>
        </td>
      </tr>`;
  }

  // ── MODAL ─────────────────────────────────────────────────
  const modalOverlay = document.getElementById("modalOverlay");
  const modalClose   = document.getElementById("modalClose");
  const modalContent = document.getElementById("modalContent");

  modalClose.addEventListener("click", cerrarModal);
  modalOverlay.addEventListener("click", e => { if (e.target === modalOverlay) cerrarModal(); });

  function cerrarModal() { modalOverlay.style.display = "none"; }

  async function abrirModal(r) {
    modalContent.innerHTML = '<div class="loading-state">Cargando…</div>';
    modalOverlay.style.display = "flex";

    // Cargar historial
    let timelineHtml = "";
    try {
      const res  = await fetch(`../php/historial_reporte.php?id=${r.id}`);
      const data = await res.json();
      if (data.ok && data.historial.length > 0) {
        timelineHtml = `
          <ul class="timeline">
            ${data.historial.map(h => `
            <li class="timeline-item">
              <span class="tl-dot estado-${h.estado_nuevo}"></span>
              <div class="tl-body">
                <span class="tl-estado">${estadoLabel(h.estado_anterior)} → ${estadoLabel(h.estado_nuevo)}</span>
                ${h.nivel_amenaza ? `<span class="amenaza-badge amenaza-${h.nivel_amenaza}">${h.nivel_amenaza}</span>` : ""}
                ${h.nota ? `<p class="tl-nota">"${escHtml(h.nota)}"</p>` : ""}
                <span class="tl-fecha">${new Date(h.fecha).toLocaleString("es-CO")} · ${escHtml(h.admin_nombre)}</span>
              </div>
            </li>`).join("")}
          </ul>`;
      } else {
        timelineHtml = "<em class='text-muted'>Sin cambios previos.</em>";
      }
    } catch { timelineHtml = "<em>Error al cargar historial.</em>"; }

    const fotoHtml = r.foto
      ? `<div class="modal-foto"><a href="../${r.foto}" target="_blank"><img src="../${r.foto}" alt="Evidencia"/></a></div>` : "";

    modalContent.innerHTML = `
      <div class="modal-header">
        <h3>Reporte <span class="mono">#${r.id}</span></h3>
        <span class="estado-badge estado-${r.estado}">${estadoLabel(r.estado)}</span>
      </div>

      <div class="modal-info-grid">
        <div><label>Estudiante</label><span>${escHtml(r.nombres)} ${escHtml(r.apellidos)}</span></div>
        <div><label>Código</label><span class="mono">${escHtml(r.codigo)}</span></div>
        <div><label>Correo</label><span>${escHtml(r.correo)}</span></div>
        <div><label>Teléfono</label><span>${escHtml(r.telefono)}</span></div>
        <div><label>Tipo de falla</label><span>${r.tipo_falla}</span></div>
        <div><label>Bloque</label><span>Bloque ${r.bloque}</span></div>
        <div class="span2"><label>Ubicación</label><span>${escHtml(r.ubicacion_detalle)}</span></div>
        <div class="span2"><label>Descripción</label><p class="descripcion-modal">${escHtml(r.descripcion)}</p></div>
      </div>

      ${fotoHtml}

      <div class="modal-section">
        <h4>Historial de cambios</h4>
        ${timelineHtml}
      </div>

      <div class="modal-section">
        <h4>Actualizar estado</h4>
        <div id="updateMsg" class="msg"></div>
        <div class="form-row">
          <div class="field">
            <label>Nuevo estado</label>
            <select id="selEstado">
              <option value="pendiente"  ${r.estado==="pendiente"  ? "selected":""}>Pendiente</option>
              <option value="en_proceso" ${r.estado==="en_proceso" ? "selected":""}>En Proceso</option>
              <option value="completado" ${r.estado==="completado" ? "selected":""}>Completado</option>
            </select>
          </div>
          <div class="field">
            <label>Nivel de amenaza</label>
            <select id="selAmenaza">
              <option value="">— Sin cambiar —</option>
              <option value="bajo"  ${r.nivel_amenaza==="bajo"  ? "selected":""}>🟢 Bajo</option>
              <option value="medio" ${r.nivel_amenaza==="medio" ? "selected":""}>🟡 Medio</option>
              <option value="alto"  ${r.nivel_amenaza==="alto"  ? "selected":""}>🔴 Alto</option>
            </select>
          </div>
        </div>
        <div class="field">
          <label>Nota para el estudiante <span class="optional">(opcional)</span></label>
          <textarea id="txtNota" rows="3" placeholder="Ej: Se asignó técnico, visita programada para el lunes…"></textarea>
        </div>
        <button class="btn btn-primary" id="btnActualizar">Guardar cambios</button>
      </div>
    `;

    document.getElementById("btnActualizar").addEventListener("click", () => actualizarReporte(r.id));
  }

  async function actualizarReporte(id) {
    const estado_nuevo  = document.getElementById("selEstado").value;
    const nivel_amenaza = document.getElementById("selAmenaza").value;
    const nota          = document.getElementById("txtNota").value.trim();
    const msgEl         = document.getElementById("updateMsg");

    msgEl.textContent = "Guardando…";
    msgEl.className   = "msg";

    try {
      const res  = await fetch("../php/actualizar_reporte.php", {
        method:  "POST",
        headers: { "Content-Type": "application/json" },
        body:    JSON.stringify({ id_reporte: id, estado_nuevo, nivel_amenaza, nota }),
      });
      const data = await res.json();

      if (data.ok) {
        msgEl.textContent = "✓ " + data.msg;
        msgEl.className   = "msg success";
        // Recargar todas las listas
        setTimeout(() => {
          cerrarModal();
          tabLoaders.pendientes();
          tabLoaders.en_proceso();
        }, 1000);
      } else {
        msgEl.textContent = data.msg;
        msgEl.className   = "msg error";
      }
    } catch {
      msgEl.textContent = "Error de conexión.";
      msgEl.className   = "msg error";
    }
  }

  // ── UTILS ─────────────────────────────────────────────────
  function estadoLabel(e) {
    return { pendiente:"Pendiente", en_proceso:"En Proceso", completado:"Completado" }[e] || e;
  }
  function escHtml(str) {
    return String(str||"").replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;");
  }
});
