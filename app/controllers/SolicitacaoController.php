<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Helpers\Response;
use App\Helpers\Logger;

class SolicitacaoController extends Controller {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function pagina(): void {
        $this->view("solicitacoes/index");
    }

    public function listar(): void {
        // Expirar automaticamente
        $this->db->query("
            UPDATE solicitacoes SET estado = 'expirado'
            WHERE estado = 'pendente' AND valido_ate < NOW()
        ");

        $estado = $_GET["estado"] ?? "pendente";

        $stmt = $this->db->prepare("
            SELECT s.id, s.codigo, s.estado, s.criado_em,
                   s.valido_ate, s.observacao,
                   a.nome AS aluno, a.numero_estudante,
                   l.titulo AS livro, l.autor, l.capa,
                   l.quantidade_disponivel
            FROM solicitacoes s
            JOIN alunos a ON s.aluno_id = a.id
            JOIN livros l ON s.livro_id = l.id
            WHERE s.estado = ?
            ORDER BY s.criado_em DESC
        ");
        $stmt->execute([$estado]);
        Response::success($stmt->fetchAll());
    }

    public function contar(): void {
        $pendentes = $this->db->query("
            SELECT COUNT(*) FROM solicitacoes
            WHERE estado = 'pendente' AND valido_ate > NOW()
        ")->fetchColumn();

        Response::success(["pendentes" => $pendentes]);
    }

    public function verificar(): void {
        $codigo = strtoupper(trim($_GET["codigo"] ?? ""));

        $stmt = $this->db->prepare("
            SELECT s.*, a.nome AS aluno, a.numero_estudante,
                   l.titulo AS livro, l.autor, l.quantidade_disponivel
            FROM solicitacoes s
            JOIN alunos a ON s.aluno_id = a.id
            JOIN livros l ON s.livro_id = l.id
            WHERE s.codigo = ?
        ");
        $stmt->execute([$codigo]);
        $solicitacao = $stmt->fetch();

        if (!$solicitacao) {
            Response::error("Código inválido ou não encontrado.");
        }

        Response::success($solicitacao);
    }

    public function aprovar(string $id): void {
        $dados = json_decode(file_get_contents("php://input"), true);

        $stmt = $this->db->prepare("
            SELECT s.*, a.id AS aluno_id, l.id AS livro_id,
                   l.quantidade_disponivel, a.nome AS aluno_nome,
                   l.titulo AS livro_titulo
            FROM solicitacoes s
            JOIN alunos a ON s.aluno_id = a.id
            JOIN livros l ON s.livro_id = l.id
            WHERE s.id = ?
        ");
        $stmt->execute([intval($id)]);
        $s = $stmt->fetch();

        if (!$s) Response::error("Solicitação não encontrada.");
        if ($s["estado"] !== "pendente") Response::error("Esta solicitação já foi processada.");
        if ($s["quantidade_disponivel"] < 1) Response::error("Livro sem exemplares disponíveis.");

        // Verificar se aluno tem empréstimo activo
        $check = $this->db->prepare("
            SELECT COUNT(*) FROM emprestimos
            WHERE aluno_id = ? AND estado IN ('activo', 'atrasado')
        ");
        $check->execute([$s["aluno_id"]]);
        if (intval($check->fetchColumn()) > 0) {
            Response::error("Este aluno já tem um empréstimo activo.");
        }

        $pdo = $this->db;
        try {
            $pdo->beginTransaction();

            // Criar empréstimo
            $dataEmprestimo = date("Y-m-d");
            $dataDevolucao  = date("Y-m-d", strtotime("+14 days"));

            $pdo->prepare("INSERT INTO emprestimos
                (aluno_id, livro_id, data_emprestimo, data_devolucao_prevista, estado)
                VALUES (?, ?, ?, ?, 'activo')")
                ->execute([$s["aluno_id"], $s["livro_id"], $dataEmprestimo, $dataDevolucao]);

            // Decrementar livro
            $pdo->prepare("UPDATE livros SET quantidade_disponivel = quantidade_disponivel - 1 WHERE id = ?")
                ->execute([$s["livro_id"]]);

            // Actualizar solicitação
            $pdo->prepare("UPDATE solicitacoes SET estado = 'aprovado', observacao = ? WHERE id = ?")
                ->execute([$dados["observacao"] ?? null, intval($id)]);

            Logger::registar("ALTERACAO", "Solicitações",
                "Solicitação aprovada: {$s['aluno_nome']} → {$s['livro_titulo']}");

            $pdo->commit();
            Response::success(null, "Solicitação aprovada e empréstimo criado.");

        } catch (\Exception $e) {
            $pdo->rollBack();
            Response::error("Erro ao aprovar solicitação.");
        }
    }

    public function rejeitar(string $id): void {
        $dados = json_decode(file_get_contents("php://input"), true);

        $stmt = $this->db->prepare("
            SELECT s.*, a.nome AS aluno_nome, l.titulo AS livro_titulo
            FROM solicitacoes s
            JOIN alunos a ON s.aluno_id = a.id
            JOIN livros l ON s.livro_id = l.id
            WHERE s.id = ?
        ");
        $stmt->execute([intval($id)]);
        $s = $stmt->fetch();

        if (!$s) Response::error("Solicitação não encontrada.");
        if ($s["estado"] !== "pendente") Response::error("Esta solicitação já foi processada.");

        $this->db->prepare("UPDATE solicitacoes SET estado = 'rejeitado', observacao = ? WHERE id = ?")
            ->execute([$dados["observacao"] ?? null, intval($id)]);

        Logger::registar("ALTERACAO", "Solicitações",
            "Solicitação rejeitada: {$s['aluno_nome']} → {$s['livro_titulo']}");

        Response::success(null, "Solicitação rejeitada.");
    }
}