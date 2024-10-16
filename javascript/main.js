//  Exibir/ Ocultar o menu

document.getElementById("btn-home").addEventListener("click", function() {
    document.getElementById("btn-home").style.display = "none"; // Esconde o botão de lista
    
    // Exibe o ícone de fechar com transição
    const openIcon = document.querySelector(".open");
    openIcon.style.display = "inline";
    setTimeout(() => { openIcon.style.opacity = "1"; }, 0);

    // Exibe o conteúdo #list com transição
    const list = document.getElementById("list");
    list.style.display = "flex";

    // Mudar a justificação da nav
    const menUp = document.getElementById("menu-up");
    menUp.style.justifyContent = "left";

    // Oculta a logo, quando mostrar a list
    const logoExb = document.getElementById("logo");
    setTimeout(() => { logoExb.style.opacity = "0"; }, 10);
});

document.querySelector(".open").addEventListener("click", function() {
    const openIcon = document.querySelector(".open");
    
    // Esconde o ícone de fechar com transição
    openIcon.style.opacity = "0";
    setTimeout(() => {
        openIcon.style.display = "none";
        document.getElementById("btn-home").style.display = "inline";
    }, 100);

    // Oculta o conteúdo #list com transição
    const list = document.getElementById("list");
    setTimeout(() => { list.style.display = "none"; }, 100); // Tempo igual ao da transição
    
    // Mudar a justificação da nav
    const menUp = document.getElementById("menu-up");
    menUp.style.justifyContent = "space-between";

    // Exibe a logo, quando mostra a list
    const logoExb = document.getElementById("logo");
    setTimeout(() => { logoExb.style.opacity = "1"; }, 0);
});

