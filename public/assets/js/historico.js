let moduloActual = "emprestimos";

carregarModulo();

document.querySelectorAll(".sep-btn").forEach(btn => {
    btn.addEventListener("click", function () {
        document.querySelectorAll(".sep-btn").forEach(b => b.classList.remove("activo"));
        this.classList.add("activo");
        moduloActual = this.dataset.modulo;

        const filtroEstado = document.getElementById("filtro-estado");
        if (moduloActual === "emprestimos") {
            filtroEstado.classList.remove("escondido");
        } else {
            filtroEstado.classList.add("escondido");
            filtroEstado.value = "";
        }

        limparFiltros();
        carregarModulo();
    });
});

document.getElementById("btn-filtrar").addEventListener("click", carregarModulo);
document.getElementById("btn-limpar").addEventListener("click", function () {
    limparFiltros();
    carregarModulo();
});

function limparFiltros() {
    document.getElementById("filtro-pesquisa").value    = "";
    document.getElementById("filtro-data-inicio").value = "";
    document.getElementById("filtro-data-fim").value    = "";
    document.getElementById("filtro-estado").value      = "";
}

function carregarModulo(pagina = 1) {
    const pesquisa   = document.getElementById("filtro-pesquisa").value.trim();
    const dataInicio = document.getElementById("filtro-data-inicio").value;
    const dataFim    = document.getElementById("filtro-data-fim").value;
    const estado     = document.getElementById("filtro-estado").value;

    const params = new URLSearchParams();
    params.append("pagina", pagina);
    if (pesquisa)   params.append("pesquisa",    pesquisa);
    if (dataInicio) params.append("data_inicio", dataInicio);
    if (dataFim)    params.append("data_fim",    dataFim);
    if (estado)     params.append("estado",      estado);

    fetch(`/api/historico/${moduloActual}?${params.toString()}`)
        .then(r => r.json())
        .then(res => {
            renderTabela(res.dados || []);
            renderPaginacao(res.meta, "carregarModulo");
        });
}

function renderTabela(dados) {
    const cabecalho = document.getElementById("tabela-cabecalho");
    const tbody     = document.getElementById("tabela-historico");

    const cabecalhos = {
        emprestimos: ["#", "Aluno", "Livro", "Empréstimo", "Prev. Devolução", "Devolução Real", "Estado"],
        entradas:    ["#", "Aluno", "Nº Estudante", "Tipo", "Data e Hora"],
        alunos:      ["#", "Nº Estudante", "Nome", "Curso", "Turma", "Telefone", "Registado em"],
        acervo:      ["#", "Título", "Autor", "ISBN", "Categoria", "Total", "Disponível"],
    };

    const cols = cabecalhos[moduloActual];
    cabecalho.innerHTML = `<tr>${cols.map(c => `<th>${c}</th>`).join("")}</tr>`;

    if (!dados || dados.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${cols.length}"
            style="text-align:center;color:#aaa;">
            Nenhum registo encontrado.</td></tr>`;
        return;
    }

    const linhas = {
        emprestimos: d => `
            <td>${d.id}</td>
            <td>${d.aluno}</td>
            <td>${d.livro}</td>
            <td>${formatarData(d.data_emprestimo)}</td>
            <td>${formatarData(d.data_devolucao_prevista)}</td>
            <td>${d.data_devolucao_real ? formatarData(d.data_devolucao_real) : "—"}</td>
            <td><span class="badge ${d.estado}">${d.estado}</span></td>`,

        entradas: d => `
            <td>${d.id}</td>
            <td>${d.nome}</td>
            <td>${d.numero_estudante}</td>
            <td><span class="badge ${d.tipo}">
                ${d.tipo === "entrada" ? "🚪 Entrada" : "🚶 Saída"}
            </span></td>
            <td>${formatarDataHora(d.data_hora)}</td>`,

        alunos: d => `
            <td>${d.id}</td>
            <td>${d.numero_estudante}</td>
            <td>${d.nome}</td>
            <td>${d.curso}</td>
            <td>${d.turma || "—"}</td>
            <td>${d.telefone || "—"}</td>
            <td>${formatarData(d.criado_em)}</td>`,

        acervo: d => `
            <td>${d.id}</td>
            <td>${d.titulo}</td>
            <td>${d.autor}</td>
            <td>${d.isbn || "—"}</td>
            <td>${d.categoria || "—"}</td>
            <td>${d.quantidade_total}</td>
            <td>${d.quantidade_disponivel}</td>`,
    };

    tbody.innerHTML = dados.map(d =>
        `<tr>${linhas[moduloActual](d)}</tr>`
    ).join("");
}

function formatarData(data) {
    if (!data) return "—";
    const parte = data.split("T")[0];
    const [ano, mes, dia] = parte.split("-");
    return `${dia}/${mes}/${ano}`;
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