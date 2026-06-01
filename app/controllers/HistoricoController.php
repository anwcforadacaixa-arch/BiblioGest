<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Helpers\Response;
use App\Helpers\Paginator;
use App\Middleware\AuthMiddleware;

class HistoricoController extends Controller {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function pagina(): void {
        $this->view("historico/index");
    }

    public function emprestimos(): void {

        $pesquisa   = "%" . ($_GET["pesquisa"]   ?? "") . "%";
        $estado     = $_GET["estado"]     ?? "";
        $dataInicio = $_GET["data_inicio"] ?? "";
        $dataFim    = $_GET["data_fim"]    ?? "";
        $pagina     = intval($_GET["pagina"] ?? 1);

        $condicoes = ["a.nome LIKE ?"];
        $params    = [$pesquisa];

        if ($estado)     { $condicoes[] = "e.estado = ?";             $params[] = $estado; }
        if ($dataInicio) { $condicoes[] = "e.data_emprestimo >= ?";   $params[] = $dataInicio; }
        if ($dataFim)    { $condicoes[] = "e.data_emprestimo <= ?";   $params[] = $dataFim; }

        $where = implode(" AND ", $condicoes);

        // Contar total
        $stmtTotal = $this->db->prepare("SELECT COUNT(*) FROM emprestimos e JOIN alunos a ON e.aluno_id = a.id JOIN livros l ON e.livro_id = l.id WHERE $where");
        $stmtTotal->execute($params);
        $total = intval($stmtTotal->fetchColumn());

        $paginator = new Paginator($total, 25, $pagina);

        $limite = $paginator->limite();
        $offset = $paginator->offset();

        $stmt = $this->db->prepare("SELECT e.id, a.nome AS aluno, l.titulo AS livro, e.data_emprestimo, e.data_devolucao_prevista, e.data_devolucao_real, e.estado
            FROM emprestimos e
            JOIN alunos a ON e.aluno_id = a.id
            JOIN livros l ON e.livro_id = l.id
            WHERE $where
            ORDER BY e.data_emprestimo DESC
            LIMIT $limite OFFSET $offset
        ");
        $stmt->execute($params);

        Response::paginated($stmt->fetchAll(), $paginator->meta());
    }

    public function entradas(): void {
        $pesquisa   = "%" . ($_GET["pesquisa"]   ?? "") . "%";
        $dataInicio = $_GET["data_inicio"] ?? "";
        $dataFim    = $_GET["data_fim"]    ?? "";
        $pagina     = intval($_GET["pagina"] ?? 1);

        $condicoes = ["a.nome LIKE ?"];
        $params    = [$pesquisa];

        if ($dataInicio) { $condicoes[] = "DATE(e.data_hora) >= ?"; $params[] = $dataInicio; }
        if ($dataFim)    { $condicoes[] = "DATE(e.data_hora) <= ?"; $params[] = $dataFim; }

        $where = implode(" AND ", $condicoes);

        $stmtTotal = $this->db->prepare("
            SELECT COUNT(*) FROM entradas_saidas e
            JOIN alunos a ON e.aluno_id = a.id
            WHERE $where
        ");
        $stmtTotal->execute($params);
        $total = intval($stmtTotal->fetchColumn());

        $paginator = new Paginator($total, 25, $pagina);

        $limite = $paginator->limite();
        $offset = $paginator->offset();

        $stmt = $this->db->prepare("
            SELECT e.id, a.nome, a.numero_estudante, e.tipo, e.data_hora
            FROM entradas_saidas e
            JOIN alunos a ON e.aluno_id = a.id
            WHERE $where
            ORDER BY e.data_hora DESC
            LIMIT $limite OFFSET $offset
        ");
        $stmt->execute($params);

        Response::paginated($stmt->fetchAll(), $paginator->meta());
    }

    public function alunos(): void {
        $pesquisa   = "%" . ($_GET["pesquisa"]   ?? "") . "%";
        $dataInicio = $_GET["data_inicio"] ?? "";
        $dataFim    = $_GET["data_fim"]    ?? "";
        $pagina     = intval($_GET["pagina"] ?? 1);

        $condicoes = ["nome LIKE ?"];
        $params    = [$pesquisa];

        if ($dataInicio) { $condicoes[] = "DATE(criado_em) >= ?"; $params[] = $dataInicio; }
        if ($dataFim)    { $condicoes[] = "DATE(criado_em) <= ?"; $params[] = $dataFim; }

        $where = implode(" AND ", $condicoes);

        $stmtTotal = $this->db->prepare("SELECT COUNT(*) FROM alunos WHERE $where");
        $stmtTotal->execute($params);
        $total = intval($stmtTotal->fetchColumn());

        $paginator = new Paginator($total, 25, $pagina);

        $limite = $paginator->limite();
        $offset = $paginator->offset();

        $stmt = $this->db->prepare("
            SELECT id, numero_estudante, nome, curso, turma, telefone, criado_em
            FROM alunos
            WHERE $where
            ORDER BY criado_em DESC
            LIMIT $limite OFFSET $offset
        ");
        $stmt->execute($params);

        Response::paginated($stmt->fetchAll(), $paginator->meta());
    }

    public function acervo(): void {
        $pesquisa   = "%" . ($_GET["pesquisa"]   ?? "") . "%";
        $dataInicio = $_GET["data_inicio"] ?? "";
        $dataFim    = $_GET["data_fim"]    ?? "";
        $pagina     = intval($_GET["pagina"] ?? 1);

        $condicoes = ["titulo LIKE ?"];
        $params    = [$pesquisa];

        if ($dataInicio) { $condicoes[] = "DATE(criado_em) >= ?"; $params[] = $dataInicio; }
        if ($dataFim)    { $condicoes[] = "DATE(criado_em) <= ?"; $params[] = $dataFim; }

        $where = implode(" AND ", $condicoes);

        $stmtTotal = $this->db->prepare("SELECT COUNT(*) FROM livros WHERE $where");
        $stmtTotal->execute($params);
        $total = intval($stmtTotal->fetchColumn());

        $paginator = new Paginator($total, 25, $pagina);

        $limite = $paginator->limite();
        $offset = $paginator->offset();

        $stmt = $this->db->prepare("SELECT id, titulo, autor, isbn, categoria, quantidade_total, quantidade_disponivel, criado_em
            FROM livros WHERE $where ORDER BY criado_em DESC LIMIT $limite OFFSET $offset");
        $stmt->execute($params);

        Response::paginated($stmt->fetchAll(), $paginator->meta());
    }
}