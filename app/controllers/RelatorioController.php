<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Helpers\Response;
use App\Middleware\AuthMiddleware;

class RelatorioController extends Controller {
    private $db;

    public function __construct() {

        $this->db = Database::getInstance()->getConnection();

    }

    public function pagina(): void {

        $this->view("relatorios/index");

    }

    // ─── OPERACIONAIS ────────────────────────────────────────

    public function emprestimosActivos(): void {

        $stmt = $this->db->query("SELECT a.nome AS aluno, a.numero_estudante, l.titulo AS livro, e.data_emprestimo, e.data_devolucao_prevista, DATEDIFF(CURDATE(), e.data_devolucao_prevista) AS dias_atraso
            FROM emprestimos e JOIN alunos a ON e.aluno_id = a.id JOIN livros l ON e.livro_id = l.id WHERE e.estado = 'activo' ORDER BY e.data_devolucao_prevista ASC");
        Response::success($stmt->fetchAll());

    }

    public function livrosAtrasados(): void {
        
        $stmt = $this->db->query("SELECT a.nome AS aluno, a.numero_estudante, l.titulo AS livro, e.data_emprestimo, e.data_devolucao_prevista, DATEDIFF(CURDATE(), e.data_devolucao_prevista) AS dias_atraso
            FROM emprestimos e JOIN alunos a ON e.aluno_id = a.id JOIN livros l ON e.livro_id = l.id WHERE e.estado = 'atrasado' ORDER BY dias_atraso DESC");
        Response::success($stmt->fetchAll());

    }

    public function livrosDisponiveis(): void {

        $stmt = $this->db->query("SELECT titulo, autor, categoria, quantidade_total, quantidade_disponivel, (quantidade_total - quantidade_disponivel) AS emprestados
            FROM livros WHERE eliminado_em IS NULL ORDER BY quantidade_disponivel DESC");
        Response::success($stmt->fetchAll());

    }

    // ─── ESTATÍSTICOS ─────────────────────────────────────────

    public function livrosMaisEmprestados(): void {

        $stmt = $this->db->query("SELECT l.titulo, l.autor, l.categoria, COUNT(e.id) AS total_emprestimos FROM emprestimos e JOIN livros l ON e.livro_id = l.id GROUP BY l.id ORDER BY total_emprestimos DESC LIMIT 10");
        Response::success($stmt->fetchAll());
        
    }

    public function alunosMaisActivos(): void {

        $stmt = $this->db->query("SELECT a.nome, a.numero_estudante, a.curso, COUNT(e.id) AS total_emprestimos FROM emprestimos e JOIN alunos a ON e.aluno_id = a.id GROUP BY a.id
            ORDER BY total_emprestimos DESC LIMIT 10");
        Response::success($stmt->fetchAll());

    }

    public function categoriasMaisSolicitadas(): void {

        $stmt = $this->db->query("SELECT l.categoria, COUNT(e.id) AS total_emprestimos FROM emprestimos e JOIN livros l ON e.livro_id = l.id
            WHERE l.categoria IS NOT NULL GROUP BY l.categoria ORDER BY total_emprestimos DESC");
        Response::success($stmt->fetchAll());

    }

    // ─── ADMINISTRATIVOS ──────────────────────────────────────

    public function movimentacaoDiaria(): void {

        $data = $_GET["data"] ?? date("Y-m-d");

        $entradas = $this->db->prepare("SELECT a.nome, a.numero_estudante, e.tipo, e.data_hora FROM entradas_saidas e JOIN alunos a ON e.aluno_id = a.id WHERE DATE(e.data_hora) = ? ORDER BY e.data_hora ASC");
        $entradas->execute([$data]);


        $emprestimos = $this->db->prepare("SELECT a.nome AS aluno, l.titulo AS livro, e.data_emprestimo, e.estado FROM emprestimos e JOIN alunos a ON e.aluno_id = a.id JOIN livros l ON e.livro_id = l.id WHERE DATE(e.data_emprestimo) = ? ORDER BY e.data_emprestimo ASC");
        $emprestimos->execute([$data]);


        Response::success([
            "data"        => $data,
            "entradas"    => $entradas->fetchAll(),
            "emprestimos" => $emprestimos->fetchAll()
        ]);

    }

    public function cadastrosMes(): void {

        $mes = $_GET["mes"] ?? date("Y-m");

        $alunos = $this->db->prepare("SELECT nome, numero_estudante, curso, criado_em FROM alunos WHERE DATE_FORMAT(criado_em, '%Y-%m') = ? AND eliminado_em IS NULL ORDER BY criado_em DESC");
        $alunos->execute([$mes]);


        $livros = $this->db->prepare("SELECT titulo, autor, categoria, criado_em FROM livros WHERE DATE_FORMAT(criado_em, '%Y-%m') = ? AND eliminado_em IS NULL ORDER BY criado_em DESC");
        $livros->execute([$mes]);


        Response::success([
            "mes"    => $mes,
            "alunos" => $alunos->fetchAll(),
            "livros" => $livros->fetchAll()
        ]);

    }

    public function historicoMensalEmprestimos(): void {

        $stmt = $this->db->query("SELECT DATE_FORMAT(data_emprestimo, '%Y-%m') AS mes,
                   COUNT(*) AS total,
                   SUM(CASE WHEN estado = 'devolvido' THEN 1 ELSE 0 END) AS devolvidos,
                   SUM(CASE WHEN estado = 'atrasado'  THEN 1 ELSE 0 END) AS atrasados,
                   SUM(CASE WHEN estado = 'activo'    THEN 1 ELSE 0 END) AS activos
            FROM emprestimos
            GROUP BY mes
            ORDER BY mes DESC
            LIMIT 12");
        Response::success($stmt->fetchAll());

    }

    public function exportar(): void {

        $tipo      = $_GET["tipo"]      ?? "pdf";
        $relatorio = $_GET["relatorio"] ?? "";

        $config = [
            "emprestimos-activos" => [
                "titulo"  => "Empréstimos Activos",
                "colunas" => ["Aluno", "Nº Estudante", "Livro", "Data Empréstimo", "Devolução Prevista"],
                "query"   => "SELECT a.nome, a.numero_estudante, l.titulo, e.data_emprestimo, e.data_devolucao_prevista FROM emprestimos e JOIN alunos a ON e.aluno_id = a.id
                    JOIN livros l ON e.livro_id = l.id WHERE e.estado = 'activo' ORDER BY e.data_devolucao_prevista ASC"
            ],
            "livros-atrasados" => [
                "titulo"  => "Livros Atrasados",
                "colunas" => ["Aluno", "Nº Estudante", "Livro", "Devolução Prevista", "Dias em Atraso"],
                "query"   => "SELECT a.nome, a.numero_estudante, l.titulo, e.data_devolucao_prevista, DATEDIFF(CURDATE(), e.data_devolucao_prevista) AS dias_atraso
                    FROM emprestimos e JOIN alunos a ON e.aluno_id = a.id JOIN livros l ON e.livro_id = l.id WHERE e.estado = 'atrasado' ORDER BY dias_atraso DESC"
            ],
            "livros-disponiveis" => [
                "titulo"  => "Livros Disponíveis",
                "colunas" => ["Título", "Autor", "Categoria", "Total", "Disponível", "Emprestados"],
                "query"   => "SELECT titulo, autor, categoria, quantidade_total, quantidade_disponivel, (quantidade_total - quantidade_disponivel) AS emprestados
                    FROM livros WHERE eliminado_em IS NULL ORDER BY quantidade_disponivel DESC"
            ],
            "livros-mais-emprestados" => [
                "titulo"  => "Livros Mais Emprestados",
                "colunas" => ["Título", "Autor", "Categoria", "Total Empréstimos"],
                "query"   => "SELECT l.titulo, l.autor, l.categoria, COUNT(e.id) AS total_emprestimos FROM emprestimos e JOIN livros l ON e.livro_id = l.id GROUP BY l.id
                    ORDER BY total_emprestimos DESC LIMIT 20"
            ],
            "alunos-mais-activos" => [
                "titulo"  => "Alunos Mais Activos",
                "colunas" => ["Nome", "Nº Estudante", "Curso", "Total Empréstimos"],
                "query"   => "SELECT a.nome, a.numero_estudante, a.curso, COUNT(e.id) AS total_emprestimos FROM emprestimos e JOIN alunos a ON e.aluno_id = a.id
                    GROUP BY a.id ORDER BY total_emprestimos DESC LIMIT 20"
            ],
            "historico-mensal" => [
                "titulo"  => "Histórico Mensal de Empréstimos",
                "colunas" => ["Mês", "Total", "Devolvidos", "Atrasados", "Activos"],
                "query"   => "SELECT DATE_FORMAT(data_emprestimo, '%Y-%m') AS mes, COUNT(*) AS total, SUM(CASE WHEN estado = 'devolvido' THEN 1 ELSE 0 END) AS devolvidos,
                        SUM(CASE WHEN estado = 'atrasado'  THEN 1 ELSE 0 END) AS atrasados, SUM(CASE WHEN estado = 'activo'    THEN 1 ELSE 0 END) AS activos
                    FROM emprestimos GROUP BY mes ORDER BY mes DESC LIMIT 12"
            ],
        ];

        if (!isset($config[$relatorio])) {
            Response::error("Relatório não encontrado.");
        }

        $c     = $config[$relatorio];
        $dados = $this->db->query($c["query"])->fetchAll();

        if ($tipo === "excel") {
            \App\Helpers\Exportador::excel($c["titulo"], $c["colunas"], $dados, $relatorio);
        } else {
            \App\Helpers\Exportador::pdf($c["titulo"], $c["colunas"], $dados, $relatorio);
        }
        
    }
    
}