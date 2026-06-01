carregarDados();

// TABS
document.querySelectorAll(".sep-btn[data-tab]").forEach(btn => {
    btn.addEventListener("click", function () {
        document.querySelectorAll(".sep-btn[data-tab]").forEach(b => b.classList.remove("activo"));
        document.querySelectorAll(".tab-content").forEach(t => t.classList.add("escondido"));
        this.classList.add("activo");
        document.getElementById("tab-" + this.dataset.tab).classList.remove("escondido");

        if (this.dataset.tab === "historico") carregarHistorico();
    });
});

// FOTO
document.getElementById("input-foto").addEventListener("change", function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => document.getElementById("foto-perfil").src = e.target.result;
    reader.readAsDataURL(file);
    guardarDados();
});

// BOTÕES
document.getElementById("btn-guardar-dados").addEventListener("click", guardarDados);
document.getElementById("btn-alterar-senha").addEventListener("click", alterarSenha);

function carregarDados() {
    fetch("/api/perfil/dados")
        .then(r => r.json())
        .then(res => {
            if (!res.sucesso) return;
            const d = res.dados;

            document.getElementById("foto-perfil").src  = d.foto || "/assets/img/avatar.png";
            document.getElementById("perfil-nome").textContent   = d.nome;
            document.getElementById("perfil-email").textContent  = d.email || "—";
            document.getElementById("perfil-cargo").textContent  = ucfirst(d.perfil);

            document.getElementById("foto-actual").value  = d.foto || "";
            document.getElementById("edit-nome").value    = d.nome;
            document.getElementById("edit-email").value   = d.email    || "";
            document.getElementById("edit-telefone").value = d.telefone || "";
            document.getElementById("edit-genero").value  = d.genero   || "";
        });
}

function guardarDados() {
    const erro    = document.getElementById("dados-erro");
    const sucesso = document.getElementById("dados-sucesso");
    erro.classList.add("escondido");
    sucesso.classList.add("escondido");

    const formData = new FormData();
    formData.append("nome",       document.getElementById("edit-nome").value.trim());
    formData.append("email",      document.getElementById("edit-email").value.trim());
    formData.append("telefone",   document.getElementById("edit-telefone").value.trim());
    formData.append("genero",     document.getElementById("edit-genero").value);
    formData.append("foto_actual", document.getElementById("foto-actual").value);

    const fotoInput = document.getElementById("input-foto");
    if (fotoInput.files[0]) formData.append("foto", fotoInput.files[0]);

    fetch("/api/perfil/actualizar", {
        method: "POST",
        headers: { "X-CSRF-Token": getCsrfToken() },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) {
            sucesso.textContent = "✅ Perfil actualizado com sucesso!";
            sucesso.classList.remove("escondido");
            if (data.dados?.foto) {
                document.getElementById("foto-perfil").src = data.dados.foto;
                document.getElementById("foto-actual").value = data.dados.foto;
            }
            carregarDados();
        } else {
            erro.textContent = data.erro;
            erro.classList.remove("escondido");
        }
    });
}

function alterarSenha() {
    const erro    = document.getElementById("senha-erro");
    const sucesso = document.getElementById("senha-sucesso");
    erro.classList.add("escondido");
    sucesso.classList.add("escondido");

    const dados = {
        senha_actual: document.getElementById("senha-actual").value,
        nova_senha:   document.getElementById("nova-senha").value,
        confirmar:    document.getElementById("confirmar-senha").value,
    };

    postJson("/api/perfil/senha", dados).then(data => {
        if (data.sucesso) {
            sucesso.textContent = "✅ Senha alterada com sucesso!";
            sucesso.classList.remove("escondido");
            document.getElementById("senha-actual").value  = "";
            document.getElementById("nova-senha").value    = "";
            document.getElementById("confirmar-senha").value = "";
        } else {
            erro.textContent = data.erro;
            erro.classList.remove("escondido");
        }
    });
}

function carregarHistorico(pagina = 1) {
    fetch(`/api/perfil/historico?pagina=${pagina}`)
        .then(r => r.json())
        .then(res => {
            const tbody = document.getElementById("tabela-historico-perfil");
            const logs  = res.dados || [];

            if (logs.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4"
                    style="text-align:center;color:#aaa;">
                    Nenhuma acção registada.</td></tr>`;
                renderPaginacao(null, "carregarHistorico");
                return;
            }

            tbody.innerHTML = logs.map(l => `
                <tr>
                    <td>
                        <span class="badge-log ${l.acao.toLowerCase()}">${l.acao}</span>
                    </td>
                    <td>${l.modulo}</td>
                    <td>${l.descricao}</td>
                    <td>${formatarDataHora(l.data_hora)}</td>
                </tr>
            `).join("");

            renderPaginacao(res.meta, "carregarHistorico");
        });
}

function ucfirst(str) {
    if (!str) return "";
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function formatarDataHora(dataHora) {
    const d      = new Date(dataHora);
    const dia    = String(d.getDate()).padStart(2, "0");
    const mes    = String(d.getMonth() + 1).padStart(2, "0");
    const ano    = d.getFullYear();
    const hora   = String(d.getHours()).padStart(2, "0");
    const minuto = String(d.getMinutes()).padStart(2, "0");
    return `${dia}/${mes}/${ano} ${hora}:${minuto}`;
}