const SUPABASE_URL  = 'https://ulxkvuoadvzirseoequo.supabase.co';
const SUPABASE_ANON = 'sb_publishable_9Bg_X4sJUok4nQK8T2DOEQ_CzcT7_C9';

const { createClient } = supabase;
const sb = createClient(SUPABASE_URL, SUPABASE_ANON);

const $ = id => document.getElementById(id);

// ─── GUARD: si no hay sesión, redirige al login ────────
sb.auth.getSession().then(({ data: { session } }) => {
  if (!session) {
    window.location.href = 'auth.html';
  } else {
    $('navEmail').textContent = session.user.email;
    loadReportes();
  }
});

// Si la sesión expira mientras el usuario está en el dashboard
sb.auth.onAuthStateChange((event, session) => {
  if (!session) window.location.href = 'auth.html';
});

// ─── LOGOUT ───────────────────────────────────────────
$('btnLogout').addEventListener('click', async () => {
  await sb.auth.signOut();
  window.location.href = 'auth.html';
});

// ─── FILTRO ACTIVO ────────────────────────────────────
let activeFilter = 'all';
let allReportes  = [];

// ─── TABS ─────────────────────────────────────────────
document.querySelectorAll('.sidebar-item[data-tab]').forEach(item => {
  item.addEventListener('click', () => {
    document.querySelectorAll('.sidebar-item[data-tab]').forEach(i => i.classList.remove('active'));
    item.classList.add('active');

    const tabId = 'tab' + item.dataset.tab.charAt(0).toUpperCase() + item.dataset.tab.slice(1);
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    $(tabId).classList.add('active');
  });
});

// ─── FILTROS RÁPIDOS ──────────────────────────────────
document.querySelectorAll('.filter-item').forEach(item => {
  item.addEventListener('click', () => {
    document.querySelectorAll('.filter-item').forEach(i => i.classList.remove('active'));
    item.classList.add('active');
    activeFilter = item.dataset.filter;
    renderTable(allReportes);
  });
});

// ─── BOTÓN ACTUALIZAR ─────────────────────────────────
$('btnRefresh').addEventListener('click', loadReportes);

// ─── CARGAR REPORTES ──────────────────────────────────
async function loadReportes() {
  const tbody = $('reportTable');
  tbody.innerHTML = '<tr class="empty-row"><td colspan="8">Cargando reportes...</td></tr>';

  const { data, error } = await sb
    .from('reportes')
    .select('*')
    .order('created_at', { ascending: false });

  if (error) {
    tbody.innerHTML = `<tr class="empty-row"><td colspan="8">Error al cargar: ${escHtml(error.message)}</td></tr>`;
    return;
  }

  allReportes = data || [];
  renderTable(allReportes);
}

// ─── RENDERIZAR TABLA ─────────────────────────────────
function renderTable(reportes) {
  const filtered = activeFilter === 'all'
    ? reportes
    : reportes.filter(r => r.estado === activeFilter);

  $('statTotal').textContent     = reportes.length;
  $('statPendiente').textContent = reportes.filter(r => r.estado === 'Pendiente').length;
  $('statEnProceso').textContent = reportes.filter(r => r.estado === 'En proceso').length;
  $('statResuelto').textContent  = reportes.filter(r => r.estado === 'Resuelto').length;
  $('badgeCount').textContent    = filtered.length + ' registros';

  const tbody = $('reportTable');
  if (filtered.length === 0) {
    tbody.innerHTML = '<tr class="empty-row"><td colspan="8">No hay reportes en esta categoría aún.</td></tr>';
    return;
  }

  tbody.innerHTML = filtered.map((r, i) => `
    <tr>
      <td class="date-text">${i + 1}</td>
      <td><span class="tipo-text">${escHtml(r.tipo || '—')}</span></td>
      <td>${escHtml(r.ubicacion || '—')}</td>
      <td style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:var(--muted)">${escHtml(r.descripcion || '—')}</td>
      <td>${prioridadBadge(r.prioridad)}</td>
      <td>${estadoBadge(r.estado)}</td>
      <td class="date-text">${formatDate(r.created_at)}</td>
      <td><button class="btn-del" onclick="deleteReporte(${r.id})">Eliminar</button></td>
    </tr>
  `).join('');
}

function prioridadBadge(p) {
  const map = { 'Alta': ['pri-alta', '🔴 Alta'], 'Media': ['pri-media', '🟡 Media'], 'Baja': ['pri-baja', '🟢 Baja'] };
  const [cls, label] = map[p] || ['pri-baja', p || '—'];
  return `<span class="prioridad-badge ${cls}">${label}</span>`;
}

function estadoBadge(e) {
  const map = { 'Pendiente': ['status-pendiente', '⏳ Pendiente'], 'En proceso': ['status-en-proceso', '🔧 En proceso'], 'Resuelto': ['status-resuelto', '✅ Resuelto'] };
  const [cls, label] = map[e] || ['status-pendiente', e || 'Pendiente'];
  return `<span class="status-badge ${cls}">${label}</span>`;
}

// ─── GUARDAR REPORTE ──────────────────────────────────
$('btnGuardar').addEventListener('click', async () => {
  const tipo         = $('fTipo').value;
  const ubicacion    = $('fUbicacion').value.trim();
  const desc         = $('fDesc').value.trim();
  const prioridad    = $('fPrioridad').value;
  const reportadoPor = $('fReportadoPor').value.trim();

  if (!tipo)      { showFormMsg('Selecciona el tipo de problema', 'error'); return; }
  if (!ubicacion) { showFormMsg('Indica la ubicación del problema', 'error'); return; }
  if (!desc)      { showFormMsg('Añade una descripción del problema', 'error'); return; }

  const btn = $('btnGuardar');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner"></span> Enviando reporte...';

  const { error } = await sb.from('reportes').insert([{
    tipo, ubicacion, descripcion: desc, prioridad,
    estado: 'Pendiente',
    reportado_por: reportadoPor || null,
  }]);

  if (error) {
    showFormMsg(error.message, 'error');
  } else {
    showFormMsg('¡Reporte enviado correctamente!', 'success');
    $('fTipo').value = ''; $('fUbicacion').value = '';
    $('fDesc').value = ''; $('fPrioridad').value = 'Media';
    $('fReportadoPor').value = '';
    await loadReportes();
    setTimeout(() => document.querySelector('.sidebar-item[data-tab="reportes"]').click(), 1200);
  }

  btn.disabled = false;
  btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Enviar reporte';
});

// ─── ELIMINAR REPORTE ─────────────────────────────────
async function deleteReporte(id) {
  if (!confirm('¿Eliminar este reporte? Esta acción no se puede deshacer.')) return;
  const { error } = await sb.from('reportes').delete().eq('id', id);
  if (!error) await loadReportes();
  else alert('Error al eliminar: ' + error.message);
}

// ─── HELPERS ──────────────────────────────────────────
function showFormMsg(text, type) {
  const el = $('formMsg');
  el.className = 'form-msg ' + type;
  el.textContent = text;
  setTimeout(() => el.className = 'form-msg', 4000);
}

function escHtml(str) {
  return String(str ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function formatDate(iso) {
  if (!iso) return '—';
  return new Date(iso).toLocaleDateString('es-CO', { day: '2-digit', month: 'short', year: 'numeric' });
}
