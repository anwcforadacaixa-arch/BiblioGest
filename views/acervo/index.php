<?php if (!isset($_SESSION["id"])) { header("Location: /"); exit; } ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/assets/img/logo.png">
    <title>BiblioGest — Acervo</title>
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

            <h2>Gestão do Acervo</h2>
            <button id="btn-novo">+ Novo Livro</button>
        </div>

        <div class="tabela-container">
            <table>
                <thead>
                    <tr>
                        <th>Capa</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>ISBN</th>
                        <th>Categoria</th>
                        <th>Total</th>
                        <th>Disponível</th>
                        <th>Acções</th>
                    </tr>
                </thead>
                <tbody id="tabela-acervo">
                    <tr><td colspan="8">A carregar...</td></tr>
                </tbody>
            </table>
        </div>
        <div id="paginacao"></div>
    </div>

    <!-- MODAL -->
    <div class="modal-overlay escondido" id="modal-overlay">
        <div class="modal">
            <h3 id="modal-titulo">Novo Livro</h3>
            <input type="hidden" id="livro-id">
            <input type="hidden" id="capa-actual">

            <!-- Preview de capa -->
            <div class="foto-preview-container">
                <img id="capa-preview"
                    src="/assets/img/capa_padrao.png"
                    alt="Capa"
                    style="width:80px;height:110px;border-radius:4px;object-fit:cover;">
                <label for="capa" class="btn-foto">📷 Escolher capa</label>
                <input type="file" id="capa" accept="image/*" style="display:none;">
            </div>

            <div class="campo">
                <label>Título *</label>
                <input type="text" id="titulo" placeholder="Título do livro">
            </div>
            <div class="campo">
                <label>Autor *</label>
                <input type="text" id="autor" placeholder="Nome do autor">
            </div>
            <div class="campo">
                <label>ISBN</label>
                <input type="text" id="isbn" placeholder="Ex: 978-989-000-000-0">
            </div>
            <div class="campo">
                <label>Categoria</label>
                <select id="categoria">
                    <option value="">— Seleccionar categoria —</option>
                    <option value="Engenharia">Engenharia</option>
                    <option value="Informática">Informática</option>
                    <option value="Matemática">Matemática</option>
                    <option value="Literatura">Literatura</option>
                    <option value="Direito">Direito</option>
                    <option value="Medicina">Medicina</option>
                    <option value="Gestão">Gestão</option>
                    <option value="Psicologia">Psicologia</option>
                    <option value="Arquitectura">Arquitectura</option>
                    <option value="Outros">Outros</option>
                </select>
            </div>
            <div class="campo">
                <label>Quantidade Total *</label>
                <input type="number" id="quantidade_total" placeholder="Ex: 3" min="1">
            </div>

            <div id="mensagem-erro" class="erro escondido"></div>

            <div class="modal-botoes">
                <button class="btn-cancelar" id="btn-cancelar">Cancelar</button>
                <button id="btn-guardar">Guardar</button>
            </div>
        </div>
    </div>

    <script src="/assets/js/csrf.js"></script>
    <script src="/assets/js/paginator.js"></script>
    <script src="/assets/js/acervo.js"></script>
    <script src="/assets/js/sidebar.js"></script>
    
</body>
</html>