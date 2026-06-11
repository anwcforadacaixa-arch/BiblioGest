<?php
session_start();
if (isset($_SESSION["id"])) {
    $destino = match($_SESSION["perfil"] ?? "") {
        "aluno"  => "/painel-aluno",
        default  => "/dashboard"
    };
    header("Location: $destino");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiblioGest — Sistema de Gestão de Biblioteca</title>
    <meta name="description" content="BiblioGest é o sistema de gestão de bibliotecas. Organiza, controla e simplifica a gestão do acervo, empréstimos e entradas de alunos.">
    <link rel="shortcut icon" type="image/png" href="/assets/img/logo.png">
    <link rel="stylesheet" href="/assets/css/landing.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
</head>
<body>

<div class="grid-bg"></div>

<!-- NAV -->
<nav>
    <a href="/" class="nav-logo">
        <div class="nav-logo-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
            </svg>
        </div>
        <span class="nav-logo-text">BiblioGest</span>
    </a>
    <ul class="nav-links">
        <li><a href="#funcionalidades">Funcionalidades</a></li>
        <li><a href="#como-funciona">Como Funciona</a></li>
        <li><a href="#perfis">Perfis</a></li>
        <li><a href="/auth/login" class="nav-cta">Entrar no Sistema</a></li>
    </ul>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-glow"></div>
    <div class="hero-content">
        <div class="hero-badge">
            <div class="hero-badge-dot"></div>
            Sistema Web de Gestão de Biblioteca.
        </div>
        <h1>Gestão de Biblioteca<br><span>Inteligente</span></h1>
        <p class="hero-slogan">Organiza. Controla. Simplifica.</p>
        <p class="hero-desc">
            O BiblioGest digitaliza e automatiza todos os processos
            da tua biblioteca — do acervo aos empréstimos,
            das presenças aos relatórios.
        </p>
        <div class="hero-actions">
            <a href="/auth/login" class="btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                    <polyline points="10 17 15 12 10 7"/>
                    <line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
                Entrar no Sistema
            </a>
            <a href="#funcionalidades" class="btn-secondary">
                Saber mais
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- STATS BAR -->
<div class="stats-bar reveal">
    <div class="stat-item">
        <div class="stat-num">6<span>+</span></div>
        <div class="stat-label">Módulos do Sistema</div>
    </div>
    <div class="stat-item">
        <div class="stat-num">3</div>
        <div class="stat-label">Perfis de Acesso</div>
    </div>
    <div class="stat-item">
        <div class="stat-num">100<span>%</span></div>
        <div class="stat-label">Web — Sem Instalação</div>
    </div>
    <div class="stat-item">
        <div class="stat-num">0</div>
        <div class="stat-label">Papel. Tudo Digital.</div>
    </div>
</div>

<!-- FUNCIONALIDADES -->
<section id="funcionalidades">
    <div class="section-header reveal">
        <span class="section-tag">Funcionalidades</span>
        <h2 class="section-title">Tudo o que a tua biblioteca precisa</h2>
        <p class="section-sub">Do registo de entradas aos relatórios estatísticos — o BiblioGest centraliza tudo numa única plataforma.</p>
    </div>

    <div class="features-grid">
        <div class="feature-card reveal">
            <div class="feature-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <h3>Gestão de Alunos</h3>
            <p>Cadastro completo com foto, curso, turma e credenciais de acesso geradas automaticamente pelo sistema.</p>
        </div>

        <div class="feature-card reveal">
            <div class="feature-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
            </div>
            <h3>Gestão do Acervo</h3>
            <p>Registo de livros com capa, categoria e controlo automático de disponibilidade por exemplar.</p>
        </div>

        <div class="feature-card reveal">
            <div class="feature-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="17 1 21 5 17 9"/>
                    <path d="M3 11V9a4 4 0 0 1 4-4h14"/>
                    <polyline points="7 23 3 19 7 15"/>
                    <path d="M21 13v2a4 4 0 0 1-4 4H3"/>
                </svg>
            </div>
            <h3>Empréstimos e Devoluções</h3>
            <p>Controlo completo de empréstimos com detecção automática de atrasos e histórico permanente.</p>
        </div>

        <div class="feature-card reveal">
            <div class="feature-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                    <polyline points="10 17 15 12 10 7"/>
                    <line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
            </div>
            <h3>Entradas e Saídas</h3>
            <p>Registo de presenças em tempo real com contador de alunos presentes e histórico diário.</p>
        </div>

        <div class="feature-card reveal">
            <div class="feature-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"/>
                    <line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
            </div>
            <h3>Relatórios e Gráficos</h3>
            <p>Dashboard com estatísticas em tempo real, gráficos interactivos e exportação em PDF e Excel.</p>
        </div>

        <div class="feature-card reveal">
            <div class="feature-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="12" y1="18" x2="12" y2="12"/>
                    <line x1="9" y1="15" x2="15" y2="15"/>
                </svg>
            </div>
            <h3>Solicitações com Código</h3>
            <p>O aluno solicita um livro online e recebe um código único para levantamento na biblioteca.</p>
        </div>
    </div>
</section>

<div class="divider"></div>

<!-- COMO FUNCIONA -->
<div class="how-section" id="como-funciona">
    <div class="how-inner">
        <div class="section-header reveal">
            <span class="section-tag">Como Funciona</span>
            <h2 class="section-title">Simples para todos</h2>
            <p class="section-sub">Três passos e a biblioteca está digitalizada e operacional.</p>
        </div>
        <div class="steps">
            <div class="step reveal">
                <div class="step-num">1</div>
                <h3>Administrador configura</h3>
                <p>O administrador cadastra os bibliotecários, alunos e o acervo bibliográfico no sistema.</p>
            </div>
            <div class="step reveal">
                <div class="step-num">2</div>
                <h3>Biblioteca opera</h3>
                <p>A bibliotecária regista entradas, gere empréstimos e aprova solicitações em tempo real.</p>
            </div>
            <div class="step reveal">
                <div class="step-num">3</div>
                <h3>Aluno acede</h3>
                <p>O aluno consulta o acervo, solicita livros com código e acompanha os seus empréstimos.</p>
            </div>
        </div>
    </div>
</div>

<div class="divider"></div>

<!-- PERFIS -->
<section id="perfis">
    <div class="section-header reveal">
        <span class="section-tag">Perfis de Acesso</span>
        <h2 class="section-title">Cada utilizador no seu lugar</h2>
        <p class="section-sub">O BiblioGest tem três perfis distintos com permissões controladas.</p>
    </div>

    <div class="perfis-grid">
        <div class="perfil-card reveal">
            <div class="perfil-header">
                <div class="perfil-avatar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M20 21a8 8 0 1 0-16 0"/>
                        <path d="M16 11l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <h3>Administrador</h3>
                    <small>Controlo total do sistema</small>
                </div>
            </div>
            <ul class="perfil-list">
                <li>Gerir bibliotecários e alunos</li>
                <li>Acesso a todos os módulos</li>
                <li>Visualizar logs e auditoria</li>
                <li>Criar e restaurar backups</li>
                <li>Configurar o sistema</li>
            </ul>
        </div>

        <div class="perfil-card reveal">
            <div class="perfil-header">
                <div class="perfil-avatar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        <line x1="12" y1="6" x2="12" y2="12"/>
                        <line x1="9" y1="9" x2="15" y2="9"/>
                    </svg>
                </div>
                <div>
                    <h3>Bibliotecário</h3>
                    <small>Operação diária da biblioteca</small>
                </div>
            </div>
            <ul class="perfil-list">
                <li>Gerir alunos e acervo</li>
                <li>Registar empréstimos e devoluções</li>
                <li>Aprovar solicitações com código</li>
                <li>Registar entradas e saídas</li>
                <li>Visualizar relatórios operacionais</li>
            </ul>
        </div>

        <div class="perfil-card reveal" style="grid-column: 1 / -1;">
            <div class="perfil-header">
                <div class="perfil-avatar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <div>
                    <h3>Aluno</h3>
                    <small>Acesso ao painel pessoal</small>
                </div>
            </div>
            <ul class="perfil-list perfil-list-grid">
                <li>Consultar o acervo disponível</li>
                <li>Solicitar empréstimos online</li>
                <li>Receber código de levantamento</li>
                <li>Ver livros em seu poder</li>
                <li>Acompanhar prazos de devolução</li>
                <li>Gerir o perfil pessoal</li>
            </ul>
        </div>
    </div>
</section>

<div class="divider"></div>

<!-- CTA FINAL -->
<div class="cta-section">
    <div class="cta-glow"></div>
    <div class="cta-content reveal">
        <h2>Pronto para começar?</h2>
        <p>Acede ao sistema com as tuas credenciais institucionais.</p>
        <a href="/auth/login" class="btn-primary btn-large">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                <polyline points="10 17 15 12 10 7"/>
                <line x1="15" y1="12" x2="3" y2="12"/>
            </svg>
            Entrar no Sistema
        </a>
    </div>
</div>

<!-- FOOTER -->
<footer>
    <div class="footer-logo">
        <div class="footer-logo-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
            </svg>
        </div>
        <span class="footer-logo-text">BiblioGest</span>
    </div>
    <div class="footer-info">
        <?= date('Y') ?> &copy; Todos os direitos reservados.
    </div>
    <div class="footer-right">
        Aires Almeida
    </div>
</footer>

<script src="/assets/js/landing.js"></script>
</body>
</html>