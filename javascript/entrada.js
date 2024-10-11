// Formatar os campos de data para date (sem ocultar o placeholder)
function formatDate(input) {
    // Remove caracteres não numéricos
    let value = input.value.replace(/\D/g, "");

    // Adiciona barras conforme a quantidade de caracteres
    if (value.length >= 5) {
        let day = value.slice(0, 2);
        let month = value.slice(2, 4);
        let year = value.slice(4, 8);

        // Validação do dia (1 a 31)
        if (parseInt(day) < 1 || parseInt(day) > 31) {
            day = "31"; // Se o dia estiver fora do intervalo, corrige para 31
        }

        // Validação do mês (1 a 12)
        if (parseInt(month) < 1 || parseInt(month) > 12) {
            month = "12"; // Se o mês estiver fora do intervalo, corrige para 12
        }

        input.value = `${day}/${month}/${year}`;
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