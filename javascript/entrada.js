// Formatar os campos de data para date (sem ocultar o placeholder)
function formatDate(input) {
    let value = input.value.replace(/\D/g, ""); // Remove caracteres não numéricos
    if (value.length >= 5) {
        input.value = `${value.slice(0, 2)}/${value.slice(2, 4)}/${value.slice(4, 8)}`;
    } else if (value.length >= 3) {
        input.value = `${value.slice(0, 2)}/${value.slice(2, 4)}`;
    } else {
        input.value = value;
    }
}

// Limitar o número de quantidade litros
function limitDigits(input, maxDigits) {
    if (input.value.length > maxDigits) {
        input.value = input.value.slice(0, maxDigits);
    }
}