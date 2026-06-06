// js/dashboard.js — Lógica del dashboard del estudiante

document.addEventListener("DOMContentLoaded", () => {

  // ── TABS ─────────────────────────────────────────────────
  const navItems = document.querySelectorAll(".nav-item");
  const tabs     = document.querySelectorAll(".tab-panel");

  navItems.forEach(btn => {
    btn.addEventListener("click", () => {
      navItems.forEach(b => b.classList.remove("active"));
      tabs.forEach(t => t.classList.remove("active"));
      btn.classList.add("active");
      const target = document.getElementById("tab-" + btn.dataset.tab);
      if (target) target.classList.add("active");
      if (btn.dataset.tab === "historial") cargarHistorial();
    });
  });

  // ── PREVIEW FOTO ─────────────────────────────────────────
  const fotoInput   = document.getElementById("fotoInput");
  const fileDrop    = document.getElementById("fileDrop");
  const filePreview = document.getElementById("filePreview");
  const previewImg  = document.getElementById("previewImg");
  const fileRemove  = document.getElementById("fileRemove");
  const dropInner   = document.getElementById("fileDropInner");

  let seleccionando = false;

  // Abrir selector
  dropInner.addEventListener("click", e => {
    e.preventDefault();
    e.stopPropagation();
    if (seleccionando) return;
    seleccionando = true;
    fotoInput.click();
  });

  // Cuando termina el selector — liberar flag después del change
  fotoInput.addEventListener("change", () => {
    if (fotoInput.files.length > 0) mostrarPreview(fotoInput.files[0]);
    setTimeout(() => { seleccionando = false; }, 300);
  });

  // Evita que el contenedor padre propague clics innecesarios
  fileDrop.addEventListener("click", e => e.stopPropagation());

  fileDrop.addEventListener("dragover", e => { e.preventDefault(); fileDrop.classList.add("drag-over"); });
  fileDrop.addEventListener("dragleave", ()  => fileDrop.classList.remove("drag-over"));
  fileDrop.addEventListener("drop", e => {
    e.preventDefault();
    fileDrop.classList.remove("drag-over");
    if (e.dataTransfer.files[0]) mostrarPreview(e.dataTransfer.files[0]);
  });

  fileRemove.addEventListener("click", e => {
    e.preventDefault();
    e.stopPropagation();
    fotoInput.value = "";
    previewImg.src  = "";
    filePreview.style.display = "none";
    dropInner.style.display   = "flex";
  });

  function mostrarPreview(file) {
    const reader = new FileReader();
    reader.onload = ev => {
      previewImg.src = ev.target.result;
      filePreview.style.display = "flex";
      dropInner.style.display   = "none";
    };
    reader.readAsDataURL(file);
  }

  // ── ENVIAR REPORTE ────────────────────────────────────────
  const form     = document.getElementById("reporteForm");
  const formMsg  = document.getElementById("formMsg");
  const btnEnviar= document.getElementById("btnEnviar");

  form.addEventListener("submit", async e => {
    e.preventDefault();
    clearMsg();

    const fd = new FormData(form);

    // Adjuntar el archivo si fue seleccionado por drag&drop
    if (fotoInput.files[0]) fd.set("foto", fotoInput.files[0]);

    btnEnviar.disabled    = true;
    btnEnviar.textContent = "Enviando…";

    try {
      const res  = await fetch("../php/crear_reporte.php", { method: "POST", body: fd });
      const data = await res.json();

      if (data.ok) {
        showMsg("✓ " + data.msg, "success");
        form.reset();
        fotoInput.value = "";
        filePreview.style.display = "none";
        dropInner.style.display   = "flex";
      } else {
        showMsg(data.msg, "error");
      }
    } catch (err) {
      showMsg("Error de conexión con el servidor.", "error");
    } finally {
      btnEnviar.disabled    = false;
      btnEnviar.textContent = "Enviar Reporte";
    }
  });

  // ── HISTORIAL ─────────────────────────────────────────────
  async function cargarHistorial() {
    const cont = document.getElementById("historialContent");
    cont.innerHTML = '<div class="loading-state">Cargando reportes…</div>';

    try {
      const res  = await fetch("../php/mis_reportes.php");
      const data = await res.json();

      if (!data.ok) { cont.innerHTML = `<div class="empty-state">${data.msg}</div>`; return; }
      if (data.reportes.length === 0) {
        cont.innerHTML = '<div class="empty-state">Aún no has enviado ningún reporte.</div>'; return;
      }

      cont.innerHTML = data.reportes.map(r => tarjetaReporte(r)).join("");

      // Expandir timeline
      cont.querySelectorAll(".btn-timeline").forEach(btn => {
        btn.addEventListener("click", () => toggleTimeline(btn.dataset.id, btn));
      });

    } catch {
      cont.innerHTML = '<div class="empty-state">Error al cargar reportes.</div>';
    }
  }

  function tarjetaReporte(r) {
    const fecha    = new Date(r.creado).toLocaleDateString("es-CO", { day:"2-digit", month:"short", year:"numeric" });
    const iconTipo = { estructural:"🏗️", electrica:"⚡", iluminacion:"💡", red:"🌐", sistema:"💻", mobiliario:"🪑", otro:"📌" };
    const foto     = r.foto
      ? `<a href="../${r.foto}" target="_blank" class="foto-thumb"><img src="../${r.foto}" alt="foto"/></a>` : "";

    return `
    <div class="reporte-card">
      <div class="reporte-card-head">
        <span class="tipo-badge">${iconTipo[r.tipo_falla] || "📌"} ${r.tipo_falla}</span>
        <span class="bloque-tag">Bloque ${r.bloque}</span>
        <span class="estado-badge estado-${r.estado}">${estadoLabel(r.estado)}</span>
        ${r.nivel_amenaza ? `<span class="amenaza-badge amenaza-${r.nivel_amenaza}">${r.nivel_amenaza.toUpperCase()}</span>` : ""}
        <span class="fecha-tag">${fecha}</span>
      </div>
      <div class="reporte-card-body">
        <p class="ubicacion">📍 ${escHtml(r.ubicacion_detalle)}</p>
        <p class="descripcion">${escHtml(r.descripcion)}</p>
        ${foto}
        ${r.ultima_nota ? `<div class="nota-admin">💬 <em>${escHtml(r.ultima_nota)}</em></div>` : ""}
      </div>
      <div class="reporte-card-foot">
        <button class="btn-timeline btn-link" data-id="${r.id}">Ver historial de cambios ▾</button>
        <div class="timeline-wrap" id="timeline-${r.id}" style="display:none"></div>
      </div>
    </div>`;
  }

  async function toggleTimeline(id, btn) {
    const wrap = document.getElementById("timeline-" + id);
    if (wrap.style.display === "block") { wrap.style.display = "none"; btn.textContent = "Ver historial de cambios ▾"; return; }

    wrap.innerHTML = "<em>Cargando…</em>";
    wrap.style.display = "block";
    btn.textContent = "Ocultar historial ▴";

    try {
      const res  = await fetch(`../php/historial_reporte.php?id=${id}`);
      const data = await res.json();

      if (!data.ok || data.historial.length === 0) {
        wrap.innerHTML = "<em>Sin cambios registrados aún.</em>"; return;
      }

      wrap.innerHTML = `<ul class="timeline">${data.historial.map(h => `
        <li class="timeline-item">
          <span class="tl-dot estado-${h.estado_nuevo}"></span>
          <div class="tl-body">
            <span class="tl-estado">${estadoLabel(h.estado_anterior)} → ${estadoLabel(h.estado_nuevo)}</span>
            ${h.nivel_amenaza ? `<span class="amenaza-badge amenaza-${h.nivel_amenaza}">${h.nivel_amenaza}</span>` : ""}
            ${h.nota ? `<p class="tl-nota">"${escHtml(h.nota)}"</p>` : ""}
            <span class="tl-fecha">${new Date(h.fecha).toLocaleString("es-CO")} · ${escHtml(h.admin_nombre)}</span>
          </div>
        </li>`).join("")}</ul>`;
    } catch {
      wrap.innerHTML = "<em>Error al cargar historial.</em>";
    }
  }

  // ── UTILS ─────────────────────────────────────────────────
  function estadoLabel(e) {
    return { pendiente:"Pendiente", en_proceso:"En Proceso", completado:"Completado" }[e] || e;
  }
  function escHtml(str) {
    return String(str).replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;");
  }
  function showMsg(text, type) {
    formMsg.textContent = text;
    formMsg.className   = "msg " + type;
    setTimeout(() => { formMsg.textContent = ""; formMsg.className = "msg"; }, 5000);
  }
  function clearMsg() { formMsg.textContent = ""; formMsg.className = "msg"; }
});