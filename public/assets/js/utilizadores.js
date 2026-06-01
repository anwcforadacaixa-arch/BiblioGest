carregarUtilizadores();

document.getElementById("btn-novo").addEventListener("click", abrirModal);
document.getElementById("btn-cancelar").addEventListener("click", fecharModal);
document.getElementById("btn-guardar").addEventListener("click", guardarUtilizador);

// Preview de foto
document.getElementById("foto").addEventListener("change", function () {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById("foto-preview").src = e.target.result;
        reader.readAsDataURL(file);
    }
});

function carregarUtilizadores() {
    fetch("/api/utilizadores")
        .then(r => r.json())
        .then(res => {
            const tbody = document.getElementById("tabela-utilizadores");
            const lista = res.dados || [];

            if (lista.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6"
                    style="text-align:center;color:#aaa;">
                    Nenhum bibliotecário registado.</td></tr>`;
                return;
            }

            tbody.innerHTML = lista.map(u => `
                <tr>
                    <td>
                        <img class="foto-tabela"
                            src="${u.foto || '/assets/img/avatar.png'}"
                            alt="${u.nome}">
                    </td>
                    <td>${u.nome}</td>
                    <td>${u.email}</td>
                    <td>${u.telefone || "—"}</td>
                    <td>
                        <span class="badge-status ${u.status}">${u.status}</span>
                    </td>
                    <td>
                        <button class="btn-editar btn-edit" data-id="${u.id}">Editar</button>
                        <button class="btn-editar btn-senha" data-id="${u.id}" 
                            style="background:#fff8e1;color:#f57f17;">Senha</button>
                        <select class="btn-status" data-id="${u.id}" 
                            style="padding:4px;border-radius:5px;font-size:12px;border:1px solid #ddd;">
                            <option value="">Status</option>
                            <option value="activo">Activar</option>
                            <option value="inactivo">Desactivar</option>
                            <option value="bloqueado">Bloquear</option>
                        </select>
                    </td>
                </tr>
            `).join("");

            window._utilizadores = lista;

            document.querySelectorAll(".btn-edit").forEach(btn => {
                btn.addEventListener("click", function () {
                    const u = window._utilizadores.find(x => x.id == this.dataset.id);
                    editarUtilizador(u);
                });
            });

            document.querySelectorAll(".btn-senha").forEach(btn => {
                btn.addEventListener("click", function () {
                    redefinirSenha(this.dataset.id);
                });
            });

            document.querySelectorAll(".btn-status").forEach(select => {
                select.addEventListener("change", function () {
                    if (!this.value) return;
                    alterarStatus(this.dataset.id, this.value);
                    this.value = "";
                });
            });
        });
}

function abrirModal() {
    document.getElementById("modal-titulo").textContent  = "Novo Bibliotecário";
    document.getElementById("utilizador-id").value       = "";
    document.getElementById("foto-actual").value         = "";
    document.getElementById("foto-preview").src          = "/assets/img/avatar.png";
    document.getElementById("nome").value                = "";
    document.getElementById("email").value               = "";
    document.getElementById("numero_identificacao").value = "";
    document.getElementById("telefone").value            = "";
    document.getElementById("genero").value              = "";
    document.getElementById("foto").value                = "";
    document.getElementById("mensagem-erro").classList.add("escondido");
    document.getElementById("mensagem-senha").classList.add("escondido");
    document.getElementById("modal-overlay").classList.remove("escondido");
}

function fecharModal() {
    document.getElementById("modal-overlay").classList.add("escondido");
}

function editarUtilizador(u) {
    document.getElementById("modal-titulo").textContent   = "Editar Bibliotecário";
    document.getElementById("utilizador-id").value        = u.id;
    document.getElementById("foto-actual").value          = u.foto || "";
    document.getElementById("foto-preview").src           = u.foto || "/assets/img/avatar.png";
    document.getElementById("nome").value                 = u.nome;
    document.getElementById("email").value                = u.email;
    document.getElementById("numero_identificacao").value = u.numero_identificacao || "";
    document.getElementById("telefone").value             = u.telefone || "";
    document.getElementById("genero").value               = u.genero  || "";
    document.getElementById("foto").value                 = "";
    document.getElementById("mensagem-erro").classList.add("escondido");
    document.getElementById("mensagem-senha").classList.add("escondido");
    document.getElementById("modal-overlay").classList.remove("escondido");
}

function guardarUtilizador() {
    const id   = document.getElementById("utilizador-id").value;
    const nome = document.getElementById("nome").value.trim();
    const email = document.getElementById("email").value.trim();

    if (!nome || !email) {
        const msg = document.getElementById("mensagem-erro");
        msg.textContent = "Nome e email são obrigatórios.";
        msg.classList.remove("escondido");
        return;
    }

    const formData = new FormData();
    formData.append("nome",                  nome);
    formData.append("email",                 email);
    formData.append("numero_identificacao",  document.getElementById("numero_identificacao").value);
    formData.append("telefone",              document.getElementById("telefone").value);
    formData.append("genero",                document.getElementById("genero").value);
    formData.append("foto_actual",           document.getElementById("foto-actual").value);

    const fotoInput = document.getElementById("foto");
    if (fotoInput.files[0]) {
        formData.append("foto", fotoInput.files[0]);
    }

    const url = id ? `/api/utilizadores/${id}` : "/api/utilizadores";

    fetch(url, {
        method: "POST",
        headers: { "X-CSRF-Token": getCsrfToken() },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) {
            if (data.dados?.senha_temporaria) {
                document.getElementById("mensagem-senha").textContent =
                    "✅ Bibliotecário criado! Senha temporária: " + data.dados.senha_temporaria;
                document.getElementById("mensagem-senha").classList.remove("escondido");
                setTimeout(() => {
                    fecharModal();
                    carregarUtilizadores();
                }, 4000);
            } else {
                fecharModal();
                carregarUtilizadores();
            }
        } else {
            const msg = document.getElementById("mensagem-erro");
            msg.textContent = data.erro;
            msg.classList.remove("escondido");
        }
    });
}

function redefinirSenha(id) {
    if (!confirm("Tens a certeza que queres redefinir a senha deste bibliotecário?")) return;

    postJson(`/api/utilizadores/${id}/redefinir-senha`, {}).then(data => {
        if (data.sucesso) {
            document.getElementById("senha-display").textContent = data.dados.senha_temporaria;
            document.getElementById("modal-senha").classList.remove("escondido");
        }
    });
}

function alterarStatus(id, status) {
    if (!confirm(`Tens a certeza que queres mudar o status para '${status}'?`)) return;

    postJson(`/api/utilizadores/${id}/status`, { status }).then(data => {
        if (data.sucesso) carregarUtilizadores();
    });
}