let estadoActual = "activo";
const hoje = new Date().toISOString().split("T")[0];

carregarContagens();
carregarEmprestimos();

document.getElementById("btn-novo").addEventListener("click", abrirModal);
document.getElementById("btn-cancelar").addEventListener("click", fecharModal);
document.getElementById("btn-guardar").addEventListener("click", guardarEmprestimo);
document.getElementById("btn-cancelar-dev").addEventListener("click", fecharModalDevolucao);
document.getElementById("btn-guardar-dev").addEventListener("click", confirmarDevolucao);
document.getElementById("data_emprestimo").value       = hoje;
document.getElementById("data_devolucao_prevista").min = hoje;

document.querySelectorAll(".sep-btn").forEach(btn => {
    btn.addEventListener("click", function () {
        document.querySelectorAll(".sep-btn").forEach(b => b.classList.remove("activo"));
        this.classList.add("activo");
        estadoActual = this.dataset.estado;
        carregarEmprestimos();
    });
});

const alunoBusca = document.getElementById("aluno-search");
const alunoLista = document.getElementById("aluno-lista");
const livroBusca = document.getElementById("livro-search");
const livroLista = document.getElementById("livro-lista");

alunoBusca.addEventListener("input", function () {
    const termo = this.value.trim();
    document.getElementById("aluno_id").value = "";
    if (termo.length < 2) { alunoLista.classList.add("escondido"); return; }

    fetch(`/api/alunos/pesquisar?termo=${encodeURIComponent(termo)}`)
        .then(r => r.json())
        .then(res => {
            const alunos = res.dados || [];
            alunoLista.innerHTML = "";
            if (alunos.length === 0) {
                alunoLista.innerHTML = `<div class="sem-resultados">Nenhum aluno encontrado.</div>`;
                alunoLista.classList.remove("escondido");
                return;
            }
            alunos.forEach(a => {
                const item = document.createElement("div");
                item.textContent = `${a.nome} — ${a.numero_estudante}`;
                item.addEventListener("click", function () {
                    alunoBusca.value = `${a.nome} (${a.numero_estudante})`;
                    document.getElementById("aluno_id").value = a.id;
                    alunoLista.classList.add("escondido");
                });
                alunoLista.appendChild(item);
            });
            alunoLista.classList.remove("escondido");
        });
});

livroBusca.addEventListener("input", function () {
    const termo = this.value.trim();
    document.getElementById("livro_id").value = "";
    if (termo.length < 2) { livroLista.classList.add("escondido"); return; }

    fetch(`/api/livros/pesquisar?termo=${encodeURIComponent(termo)}`)
        .then(r => r.json())
        .then(res => {
            const livros = res.dados || [];
            livroLista.innerHTML = "";
            if (livros.length === 0) {
                livroLista.innerHTML = `<div class="sem-resultados">Nenhum livro disponível.</div>`;
                livroLista.classList.remove("escondido");
                return;
            }
            livros.forEach(l => {
                const item = document.createElement("div");
                item.textContent = `${l.titulo} — ${l.autor} (${l.quantidade_disponivel} disponível)`;
                item.addEventListener("click", function () {
                    livroBusca.value = `${l.titulo} — ${l.autor}`;
                    document.getElementById("livro_id").value = l.id;
                    livroLista.classList.add("escondido");
                });
                livroLista.appendChild(item);
            });
            livroLista.classList.remove("escondido");
        });
});

document.addEventListener("click", function (e) {
    if (!alunoBusca.contains(e.target) && !alunoLista.contains(e.target))
        alunoLista.classList.add("escondido");
    if (!livroBusca.contains(e.target) && !livroLista.contains(e.target))
        livroLista.classList.add("escondido");
});

function carregarContagens() {
    fetch("/api/emprestimos/contar")
        .then(r => r.json())
        .then(res => {
            const d = res.dados;
            document.getElementById("count-activo").textContent    = d.activo;
            document.getElementById("count-atrasado").textContent  = d.atrasado;
            document.getElementById("count-devolvido").textContent = d.devolvido;
        });
}

