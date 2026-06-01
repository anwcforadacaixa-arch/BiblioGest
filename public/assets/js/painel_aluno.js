carregarDados();

// Editar perfil
document.getElementById("btn-editar-perfil").addEventListener("click", function () {
    document.getElementById("perfil-form").classList.remove("escondido");
    this.closest(".aluno-perfil").style.marginBottom = "0";
});

document.getElementById("btn-cancelar-perfil").addEventListener("click", function () {
    document.getElementById("perfil-form").classList.add("escondido");
    document.getElementById("perfil-erro").classList.add("escondido");
    document.getElementById("perfil-sucesso").classList.add("escondido");
});

document.getElementById("btn-guardar-perfil").addEventListener("click", guardarPerfil);

// Foto de perfil
document.getElementById("foto-perfil").addEventListener("change", function () {
    const file = this.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = e => document.getElementById("aluno-foto").src = e.target.result;
    reader.readAsDataURL(file);

    // Guardar automaticamente a foto
    guardarPerfil();
});

function carregarDados() {
    fetch("/api/painel-aluno/dados")
        .then(r => r.json())
        .then(res => {
            if (!res.sucesso) return;
            const d = res.dados;

            // Perfil
            document.getElementById("nome-aluno").textContent   = d.aluno.nome;
            document.getElementById("aluno-nome").textContent   = d.aluno.nome;
            document.getElementById("aluno-curso").textContent  = d.aluno.curso + " — " + (d.aluno.turma || "");
            document.getElementById("aluno-numero").textContent = "Nº " + d.aluno.numero_estudante;
            document.getElementById("edit-telefone").value = d.aluno.telefone || "";

            if (d.aluno.foto) {
                document.getElementById("aluno-foto").src = d.aluno.foto;
            }

            // Empréstimos activos
            const tbodyEmp = document.getElementById("tabela-emprestimos-aluno");
            if (d.emprestimos.length === 0) {
                tbodyEmp.innerHTML = `<tr><td colspan="5"
                    style="text-align:center;color:#aaa;">
                    Nenhum livro em teu poder.</td></tr>`;
            } else {
                tbodyEmp.innerHTML = d.emprestimos.map(e => `
                    <tr>
                        <td><strong>${e.titulo}</strong></td>
                        <td>${e.autor}</td>
                        <td>${formatarData(e.data_emprestimo)}</td>
                        <td>${formatarData(e.data_devolucao_prevista)}
                            ${e.dias_restantes < 0
                                ? `<br><small style="color:#c62828;">
                                    ${Math.abs(e.dias_restantes)} dias em atraso</small>`
                                : e.dias_restantes <= 2
                                ? `<br><small style="color:#f57f17;">
                                    ${e.dias_restantes} dias restantes</small>`
                                : `<br><small style="color:#2e7d32;">
                                    ${e.dias_restantes} dias restantes</small>`
                            }
                        </td>
                        <td><span class="badge ${e.estado}">${e.estado}</span></td>
                    </tr>
                `).join("");
            }

            // Histórico
            const tbodyHist = document.getElementById("tabela-historico-aluno");
            if (d.historico.length === 0) {
                tbodyHist.innerHTML = `<tr><td colspan="5"
                    style="text-align:center;color:#aaa;">
                    Nenhum empréstimo no histórico.</td></tr>`;
            } else {
                tbodyHist.innerHTML = d.historico.map(e => `
                    <tr>
                        <td>${e.titulo}</td>
                        <td>${formatarData(e.data_emprestimo)}</td>
                        <td>${formatarData(e.data_devolucao_prevista)}</td>
                        <td>${e.data_devolucao_real
                            ? formatarData(e.data_devolucao_real) : "—"}</td>
                        <td><span class="badge ${e.estado}">${e.estado}</span></td>
                    </tr>
                `).join("");
            }
        });

    // Minhas solicitações
    fetch("/api/painel-aluno/solicitacoes")
        .then(r => r.json())
        .then(res => {
            const tbody = document.getElementById("tabela-solicitacoes-aluno");
            const lista = res.dados || [];

            if (lista.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5"
                    style="text-align:center;color:#aaa;">
                    Nenhuma solicitação.</td></tr>`;
                return;
            }

            tbody.innerHTML = lista.map(s => `
                <tr>
                    <td><strong style="letter-spacing:2px;">${s.codigo}</strong></td>
                    <td>${s.titulo}<br>
                        <small style="color:#aaa;">${s.autor}</small>
                    </td>
                    <td>${formatarData(s.criado_em)}</td>
                    <td>${formatarData(s.valido_ate)}</td>
                    <td><span class="badge ${s.estado}">${s.estado}</span></td>
                </tr>
            `).join("");
        });
}

function guardarPerfil() {
    const telefone = document.getElementById("edit-telefone").value.trim();
    const foto     = document.getElementById("foto-perfil").files[0];
    const erro     = document.getElementById("perfil-erro");
    const sucesso  = document.getElementById("perfil-sucesso");

    erro.classList.add("escondido");
    sucesso.classList.add("escondido");

    const formData = new FormData();
    formData.append("telefone", telefone);
    if (foto) formData.append("foto", foto);

    fetch("/api/painel-aluno/perfil", {
        method: "POST",
        headers: { "X-CSRF-Token": getCsrfToken() },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) {
            sucesso.textContent = "✅ Perfil actualizado com sucesso!";
            sucesso.classList.remove("escondido");

            // Actualizar foto no perfil
            if (data.dados?.foto) {
                document.getElementById("aluno-foto").src = data.dados.foto;
            }

            setTimeout(() => {
                document.getElementById("perfil-form").classList.add("escondido");
                carregarDados();
            }, 1500);
        } else {
            erro.textContent = data.erro;
            erro.classList.remove("escondido");
        }
    });
}

// ─── ACERVO ───────────────────────────────────────────────────
carregarCategorias();

document.getElementById("btn-pesquisar")
    .addEventListener("click", pesquisarAcervo);

document.getElementById("btn-limpar-pesquisa")
    .addEventListener("click", function () {
        document.getElementById("pesquisa-acervo").value  = "";
        document.getElementById("filtro-categoria").value = "";
        document.getElementById("container-acervo").style.display = "none";
    });

document.getElementById("pesquisa-acervo")
    .addEventListener("keypress", function (e) {
        if (e.key === "Enter") pesquisarAcervo();
    });

function carregarCategorias() {
    fetch("/api/painel-aluno/categorias")
        .then(r => r.json())
        .then(res => {
            const select = document.getElementById("filtro-categoria");
            (res.dados || []).forEach(cat => {
                select.innerHTML += `<option value="${cat}">${cat}</option>`;
            });
        });
}

function pesquisarAcervo() {
    const termo     = document.getElementById("pesquisa-acervo").value.trim();
    const categoria = document.getElementById("filtro-categoria").value;

    const params = new URLSearchParams();
    if (termo)     params.append("termo",     termo);
    if (categoria) params.append("categoria", categoria);

    fetch(`/api/painel-aluno/acervo?${params.toString()}`)
        .then(r => r.json())
        .then(res => {
            const tbody     = document.getElementById("tabela-acervo-aluno");
            const container = document.getElementById("container-acervo");
            const livros    = res.dados || [];

            container.style.display = "block";

            if (livros.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6"
                    style="text-align:center;color:#aaa;">
                    Nenhum livro encontrado.</td></tr>`;
                return;
            }

            tbody.innerHTML = livros.map(l => `
                <tr>
                    <td>
                        <img src="${l.capa || '/assets/img/capa_padrao.png'}"
                            alt="${l.titulo}"
                            style="width:45px;height:60px;object-fit:cover;border-radius:4px;">
                    </td>
                    <td><strong>${l.titulo}</strong></td>
                    <td>${l.autor}</td>
                    <td>${l.categoria || "—"}</td>
                    <td>${l.quantidade_disponivel} / ${l.quantidade_total}</td>
                    <td>
                        ${l.quantidade_disponivel > 0
                            ? `<button class="btn-solicitar btn-editar"
                                data-id="${l.id}"
                                data-titulo="${l.titulo}">
                                Solicitar
                              </button>`
                            : `<span class="badge atrasado">Indisponível</span>`
                        }
                    </td>
                </tr>
            `).join("");

            // ✅ DENTRO do .then() — botões já existem no DOM
            document.querySelectorAll(".btn-solicitar").forEach(btn => {
                btn.addEventListener("click", function () {
                    solicitarEmprestimo(this.dataset.id, this.dataset.titulo);
                });
            });
        });
}

function solicitarEmprestimo(livroId, titulo) {
    if (!confirm(`Confirmas a solicitação do livro:\n"${titulo}"?\n\nSerás notificado com um código de verificação.`)) return;

    postJson("/api/painel-aluno/solicitar", { livro_id: livroId })
        .then(data => {
            if (data.sucesso) {
                const d = data.dados;
                alert(
                    `✅ Solicitação enviada com sucesso!\n\n` +
                    `📖 Livro: ${d.livro}\n` +
                    `🔑 Código: ${d.codigo}\n` +
                    `⏰ Válido até: ${d.valido_ate}\n\n` +
                    `Apresenta este código na biblioteca para levantar o livro.`
                );
                carregarDados();
            } else {
                alert("Erro: " + data.erro);
            }
        });
}

function formatarData(data) {
    if (!data) return "—";
    const parte = data.split("T")[0];
    const [ano, mes, dia] = parte.split("-");
    return `${dia}/${mes}/${ano}`;
}