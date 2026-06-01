<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Emprestimo {
    private PDO $db;

    public function __construct() {

        $this->db = Database::getInstance()->getConnection();

    }

    public function totalPorEstado(string $estado): int {

        if ($estado === "devolvido") {

            return (int) $this->db->query("SELECT COUNT(*) FROM emprestimos WHERE estado = 'devolvido' AND DATE(data_devolucao_real) = CURDATE()")->fetchColumn();
        
        }
        
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM emprestimos WHERE estado = ?");
        $stmt->execute([$estado]);
        return (int) $stmt->fetchColumn();

    }

    public function listarPorEstado(string $estado, int $limite = 25, int $offset = 0): array {

        if ($estado === "devolvido") {

            $stmt = $this->db->prepare("SELECT e.id, a.nome AS aluno, l.titulo AS livro, e.data_emprestimo, e.data_devolucao_prevista, e.data_devolucao_real, e.estado FROM emprestimos e JOIN alunos a ON e.aluno_id = a.id JOIN livros l ON e.livro_id = l.id WHERE e.estado = 'devolvido' AND DATE(e.data_devolucao_real) = CURDATE() ORDER BY e.data_devolucao_real DESC LIMIT ? OFFSET ?");
            $stmt->bindValue(1, $limite, \PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        }

        $stmt = $this->db->prepare("SELECT e.id, a.nome AS aluno, l.titulo AS livro, e.data_emprestimo, e.data_devolucao_prevista, e.data_devolucao_real, e.estado FROM emprestimos e JOIN alunos a ON e.aluno_id = a.id JOIN livros l ON e.livro_id = l.id WHERE e.estado = ? ORDER BY e.data_emprestimo DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $estado, \PDO::PARAM_STR);
        $stmt->bindValue(2, $limite, \PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();

    }

    public function contarPorEstado(): array {

        $activos   = $this->db->query("SELECT COUNT(*) FROM emprestimos WHERE estado = 'activo'")->fetchColumn();
        $atrasados = $this->db->query("SELECT COUNT(*) FROM emprestimos WHERE estado = 'atrasado'")->fetchColumn();
        $devolvidos = $this->db->query("SELECT COUNT(*) FROM emprestimos WHERE estado = 'devolvido' AND DATE(data_devolucao_real) = CURDATE()")->fetchColumn();

        return ["activo"    => $activos, "atrasado"  => $atrasados, "devolvido" => $devolvidos];

    }

    public function encontrarPorId(int $id): array|false {

        $stmt = $this->db->prepare("SELECT * FROM emprestimos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();

    }

    public function alunoTemEmprestimoActivo(int $alunoId): bool {

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM emprestimos WHERE aluno_id = ? AND estado = 'activo'");
        $stmt->execute([$alunoId]);
        return intval($stmt->fetchColumn()) > 0;
        
    }

    public function criar(array $dados): bool {

        $stmt = $this->db->prepare("INSERT INTO emprestimos (aluno_id, livro_id, data_emprestimo, data_devolucao_prevista, estado) VALUES (?, ?, ?, ?, 'activo')");
        return $stmt->execute([$dados["aluno_id"], $dados["livro_id"], $dados["data_emprestimo"], $dados["data_devolucao_prevista"]]);

    }

    public function devolver(int $id, string $dataReal): bool {

        $stmt = $this->db->prepare("UPDATE emprestimos SET data_devolucao_real = ?, estado = 'devolvido' WHERE id = ?");
        return $stmt->execute([$dataReal, $id]);

    }

    public function actualizarAtrasados(): void {

        $this->db->query("UPDATE emprestimos SET estado = 'atrasado' WHERE estado = 'activo' AND data_devolucao_prevista < CURDATE()");
    
    }

    public function estatisticas(): array {

        $alunos     = $this->db->query("SELECT COUNT(*) FROM alunos")->fetchColumn();
        $livros     = $this->db->query("SELECT COUNT(*) FROM livros")->fetchColumn();
        $emprestimos = $this->db->query("SELECT COUNT(*) FROM emprestimos WHERE estado = 'activo'")->fetchColumn();
        $entradas   = $this->db->query("SELECT COUNT(*) FROM entradas_saidas WHERE tipo = 'entrada' AND DATE(data_hora) = CURDATE()")->fetchColumn();

        return ["alunos"      => $alunos, "livros"      => $livros, "emprestimos" => $emprestimos, "entradas"    => $entradas];

    }
    
}