carregarAlunos();

document.getElementById("btn-novo").addEventListener("click", abrirModal);
document.getElementById("btn-cancelar").addEventListener("click", fecharModal);
document.getElementById("btn-guardar").addEventListener("click", guardarAluno);
document.getElementById("btn-fechar-senha").addEventListener("click", () => { document.getElementById("modal-senha").classList.add("escondido");});

// Preview de foto
document.getElementById("foto").addEventListener("change", function () {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById("foto-preview").src = e.target.result;
        reader.readAsDataURL(file);
    }
});

function carregarAlunos(pagina = 1) {
    fetch(`/api/alunos?pagina=${pagina}`)
        .then(r => r.json())
        .then(res => {
            const tbody  = document.getElementById("tabela-alunos");
            const alunos = res.dados;

            if (!alunos || alunos.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8"
                    style="text-align:center;color:#aaa;">
                    Nenhum aluno registado.</td></tr>`;
                renderPaginacao(null, "carregarAlunos");
                return;
            }

            tbody.innerHTML = alunos.map(a => `
                <tr>
                    <td>
                        <img class="foto-tabela"
                            src="${a.foto || '/assets/img/avatar.png'}"
                            alt="${a.nome}">
                    </td>
                    <td>${a.numero_estudante}</td>
                    <td>${a.nome}</td>
                    <td>${a.curso}</td>
                    <td>${a.turma || "—"}</td>
                    <td>${a.telefone || "—"}</td>
                    <td>
                        <span class="badge-status ${a.status || 'activo'}">
                            ${a.status || 'activo'}
                        </span>
                    </td>
                    <td>
                        <button class="btn-editar btn-edit" data-id="${a.id}">Editar</button>
                        <button class="btn-editar btn-senha" data-id="${a.id}" data-nome="${a.nome}"
                            style="background:#e3f2fd;color:#1565c0;">Senha</button>
                        <select class="btn-status" data-id="${a.id}"
                            style="padding:4px;border-radius:5px;font-size:12px;border:1px solid #ddd;">
                            <option value="">Status</option>
                            <option value="activo">Activar</option>
                            <option value="inactivo">Desactivar</option>
                            <option value="bloqueado">Bloquear</option>
                            <option value="suspenso">Suspender</option>
                        </select>
                        <button class="btn-apagar btn-del" data-id="${a.id}">Apagar</button>
                    </td>
                </tr>
            `).join("");

            window._alunos = alunos;

            document.querySelectorAll(".btn-edit").forEach(btn => {
                btn.addEventListener("click", function () {
                    const a = window._alunos.find(x => x.id == this.dataset.id);
                    editarAluno(a);
                });
            });

            document.querySelectorAll(".btn-status").forEach(select => {
                select.addEventListener("change", function () {
                    if (!this.value) return;
                    alterarStatus(this.dataset.id, this.value);
                    this.value = "";
                });
            });

            document.querySelectorAll(".btn-del").forEach(btn => {
                btn.addEventListener("click", function () {
                    apagarAluno(this.dataset.id);
                });
            });

            document.querySelectorAll(".btn-senha").forEach(btn => {
                btn.addEventListener("click", function () {
                    verOuRedefinirSenha(this.dataset.id, this.dataset.nome);
                });
            });

            renderPaginacao(res.meta, "carregarAlunos");
        });
}

function abrirModal() {
    document.getElementById("modal-titulo").textContent  = "Novo Aluno";
    document.getElementById("aluno-id").value            = "";
    document.getElementById("foto-actual").value         = "";
    document.getElementById("foto-preview").src          = "/assets/img/avatar.png";
    document.getElementById("numero_estudante").value    = "";
    document.getElementById("nome").value                = "";
    document.getElementById("curso").value               = "";
    document.getElementById("turma").value               = "";
    document.getElementById("telefone").value            = "";
    document.getElementById("email").value               = "";
    document.getElementById("foto").value                = "";
    document.getElementById("mensagem-erro").classList.add("escondido");
    document.getElementById("mensagem-senha").classList.add("escondido");
    document.getElementById("modal-overlay").classList.remove("escondido");
}

function fecharModal() {
    document.getElementById("modal-overlay").classList.add("escondido");
}

function editarAluno(a) {
    document.getElementById("modal-titulo").textContent  = "Editar Aluno";
    document.getElementById("aluno-id").value            = a.id;
    document.getElementById("foto-actual").value         = a.foto || "";
    document.getElementById("foto-preview").src          = a.foto || "/assets/img/avatar.png";
    document.getElementById("numero_estudante").value    = a.numero_estudante;
    document.getElementById("nome").value                = a.nome;
    document.getElementById("curso").value               = a.curso;
    document.getElementById("turma").value               = a.turma    || "";
    document.getElementById("telefone").value            = a.telefone || "";
    document.getElementById("email").value               = a.email    || "";
    document.getElementById("foto").value                = "";
    document.getElementById("mensagem-erro").classList.add("escondido");
    document.getElementById("mensagem-senha").classList.add("escondido");
    document.getElementById("modal-overlay").classList.remove("escondido");
}

function guardarAluno() {
    const id    = document.getElementById("aluno-id").value;
    const nome  = document.getElementById("nome").value.trim();
    const numero = document.getElementById("numero_estudante").value.trim();
    const curso  = document.getElementById("curso").value;

    if (!numero || !nome || !curso) {
        const msg = document.getElementById("mensagem-erro");
        msg.textContent = "Nº Estudante, Nome e Curso são obrigatórios.";
        msg.classList.remove("escondido");
        return;
    }

    const formData = new FormData();
    formData.append("numero_estudante", numero);
    formData.append("nome",             nome);
    formData.append("curso",            curso);
    formData.append("turma",            document.getElementById("turma").value);
    formData.append("telefone",         document.getElementById("telefone").value);
    formData.append("email",            document.getElementById("email").value);
    formData.append("foto_actual",      document.getElementById("foto-actual").value);

    const fotoInput = document.getElementById("foto");
    if (fotoInput.files[0]) {
        formData.append("foto", fotoInput.files[0]);
    }

    const url = id ? `/api/alunos/${id}` : "/api/alunos";

    fetch(url, {
        method: "POST",
        headers: { "X-CSRF-Token": getCsrfToken() },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) {
            if (data.dados?.senha_temporaria) {
                const msg = document.getElementById("mensagem-senha");
                msg.textContent = "✅ Aluno criado! Senha temporária: " + data.dados.senha_temporaria;
                msg.classList.remove("escondido");
                setTimeout(() => {
                    fecharModal();
                    carregarAlunos();
                }, 4000);
            } else {
                fecharModal();
                carregarAlunos();
            }
        } else {
            const msg = document.getElementById("mensagem-erro");
            msg.textContent = data.erro;
            msg.classList.remove("escondido");
        }
    });
}

function alterarStatus(id, status) {
    if (!confirm(`Tens a certeza que queres mudar o status para '${status}'?`)) return;
    postJson(`/api/alunos/${id}/status`, { status }).then(data => {
        if (data.sucesso) carregarAlunos();
    });
}

function apagarAluno(id) {
    if (!confirm("Tens a certeza que queres apagar este aluno?")) return;
    postJson(`/api/alunos/${id}/eliminar`, { id }).then(data => {
        if (data.sucesso) carregarAlunos();
    });
}

function verOuRedefinirSenha(id, nome) {
    if (!confirm(`Redefinir a senha do aluno ${nome}?\nA nova senha será: [número de estudante]@BG`)) return;

    postJson(`/api/alunos/${id}/redefinir-senha`, {}).then(data => {
        if (data.sucesso) {
            document.getElementById("nome-aluno-senha").textContent = nome;
            document.getElementById("senha-display").textContent    = data.dados.senha_temporaria;
            document.getElementById("modal-senha").classList.remove("escondido");
        }
    });
}