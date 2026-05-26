// saida.js - blood chips + últimas saídas

// CSRF
let csrfToken = '';
fetch(BASE_URL + '/includes/functions/csrf.php')
    .then(r => r.json())
    .then(d => { csrfToken = d.token; })
    .catch(() => {});

// ── Blood chip selection ────────────────────────────────────
const chips     = document.querySelectorAll('.blood-chip');
const tipoInput = document.getElementById('tipo-hidden');

chips.forEach(chip => {
    chip.addEventListener('click', () => {
        chips.forEach(c => c.classList.remove('selected'));
        chip.classList.add('selected');
        tipoInput.value = chip.dataset.tipo;
    });
});

// ── Últimas saídas (painel direito) ─────────────────────────
async function carregarUltimas() {
    const wrap = document.getElementById('ultimas-saidas');
    if (!wrap) return;
    try {
        const r    = await fetch(
            BASE_URL + '/includes/actions/bolsas.php?action=historico&operacao=saida&page=1'
        );
        const data = await r.json();

        const rows = data.rows ?? [];
        if (rows.length === 0) {
            wrap.innerHTML = '<p class="text-muted small">Nenhuma saída registrada.</p>';
            return;
        }

        const items = rows.slice(0, 8).map(r => {
            const raw = r.data_evento ?? '';
            const d   = raw ? new Date(raw + (raw.includes('T') ? '' : 'T00:00:00')) : null;
            const fmt = d ? d.toLocaleDateString('pt-BR') : '-';
            return `<li>
                <span class="estoque-tipo">${r.tipo_sanguineo}</span>
                <span class="estoque-litros">${parseFloat(r.quantidade).toFixed(2)} L</span>
                <span style="font-size:11.5px;color:var(--hemo-text-3);margin-left:auto;">${fmt}</span>
            </li>`;
        }).join('');

        wrap.innerHTML = `<ul class="estoque-list">${items}</ul>`;
    } catch {
        wrap.innerHTML = '<p class="text-muted small">Erro ao carregar saídas.</p>';
    }
}

carregarUltimas();

// ── Form submit ──────────────────────────────────────────────
document.getElementById('saida').addEventListener('submit', async (e) => {
    e.preventDefault();

    if (!tipoInput.value) {
        showToast('Selecione o tipo sanguíneo.', 'error');
        return;
    }

    const form = new FormData(e.target);
    form.append('csrf_token', csrfToken);
    form.append('tipo', tipoInput.value);  // hidden input já incluído, mas garante

    const btn = e.target.querySelector('button[type="submit"]');
    btn.disabled = true;

    const res    = await fetch(BASE_URL + '/includes/actions/bolsas.php?action=saida', {
        method: 'POST', body: form,
    });
    const result = await res.json();

    btn.disabled = false;

    if (result.status === 'success') {
        showToast(result.message, 'success');
        e.target.reset();
        chips.forEach(c => c.classList.remove('selected'));
        tipoInput.value = '';
        carregarUltimas();
    } else {
        showToast(result.message, 'error');
    }
});
