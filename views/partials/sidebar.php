<?php
$uri          = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$paginaActual = trim($uri, '/');
$perfil       = $_SESSION["perfil"] ?? "aluno";
?>
<div class="sidebar">

    <!-- LOGO -->
    <div class="sidebar-logo">
        <!-- Substitui a tag img pelo teu logo quando tiveres -->
        <!-- <img src="/assets/img/logo.png" alt="BiblioGest"> -->
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
        <!-- PRINCIPAL -->
        <span class="sidebar-section-label">Principal</span>

        <a href="/dashboard" <?= $paginaActual === 'dashboard' ? 'class="activo"' : '' ?>>
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
            </svg>
            Dashboard
        </a>

        <?php if (in_array($perfil, ['admin', 'bibliotecario'])): ?>

        <!-- GESTÃO -->
        <span class="sidebar-section-label">Gestão</span>

        <a href="/alunos" <?= $paginaActual === 'alunos' ? 'class="activo"' : '' ?>>
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            Alunos
        </a>

        <a href="/acervo" <?= $paginaActual === 'acervo' ? 'class="activo"' : '' ?>>
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
            </svg>
            Acervo
        </a>

        <a href="/emprestimos" <?= $paginaActual === 'emprestimos' ? 'class="activo"' : '' ?>>
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <polyline points="17 1 21 5 17 9"/>
                <path d="M3 11V9a4 4 0 0 1 4-4h14"/>
                <polyline points="7 23 3 19 7 15"/>
                <path d="M21 13v2a4 4 0 0 1-4 4H3"/>
            </svg>
            Empréstimos
        </a>

        <a href="/entradas" <?= $paginaActual === 'entradas' ? 'class="activo"' : '' ?>>
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                <polyline points="10 17 15 12 10 7"/>
                <line x1="15" y1="12" x2="3" y2="12"/>
            </svg>
            Entradas/Saídas
        </a>

        <a href="/solicitacoes" <?= $paginaActual === 'solicitacoes' ? 'class="activo"' : '' ?>>
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="12" y1="18" x2="12" y2="12"/>
                <line x1="9" y1="15" x2="15" y2="15"/>
            </svg>
            Solicitações
        </a>

        <!-- ANÁLISE -->
        <span class="sidebar-section-label">Análise</span>

        <a href="/historico" <?= $paginaActual === 'historico' ? 'class="activo"' : '' ?>>
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
            Histórico
        </a>

        <a href="/relatorios" <?= $paginaActual === 'relatorios' ? 'class="activo"' : '' ?>>
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="20" x2="18" y2="10"/>
                <line x1="12" y1="20" x2="12" y2="4"/>
                <line x1="6" y1="20" x2="6" y2="14"/>
            </svg>
            Relatórios
        </a>

        <?php endif; ?>

        <?php if ($perfil === 'admin'): ?>

        <!-- SISTEMA -->
        <span class="sidebar-section-label">Sistema</span>

        <a href="/utilizadores" <?= $paginaActual === 'utilizadores' ? 'class="activo"' : '' ?>>
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
            Bibliotecários
        </a>

        <a href="/logs" <?= $paginaActual === 'logs' ? 'class="activo"' : '' ?>>
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
            </svg>
            Logs
        </a>

        <a href="/backup" <?= $paginaActual === 'backup' ? 'class="activo"' : '' ?>>
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <ellipse cx="12" cy="5" rx="9" ry="3"/>
                <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
                <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
            </svg>
            Backup
        </a>

        <?php endif; ?>
    </nav>

    <!-- FOOTER -->
    <div class="sidebar-footer">
        <a href="/perfil" class="perfil-link">
            <img src="<?= $_SESSION['foto'] ?? '/assets/img/avatar.png' ?>" alt="Foto">
            <div class="perfil-link-info">
                <span><?= htmlspecialchars($_SESSION["nome"]) ?></span>
                <small><?= ucfirst($_SESSION["perfil"]) ?></small>
            </div>
        </a>
        <a href="/auth/logout">Sair</a>
    </div>
</div>