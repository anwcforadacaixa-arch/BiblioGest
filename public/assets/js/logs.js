carregarLogs();

document.getElementById("btn-filtrar").addEventListener("click", carregarLogs);
document.getElementById("btn-limpar").addEventListener("click", function () {
    document.getElementById("filtro-utilizador").value  = "";
    document.getElementById("filtro-acao").value        = "";
    document.getElementById("filtro-modulo").value      = "";
    document.getElementById("filtro-data-inicio").value = "";
    document.getElementById("filtro-data-fim").value    = "";
    carregarLogs();
});

function carregarLogs(pagina = 1) {
    const utilizador = document.getElementById("filtro-utilizador").value.trim();
    const acao       = document.getElementById("filtro-acao").value;
    const modulo     = document.getElementById("filtro-modulo").value;
    const dataInicio = document.getElementById("filtro-data-inicio").value;
    const dataFim    = document.getElementById("filtro-data-fim").value;

    const params = new URLSearchParams();
    params.append("pagina", pagina);
    if (utilizador) params.append("utilizador",  utilizador);
    if (acao)       params.append("filtro_acao", acao);
    if (modulo)     params.append("modulo",      modulo);
    if (dataInicio) params.append("data_inicio", dataInicio);
    if (dataFim)    params.append("data_fim",    dataFim);

    fetch(`/api/logs?${params.toString()}`)
        .then(r => r.json())
        .then(res => {
            const tbody = document.getElementById("tabela-logs");
            const logs  = res.dados || [];

            if (logs.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6"
                    style="text-align:center;color:#aaa;">
                    Nenhum log encontrado.</td></tr>`;
                renderPaginacao(null, "carregarLogs");
                return;
            }

            tbody.innerHTML = logs.map(l => `
                <tr>
                    <td>${l.id}</td>
                    <td>${l.utilizador_nome}</td>
                    <td><span class="badge-log ${l.acao.toLowerCase()}">${l.acao}</span></td>
                    <td>${l.modulo}</td>
                    <td>${l.descricao}</td>
                    <td>${formatarDataHora(l.data_hora)}</td>
                </tr>
            `).join("");

            renderPaginacao(res.meta, "carregarLogs");
        });
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