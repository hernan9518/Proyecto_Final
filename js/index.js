// js/index.js — Lógica de autenticación para index.php

document.addEventListener("DOMContentLoaded", () => {
  const btnLogin = document.getElementById("btnLogin");
  const msgBox = document.getElementById("loginMsg");
  const inputCod = document.getElementById("codigo");
  const inputPass = document.getElementById("password");

  // Permitir login con Enter
  [inputCod, inputPass].forEach(el => {
    el.addEventListener("keydown", e => {
      if (e.key === "Enter") btnLogin.click();
    });
  });

  btnLogin.addEventListener("click", async () => {
    clearMsg();

    const codigo = inputCod.value.trim();
    const password = inputPass.value.trim();

    if (!codigo || !password) {
      showMsg("Completa todos los campos.", "error");
      return;
    }

    btnLogin.disabled = true;
    btnLogin.textContent = "Verificando…";

    try {
      const res = await fetch("php/Auth.php", {

        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ codigo, password }),
      });

      const data = await res.json();

      if (!data.ok) {
        showMsg(data.msg || "Error al iniciar sesión.", "error");
        return;
      }

      showMsg(data.msg, "success");

      // Redirigir según rol
      setTimeout(() => {
        if (data.rol === "admin") {
          window.location.href = "admin/panel.php";
        } else {
          window.location.href = "dashboard/index.php";
        }
      }, 800);

    } catch (err) {
      console.error(err);
      showMsg("No se pudo conectar con el servidor.", "error");
    } finally {
      btnLogin.disabled = false;
      btnLogin.textContent = "Ingresar al sistema";
    }
  });

  // ── Helpers ──────────────────────────────────────────────
  function showMsg(text, type = "info") {
    msgBox.textContent = text;
    msgBox.className = `msg ${type}`;
  }

  function clearMsg() {
    msgBox.textContent = "";
    msgBox.className = "msg";
  }
});
