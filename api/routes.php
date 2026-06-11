<?php
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\AlunoController;
use App\Controllers\LivroController;
use App\Controllers\EmprestimoController;
use App\Controllers\EntradaController;
use App\Controllers\HistoricoController;
use App\Controllers\LogController;
use App\Middleware\AuthMiddleware;
use App\Controllers\RelatorioController;
use App\Controllers\UtilizadorController;
use App\Controllers\PainelAlunoController;

/** @var \App\Core\Router $router */

// ─── PÚBLICAS ─────────────────────────────────────────────────

$router->get('/',            [AuthController::class, 'landing']);
$router->post('/auth/login', [AuthController::class, 'processarLogin']);
$router->get('/auth/logout', [AuthController::class, 'logout']);
$router->get('/auth/alterar-senha',  [AuthController::class, 'alterarSenhaPagina']);
$router->post('/auth/alterar-senha', [AuthController::class, 'alterarSenha']);

// ─── DASHBOARD ────────────────────────────────────────────────
$router->get('/dashboard',
    [DashboardController::class, 'index'],
    AuthMiddleware::auth());

$router->get('/api/estatisticas',
    [EmprestimoController::class, 'estatisticas'],
    AuthMiddleware::auth());

$router->get('/api/verificar-atrasos',
    [DashboardController::class, 'verificarAtrasos'],
    AuthMiddleware::auth());

    $router->get('/api/graficos/emprestimos-mensais',
    [DashboardController::class, 'graficosEmprestimosMensais'],
    AuthMiddleware::auth());

$router->get('/api/graficos/cursos-frequentes',
    [DashboardController::class, 'graficosCursosFrequentes'],
    AuthMiddleware::auth());

$router->get('/api/graficos/categorias-emprestadas',
    [DashboardController::class, 'graficosCategoriasEmprestadas'],
    AuthMiddleware::auth());

$router->get('/api/graficos/fluxo-horario',
    [DashboardController::class, 'graficosFluxoHorario'],
    AuthMiddleware::auth());

$router->get('/dashboard',
    [DashboardController::class, 'index'],
    AuthMiddleware::bibliotecario());

// ─── ALUNOS ───────────────────────────────────────────────────
$router->get('/alunos',
    [AlunoController::class, 'pagina'],
    AuthMiddleware::bibliotecario());

$router->get('/api/alunos',
    [AlunoController::class, 'listar'],
    AuthMiddleware::bibliotecario());

$router->get('/api/alunos/pesquisar',
    [AlunoController::class, 'pesquisar'],
    AuthMiddleware::bibliotecario());

$router->post('/api/alunos',
    [AlunoController::class, 'criar'],
    function() {
        AuthMiddleware::bibliotecario()();
        AuthMiddleware::csrf()();
    });

$router->post('/api/alunos/{id}',
    [AlunoController::class, 'actualizar'],
    function() {
        AuthMiddleware::bibliotecario()();
        AuthMiddleware::csrf()();
    });

$router->post('/api/alunos/{id}/eliminar',
    [AlunoController::class, 'eliminar'],
    function() {
        AuthMiddleware::bibliotecario()();
        AuthMiddleware::csrf()();
    });

$router->post('/api/alunos/{id}/status',
    [AlunoController::class, 'alterarStatus'],
    function() {
        AuthMiddleware::bibliotecario()();
        AuthMiddleware::csrf()();
    });

$router->post('/api/alunos/{id}/redefinir-senha',
    [AlunoController::class, 'redefinirSenha'],
    function() {
        AuthMiddleware::bibliotecario()();
        AuthMiddleware::csrf()();
    });

// ─── ACERVO ───────────────────────────────────────────────────
$router->get('/acervo',
    [LivroController::class, 'pagina'],
    AuthMiddleware::bibliotecario());

$router->get('/api/livros',
    [LivroController::class, 'listar'],
    AuthMiddleware::bibliotecario());

$router->get('/api/livros/pesquisar',
    [LivroController::class, 'pesquisar'],
    AuthMiddleware::bibliotecario());

$router->post('/api/livros',
    [LivroController::class, 'criar'],
    function() {
        AuthMiddleware::bibliotecario()();
        AuthMiddleware::csrf()();
    });

$router->post('/api/livros/{id}',
    [LivroController::class, 'actualizar'],
    function() {
        AuthMiddleware::bibliotecario()();
        AuthMiddleware::csrf()();
    });

$router->post('/api/livros/{id}/eliminar',
    [LivroController::class, 'eliminar'],
    function() {
        AuthMiddleware::bibliotecario()();
        AuthMiddleware::csrf()();
    });

// ─── EMPRÉSTIMOS ──────────────────────────────────────────────
$router->get('/emprestimos',
    [EmprestimoController::class, 'pagina'],
    AuthMiddleware::bibliotecario());

