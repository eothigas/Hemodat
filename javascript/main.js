//  Exibir/ Ocultar o menu

document.getElementById("btn-home").addEventListener("click", function() {
    document.getElementById("btn-home").style.display = "none"; // Esconde o botão de lista
    
    // Exibe o ícone de fechar com transição
    const openIcon = document.querySelector(".open");
    openIcon.style.display = "inline";
    setTimeout(() => { openIcon.style.opacity = "1"; }, 10);

    // Exibe o conteúdo #list com transição
    const list = document.getElementById("list");
    list.style.display = "block";
    setTimeout(() => {
        list.style.opacity = "1";
    }, 10);
});

document.querySelector(".open").addEventListener("click", function() {
    const openIcon = document.querySelector(".open");
    
    // Esconde o ícone de fechar com transição
    openIcon.style.opacity = "0";
    setTimeout(() => {
        openIcon.style.display = "none";
        document.getElementById("btn-home").style.display = "inline";
    }, 200);

    // Oculta o conteúdo #list com transição
    const list = document.getElementById("list");
    list.style.opacity = "0";
    setTimeout(() => { list.style.display = "none"; }, 200); // Tempo igual ao da transição
});