function carregarEmprestimos(pagina = 1) {
    fetch(`/api/emprestimos?estado=${estadoActual}&pagina=${pagina}`)
        .then(r => r.json())
        .then(res => {
            const tbody       = document.getElementById("tabela-emprestimos");
            const emprestimos = res.dados || [];

            if (emprestimos.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7"
                    style="text-align:center;color:#aaa;">
                    Nenhum empréstimo encontrado.</td></tr>`;
                renderPaginacao(null, "carregarEmprestimos");
                return;
            }

            tbody.innerHTML = emprestimos.map(e => `
                <tr>
                    <td>${e.aluno}</td>
                    <td>${e.livro}</td>
                    <td>${formatarData(e.data_emprestimo)}</td>
                    <td>${formatarData(e.data_devolucao_prevista)}</td>
                    <td>${e.data_devolucao_real ? formatarData(e.data_devolucao_real) : "—"}</td>
                    <td><span class="badge ${e.estado}">${e.estado}</span></td>
                    <td>
                        ${(e.estado === "activo" || e.estado === "atrasado") ? `
                        <button class="btn-editar btn-devolver" data-id="${e.id}">
                            Devolver
                        </button>` : ""}
                    </td>
                </tr>
            `).join("");

            document.querySelectorAll(".btn-devolver").forEach(btn => {
                btn.addEventListener("click", function () {
                    abrirModalDevolucao(this.dataset.id);
                });
            });

            renderPaginacao(res.meta, "carregarEmprestimos");
        });
}

function abrirModal() {
    document.getElementById("aluno-search").value            = "";
    document.getElementById("aluno_id").value                = "";
    document.getElementById("livro-search").value            = "";
    document.getElementById("livro_id").value                = "";
    document.getElementById("data_devolucao_prevista").value = "";
    document.getElementById("data_devolucao_prevista").min   = hoje;
    document.getElementById("mensagem-erro").classList.add("escondido");
    alunoLista.classList.add("escondido");
    livroLista.classList.add("escondido");
    document.getElementById("modal-overlay").classList.remove("escondido");
}

function fecharModal() {
    document.getElementById("modal-overlay").classList.add("escondido");
}

function abrirModalDevolucao(id) {
    document.getElementById("emprestimo-id").value       = id;
    document.getElementById("data_devolucao_real").value = hoje;
    document.getElementById("mensagem-erro-dev").classList.add("escondido");
    document.getElementById("modal-devolucao").classList.remove("escondido");
}

function fecharModalDevolucao() {
    document.getElementById("modal-devolucao").classList.add("escondido");
}

function guardarEmprestimo() {
    const dados = {
        aluno_id:                document.getElementById("aluno_id").value,
        livro_id:                document.getElementById("livro_id").value,
        data_emprestimo:         document.getElementById("data_emprestimo").value,
        data_devolucao_prevista: document.getElementById("data_devolucao_prevista").value,
    };

    if (!dados.aluno_id || !dados.livro_id || !dados.data_devolucao_prevista) {
        const msg = document.getElementById("mensagem-erro");
        msg.textContent = "Todos os campos são obrigatórios.";
        msg.classList.remove("escondido");
        return;
    }

    postJson("/api/emprestimos", dados).then(data => {
        if (data.sucesso) {
            fecharModal();
            carregarEmprestimos();
            carregarContagens();
        } else {
            const msg = document.getElementById("mensagem-erro");
            msg.textContent = data.erro;
            msg.classList.remove("escondido");
        }
    });
}

function confirmarDevolucao() {
    const id    = document.getElementById("emprestimo-id").value;
    const dados = {
        data_devolucao_real: document.getElementById("data_devolucao_real").value,
    };

    if (!dados.data_devolucao_real) {
        const msg = document.getElementById("mensagem-erro-dev");
        msg.textContent = "Indica a data de devolução.";
        msg.classList.remove("escondido");
        return;
    }

    postJson(`/api/emprestimos/${id}/devolver`, dados).then(data => {
        if (data.sucesso) {
            fecharModalDevolucao();
            carregarEmprestimos();
            carregarContagens();
        }
    });
}

function formatarData(data) {
    if (!data) return "—";
    const [ano, mes, dia] = data.split("-");
    return `${dia}/${mes}/${ano}`;
}