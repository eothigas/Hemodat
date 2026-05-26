// entrada.js — blood chips + estoque resumo

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

// ── Estoque resumo (painel direito) ─────────────────────────
async function carregarEstoque() {
    const wrap = document.getElementById('estoque-resumo');
    if (!wrap) return;
    try {
        const r   = await fetch(BASE_URL + '/includes/actions/bolsas.php?action=buscar_total');
        const raw = await r.json();

        // buscar_total returns {tipos_sanguineos:[...], quantidades:[...]}
        const tipos = raw.tipos_sanguineos ?? [];
        const qtds  = raw.quantidades      ?? [];
        const data  = tipos.map((t, i) => ({ tipo_sanguineo: t, quantidade: qtds[i] ?? 0 }));

        if (data.length === 0) {
            wrap.innerHTML = '<p class="text-muted small">Sem dados de estoque.</p>';
            return;
        }

        const max = Math.max(...data.map(d => parseFloat(d.quantidade) || 0), 1);

        const items = data.map(d => {
            const q   = parseFloat(d.quantidade) || 0;
            const pct = Math.round((q / max) * 100);
            const cls = q <= 0 ? '' : q < 1 ? 'warn' : 'ok';
            return `<li>
                <span class="estoque-tipo">${d.tipo_sanguineo}</span>
                <div class="estoque-bar-wrap">
                    <div class="estoque-bar ${cls}" style="width:${pct}%"></div>
                </div>
                <span class="estoque-litros">${q.toFixed(2)} L</span>
            </li>`;
        }).join('');

        wrap.innerHTML = `<ul class="estoque-list">${items}</ul>`;
    } catch {
        wrap.innerHTML = '<p class="text-muted small">Erro ao carregar estoque.</p>';
    }
}

carregarEstoque();

// ── Form submit ──────────────────────────────────────────────
document.getElementById('entrada').addEventListener('submit', async (e) => {
    e.preventDefault();

    if (!tipoInput.value) {
        showToast('Selecione o tipo sanguíneo.', 'error');
        return;
    }

    const form = new FormData(e.target);
    form.append('csrf_token', csrfToken);

    const btn = e.target.querySelector('button[type="submit"]');
    btn.disabled = true;

    const res    = await fetch(BASE_URL + '/includes/actions/bolsas.php?action=entrada', {
        method: 'POST', body: form,
    });
    const result = await res.json();

    btn.disabled = false;

    if (result.status === 'success') {
        showToast(result.message, 'success');
        e.target.reset();
        chips.forEach(c => c.classList.remove('selected'));
        tipoInput.value = '';
        carregarEstoque();
    } else {
        showToast(result.message, 'error');
    }
});
