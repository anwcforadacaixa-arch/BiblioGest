<?php if (!isset($_SESSION["id"])) { header("Location: /"); exit; } ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/assets/img/logo.png">
    <title>BiblioGest — Meu Perfil</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/tabelas.css">
    <link rel="stylesheet" href="/assets/css/utilizadores.css">
    <link rel="stylesheet" href="/assets/css/perfil.css">
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

            <h2>Meu Perfil</h2>
        </div>

        <!-- CABEÇALHO DO PERFIL -->
        <div class="perfil-header">
            <div class="perfil-foto-container">
                <img id="foto-perfil" src="/assets/img/avatar.png" alt="Foto">
                <label for="input-foto" class="btn-editar-foto-perfil">✏️</label>
                <input type="file" id="input-foto" accept="image/*" style="display:none;">
            </div>
            <div class="perfil-info">
                <h3 id="perfil-nome">—</h3>
                <p id="perfil-email">—</p>
                <p id="perfil-cargo">—</p>
            </div>
        </div>

        <!-- SEPARADORES -->
        <div class="separadores" style="margin-top:24px;">
            <button class="sep-btn activo" data-tab="dados">👤 Dados Pessoais</button>
            <button class="sep-btn" data-tab="senha">🔑 Alterar Senha</button>
            <button class="sep-btn" data-tab="historico">📋 Histórico de Acções</button>
        </div>

        <!-- TAB: DADOS PESSOAIS -->
        <div class="tab-content" id="tab-dados">
            <div class="perfil-form-card">
                <input type="hidden" id="foto-actual">

                <div class="campo">
                    <label>Nome Completo</label>
                    <input type="text" id="edit-nome" placeholder="Nome completo">
                </div>
                <div class="campo">
                    <label>Email</label>
                    <input type="email" id="edit-email" placeholder="email@unibelas.ao">
                </div>
                <div class="campo">
                    <label>Telefone</label>
                    <input type="text" id="edit-telefone" placeholder="Ex: 923000000">
                </div>
                <div class="campo">
                    <label>Género</label>
                    <select id="edit-genero">
                        <option value="">— Opcional —</option>
                        <option value="masculino">Masculino</option>
                        <option value="feminino">Feminino</option>
                        <option value="outro">Outro</option>
                    </select>
                </div>

                <div id="dados-erro"    class="erro escondido"></div>
                <div id="dados-sucesso" class="escondido"
                    style="background:#e8f5e9;color:#2e7d32;padding:10px;
                    border-radius:6px;font-size:13px;margin-bottom:12px;">
                </div>

                <button id="btn-guardar-dados">Guardar Alterações</button>
            </div>
        </div>

        <!-- TAB: ALTERAR SENHA -->
        <div class="tab-content escondido" id="tab-senha">
            <div class="perfil-form-card">
                <div class="campo">
                    <label>Senha Actual</label>
                    <input type="password" id="senha-actual" placeholder="••••••••">
                </div>
                <div class="campo">
                    <label>Nova Senha</label>
                    <input type="password" id="nova-senha" placeholder="Mínimo 6 caracteres">
                </div>
                <div class="campo">
                    <label>Confirmar Nova Senha</label>
                    <input type="password" id="confirmar-senha" placeholder="Repete a nova senha">
                </div>

                <div id="senha-erro"    class="erro escondido"></div>
                <div id="senha-sucesso" class="escondido"
                    style="background:#e8f5e9;color:#2e7d32;padding:10px;
                    border-radius:6px;font-size:13px;margin-bottom:12px;">
                </div>

                <button id="btn-alterar-senha">Alterar Senha</button>
            </div>
        </div>

        <!-- TAB: HISTÓRICO -->
        <div class="tab-content escondido" id="tab-historico">
            <div class="tabela-container">
                <table>
                    <thead>
                        <tr>
                            <th>Acção</th>
                            <th>Módulo</th>
                            <th>Descrição</th>
                            <th>Data e Hora</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-historico-perfil">
                        <tr><td colspan="4">A carregar...</td></tr>
                    </tbody>
                </table>
            </div>
            <div id="paginacao"></div>
        </div>

    </div>

    <script src="/assets/js/csrf.js"></script>
    <script src="/assets/js/paginator.js"></script>
    <script src="/assets/js/perfil.js"></script>
    <script src="/assets/js/sidebar.js"></script>
</body>
</html>