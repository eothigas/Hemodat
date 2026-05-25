// main.js - Utilitários de formulário compartilhados (Bootstrap gerencia o menu)

/**
 * Formata input de texto como data DD/MM/AAAA ao digitar.
 */
function formatDate(input) {
    let value = input.value.replace(/\D/g, '');

    if (value.length >= 5) {
        let day   = value.slice(0, 2);
        let month = value.slice(2, 4);
        let year  = value.slice(4, 8);

        if (parseInt(day,   10) < 1 || parseInt(day,   10) > 31) day   = '01';
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
