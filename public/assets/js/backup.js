carregarBackups();

document.getElementById("btn-criar-backup").addEventListener("click", function () {
    this.textContent  = "A criar...";
    this.disabled     = true;

    // Download directo
    window.location.href = "/api/backup/criar";

    // Recarregar lista após 3 segundos
    setTimeout(() => {
        this.textContent = "⬇ Criar Backup";
        this.disabled    = false;
        carregarBackups();
    }, 3000);
});

function carregarBackups() {
    fetch("/api/backup/listar")
        .then(r => r.json())
        .then(res => {
            const tbody   = document.getElementById("tabela-backups");
            const backups = res.dados || [];

            document.getElementById("total-backups").textContent =
                backups.length;
            document.getElementById("ultimo-backup").textContent =
                backups.length > 0 ? backups[0].data : "Nenhum";

            if (backups.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4"
                    style="text-align:center;color:#aaa;">
                    Nenhum backup encontrado.</td></tr>`;
                return;
            }

            tbody.innerHTML = backups.map(b => `
                <tr>
                    <td>${b.nome}</td>
                    <td>${b.tamanho}</td>
                    <td>${b.data}</td>
                    <td>
                        <button class="btn-editar btn-download" data-nome="${b.nome}">
                            Download
                        </button>
                        <button class="btn-apagar btn-eliminar-backup" data-nome="${b.nome}">
                            Apagar
                        </button>
                    </td>
                </tr>
            `).join("");

            document.querySelectorAll(".btn-download").forEach(btn => {
                btn.addEventListener("click", function () {
                    window.location.href = `/api/backup/download?ficheiro=${this.dataset.nome}`;
                });
            });

            document.querySelectorAll(".btn-eliminar-backup").forEach(btn => {
                btn.addEventListener("click", function () {
                    if (!confirm("Tens a certeza que queres apagar este backup?")) return;
                    eliminarBackup(this.dataset.nome);
                });
            });
        });
}

function eliminarBackup(nome) {
    postJson("/api/backup/eliminar", { ficheiro: nome }).then(data => {
        if (data.sucesso) carregarBackups();
    });
}