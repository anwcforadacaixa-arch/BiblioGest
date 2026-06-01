document.getElementById("btn-login").addEventListener("click", fazerLogin);

document.getElementById("senha").addEventListener("keypress", function (e) {
    if (e.key === "Enter") fazerLogin();
});

function fazerLogin() {
    const identificador = document.getElementById("identificador").value.trim();
    const senha         = document.getElementById("senha").value.trim();
    const mensagem      = document.getElementById("mensagem-erro");

    mensagem.classList.add("escondido");

    if (!identificador || !senha) {
        mensagem.textContent = "Por favor, preenche todos os campos.";
        mensagem.classList.remove("escondido");
        return;
    }

    fetch("/auth/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ identificador, senha })
    })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) {
            // Destino vem sempre do servidor — lógica centralizada
            window.location.href = data.dados.destino;
        } else {
            mensagem.textContent = data.erro;
            mensagem.classList.remove("escondido");
        }
    });
}