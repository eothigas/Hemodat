// admin.js - Painel de administração: usuários + estoque mínimo

// Busca token CSRF e armazena como Promise (evita race condition)
const csrfReady = fetch(BASE_URL + '/includes/functions/csrf.php')
    .then(r => r.json())
    .then(d => d.token)
    .catch(() => '');

// ── Usuários ──────────────────────────────────────────────────────────────────

async function carregarUsuarios() {
    const tbody = document.getElementById('usuarios-body');
    try {
        const res   = await fetch(BASE_URL + '/includes/actions/auth.php?action=listar_usuarios');
        const users = await res.json();

        if (!Array.isArray(users) || users.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">Nenhum usuário encontrado.</td></tr>';
            return;
        }

        tbody.innerHTML = users.map(u => {
            const isAdmin   = u.role === 'admin';
            const badgeHtml = isAdmin
                ? '<span class="badge badge-brand">Admin</span>'
                : '<span class="badge badge-gray">Operador</span>';
            const btnLabel  = isAdmin ? 'Rebaixar' : 'Promover';
            const btnClass  = isAdmin ? 'btn-ghost' : 'btn-secondary';
            const novaRole  = isAdmin ? 'operador' : 'admin';

            return `<tr>
                <td style="font-weight:600;">${escHtml(u.nome)}</td>
                <td class="small muted">${escHtml(u.email)}</td>
                <td>${badgeHtml}</td>
                <td>
                    <button class="btn ${btnClass} btn-sm btn-role"
                            data-id="${u.id}" data-role="${novaRole}">
                        ${btnLabel}
                    </button>
                </td>
            </tr>`;
        }).join('');

        // Delegação de evento
        tbody.querySelectorAll('.btn-role').forEach(btn => {
            btn.addEventListener('click', () => alterarRole(btn.dataset.id, btn.dataset.role));
        });

    } catch (e) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-danger text-center py-3">Erro ao carregar usuários.</td></tr>';
    }
}

async function alterarRole(id, role) {
    const body = new FormData();
    body.append('id',   id);
    body.append('role', role);
    body.append('csrf_token', await csrfReady);

    try {
        const res    = await fetch(BASE_URL + '/includes/actions/auth.php?action=alterar_role', {
            method: 'POST', body,
        });
        const result = await res.json();

        if (result.status === 'success') {
            showToast(result.message, 'success');
            carregarUsuarios();
        } else {
            showToast(result.message, 'error');
        }
    } catch (e) {
        showToast('Erro ao alterar permissão.', 'error');
    }
}

// ── Estoque Mínimo ────────────────────────────────────────────────────────────

async function carregarEstoqueMin() {
    const campos = document.getElementById('estoque-min-campos');
    try {
        const res  = await fetch(BASE_URL + '/includes/actions/bolsas.php?action=estoque_min_get');
        const rows = await res.json();

        campos.innerHTML = rows.map(r => `
            <label class="field">
                <span class="field-lbl"><span class="bt" style="margin-right:6px;">${r.tipo_sanguineo}</span></span>
                <span class="input-wrap">
                    <input type="number" step="0.01" min="0"
                           name="minimos[${r.tipo_sanguineo}]"
                           value="${parseFloat(r.minimo_litros).toFixed(2)}">
                    <span class="input-ic right" style="pointer-events:none;">L</span>
                </span>
            </label>
        `).join('');

    } catch (e) {
        campos.innerHTML = '<div class="small" style="color:var(--brand-700);">Erro ao carregar configurações.</div>';
    }
}

document.getElementById('form-estoque-min').addEventListener('submit', async (e) => {
    e.preventDefault();
    const body = new FormData(e.target);
    body.append('csrf_token', await csrfReady);

    try {
        const res    = await fetch(BASE_URL + '/includes/actions/auth.php?action=salvar_estoque_min', {
            method: 'POST', body,
        });
        const result = await res.json();
        showToast(result.message, result.status === 'success' ? 'success' : 'error');
    } catch (err) {
        showToast('Erro ao salvar configurações.', 'error');
    }
});

// ── Utilitários ───────────────────────────────────────────────────────────────

function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

// ── Tabs ──────────────────────────────────────────────────────────────────────

document.querySelectorAll('#admin-tabs .tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('#admin-tabs .tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        document.querySelectorAll('[data-panel]').forEach(p => p.classList.add('d-none'));
        document.getElementById('tab-' + tab.dataset.tab).classList.remove('d-none');
    });
});

// ── Carga inicial ─────────────────────────────────────────────────────────────

carregarUsuarios();
carregarEstoqueMin();
