<?php if (!isset($_SESSION["id"])) { header("Location: /"); exit; } ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/assets/img/logo.png">
    <title>BiblioGest — Painel do Aluno</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="stylesheet" href="/assets/css/tabelas.css">
    <link rel="stylesheet" href="/assets/css/painel_aluno.css">
    <?= \App\Helpers\Csrf::metaTag() ?>
</head>
<body>

    <!-- Overlay mobile -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- SIDEBAR DO ALUNO -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-placeholder">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                    viewBox="0 0 24 24" fill="none" stroke="white"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
            </div>
            <div class="sidebar-logo-text">
                <strong>BiblioGest</strong>
                <small>Gestão de Biblioteca</small>
            </div>
        </div>
        <nav>
            <span class="sidebar-section-label">Menu</span>
            <a href="/painel-aluno" class="activo">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"/>
                    <rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/>
                    <rect x="3" y="14" width="7" height="7"/>
                </svg>
                Meu Painel
            </a>
            <a href="/auth/alterar-senha">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                Alterar Senha
            </a>
        </nav>
        <div class="sidebar-footer">
            <a class="perfil-link">
                <img id="sidebar-foto-aluno"
                    src="/assets/img/avatar.png" alt="Foto"
                    style="width:34px;height:34px;border-radius:50%;
                    object-fit:cover;border:2px solid rgba(255,255,255,0.2);">
                <div class="perfil-link-info">
                    <span id="nome-aluno">—</span>
                    <small>Aluno</small>
                </div>
            </a>
            <a href="/auth/logout">Sair</a>
        </div>
    </div>

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

            <h2>Meu Painel</h2>
        </div>

        <div class="dashboard-scroll">

            <!-- PERFIL DO ALUNO -->
            <div class="aluno-perfil" id="aluno-perfil">
                <div style="position:relative;">
                    <img id="aluno-foto" src="/assets/img/avatar.png" alt="Foto">
                    <label for="foto-perfil" class="btn-editar-foto" title="Alterar foto">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                            viewBox="0 0 24 24" fill="none" stroke="white"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </label>
                    <input type="file" id="foto-perfil" accept="image/*" style="display:none;">
                </div>
                <div style="flex:1;">
                    <h3 id="aluno-nome">—</h3>
                    <p id="aluno-curso">—</p>
                    <p id="aluno-numero">—</p>
                </div>
                <div class="perfil-acoes">
                    <button id="btn-editar-perfil">Editar Perfil</button>
                </div>
            </div>

            <!-- FORMULÁRIO EDIÇÃO -->
            <div class="perfil-form escondido" id="perfil-form">
                <h3>Editar Perfil</h3>
                <div class="campo">
                    <label>Telefone</label>
                    <input type="text" id="edit-telefone" placeholder="Ex: 923000000">
                </div>
                <div id="perfil-erro" class="erro escondido"></div>
                <div id="perfil-sucesso" class="escondido"
                    style="background:#d1fae5;color:#065f46;padding:10px;
                    border-radius:8px;font-size:13px;margin-bottom:12px;">
                </div>
                <div class="modal-botoes">
                    <button class="btn-cancelar" id="btn-cancelar-perfil">Cancelar</button>
                    <button id="btn-guardar-perfil">Guardar</button>
                </div>
            </div>

            <!-- PESQUISA DO ACERVO -->
            <div class="secao">
                <h3>Consultar Acervo</h3>
                <div class="filtros">
                    <input type="text" id="pesquisa-acervo"
                        placeholder="Pesquisar por título ou autor...">
                    <select id="filtro-categoria">
                        <option value="">Todas as categorias</option>
                    </select>
                    <button id="btn-pesquisar">Pesquisar</button>
                    <button id="btn-limpar-pesquisa" class="btn-cancelar">Limpar</button>
                </div>

                <div class="tabela-container" id="container-acervo" style="display:none;max-height:300px;">
                    <table>
                        <thead>
                            <tr>
                                <th>Capa</th>
                                <th>Título</th>
                                <th>Autor</th>
                                <th>Categoria</th>
                                <th>Disponível</th>
                                <th>Acção</th>
                            </tr>
                        </thead>
                        <tbody id="tabela-acervo-aluno"></tbody>
                    </table>
                </div>
            </div>

            <!-- LIVROS EM MEU PODER -->
            <div class="secao">
                <h3>Livros em Meu Poder</h3>
                <div class="tabela-container" style="max-height:300px;">
                    <table>
                        <thead>
                            <tr>
                                <th>Livro</th>
                                <th>Autor</th>
                                <th>Empréstimo</th>
                                <th>Devolução Prevista</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody id="tabela-emprestimos-aluno">
                            <tr><td colspan="5">A carregar...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- MINHAS SOLICITAÇÕES -->
            <div class="secao">
                <h3>Minhas Solicitações</h3>
                <div class="tabela-container" style="max-height:300px;">
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Livro</th>
                                <th>Solicitado em</th>
                                <th>Válido até</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody id="tabela-solicitacoes-aluno">
                            <tr><td colspan="5">A carregar...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- HISTÓRICO -->
            <div class="secao">
                <h3>Histórico de Empréstimos</h3>
                <div class="tabela-container" style="max-height:300px;">
                    <table>
                        <thead>
                            <tr>
                                <th>Livro</th>
                                <th>Empréstimo</th>
                                <th>Devolução Prevista</th>
                                <th>Devolução Real</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody id="tabela-historico-aluno">
                            <tr><td colspan="5">A carregar...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script src="/assets/js/csrf.js"></script>
    <script src="/assets/js/painel_aluno.js"></script>
    <script src="/assets/js/sidebar.js"></script>
</body>
</html>