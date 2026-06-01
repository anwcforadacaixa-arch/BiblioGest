<?php if (!isset($_SESSION["id"])) { header("Location: /"); exit; } ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/assets/img/logo.png">
    <title>BiblioGest — Backup</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/tabelas.css">
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/lucide.min.css">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <?= \App\Helpers\Csrf::metaTag() ?>
</head>
<body>

    <!-- Overlay mobile -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <?php include __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="main">
        <div class="topbar">

            <button class="btn-menu" id="btn-menu" style="display:none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>

            <h2>Backup do Sistema</h2>
            <button id="btn-criar-backup">⬇ Criar Backup</button>
        </div>

        <!-- INFO -->
        <div class="cards" style="margin-bottom:24px;">
            <div class="card">
                <span class="card-icon">💾</span>
                <div>
                    <p class="card-label">Total de Backups</p>
                    <p class="card-valor" id="total-backups">—</p>
                </div>
            </div>
            <div class="card">
                <span class="card-icon">🕐</span>
                <div>
                    <p class="card-label">Último Backup</p>
                    <p class="card-valor" style="font-size:16px;" id="ultimo-backup">—</p>
                </div>
            </div>
        </div>

        <!-- LISTA DE BACKUPS -->
        <div class="tabela-container">
            <table>
                <thead>
                    <tr>
                        <th>Nome do Ficheiro</th>
                        <th>Tamanho</th>
                        <th>Data</th>
                        <th>Acções</th>
                    </tr>
                </thead>
                <tbody id="tabela-backups">
                    <tr><td colspan="4">A carregar...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <script src="/assets/js/csrf.js"></script>
    <script src="/assets/js/backup.js"></script>
    <script src="/assets/js/sidebar.js"></script>
</body>
</html>