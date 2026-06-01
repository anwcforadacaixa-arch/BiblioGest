let estadoActual  = "pendente";
let solicitacaoId = null;

carregarSolicitacoes();
carregarContagem();

// Separadores
document.querySelectorAll(".sep-btn").forEach(btn => {
    btn.addEventListener("click", function () {
        document.querySelectorAll(".sep-btn").forEach(b => b.classList.remove("activo"));
        this.classList.add("activo");
        estadoActual = this.dataset.estado;
        carregarSolicitacoes();
    });
});

// Verificar código
document.getElementById("btn-verificar").addEventListener("click", verificarCodigo);
document.getElementById("input-codigo").addEventListener("keypress", function (e) {
    if (e.key === "Enter") verificarCodigo();
});

document.getElementById("btn-aprovar-verif").addEventListener("click", function () {
    if (!solicitacaoId) return;
    aprovar(solicitacaoId);
});

document.getElementById("btn-rejeitar-verif").addEventListener("click", function () {
    if (!solicitacaoId) return;
    rejeitar(solicitacaoId);
});

function carregarContagem() {
    fetch("/api/solicitacoes/contar")
        .then(r => r.json())
        .then(res => {
            document.getElementById("count-pendentes").textContent =
                res.dados?.pendentes || 0;
        });
}

function carregarSolicitacoes() {
    fetch(`/api/solicitacoes?estado=${estadoActual}`)
        .then(r => r.json())
        .then(res => {
            const tbody = document.getElementById("tabela-solicitacoes");
            const lista = res.dados || [];

            if (lista.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7"
                    style="text-align:center;color:#aaa;">
                    Nenhuma solicitação encontrada.</td></tr>`;
                return;
            }

            tbody.innerHTML = lista.map(s => `
                <tr>
                    <td><strong class="codigo-cell">${s.codigo}</strong></td>
                    <td>${s.aluno}<br>
                        <small style="color:#aaa;">${s.numero_estudante}</small>
                    </td>
                    <td>${s.livro}<br>
                        <small style="color:#aaa;">${s.autor}</small>
                    </td>
                    <td>${formatarDataHora(s.criado_em)}</td>
                    <td>${formatarDataHora(s.valido_ate)}</td>
                    <td><span class="badge ${s.estado}">${s.estado}</span></td>
                    <td>
                        ${s.estado === "pendente" ? `
                        <button class="btn-editar btn-apr" data-id="${s.id}">
                            Aprovar
                        </button>
                        <button class="btn-apagar btn-rej" data-id="${s.id}">
                            Rejeitar
                        </button>` : "—"}
                    </td>
                </tr>
            `).join("");

            document.querySelectorAll(".btn-apr").forEach(btn => {
                btn.addEventListener("click", function () {
                    aprovar(this.dataset.id);
                });
            });

            document.querySelectorAll(".btn-rej").forEach(btn => {
                btn.addEventListener("click", function () {
                    rejeitar(this.dataset.id);
                });
            });
        });
}

function verificarCodigo() {
    const codigo    = document.getElementById("input-codigo").value.trim().toUpperCase();
    const resultado = document.getElementById("resultado-verificacao");
    const botoes    = document.getElementById("botoes-verificacao");

    if (!codigo) return;

    fetch(`/api/solicitacoes/verificar?codigo=${codigo}`)
        .then(r => r.json())
        .then(res => {
            document.getElementById("modal-verificacao").classList.remove("escondido");

            if (!res.sucesso) {
                resultado.innerHTML = `<p style="color:#c62828;">${res.erro}</p>`;
                botoes.style.display = "none";
                return;
            }

            const s = res.dados;
            solicitacaoId = s.id;

            const disponivel = s.quantidade_disponivel > 0;
            const pendente   = s.estado === "pendente";

            resultado.innerHTML = `
                <div class="resultado-card">
                    <div class="codigo-grande">${s.codigo}</div>
                    <h4>📚 ${s.livro}</h4>
                    <p>✍️ ${s.autor}</p>
                    <p>👤 Aluno: <strong>${s.aluno}</strong> (${s.numero_estudante})</p>
                    <p>📅 Solicitado em: ${formatarDataHora(s.criado_em)}</p>
                    <p>⏰ Válido até: ${formatarDataHora(s.valido_ate)}</p>
                    <p>Estado: <span class="badge ${s.estado}">${s.estado}</span></p>
                    ${!disponivel ? `<p style="color:#c62828;">⚠️ Livro sem exemplares disponíveis.</p>` : ""}
                </div>
            `;

            botoes.style.display = pendente && disponivel ? "flex" : "none";
        });
}

function aprovar(id) {
    if (!confirm("Confirmas a aprovação desta solicitação?\nUm empréstimo será criado automaticamente.")) return;

    postJson(`/api/solicitacoes/${id}/aprovar`, {}).then(data => {
        if (data.sucesso) {
            document.getElementById("modal-verificacao").classList.add("escondido");
            document.getElementById("input-codigo").value = "";
            carregarSolicitacoes();
            carregarContagem();
            alert("✅ Solicitação aprovada! Empréstimo criado.");
        } else {
            alert("Erro: " + data.erro);
        }
    });
}

function rejeitar(id) {
    const motivo = prompt("Motivo da rejeição (opcional):");
    if (motivo === null) return;

    postJson(`/api/solicitacoes/${id}/rejeitar`, { observacao: motivo }).then(data => {
        if (data.sucesso) {
            document.getElementById("modal-verificacao").classList.add("escondido");
            document.getElementById("input-codigo").value = "";
            carregarSolicitacoes();
            carregarContagem();
        }
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