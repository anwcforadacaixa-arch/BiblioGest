let relatorioActual = "emprestimos-activos";

carregarRelatorio();

document.querySelectorAll(".sep-btn").forEach(btn => {
    btn.addEventListener("click", function () {
        document.querySelectorAll(".sep-btn").forEach(b => b.classList.remove("activo"));
        this.classList.add("activo");
        relatorioActual = this.dataset.relatorio;
        renderFiltros();
        carregarRelatorio();
    });
});

function renderFiltros() {
    const area = document.getElementById("area-filtros");

    if (relatorioActual === "movimentacao-diaria") {
        area.innerHTML = `
            <input type="date" id="filtro-data" value="${new Date().toISOString().split('T')[0]}">
            <button id="btn-filtrar">Filtrar</button>
        `;
        document.getElementById("btn-filtrar")
            .addEventListener("click", carregarRelatorio);
        return;
    }

    if (relatorioActual === "cadastros-mes") {
        const mes = new Date().toISOString().slice(0, 7);
        area.innerHTML = `
            <input type="month" id="filtro-mes" value="${mes}">
            <button id="btn-filtrar">Filtrar</button>
        `;
        document.getElementById("btn-filtrar")
            .addEventListener("click", carregarRelatorio);
        return;
    }

    area.innerHTML = "";
}

function carregarRelatorio() {
    const rotas = {
        "emprestimos-activos":      "/api/relatorios/emprestimos-activos",
        "livros-atrasados":         "/api/relatorios/livros-atrasados",
        "livros-disponiveis":       "/api/relatorios/livros-disponiveis",
        "livros-mais-emprestados":  "/api/relatorios/livros-mais-emprestados",
        "alunos-mais-activos":      "/api/relatorios/alunos-mais-activos",
        "categorias":               "/api/relatorios/categorias",
        "movimentacao-diaria":      "/api/relatorios/movimentacao-diaria",
        "cadastros-mes":            "/api/relatorios/cadastros-mes",
        "historico-mensal":         "/api/relatorios/historico-mensal",
    };

    let url = rotas[relatorioActual];

    if (relatorioActual === "movimentacao-diaria") {
        const data = document.getElementById("filtro-data")?.value || new Date().toISOString().split('T')[0];
        url += `?data=${data}`;
    }

    if (relatorioActual === "cadastros-mes") {
        const mes = document.getElementById("filtro-mes")?.value || new Date().toISOString().slice(0, 7);
        url += `?mes=${mes}`;
    }

    fetch(url)
        .then(r => r.json())
        .then(res => renderRelatorio(res.dados));
}

