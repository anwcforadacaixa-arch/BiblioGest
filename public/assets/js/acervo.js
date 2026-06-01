carregarAcervo();

document.getElementById("btn-novo").addEventListener("click", abrirModal);
document.getElementById("btn-cancelar").addEventListener("click", fecharModal);
document.getElementById("btn-guardar").addEventListener("click", guardarLivro);

// Preview de capa
document.getElementById("capa").addEventListener("change", function () {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById("capa-preview").src = e.target.result;
        reader.readAsDataURL(file);
    }
});

function carregarAcervo(pagina = 1) {
    fetch(`/api/livros?pagina=${pagina}`)
        .then(r => r.json())
        .then(res => {
            const tbody  = document.getElementById("tabela-acervo");
            const livros = res.dados;

            if (!livros || livros.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8"
                    style="text-align:center;color:#aaa;">
                    Nenhum livro registado.</td></tr>`;
                renderPaginacao(null, "carregarAcervo");
                return;
            }

            tbody.innerHTML = livros.map(l => `
                <tr>
                    <td>
                        <img src="${l.capa || '/assets/img/capa_padrao.png'}"
                            alt="${l.titulo}"
                            style="width:40px;height:55px;
                            object-fit:cover;border-radius:3px;">
                    </td>
                    <td><strong>${l.titulo}</strong></td>
                    <td>${l.autor}</td>
                    <td>${l.isbn || "—"}</td>
                    <td>${l.categoria || "—"}</td>
                    <td>${l.quantidade_total}</td>
                    <td>${l.quantidade_disponivel}</td>
                    <td>
                        <button class="btn-editar" data-id="${l.id}">Editar</button>
                        <button class="btn-apagar" data-id="${l.id}">Apagar</button>
                    </td>
                </tr>
            `).join("");

            window._livros = livros;

            document.querySelectorAll(".btn-editar").forEach(btn => {
                btn.addEventListener("click", function () {
                    const l = window._livros.find(x => x.id == this.dataset.id);
                    editarLivro(l);
                });
            });

            document.querySelectorAll(".btn-apagar").forEach(btn => {
                btn.addEventListener("click", function () {
                    apagarLivro(this.dataset.id);
                });
            });

            renderPaginacao(res.meta, "carregarAcervo");
        });
}

function abrirModal() {
    document.getElementById("modal-titulo").textContent = "Novo Livro";
    document.getElementById("livro-id").value           = "";
    document.getElementById("capa-actual").value        = "";
    document.getElementById("capa-preview").src         = "/assets/img/capa_padrao.png";
    document.getElementById("titulo").value             = "";
    document.getElementById("autor").value              = "";
    document.getElementById("isbn").value               = "";
    document.getElementById("categoria").value          = "";
    document.getElementById("quantidade_total").value   = "";
    document.getElementById("capa").value               = "";
    document.getElementById("mensagem-erro").classList.add("escondido");
    document.getElementById("modal-overlay").classList.remove("escondido");
}

function fecharModal() {
    document.getElementById("modal-overlay").classList.add("escondido");
}

function editarLivro(l) {
    document.getElementById("modal-titulo").textContent = "Editar Livro";
    document.getElementById("livro-id").value           = l.id;
    document.getElementById("capa-actual").value        = l.capa || "";
    document.getElementById("capa-preview").src         = l.capa || "/assets/img/capa_padrao.png";
    document.getElementById("titulo").value             = l.titulo;
    document.getElementById("autor").value              = l.autor;
    document.getElementById("isbn").value               = l.isbn      || "";
    document.getElementById("categoria").value          = l.categoria || "";
    document.getElementById("quantidade_total").value   = l.quantidade_total;
    document.getElementById("capa").value               = "";
    document.getElementById("mensagem-erro").classList.add("escondido");
    document.getElementById("modal-overlay").classList.remove("escondido");
}

function guardarLivro() {
    const id = document.getElementById("livro-id").value;

    const titulo    = document.getElementById("titulo").value.trim();
    const autor     = document.getElementById("autor").value.trim();
    const quantidade = document.getElementById("quantidade_total").value;

    if (!titulo || !autor || !quantidade) {
        const msg = document.getElementById("mensagem-erro");
        msg.textContent = "Título, Autor e Quantidade são obrigatórios.";
        msg.classList.remove("escondido");
        return;
    }

    const formData = new FormData();
    formData.append("titulo",           titulo);
    formData.append("autor",            autor);
    formData.append("isbn",             document.getElementById("isbn").value.trim());
    formData.append("categoria",        document.getElementById("categoria").value);
    formData.append("quantidade_total", quantidade);
    formData.append("capa_actual",      document.getElementById("capa-actual").value);

    const capaInput = document.getElementById("capa");
    if (capaInput.files[0]) {
        formData.append("capa", capaInput.files[0]);
    }

    const url = id ? `/api/livros/${id}` : "/api/livros";

    fetch(url, {
        method: "POST",
        headers: { "X-CSRF-Token": getCsrfToken() },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) {
            fecharModal();
            carregarAcervo();
        } else {
            const msg = document.getElementById("mensagem-erro");
            msg.textContent = data.erro;
            msg.classList.remove("escondido");
        }
    });
}

function apagarLivro(id) {
    if (!confirm("Tens a certeza que queres apagar este livro?")) return;
    postJson(`/api/livros/${id}/eliminar`, {}).then(data => {
        if (data.sucesso) carregarAcervo();
    });
}