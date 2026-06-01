// Mostrar botão hamburger no mobile
function verificarMobile() {
    const btnMenu = document.getElementById("btn-menu");
    if (btnMenu) {
        btnMenu.style.display = window.innerWidth <= 768 ? "flex" : "none";
    }
}

verificarMobile();
window.addEventListener("resize", verificarMobile);

// Toggle sidebar
const btnMenu  = document.getElementById("btn-menu");
const sidebar  = document.querySelector(".sidebar");
const overlay  = document.getElementById("sidebar-overlay");

if (btnMenu) {
    btnMenu.addEventListener("click", function () {
        sidebar.classList.toggle("aberta");
        overlay.classList.toggle("activo");
    });
}

if (overlay) {
    overlay.addEventListener("click", function () {
        sidebar.classList.remove("aberta");
        overlay.classList.remove("activo");
    });
}