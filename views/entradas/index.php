<?php if (!isset($_SESSION["id"])) { header("Location: /"); exit; } ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/assets/img/logo.png">
    <title>BiblioGest — Entradas/Saídas</title>
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

            <h2>Entradas / Saídas</h2>
            <button id="btn-novo">+ Registar Movimento</button>
        </div>

        <!-- RESUMO DO DIA -->
        <div class="cards" style="margin-bottom: 24px;">
            <div class="card">
                <span class="card-icon">🚪</span>
                <div>
                    <p class="card-label">Entradas Hoje</p>
                    <p class="card-valor" id="total-entradas-hoje">—</p>
                </div>
            </div>
            <div class="card">
                <span class="card-icon">🚶</span>
                <div>
                    <p class="card-label">Saídas Hoje</p>
                    <p class="card-valor" id="total-saidas-hoje">—</p>
                </div>
            </div>
            <div class="card">
                <span class="card-icon">👥</span>
                <div>
                    <p class="card-label">Presentes Agora</p>
                    <p class="card-valor" id="total-presentes">—</p>
                </div>
            </div>
        </div>

        <div class="tabela-container">
            <table>
                <thead>
                    <tr>
                        <th>Aluno</th>
                        <th>Nº Estudante</th>
                        <th>Tipo</th>
                        <th>Data e Hora</th>
                        <th>Acções</th>
                    </tr>
                </thead>
                <tbody id="tabela-entradas">
                    <tr><td colspan="5">A carregar...</td></tr>
                </tbody>
            </table>
        </div>

        <div id="paginacao"></div>
        
    </div>

    <!-- MODAL -->
    <div class="modal-overlay escondido" id="modal-overlay">
        <div class="modal">
            <h3>Registar Movimento</h3>

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
                <label>Tipo de Movimento</label>
                <select id="tipo">
                    <option value="">— Seleccionar —</option>
                    <option value="entrada">🚪 Entrada</option>
                    <option value="saida">🚶 Saída</option>
                </select>
            </div>

            <div id="mensagem-erro" class="erro escondido"></div>

            <div class="modal-botoes">
                <button class="btn-cancelar" id="btn-cancelar">Cancelar</button>
                <button id="btn-guardar">Registar</button>
            </div>
        </div>
    </div>

    <script src="/assets/js/csrf.js"></script>
    <script src="/assets/js/paginator.js"></script>
    <script src="/assets/js/entradas.js"></script>
    <script src="/assets/js/sidebar.js"></script>
    
</body>
</html>