function renderRelatorio(dados) {
    const cabecalho = document.getElementById("tabela-cabecalho");
    const tbody     = document.getElementById("tabela-relatorio");

    // Relatórios especiais com estrutura diferente
    if (relatorioActual === "movimentacao-diaria") {
        renderMovimentacaoDiaria(dados, cabecalho, tbody);
        return;
    }

    if (relatorioActual === "cadastros-mes") {
        renderCadastrosMes(dados, cabecalho, tbody);
        return;
    }

    // Configuração das tabelas simples
    const config = {
        "emprestimos-activos": {
            cols:  ["Aluno", "Nº Estudante", "Livro", "Data Empréstimo", "Devolução Prevista", "Dias em Atraso"],
            linha: d => `
                <td>${d.aluno}</td>
                <td>${d.numero_estudante}</td>
                <td>${d.livro}</td>
                <td>${formatarData(d.data_emprestimo)}</td>
                <td>${formatarData(d.data_devolucao_prevista)}</td>
                <td>${d.dias_atraso > 0 ? 
                    `<span class="badge-atraso critico">${d.dias_atraso} dias</span>` : 
                    '<span class="badge-atraso normal">A tempo</span>'}</td>`
        },
        "livros-atrasados": {
            cols:  ["Aluno", "Nº Estudante", "Livro", "Data Empréstimo", "Devolução Prevista", "Dias em Atraso"],
            linha: d => `
                <td>${d.aluno}</td>
                <td>${d.numero_estudante}</td>
                <td>${d.livro}</td>
                <td>${formatarData(d.data_emprestimo)}</td>
                <td>${formatarData(d.data_devolucao_prevista)}</td>
                <td><span class="badge-atraso ${d.dias_atraso > 7 ? 'critico' : 'atencao'}">${d.dias_atraso} dias</span></td>`
        },
        "livros-disponiveis": {
            cols:  ["Título", "Autor", "Categoria", "Total", "Disponível", "Emprestados"],
            linha: d => `
                <td>${d.titulo}</td>
                <td>${d.autor}</td>
                <td>${d.categoria || "—"}</td>
                <td>${d.quantidade_total}</td>
                <td>${d.quantidade_disponivel}</td>
                <td>${d.emprestados}</td>`
        },
        "livros-mais-emprestados": {
            cols:  ["Título", "Autor", "Categoria", "Total de Empréstimos"],
            linha: d => `
                <td>${d.titulo}</td>
                <td>${d.autor}</td>
                <td>${d.categoria || "—"}</td>
                <td><strong>${d.total_emprestimos}</strong></td>`
        },
        "alunos-mais-activos": {
            cols:  ["Nome", "Nº Estudante", "Curso", "Total de Empréstimos"],
            linha: d => `
                <td>${d.nome}</td>
                <td>${d.numero_estudante}</td>
                <td>${d.curso}</td>
                <td><strong>${d.total_emprestimos}</strong></td>`
        },
        "categorias": {
            cols:  ["Categoria", "Total de Empréstimos"],
            linha: d => `
                <td>${d.categoria}</td>
                <td><strong>${d.total_emprestimos}</strong></td>`
        },
        "historico-mensal": {
            cols:  ["Mês", "Total", "Devolvidos", "Atrasados", "Activos"],
            linha: d => `
                <td>${d.mes}</td>
                <td><strong>${d.total}</strong></td>
                <td>${d.devolvidos}</td>
                <td>${d.atrasados}</td>
                <td>${d.activos}</td>`
        },
    };

    const c = config[relatorioActual];
    if (!c) return;

    cabecalho.innerHTML = `<tr>${c.cols.map(col => `<th>${col}</th>`).join("")}</tr>`;

    if (!dados || dados.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${c.cols.length}"
            style="text-align:center;color:#aaa;">
            Nenhum dado encontrado.</td></tr>`;
        return;
    }

    tbody.innerHTML = dados.map(d => `<tr>${c.linha(d)}</tr>`).join("");
}

function renderMovimentacaoDiaria(dados, cabecalho, tbody) {
    cabecalho.innerHTML = `<tr><th>Nome</th><th>Nº Estudante</th><th>Tipo</th><th>Hora</th></tr>`;

    if (!dados || (!dados.entradas?.length && !dados.emprestimos?.length)) {
        tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;color:#aaa;">
            Nenhum movimento neste dia.</td></tr>`;
        return;
    }

    let html = "";

    if (dados.entradas?.length) {
        html += `<tr><td colspan="4" class="relatorio-secao"><strong>Entradas/Saídas</strong></td></tr>`;
        html += dados.entradas.map(e => `
            <tr>
                <td>${e.nome}</td>
                <td>${e.numero_estudante}</td>
                <td><span class="badge ${e.tipo}">${e.tipo}</span></td>
                <td>${formatarDataHora(e.data_hora)}</td>
            </tr>`).join("");
    }

    if (dados.emprestimos?.length) {
        html += `<tr><td colspan="4" class="relatorio-secao"><strong>Empréstimos</strong></td></tr>`;
        html += dados.emprestimos.map(e => `
            <tr>
                <td>${e.aluno}</td>
                <td>—</td>
                <td>${e.livro}</td>
                <td>${formatarData(e.data_emprestimo)}</td>
            </tr>`).join("");
    }

    tbody.innerHTML = html;
}

function renderCadastrosMes(dados, cabecalho, tbody) {
    cabecalho.innerHTML = `<tr><th>Nome/Título</th><th>Detalhes</th><th>Data</th></tr>`;

    if (!dados || (!dados.alunos?.length && !dados.livros?.length)) {
        tbody.innerHTML = `<tr><td colspan="3" style="text-align:center;color:#aaa;">
            Nenhum cadastro neste mês.</td></tr>`;
        return;
    }

    let html = "";

    if (dados.alunos?.length) {
        html += `<tr><td colspan="3"><strong>Alunos Cadastrados</strong></td></tr>`;
        html += dados.alunos.map(a => `
            <tr>
                <td>${a.nome}</td>
                <td>${a.numero_estudante} — ${a.curso}</td>
                <td>${formatarData(a.criado_em)}</td>
            </tr>`).join("");
    }

    if (dados.livros?.length) {
        html += `<tr><td colspan="3"><strong>Livros Adicionados</strong></td></tr>`;
        html += dados.livros.map(l => `
            <tr>
                <td>${l.titulo}</td>
                <td>${l.autor} — ${l.categoria || "—"}</td>
                <td>${formatarData(l.criado_em)}</td>
            </tr>`).join("");
    }

    tbody.innerHTML = html;
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

// Relatórios que suportam exportação
const relatoriosExportaveis = [
    "emprestimos-activos",
    "livros-atrasados",
    "livros-disponiveis",
    "livros-mais-emprestados",
    "alunos-mais-activos",
    "historico-mensal"
];

document.getElementById("btn-exportar-pdf").addEventListener("click", function () {
    if (!relatoriosExportaveis.includes(relatorioActual)) {
        alert("Este relatório não suporta exportação.");
        return;
    }
    window.location.href = `/api/relatorios/exportar?tipo=pdf&relatorio=${relatorioActual}`;
});

document.getElementById("btn-exportar-excel").addEventListener("click", function () {
    if (!relatoriosExportaveis.includes(relatorioActual)) {
        alert("Este relatório não suporta exportação.");
        return;
    }
    window.location.href = `/api/relatorios/exportar?tipo=excel&relatorio=${relatorioActual}`;
});