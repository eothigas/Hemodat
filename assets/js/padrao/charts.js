// charts.js - SVG charts leves do design system HEMODAT (sem libs externas).
// Porta direta de charts.jsx (mockup React) para JS puro, gerando strings SVG.

const HemoCharts = (function () {

    function niceMax(max) {
        if (max <= 0) return 10;
        const pow = Math.pow(10, Math.floor(Math.log10(max)));
        const norm = max / pow;
        const nice = norm <= 1 ? 1 : norm <= 2 ? 2 : norm <= 5 ? 5 : 10;
        return nice * pow;
    }

    // ── Barra horizontal (estoque por tipo) ─────────────────────
    function hbar(data, { height = 240, color = 'var(--brand)' } = {}) {
        const w = 600, h = height;
        const padL = 60, padR = 60, padT = 8, padB = 8;
        const innerW = w - padL - padR;
        const max = Math.max(1, ...data.map(d => d.value));
        const rowH = (h - padT - padB) / Math.max(1, data.length);

        const rows = data.map((d, i) => {
            const y  = padT + i * rowH;
            const bh = Math.min(rowH - 8, 18);
            const by = y + (rowH - bh) / 2;
            const bw = (d.value / max) * innerW;
            const low = d.value / max < 0.25;
            return `
                <text class="ax-tick" x="${padL - 10}" y="${by + bh / 2 + 4}" text-anchor="end" style="font-weight:600;font-size:11px;">${d.label}</text>
                <rect x="${padL}" y="${by}" width="${innerW}" height="${bh}" rx="4" fill="var(--surface-2)"/>
                <rect x="${padL}" y="${by}" width="${bw}" height="${bh}" rx="4" fill="${low ? 'var(--amber-500)' : color}"><title>${d.label}: ${d.value}</title></rect>
                <text class="ax-tick" x="${padL + bw + 8}" y="${by + bh / 2 + 4}" style="font-weight:600;">${d.value}</text>
            `;
        }).join('');

        return `<svg class="chart" viewBox="0 0 ${w} ${h}" preserveAspectRatio="none">${rows}</svg>`;
    }

    // ── Linha (série temporal, multi-key) ───────────────────────
    function lineChart(data, keys, { colors = ['var(--brand)', 'var(--blue-600)'], height = 260 } = {}) {
        const w = 700, h = height;
        const padL = 40, padR = 16, padT = 16, padB = 32;
        const innerW = w - padL - padR;
        const innerH = h - padT - padB;
        const maxV = niceMax(Math.max(1, ...data.flatMap(d => keys.map(k => d[k] || 0))));
        const ticks = 4;
        const tickVals = Array.from({ length: ticks + 1 }, (_, i) => (maxV / ticks) * i);
        const xAt = i => padL + (i / Math.max(1, data.length - 1)) * innerW;
        const yAt = v => padT + innerH - (v / maxV) * innerH;

        const lineFor = key => data.map((d, i) => `${i === 0 ? 'M' : 'L'} ${xAt(i)} ${yAt(d[key] || 0)}`).join(' ');
        const areaFor = key => {
            const start = `M ${xAt(0)} ${yAt(0)}`;
            const top   = data.map((d, i) => `L ${xAt(i)} ${yAt(d[key] || 0)}`).join(' ');
            const end   = `L ${xAt(data.length - 1)} ${yAt(0)} Z`;
            return `${start} ${top} ${end}`;
        };

        const step = data.length > 12 ? Math.ceil(data.length / 8) : 1;

        const grid = tickVals.map(v => {
            const y = yAt(v);
            return `<line class="grid-line" x1="${padL}" x2="${w - padR}" y1="${y}" y2="${y}"/>
                    <text class="ax-tick" x="${padL - 6}" y="${y + 3}" text-anchor="end">${Math.round(v)}</text>`;
        }).join('');

        const areas = keys.map((k, j) => `<path d="${areaFor(k)}" fill="${colors[j % colors.length]}" class="area"/>`).join('');
        const lines = keys.map((k, j) => `<path d="${lineFor(k)}" stroke="${colors[j % colors.length]}" stroke-width="2" fill="none" stroke-linejoin="round" stroke-linecap="round"/>`).join('');
        const labels = data.map((d, i) => i % step === 0
            ? `<text class="ax-tick" x="${xAt(i)}" y="${h - padB + 16}" text-anchor="middle">${d.label}</text>` : '').join('');
        const dots = keys.map((k, j) => {
            const last = data[data.length - 1] || {};
            return `<circle cx="${xAt(data.length - 1)}" cy="${yAt(last[k] || 0)}" r="3" fill="${colors[j % colors.length]}" stroke="var(--surface)" stroke-width="2"/>`;
        }).join('');

        return `<svg class="chart" viewBox="0 0 ${w} ${h}" preserveAspectRatio="none">${grid}${areas}${lines}${labels}${dots}</svg>`;
    }

    // ── Donut ────────────────────────────────────────────────────
    function donut(data, { size = 160, thickness = 22, centerHtml = '' } = {}) {
        const total = Math.max(1, data.reduce((s, d) => s + d.value, 0));
        const r = size / 2 - thickness / 2;
        const c = size / 2;
        let acc = 0;
        const circles = data.map(d => {
            const frac   = d.value / total;
            const dash   = 2 * Math.PI * r * frac;
            const gap    = 2 * Math.PI * r - dash;
            const offset = -2 * Math.PI * r * (acc / total);
            acc += d.value;
            return `<circle cx="${c}" cy="${c}" r="${r}" stroke="${d.color}" stroke-width="${thickness}" fill="none"
                        stroke-dasharray="${dash} ${gap}" stroke-dashoffset="${offset}"
                        transform="rotate(-90 ${c} ${c})" stroke-linecap="butt"><title>${d.label}: ${d.value}</title></circle>`;
        }).join('');

        return `<div style="position:relative; width:${size}px; height:${size}px;">
            <svg viewBox="0 0 ${size} ${size}" width="${size}" height="${size}" aria-hidden="true">
                <circle cx="${c}" cy="${c}" r="${r}" stroke="var(--surface-2)" stroke-width="${thickness}" fill="none"/>
                ${circles}
            </svg>
            <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; flex-direction:column;">${centerHtml}</div>
        </div>`;
    }

    // ── Sparkline ────────────────────────────────────────────────
    function sparkline(data, { color = 'var(--brand)', height = 36 } = {}) {
        if (!data || !data.length) return '';
        const w = 200, h = height;
        const min = Math.min(...data), max = Math.max(...data);
        const range = (max - min) || 1;
        const xAt = i => (i / Math.max(1, data.length - 1)) * w;
        const yAt = v => h - ((v - min) / range) * (h - 4) - 2;
        const path = data.map((v, i) => `${i === 0 ? 'M' : 'L'} ${xAt(i)} ${yAt(v)}`).join(' ');
        const area = `${path} L ${xAt(data.length - 1)} ${h} L 0 ${h} Z`;
        return `<svg viewBox="0 0 ${w} ${h}" preserveAspectRatio="none" style="width:100%;height:${h}px;" aria-hidden="true">
            <path d="${area}" fill="${color}" opacity="0.12"/>
            <path d="${path}" fill="none" stroke="${color}" stroke-width="1.6" stroke-linejoin="round" stroke-linecap="round"/>
        </svg>`;
    }

    // ── Legend ───────────────────────────────────────────────────
    function legend(items) {
        return `<div class="row" style="gap:16px; flex-wrap:wrap;">${items.map(it => `
            <span class="row" style="gap:6px;">
                <span style="width:10px;height:10px;border-radius:3px;background:${it.color};display:inline-block;"></span>
                <span class="small muted" style="font-weight:500;">${it.label}</span>
                ${it.value != null ? `<span class="small tnum" style="font-weight:600;">${it.value}</span>` : ''}
            </span>`).join('')}</div>`;
    }

    return { hbar, lineChart, donut, sparkline, legend };
})();
