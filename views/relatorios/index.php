<?php if (!isset($_SESSION["id"])) { header("Location: /"); exit; } ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/assets/img/logo.png">
    <title>BiblioGest — Relatórios</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/tabelas.css">
    <link rel="stylesheet" href="/assets/css/relatorios.css">
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

            <h2>Relatórios</h2>
            <div style="display:flex;gap:10px;">
                <button id="btn-exportar-pdf"  class="btn-exportar pdf">⬇ PDF</button>
                <button id="btn-exportar-excel" class="btn-exportar excel">⬇ Excel</button>
            </div>
        </div>

        <!-- SEPARADORES -->
        <div class="separadores">
            <button class="sep-btn activo" data-relatorio="emprestimos-activos">
                Empréstimos Activos
            </button>
            <button class="sep-btn" data-relatorio="livros-atrasados">
                Livros Atrasados
            </button>
            <button class="sep-btn" data-relatorio="livros-disponiveis">
                Livros Disponíveis
            </button>
            <button class="sep-btn" data-relatorio="livros-mais-emprestados">
                Mais Emprestados
            </button>
            <button class="sep-btn" data-relatorio="alunos-mais-activos">
                Alunos Activos
            </button>
            <button class="sep-btn" data-relatorio="categorias">
                Categorias
            </button>
            <button class="sep-btn" data-relatorio="movimentacao-diaria">
                Movimentação Diária
            </button>
            <button class="sep-btn" data-relatorio="cadastros-mes">
                Cadastros do Mês
            </button>
            <button class="sep-btn" data-relatorio="historico-mensal">
                Histórico Mensal
            </button>
        </div>

        <!-- FILTROS DINÂMICOS -->
        <div id="area-filtros" class="filtros"></div>

        <!-- TABELA DINÂMICA -->
        <div class="tabela-container">
            <table>
                <thead id="tabela-cabecalho"></thead>
                <tbody id="tabela-relatorio">
                    <tr><td colspan="6">Selecciona um relatório.</td></tr>
                </tbody>
            </table>
        </div>

        <div id="paginacao"></div>
        
    </div>

    <script src="/assets/js/csrf.js"></script>
    <script src="/assets/js/paginator.js"></script>
    <script src="/assets/js/relatorios.js"></script>
    <script src="/assets/js/sidebar.js"></script>
</body>
</html>