$router->get('/api/emprestimos',
    [EmprestimoController::class, 'listar'],
    AuthMiddleware::bibliotecario());

$router->get('/api/emprestimos/contar',
    [EmprestimoController::class, 'contar'],
    AuthMiddleware::bibliotecario());

$router->post('/api/emprestimos',
    [EmprestimoController::class, 'criar'],
    function() {
        AuthMiddleware::bibliotecario()();
        AuthMiddleware::csrf()();
    });

$router->post('/api/emprestimos/{id}/devolver',
    [EmprestimoController::class, 'devolver'],
    function() {
        AuthMiddleware::bibliotecario()();
        AuthMiddleware::csrf()();
    });

// ─── ENTRADAS/SAÍDAS ──────────────────────────────────────────
$router->get('/entradas',
    [EntradaController::class, 'pagina'],
    AuthMiddleware::bibliotecario());

$router->get('/api/entradas',
    [EntradaController::class, 'listar'],
    AuthMiddleware::bibliotecario());

$router->get('/api/entradas/resumo',
    [EntradaController::class, 'resumo'],
    AuthMiddleware::bibliotecario());

$router->get('/api/entradas/pesquisar-alunos',
    [EntradaController::class, 'pesquisarAlunos'],
    AuthMiddleware::bibliotecario());

$router->post('/api/entradas',
    [EntradaController::class, 'registar'],
    function() {
        AuthMiddleware::bibliotecario()();
        AuthMiddleware::csrf()();
    });

$router->post('/api/entradas/{id}/eliminar',
    [EntradaController::class, 'eliminar'],
    function() {
        AuthMiddleware::bibliotecario()();
        AuthMiddleware::csrf()();
    });

// ─── HISTÓRICO ────────────────────────────────────────────────
$router->get('/historico',
    [HistoricoController::class, 'pagina'],
    AuthMiddleware::bibliotecario());

$router->get('/api/historico/emprestimos',
    [HistoricoController::class, 'emprestimos'],
    AuthMiddleware::bibliotecario());

$router->get('/api/historico/entradas',
    [HistoricoController::class, 'entradas'],
    AuthMiddleware::bibliotecario());

$router->get('/api/historico/alunos',
    [HistoricoController::class, 'alunos'],
    AuthMiddleware::bibliotecario());

$router->get('/api/historico/acervo',
    [HistoricoController::class, 'acervo'],
    AuthMiddleware::bibliotecario());

// ─── LOGS ─────────────────────────────────────────────────────
$router->get('/logs',
    [LogController::class, 'pagina'],
    AuthMiddleware::admin());

$router->get('/api/logs',
    [LogController::class, 'listar'],
    AuthMiddleware::admin());

// ─── RELATÓRIOS ───────────────────────────────────────────────
$router->get('/relatorios',
    [RelatorioController::class, 'pagina'],
    AuthMiddleware::bibliotecario());

$router->get('/api/relatorios/emprestimos-activos',
    [RelatorioController::class, 'emprestimosActivos'],
    AuthMiddleware::bibliotecario());

$router->get('/api/relatorios/livros-atrasados',
    [RelatorioController::class, 'livrosAtrasados'],
    AuthMiddleware::bibliotecario());

$router->get('/api/relatorios/livros-disponiveis',
    [RelatorioController::class, 'livrosDisponiveis'],
    AuthMiddleware::bibliotecario());

$router->get('/api/relatorios/livros-mais-emprestados',
    [RelatorioController::class, 'livrosMaisEmprestados'],
    AuthMiddleware::bibliotecario());

$router->get('/api/relatorios/alunos-mais-activos',
    [RelatorioController::class, 'alunosMaisActivos'],
    AuthMiddleware::bibliotecario());

$router->get('/api/relatorios/categorias',
    [RelatorioController::class, 'categoriasMaisSolicitadas'],
    AuthMiddleware::bibliotecario());

$router->get('/api/relatorios/movimentacao-diaria',
    [RelatorioController::class, 'movimentacaoDiaria'],
    AuthMiddleware::bibliotecario());

$router->get('/api/relatorios/cadastros-mes',
    [RelatorioController::class, 'cadastrosMes'],
    AuthMiddleware::bibliotecario());

$router->get('/api/relatorios/historico-mensal',
    [RelatorioController::class, 'historicoMensalEmprestimos'],
    AuthMiddleware::bibliotecario());

$router->get('/api/relatorios/exportar',
    [RelatorioController::class, 'exportar'],
    AuthMiddleware::bibliotecario());

use App\Controllers\BackupController;

// ─── BACKUP ───────────────────────────────────────────────────
$router->get('/backup',
    [BackupController::class, 'pagina'],
    AuthMiddleware::admin());

$router->get('/api/backup/criar',
    [BackupController::class, 'criar'],
    AuthMiddleware::admin());

