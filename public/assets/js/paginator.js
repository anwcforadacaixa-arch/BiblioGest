function renderPaginacao(meta, callbackCarregar) {
    const container = document.getElementById("paginacao");
    if (!container) return;

    if (!meta || meta.total_paginas <= 1) {

        container.innerHTML = "";
        return;

    }

    let html = '<div class="paginacao">';

    if (meta.tem_anterior) {

        html += `<button onclick="${callbackCarregar}(${meta.pagina_actual - 1})">← Anterior</button>`;

    }

    html += `<span>Página ${meta.pagina_actual} de ${meta.total_paginas} (${meta.total} registos)</span>`;

    if (meta.tem_proxima) {

        html += `<button onclick="${callbackCarregar}(${meta.pagina_actual + 1})">Próxima →</button>`;

    }

    html += '</div>';
    container.innerHTML = html;
    
}