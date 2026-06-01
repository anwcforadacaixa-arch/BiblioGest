<?php if (!isset($_SESSION["id"])) { header("Location: /"); exit; } ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/assets/img/logo.png">
    <title>BiblioGest — Empréstimos</title>
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

            <h2>Gestão de Empréstimos</h2>
            <button id="btn-novo">+ Novo Empréstimo</button>
        </div>

        <div class="separadores">
            <button class="sep-btn activo" data-estado="activo">
                Activos <span class="sep-count" id="count-activo">0</span>
            </button>
            <button class="sep-btn" data-estado="atrasado">
                Atrasados <span class="sep-count" id="count-atrasado">0</span>
            </button>
            <button class="sep-btn" data-estado="devolvido">
                Devolvidos hoje <span class="sep-count" id="count-devolvido">0</span>
            </button>
        </div>

        <div class="tabela-container">
            <table>
                <thead>
                    <tr>
                        <th>Aluno</th>
                        <th>Livro</th>
                        <th>Data Empréstimo</th>
                        <th>Devolução Prevista</th>
                        <th>Devolução Real</th>
                        <th>Estado</th>
                        <th>Acções</th>
                    </tr>
                </thead>
                <tbody id="tabela-emprestimos">
                    <tr><td colspan="7">A carregar...</td></tr>
                </tbody>
            </table>
        </div>

        <div id="paginacao"></div>
        
    </div>

    <!-- MODAL NOVO EMPRÉSTIMO -->
    <div class="modal-overlay escondido" id="modal-overlay">
        <div class="modal">
            <h3>Novo Empréstimo</h3>
            <div class="campo">
                <label>Aluno</label>
                <div class="autocomplete-wrapper">
                    <input type="text" id="aluno-search"
                        placeholder="Escreve o nome ou nº estudante..."
                        autocomplete="off">
                    <div class="autocomplete-lista escondido" id="aluno-lista"></div>
                </div>
                <input type="hidden" id="aluno_id">
            </div>
            <div class="campo">
                <label>Livro</label>
                <div class="autocomplete-wrapper">
                    <input type="text" id="livro-search"
                        placeholder="Escreve o título ou autor..."
                        autocomplete="off">
                    <div class="autocomplete-lista escondido" id="livro-lista"></div>
                </div>
                <input type="hidden" id="livro_id">
            </div>
            <div class="campo">
                <label>Data de Empréstimo</label>
                <input type="date" id="data_emprestimo">
            </div>
            <div class="campo">
                <label>Data de Devolução Prevista</label>
                <input type="date" id="data_devolucao_prevista">
            </div>
            <div id="mensagem-erro" class="erro escondido"></div>
            <div class="modal-botoes">
                <button class="btn-cancelar" id="btn-cancelar">Cancelar</button>
                <button id="btn-guardar">Guardar</button>
            </div>
        </div>
    </div>

    <!-- MODAL DEVOLUÇÃO -->
    <div class="modal-overlay escondido" id="modal-devolucao">
        <div class="modal">
            <h3>Registar Devolução</h3>
            <input type="hidden" id="emprestimo-id">
            <div class="campo">
                <label>Data de Devolução Real</label>
                <input type="date" id="data_devolucao_real">
            </div>
            <div id="mensagem-erro-dev" class="erro escondido"></div>
            <div class="modal-botoes">
                <button class="btn-cancelar" id="btn-cancelar-dev">Cancelar</button>
                <button id="btn-guardar-dev">Confirmar Devolução</button>
            </div>
        </div>
    </div>

    <script src="/assets/js/csrf.js"></script>
    <script src="/assets/js/paginator.js"></script>
    <script src="/assets/js/emprestimos.js"></script>
    <script src="/assets/js/sidebar.js"></script>
    
</body>
</html>