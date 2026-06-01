<?php if (!isset($_SESSION["id"])) { header("Location: /"); exit; } ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/assets/img/logo.png">
    <title>BiblioGest — Solicitações</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/tabelas.css">
    <link rel="stylesheet" href="/assets/css/solicitacoes.css">
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

            <h2>Solicitações de Empréstimo</h2>

            <!-- Verificar por código -->
            <div class="verificar-codigo">
                <input type="text" id="input-codigo"
                    placeholder="Código de verificação..."
                    maxlength="8" style="text-transform:uppercase;">
                <button id="btn-verificar">Verificar</button>
            </div>
        </div>

        <!-- SEPARADORES -->
        <div class="separadores">
            <button class="sep-btn activo" data-estado="pendente">
                Pendentes <span class="sep-count" id="count-pendentes">0</span>
            </button>
            <button class="sep-btn" data-estado="aprovado">Aprovadas</button>
            <button class="sep-btn" data-estado="rejeitado">Rejeitadas</button>
            <button class="sep-btn" data-estado="expirado">Expiradas</button>
        </div>

        <div class="tabela-container">
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Aluno</th>
                        <th>Livro</th>
                        <th>Solicitado em</th>
                        <th>Válido até</th>
                        <th>Estado</th>
                        <th>Acções</th>
                    </tr>
                </thead>
                <tbody id="tabela-solicitacoes">
                    <tr><td colspan="7">A carregar...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL VERIFICAÇÃO -->
    <div class="modal-overlay escondido" id="modal-verificacao">
        <div class="modal" style="max-width:420px;">
            <h3>Verificação de Solicitação</h3>
            <div id="resultado-verificacao"></div>
            <div class="modal-botoes" id="botoes-verificacao" style="display:none;">
                <button class="btn-cancelar" id="btn-rejeitar-verif">Rejeitar</button>
                <button id="btn-aprovar-verif">✅ Aprovar e Criar Empréstimo</button>
            </div>
            <div class="modal-botoes">
                <button class="btn-cancelar"
                    onclick="document.getElementById('modal-verificacao')
                        .classList.add('escondido')">
                    Fechar
                </button>
            </div>
        </div>
    </div>

    <script src="/assets/js/csrf.js"></script>
    <script src="/assets/js/solicitacoes.js"></script>
    <script src="/assets/js/sidebar.js"></script>
    
</body>
</html>