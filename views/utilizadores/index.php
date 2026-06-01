<?php if (!isset($_SESSION["id"])) { header("Location: /"); exit; } ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/assets/img/logo.png">
    <title>BiblioGest — Bibliotecários</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/tabelas.css">
    <link rel="stylesheet" href="/assets/css/utilizadores.css">
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

            <h2>Gestão de Bibliotecários</h2>
            <button id="btn-novo">+ Novo Bibliotecário</button>
        </div>

        <div class="tabela-container">
            <table>
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Status</th>
                        <th>Acções</th>
                    </tr>
                </thead>
                <tbody id="tabela-utilizadores">
                    <tr><td colspan="6">A carregar...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL CRIAR/EDITAR -->
    <div class="modal-overlay escondido" id="modal-overlay">
        <div class="modal">
            <h3 id="modal-titulo">Novo Bibliotecário</h3>
            <input type="hidden" id="utilizador-id">
            <input type="hidden" id="foto-actual">

            <!-- Preview de foto -->
            <div class="foto-preview-container">
                <img id="foto-preview" src="/assets/img/avatar.png" alt="Foto">
                <label for="foto" class="btn-foto">📷 Escolher foto</label>
                <input type="file" id="foto" accept="image/*" style="display:none;">
            </div>

            <div class="campo">
                <label>Nome Completo *</label>
                <input type="text" id="nome" placeholder="Nome do bibliotecário">
            </div>
            <div class="campo">
                <label>Email Institucional *</label>
                <input type="email" id="email" placeholder="email@bibliotecário.ao">
            </div>
            <div class="campo">
                <label>Nº Identificação</label>
                <input type="text" id="numero_identificacao" placeholder="Opcional">
            </div>
            <div class="campo">
                <label>Telefone</label>
                <input type="text" id="telefone" placeholder="Ex: 923000000">
            </div>
            <div class="campo">
                <label>Género</label>
                <select id="genero">
                    <option value="">— Opcional —</option>
                    <option value="masculino">Masculino</option>
                    <option value="feminino">Feminino</option>
                    <option value="outro">Outro</option>
                </select>
            </div>

            <div id="mensagem-erro" class="erro escondido"></div>
            <div id="mensagem-senha" class="senha-gerada escondido"></div>

            <div class="modal-botoes">
                <button class="btn-cancelar" id="btn-cancelar">Cancelar</button>
                <button id="btn-guardar">Guardar</button>
            </div>
        </div>
    </div>

    <!-- MODAL SENHA REDEFINIDA -->
    <div class="modal-overlay escondido" id="modal-senha">
        <div class="modal" style="text-align:center;">
            <h3>Senha Redefinida</h3>
            <p style="color:#666;margin:12px 0;">A nova senha temporária é:</p>
            <div class="senha-display" id="senha-display"></div>
            <p style="color:#888;font-size:12px;margin:12px 0;">
                Entregue esta senha ao bibliotecário.<br>
                No próximo login será obrigado a alterá-la.
            </p>
            <button onclick="document.getElementById('modal-senha').classList.add('escondido')">
                Fechar
            </button>
        </div>
    </div>

    <script src="/assets/js/csrf.js"></script>
    <script src="/assets/js/utilizadores.js"></script>
    <script src="/assets/js/sidebar.js"></script>
</body>
</html>