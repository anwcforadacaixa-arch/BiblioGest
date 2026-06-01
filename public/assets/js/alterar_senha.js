document.getElementById("btn-alterar").addEventListener("click", function () {
    const novaSenha      = document.getElementById("nova_senha").value.trim();
    const confirmarSenha = document.getElementById("confirmar_senha").value.trim();
    const erro           = document.getElementById("mensagem-erro");
    const sucesso        = document.getElementById("mensagem-sucesso");

    erro.classList.add("escondido");
    sucesso.classList.add("escondido");



    if (!novaSenha || !confirmarSenha) {
        erro.textContent = "Preenche todos os campos.";
        erro.classList.remove("escondido");
        return;
    }

    if (novaSenha.length < 6) {
        erro.textContent = "A senha deve ter pelo menos 6 caracteres.";
        erro.classList.remove("escondido");
        return;
    }

    if (novaSenha !== confirmarSenha) {
        erro.textContent = "As senhas não coincidem.";
        erro.classList.remove("escondido");
        return;
    }

    fetch("/auth/alterar-senha", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ nova_senha: novaSenha })
    })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) {
            sucesso.textContent = "Senha alterada! A redirecionar...";
            sucesso.classList.remove("escondido");
            // Destino vem do servidor — lógica centralizada
            setTimeout(() => window.location.href = data.dados.destino, 2000);
        } else {
            erro.textContent = data.erro;
            erro.classList.remove("escondido");
        }
    });
});