// admin.js - Painel de administração: usuários + estoque mínimo

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
                ? '<span class="badge" style="background:rgba(209,0,0,0.15);color:var(--hemo-red);">Admin</span>'
                : '<span class="badge bg-secondary bg-opacity-25 text-secondary">Operador</span>';
            const btnLabel  = isAdmin ? 'Rebaixar' : 'Promover';
            const btnClass  = isAdmin ? 'btn-outline-danger' : 'btn-outline-success';
            const novaRole  = isAdmin ? 'operador' : 'admin';

            return `<tr>
                <td><strong>${escHtml(u.nome)}</strong></td>
                <td class="text-muted small">${escHtml(u.email)}</td>
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
            <div class="col-sm-6 col-md-3">
                <label class="form-label fw-semibold small mb-1">
                    <i class="bi bi-droplet-fill me-1" style="color:var(--hemo-red);"></i>${r.tipo_sanguineo}
                </label>
                <div class="input-group input-group-sm">
                    <input type="number" step="0.01" min="0"
                           name="minimos[${r.tipo_sanguineo}]"
                           value="${parseFloat(r.minimo_litros).toFixed(2)}"
                           class="form-control">
                    <span class="input-group-text">L</span>
                </div>
            </div>
        `).join('');

    } catch (e) {
        campos.innerHTML = '<div class="col-12 text-danger">Erro ao carregar configurações.</div>';
    }
}

document.getElementById('form-estoque-min').addEventListener('submit', async (e) => {
    e.preventDefault();
    const body = new FormData(e.target);

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

// ── Carga inicial ─────────────────────────────────────────────────────────────

carregarUsuarios();
carregarEstoqueMin();
