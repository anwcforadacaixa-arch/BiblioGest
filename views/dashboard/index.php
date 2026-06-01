<?php if (!isset($_SESSION["id"])) { header("Location: /"); exit; } ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/assets/img/logo.png">
    <title>BiblioGest — Dashboard</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
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

            <h2>Dashboard</h2>

        </div>

        <div class="dashboard-scroll">
            
            <!-- CARDS -->
            <div class="cards" id="cards-stats">
                <div class="cards" id="cards-stats">
                    <div class="card">
                        <div class="card-icon blue">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <div>
                            <p class="card-label">Total de Alunos</p>
                            <p class="card-valor" id="total-alunos">—</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-icon green">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="card-label">Livros no Acervo</p>
                            <p class="card-valor" id="total-livros">—</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-icon purple">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="17 1 21 5 17 9"/>
                                <path d="M3 11V9a4 4 0 0 1 4-4h14"/>
                                <polyline points="7 23 3 19 7 15"/>
                                <path d="M21 13v2a4 4 0 0 1-4 4H3"/>
                            </svg>
                        </div>
                        <div>
                            <p class="card-label">Empréstimos Activos</p>
                            <p class="card-valor" id="total-emprestimos">—</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-icon orange">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                                <polyline points="10 17 15 12 10 7"/>
                                <line x1="15" y1="12" x2="3" y2="12"/>
                            </svg>
                        </div>
                        <div>
                            <p class="card-label">Entradas Hoje</p>
                            <p class="card-valor" id="total-entradas">—</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRÁFICOS -->
            <div class="graficos-grid">

                <div class="grafico-card">
                    <h3>Empréstimos por Mês</h3>
                    <canvas id="grafico-emprestimos-mensais"></canvas>
                </div>

                <div class="grafico-card">
                    <h3>Cursos com Mais Frequência</h3>
                    <canvas id="grafico-cursos"></canvas>
                </div>

                <div class="grafico-card">
                    <h3>Categorias Mais Solicitadas</h3>
                    <canvas id="grafico-categorias"></canvas>
                </div>

                <div class="grafico-card">
                    <h3>Fluxo por Hora do Dia</h3>
                    <canvas id="grafico-fluxo"></canvas>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script src="/assets/js/csrf.js"></script>
    <script src="/assets/js/dashboard.js"></script>
    <script src="/assets/js/sidebar.js"></script>

</body>
</html>