<?php if (!isset($_SESSION["id"])) { header("Location: /"); exit; } ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/assets/img/logo.png">
    <title>BiblioGest — Histórico</title>
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

            <h2>Histórico</h2>
        </div>

        <div class="separadores">
            <button class="sep-btn activo" data-modulo="emprestimos">🔄 Empréstimos</button>
            <button class="sep-btn" data-modulo="entradas">🚪 Entradas/Saídas</button>
            <button class="sep-btn" data-modulo="alunos">👤 Alunos</button>
            <button class="sep-btn" data-modulo="acervo">📚 Acervo</button>
        </div>

        <div class="filtros" id="area-filtros">
            <input type="text" id="filtro-pesquisa" placeholder="🔍 Pesquisar...">
            <input type="date" id="filtro-data-inicio">
            <input type="date" id="filtro-data-fim">
            <select id="filtro-estado" class="escondido">
                <option value="">Todos os estados</option>
                <option value="activo">Activo</option>
                <option value="devolvido">Devolvido</option>
                <option value="atrasado">Atrasado</option>
            </select>
            <button id="btn-filtrar">Filtrar</button>
            <button id="btn-limpar" class="btn-cancelar">Limpar</button>
        </div>

        <div class="tabela-container">
            <table>
                <thead id="tabela-cabecalho"></thead>
                <tbody id="tabela-historico">
                    <tr><td colspan="7">A carregar...</td></tr>
                </tbody>
            </table>
        </div>

        <div id="paginacao"></div>

        
    </div>

    <script src="/assets/js/csrf.js"></script>
    <script src="/assets/js/paginator.js"></script>
    <script src="/assets/js/historico.js"></script>
    <script src="/assets/js/sidebar.js"></script>
    
</body>
</html>