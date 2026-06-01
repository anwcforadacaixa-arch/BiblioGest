<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Entrada {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function total(): int {

        return (int) $this->db->query("SELECT COUNT(*) FROM entradas_saidas")->fetchColumn();

    }

    public function listar(int $limite = 20, int $offset = 0): array {

        $stmt = $this->db->prepare("SELECT e.id, a.nome, a.numero_estudante, e.tipo, e.data_hora FROM entradas_saidas e JOIN alunos a ON e.aluno_id = a.id ORDER BY e.data_hora DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $limite, \PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
        
    }

    public function resumoDia(): array {
        $entradas = $this->db->query("SELECT COUNT(*) FROM entradas_saidas WHERE tipo = 'entrada' AND DATE(data_hora) = CURDATE()")->fetchColumn();

        $saidas = $this->db->query("SELECT COUNT(*) FROM entradas_saidas WHERE tipo = 'saida' AND DATE(data_hora) = CURDATE()")->fetchColumn();

        return [
            "entradas"  => $entradas,
            "saidas"    => $saidas,
            "presentes" => max(0, intval($entradas) - intval($saidas))
        ];
    }

    public function ultimoMovimento(int $alunoId): string|false {
        $stmt = $this->db->prepare("SELECT tipo FROM entradas_saidas WHERE aluno_id = ? ORDER BY data_hora DESC LIMIT 1");
        $stmt->execute([$alunoId]);
        return $stmt->fetchColumn();
    }

    public function registar(int $alunoId, string $tipo): bool {
        $stmt = $this->db->prepare("INSERT INTO entradas_saidas (aluno_id, tipo) VALUES (?, ?)");
        return $stmt->execute([$alunoId, $tipo]);
    }

    public function eliminar(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM entradas_saidas WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function pesquisarAlunos(string $termo): array {
        $stmt = $this->db->prepare("SELECT id, nome, numero_estudante FROM alunos WHERE nome LIKE ? OR numero_estudante LIKE ? ORDER BY nome ASC LIMIT 8");
        $stmt->execute(["%$termo%", "%$termo%"]);
        return $stmt->fetchAll();
    }

}