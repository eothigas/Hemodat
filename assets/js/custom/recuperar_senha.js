// recuperar_senha.js — wizard de recuperação em página única

(function () {

    // ── Estado ────────────────────────────────────────────────────────────────
    let currentStep = 1;

    const stepTitles = {
        1: 'Recuperação de Senha',
        2: 'Validar Código',
        3: 'Nova Senha',
    };

    // ── Helpers ───────────────────────────────────────────────────────────────
    function goToStep(n) {
        // Painéis
        document.querySelectorAll('.rec-panel').forEach((p, i) => {
            p.classList.toggle('d-none', i + 1 !== n);
        });

        // Indicadores
        document.querySelectorAll('.rec-step').forEach(el => {
            const s = parseInt(el.dataset.step, 10);
            el.classList.toggle('active',    s === n);
            el.classList.toggle('done',      s < n);
        });

        // Título
        document.getElementById('step-title').textContent = stepTitles[n] ?? '';
        currentStep = n;

        // Foca primeiro input do painel ativo
        const panel = document.getElementById('panel-' + n);
        const first = panel?.querySelector('input');
        if (first) setTimeout(() => first.focus(), 80);
    }

    function setLoading(btn, loading) {
        btn.disabled = loading;
        btn.dataset.origText = btn.dataset.origText ?? btn.innerHTML;
        btn.innerHTML = loading
            ? '<span class="spinner-border spinner-border-sm me-1"></span> Aguarde…'
            : btn.dataset.origText;
    }

    // ── Step 1: Solicitar código ──────────────────────────────────────────────
    document.getElementById('form-recuperar').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn     = e.target.querySelector('button[type="submit"]');
        const usuario = e.target.querySelector('[name="usuario"]').value.trim();
        const email   = e.target.querySelector('[name="email"]').value.trim();

        if (!usuario || !email) {
            showToast('Preencha todos os campos.', 'error'); return;
        }

        setLoading(btn, true);
        try {
            const res    = await fetch(BASE_URL + '/includes/actions/senha.php?action=recuperar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `usuario=${encodeURIComponent(usuario)}&email=${encodeURIComponent(email)}`,
            });
            const result = await res.json();

            if (result.status === 'success') {
                showToast('Código enviado! Verifique seu e-mail.', 'success');
                // Personaliza subtitle do step 2
                const sub2 = document.getElementById('subtitle-2');
                if (sub2) sub2.innerHTML =
                    `Código enviado para <strong>${email}</strong>. Válido por <strong>15 minutos</strong>.`;
                setTimeout(() => goToStep(2), 800);
            } else {
                showToast(result.message, 'error');
            }
        } catch {
            showToast('Erro de comunicação. Tente novamente.', 'error');
        } finally {
            setLoading(btn, false);
        }
    });

    // ── Step 2: Validar código ────────────────────────────────────────────────
    document.getElementById('form-validar').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn    = e.target.querySelector('button[type="submit"]');
        const codigo = e.target.querySelector('[name="code"]').value.trim().toUpperCase();

        if (!codigo) { showToast('Insira o código.', 'error'); return; }

        setLoading(btn, true);
        try {
            const res    = await fetch(BASE_URL + '/includes/actions/senha.php?action=validar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `code=${encodeURIComponent(codigo)}`,
            });
            const result = await res.json();

            if (result.status === 'success') {
                showToast('Código válido!', 'success');
                setTimeout(() => goToStep(3), 700);
            } else {
                showToast(result.message, 'error');
                document.getElementById('code-input').value = '';
                document.getElementById('code-input').focus();
            }
        } catch {
            showToast('Erro de comunicação. Tente novamente.', 'error');
        } finally {
            setLoading(btn, false);
        }
    });

    // Botão "reenviar" — volta ao step 1 sem resetar campos
    document.getElementById('btn-reenviar').addEventListener('click', () => {
        goToStep(1);
    });

    // Auto-uppercase no campo de código
    document.getElementById('code-input').addEventListener('input', function () {
        const pos = this.selectionStart;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(pos, pos);
    });

    // ── Step 3: Nova senha ────────────────────────────────────────────────────
    document.getElementById('form-alterar').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn         = e.target.querySelector('button[type="submit"]');
        const senha       = e.target.querySelector('[name="senha"]').value;
        const confirmSenha = e.target.querySelector('[name="confirm-senha"]').value;

        if (!senha || !confirmSenha) { showToast('Preencha todos os campos.', 'error'); return; }
        if (senha.length < 9)        { showToast('Senha mínima de 9 caracteres.', 'error'); return; }
        if (senha !== confirmSenha)  { showToast('As senhas não coincidem.', 'error'); return; }

        setLoading(btn, true);
        try {
            const res    = await fetch(BASE_URL + '/includes/actions/senha.php?action=alterar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `senha=${encodeURIComponent(senha)}&confirm-senha=${encodeURIComponent(confirmSenha)}`,
            });
            const result = await res.json();

            if (result.status === 'success') {
                showToast('Senha alterada! Redirecionando…', 'success');
                setTimeout(() => { window.location.href = BASE_URL + '/login'; }, 1800);
            } else {
                showToast(result.message, 'error');
            }
        } catch {
            showToast('Erro de comunicação. Tente novamente.', 'error');
        } finally {
            setLoading(btn, false);
        }
    });

    // ── Toggle ver/ocultar senha ──────────────────────────────────────────────
    document.querySelectorAll('.pwd-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.target);
            if (!input) return;
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });

})();
