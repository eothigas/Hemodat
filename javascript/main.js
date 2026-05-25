// main.js — Lógica compartilhada: menu hamburguer + utilitários de formulário

// ─── Menu ────────────────────────────────────────────────────────────────────

document.getElementById('btn-home').addEventListener('click', function () {
    document.getElementById('btn-home').style.display = 'none';

    const openIcon = document.querySelector('.open');
    openIcon.style.display = 'inline';
    setTimeout(() => { openIcon.style.opacity = '1'; }, 0);

    const list = document.getElementById('list');
    list.style.display = 'flex';
    setTimeout(() => { list.style.opacity = '1'; }, 10);

    document.getElementById('menu-up').style.justifyContent = 'left';

    const logo = document.getElementById('logo');
    setTimeout(() => { logo.style.opacity = '0'; }, 10);
});

document.querySelector('.open').addEventListener('click', function () {
    const openIcon = document.querySelector('.open');

    openIcon.style.opacity = '0';
    setTimeout(() => {
        openIcon.style.display = 'none';
        document.getElementById('btn-home').style.display = 'inline';
    }, 100);

    const list = document.getElementById('list');
    setTimeout(() => { list.style.opacity = '0'; }, 10);

    document.getElementById('menu-up').style.justifyContent = 'space-between';

    const logo = document.getElementById('logo');
    setTimeout(() => { logo.style.opacity = '1'; }, 0);
});

// ─── Utilitários de formulário (compartilhados com entrada.js e saida.js) ────

/**
 * Formata input de texto como data DD/MM/AAAA ao digitar.
 */
function formatDate(input) {
    let value = input.value.replace(/\D/g, '');

    if (value.length >= 5) {
        let day   = value.slice(0, 2);
        let month = value.slice(2, 4);
        let year  = value.slice(4, 8);

        if (parseInt(day, 10) < 1 || parseInt(day, 10) > 31)   day   = '01';
        if (parseInt(month, 10) < 1 || parseInt(month, 10) > 12) month = '01';

        input.value = `${day}/${month}/${year}`;
    } else if (value.length >= 3) {
        input.value = `${value.slice(0, 2)}/${value.slice(2, 4)}`;
    } else {
        input.value = value;
    }
}

/**
 * Limita o número de dígitos em um input numérico.
 */
function limitDigits(input, maxDigits) {
    if (input.value.length > maxDigits) {
        input.value = input.value.slice(0, maxDigits);
    }
}
