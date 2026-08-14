// recuperar_senha.js — wizard de recuperação em página única

(function () {

    // Busca token CSRF e armazena como Promise (evita race condition)
    const csrfReady = fetch(BASE_URL + '/includes/functions/csrf.php')
        .then(r => r.json())
        .then(d => d.token)
        .catch(() => '');

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
            const csrfToken = await csrfReady;
            const res    = await fetch(BASE_URL + '/includes/actions/senha.php?action=recuperar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `usuario=${encodeURIComponent(usuario)}&email=${encodeURIComponent(email)}&csrf_token=${encodeURIComponent(csrfToken)}`,
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
            const csrfToken = await csrfReady;
            const res    = await fetch(BASE_URL + '/includes/actions/senha.php?action=validar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `code=${encodeURIComponent(codigo)}&csrf_token=${encodeURIComponent(csrfToken)}`,
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

    // ── Password strength ─────────────────────────────────────────────────────
    const strengthFill  = document.getElementById('pwd-strength-fill');
    const strengthLabel = document.getElementById('pwd-strength-label');
    const strengthLabels = ['', 'Fraca', 'Razoável', 'Boa', 'Forte'];

    function calcStrength(pwd) {
        if (!pwd) return 0;
        let score = 0;
        if (pwd.length >= 9)              score++;
        if (/[A-Z]/.test(pwd))            score++;
        if (/[0-9]/.test(pwd))            score++;
        if (/[^A-Za-z0-9]/.test(pwd))     score++;
        return score;
    }

    document.getElementById('nova-senha').addEventListener('input', function () {
        if (!this.value) {
            strengthFill.removeAttribute('data-score');
            strengthLabel.removeAttribute('data-score');
            strengthLabel.textContent = '';
            return;
        }
        const score = calcStrength(this.value);
        strengthFill.dataset.score  = score;
        strengthLabel.dataset.score = score;
        strengthLabel.textContent   = strengthLabels[score];
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
            const csrfToken = await csrfReady;
            const res    = await fetch(BASE_URL + '/includes/actions/senha.php?action=alterar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `senha=${encodeURIComponent(senha)}&confirm-senha=${encodeURIComponent(confirmSenha)}&csrf_token=${encodeURIComponent(csrfToken)}`,
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
    const SVG_EYE     = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>';
    const SVG_EYE_OFF = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A11 11 0 0 1 12 5c7 0 10 7 10 7a13 13 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13 13 0 0 0 2 12s3 7 10 7a9 9 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>';

    document.querySelectorAll('.pwd-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.target);
            if (!input) return;
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.innerHTML = show ? SVG_EYE_OFF : SVG_EYE;
        });
    });

})();