$router->get('/api/backup/listar',
    [BackupController::class, 'listar'],
    AuthMiddleware::admin());

$router->get('/api/backup/download',
    [BackupController::class, 'download'],
    AuthMiddleware::admin());

$router->post('/api/backup/eliminar',
    [BackupController::class, 'eliminar'],
    function() {
        AuthMiddleware::admin()();
        AuthMiddleware::csrf()();
    });

// ─── UTILIZADORES (apenas admin) ──────────────────────────────
$router->get('/utilizadores',
    [UtilizadorController::class, 'pagina'],
    AuthMiddleware::admin());

$router->get('/api/utilizadores',
    [UtilizadorController::class, 'listar'],
    AuthMiddleware::admin());

$router->post('/api/utilizadores',
    [UtilizadorController::class, 'criar'],
    function() {
        AuthMiddleware::admin()();
        AuthMiddleware::csrf()();
    });

$router->post('/api/utilizadores/{id}',
    [UtilizadorController::class, 'actualizar'],
    function() {
        AuthMiddleware::admin()();
        AuthMiddleware::csrf()();
    });

$router->post('/api/utilizadores/{id}/status',
    [UtilizadorController::class, 'alterarStatus'],
    function() {
        AuthMiddleware::admin()();
        AuthMiddleware::csrf()();
    });

$router->post('/api/utilizadores/{id}/redefinir-senha',
    [UtilizadorController::class, 'redefinirSenha'],
    function() {
        AuthMiddleware::admin()();
        AuthMiddleware::csrf()();
    });

// ─── PAINEL DO ALUNO ──────────────────────────────────────────
$router->get('/painel-aluno',
    [PainelAlunoController::class, 'index'],
    AuthMiddleware::auth());

$router->get('/api/painel-aluno/dados',
    [PainelAlunoController::class, 'dados'],
    AuthMiddleware::auth());

$router->get('/api/painel-aluno/acervo',
    [PainelAlunoController::class, 'acervo'],
    AuthMiddleware::auth());

$router->get('/api/painel-aluno/categorias',
    [PainelAlunoController::class, 'categorias'],
    AuthMiddleware::auth());

use App\Controllers\SolicitacaoController;

// ─── SOLICITAÇÕES DO ALUNO ────────────────────────────────────
$router->post('/api/painel-aluno/solicitar',
    [PainelAlunoController::class, 'solicitarEmprestimo'],
    AuthMiddleware::auth());

$router->get('/api/painel-aluno/solicitacoes',
    [PainelAlunoController::class, 'minhasSolicitacoes'],
    AuthMiddleware::auth());

$router->post('/api/painel-aluno/perfil',
    [PainelAlunoController::class, 'actualizarPerfil'],
    function() {
        AuthMiddleware::auth()();
        AuthMiddleware::csrf()();
    });

// ─── GESTÃO DE SOLICITAÇÕES (bibliotecário/admin) ─────────────
$router->get('/solicitacoes',
    [SolicitacaoController::class, 'pagina'],
    AuthMiddleware::bibliotecario());

$router->get('/api/solicitacoes',
    [SolicitacaoController::class, 'listar'],
    AuthMiddleware::bibliotecario());

$router->get('/api/solicitacoes/contar',
    [SolicitacaoController::class, 'contar'],
    AuthMiddleware::bibliotecario());

$router->get('/api/solicitacoes/verificar',
    [SolicitacaoController::class, 'verificar'],
    AuthMiddleware::bibliotecario());

$router->post('/api/solicitacoes/{id}/aprovar',
    [SolicitacaoController::class, 'aprovar'],
    function() {
        AuthMiddleware::bibliotecario()();
        AuthMiddleware::csrf()();
    });

$router->post('/api/solicitacoes/{id}/rejeitar',
    [SolicitacaoController::class, 'rejeitar'],
    function() {
        AuthMiddleware::bibliotecario()();
        AuthMiddleware::csrf()();
    });

use App\Controllers\PerfilController;

// ─── PERFIL ───────────────────────────────────────────────────
$router->get('/perfil',
    [PerfilController::class, 'pagina'],
    AuthMiddleware::auth());

$router->get('/api/perfil/dados',
    [PerfilController::class, 'dados'],
    AuthMiddleware::auth());

$router->get('/api/perfil/historico',
    [PerfilController::class, 'historico'],
    AuthMiddleware::auth());

$router->post('/api/perfil/actualizar',
    [PerfilController::class, 'actualizar'],
    function() {
        AuthMiddleware::auth()();
        AuthMiddleware::csrf()();
    });

$router->post('/api/perfil/senha',
    [PerfilController::class, 'alterarSenha'],
    function() {
        AuthMiddleware::auth()();
        AuthMiddleware::csrf()();
    });

    