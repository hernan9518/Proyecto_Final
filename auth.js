const SUPABASE_URL  = 'https://ulxkvuoadvzirseoequo.supabase.co';
const SUPABASE_ANON = 'sb_publishable_9Bg_X4sJUok4nQK8T2DOEQ_CzcT7_C9';

const { createClient } = supabase;
const sb = createClient(SUPABASE_URL, SUPABASE_ANON);

const $ = id => document.getElementById(id);

// Si ya hay sesión activa, ir directo al dashboard
sb.auth.getSession().then(({ data: { session } }) => {
  if (session) window.location.href = 'index.html';
});

// ─── HELPERS ──────────────────────────────────────────
function showView(id) {
  ['loginView', 'registerView'].forEach(v =>
    $(v).classList.toggle('active', v === id)
  );
}
function showMsg(id, text, type) {
  const el = $(id);
  el.className = 'msg ' + type;
  el.textContent = text;
}
function hideMsg(id) { $(id).className = 'msg'; }

// ─── LOGIN ────────────────────────────────────────────
$('btnLogin').addEventListener('click', async () => {
  const email = $('loginEmail').value.trim();
  const pass  = $('loginPass').value;
  if (!email || !pass) { showMsg('loginMsg', 'Completa todos los campos', 'error'); return; }

  const btn = $('btnLogin');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner"></span> Ingresando...';
  hideMsg('loginMsg');

  const { error } = await sb.auth.signInWithPassword({ email, password: pass });

  if (error) {
    showMsg('loginMsg', error.message, 'error');
    btn.disabled = false;
    btn.innerHTML = 'Ingresar al sistema';
  } else {
    window.location.href = 'index.html';
  }
});

// ─── REGISTER ─────────────────────────────────────────
$('btnRegister').addEventListener('click', async () => {
  const email = $('regEmail').value.trim();
  const pass  = $('regPass').value;
  if (!email || !pass) { showMsg('regMsg', 'Completa todos los campos', 'error'); return; }

  const btn = $('btnRegister');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner"></span> Creando cuenta...';

  const { error } = await sb.auth.signUp({ email, password: pass });

  if (error) {
    showMsg('regMsg', error.message, 'error');
  } else {
    showMsg('regMsg', '¡Cuenta creada! Revisa tu correo para confirmar.', 'success');
  }

  btn.disabled = false;
  btn.innerHTML = 'Crear cuenta';
});

// ─── NAVEGACIÓN ENTRE VISTAS ──────────────────────────
$('btnGoRegister').addEventListener('click', () => { hideMsg('loginMsg'); showView('registerView'); });
$('btnGoLogin').addEventListener('click',    () => { hideMsg('regMsg');   showView('loginView'); });
