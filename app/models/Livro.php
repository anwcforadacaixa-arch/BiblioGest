<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Livro
{
    private PDO $db;

    public function __construct(){

        $this->db = Database::getInstance()->getConnection();

    }

    public function total(): int {

        return (int) $this->db->query("SELECT COUNT(*) FROM livros WHERE eliminado_em IS NULL")->fetchColumn();

    }

    public function listar(int $limite = 25, int $offset = 0): array {
        $stmt = $this->db->prepare("SELECT * FROM livros WHERE eliminado_em IS NULL ORDER BY titulo ASC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $limite, \PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();

    }

    public function encontrarPorId(int $id): array|false{

        $stmt = $this->db->prepare("SELECT * FROM livros WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();

    }

    public function criar(array $dados): bool {

        $quantidade = intval($dados["quantidade_total"]);
        $stmt = $this->db->prepare("INSERT INTO livros 
            (titulo, autor, isbn, categoria, capa, quantidade_total, quantidade_disponivel) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $dados["titulo"],
            $dados["autor"],
            $dados["isbn"]      ?? null,
            $dados["categoria"] ?? null,
            $dados["capa"]      ?? null,
            $quantidade,
            $quantidade
        ]);

    }

    public function actualizar(int $id, array $dados): bool {

        $novoTotal = intval($dados["quantidade_total"]);

        $emprestados = $this->db->prepare("SELECT COUNT(*) FROM emprestimos 
            WHERE livro_id = ? AND estado = 'activo'");
        $emprestados->execute([$id]);
        $totalEmprestados = intval($emprestados->fetchColumn());
        $novoDisponivel   = max(0, $novoTotal - $totalEmprestados);

        $stmt = $this->db->prepare("UPDATE livros SET 
            titulo = ?, autor = ?, isbn = ?, categoria = ?, capa = ?,
            quantidade_total = ?, quantidade_disponivel = ?
            WHERE id = ?");
        return $stmt->execute([
            $dados["titulo"],
            $dados["autor"],
            $dados["isbn"]      ?? null,
            $dados["categoria"] ?? null,
            $dados["capa"]      ?? null,
            $novoTotal,
            $novoDisponivel,
            $id
        ]);
        
    }

    public function eliminar(int $id): bool {

        $stmt = $this->db->prepare("UPDATE livros SET eliminado_em = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
        
    }

    public function pesquisar(string $termo): array {

        $stmt = $this->db->prepare("SELECT * FROM livros WHERE (titulo LIKE ? OR autor LIKE ?) AND quantidade_disponivel > 0 AND eliminado_em IS NULL ORDER BY titulo ASC LIMIT 8");
        $stmt->execute(["%$termo%", "%$termo%"]);
        return $stmt->fetchAll();
        
    }

    public function decrementarDisponivel(int $id): bool{

        $stmt = $this->db->prepare("UPDATE livros SET quantidade_disponivel = quantidade_disponivel - 1 WHERE id = ? AND quantidade_disponivel > 0");
        return $stmt->execute([$id]);

    }

    public function incrementarDisponivel(int $id): bool{

        $stmt = $this->db->prepare("UPDATE livros SET quantidade_disponivel = quantidade_disponivel + 1 WHERE id = ?");
        return $stmt->execute([$id]);

    }
    
}
