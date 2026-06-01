carregarEntradas();
carregarResumo();

document.getElementById("btn-novo").addEventListener("click", abrirModal);
document.getElementById("btn-cancelar").addEventListener("click", fecharModal);
document.getElementById("btn-guardar").addEventListener("click", registarMovimento);

const alunoBusca = document.getElementById("aluno-search");
const alunoLista = document.getElementById("aluno-lista");

alunoBusca.addEventListener("input", function () {
    const termo = this.value.trim();
    document.getElementById("aluno_id").value = "";
    if (termo.length < 2) { alunoLista.classList.add("escondido"); return; }

    fetch(`/api/entradas/pesquisar-alunos?termo=${encodeURIComponent(termo)}`)
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

document.addEventListener("click", function (e) {
    if (!alunoBusca.contains(e.target) && !alunoLista.contains(e.target)) {
        alunoLista.classList.add("escondido");
    }
});

function carregarResumo() {
    fetch("/api/entradas/resumo")
        .then(r => r.json())
        .then(res => {
            const d = res.dados;
            document.getElementById("total-entradas-hoje").textContent = d.entradas;
            document.getElementById("total-saidas-hoje").textContent   = d.saidas;
            document.getElementById("total-presentes").textContent     = d.presentes;
        });
}

function carregarEntradas(pagina = 1) {
    fetch(`/api/entradas?pagina=${pagina}`)
        .then(r => r.json())
        .then(res => {
            const tbody      = document.getElementById("tabela-entradas");
            const movimentos = res.dados || [];

            if (movimentos.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5"
                    style="text-align:center;color:#aaa;">
                    Nenhum movimento registado.</td></tr>`;
                renderPaginacao(null, "carregarEntradas");
                return;
            }

            tbody.innerHTML = movimentos.map(m => `
                <tr>
                    <td>${m.nome}</td>
                    <td>${m.numero_estudante}</td>
                    <td>
                        <span class="badge ${m.tipo}">
                            ${m.tipo === "entrada" ? "🚪 Entrada" : "🚶 Saída"}
                        </span>
                    </td>
                    <td>${formatarDataHora(m.data_hora)}</td>
                    <td>
                        <button class="btn-apagar btn-apagar-mov" data-id="${m.id}">
                            Apagar
                        </button>
                    </td>
                </tr>
            `).join("");

            document.querySelectorAll(".btn-apagar-mov").forEach(btn => {
                btn.addEventListener("click", function () {
                    eliminarMovimento(this.dataset.id);
                });
            });

            renderPaginacao(res.meta, "carregarEntradas");
        });
}

function abrirModal() {
    document.getElementById("aluno-search").value = "";
    document.getElementById("aluno_id").value     = "";
    document.getElementById("tipo").value         = "";
    document.getElementById("mensagem-erro").classList.add("escondido");
    alunoLista.classList.add("escondido");
    document.getElementById("modal-overlay").classList.remove("escondido");
}

function fecharModal() {
    document.getElementById("modal-overlay").classList.add("escondido");
}

function registarMovimento() {
    const dados = {
        aluno_id: document.getElementById("aluno_id").value,
        tipo:     document.getElementById("tipo").value,
    };

    if (!dados.aluno_id || !dados.tipo) {
        const msg = document.getElementById("mensagem-erro");
        msg.textContent = "Selecciona o aluno e o tipo de movimento.";
        msg.classList.remove("escondido");
        return;
    }

    postJson("/api/entradas", dados).then(data => {
        if (data.sucesso) {
            fecharModal();
            carregarEntradas();
            carregarResumo();
        } else {
            const msg = document.getElementById("mensagem-erro");
            msg.textContent = data.erro;
            msg.classList.remove("escondido");
        }
    });
}

function eliminarMovimento(id) {
    if (!confirm("Tens a certeza que queres apagar este registo?")) return;
    postJson(`/api/entradas/${id}/eliminar`, {}).then(data => {
        if (data.sucesso) {
            carregarEntradas();
            carregarResumo();
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