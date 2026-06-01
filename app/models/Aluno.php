<?php

    namespace App\Models;

    use App\Core\Database;
    use PDO;

    class Aluno {

        private PDO $db;

        public function __construct() {

            $this->db = Database::getInstance()->getConnection();

        }

      public function total(): int {

            return (int) $this->db->query("SELECT COUNT(*) FROM alunos WHERE eliminado_em IS NULL")->fetchColumn();

        }

        public function listar(int $limite = 25, int $offset = 0): array {

            $stmt = $this->db->prepare("SELECT * FROM alunos WHERE eliminado_em IS NULL ORDER BY nome ASC LIMIT ? OFFSET ?");
            $stmt->bindValue(1, $limite, \PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();

        }

        public function encontrarPorId(int $id): array|false {

            $stmt = $this->db->prepare("SELECT * FROM alunos WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();

        }

        public function encontrarPorEmail(string $email, ?int $excluirId = null): array|false {

            $sql    = "SELECT * FROM alunos WHERE email = ? AND eliminado_em IS NULL";
            $params = [$email];
            
            if ($excluirId) {

                $sql .= " AND id != ?";
                $params[] = $excluirId;

            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();

        }

        public function encontrarPorNumero(string $numero, ?int $excluirId = null): array|false {

            $sql    = "SELECT * FROM alunos WHERE numero_estudante = ? AND eliminado_em IS NULL";
            $params = [$numero];

            if ($excluirId) {

                $sql .= " AND id != ?";
                $params[] = $excluirId;

            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();

        }

        public function encontrarPorTelefone(string $telefone, ?int $excluirId = null): array|false {

            $sql    = "SELECT * FROM alunos WHERE telefone = ? AND eliminado_em IS NULL";
            $params = [$telefone];
            
            if ($excluirId) {

                $sql .= " AND id != ?";
                $params[] = $excluirId;

            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();

        }

        public function criar(array $dados): bool {
            
            $stmt = $this->db->prepare("INSERT INTO alunos 
                (numero_estudante, nome, curso, turma, telefone, email, 
                foto, `status`, senha_temporaria, utilizador_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'activo', 1, ?)");
            return $stmt->execute([
                $dados["numero_estudante"],
                $dados["nome"],
                $dados["curso"],
                $dados["turma"]         ?? null,
                $dados["telefone"]      ?? null,
                $dados["email"]         ?? null,
                $dados["foto"]          ?? null,
                $dados["utilizador_id"] ?? null,

            ]);

        }

        public function actualizar(int $id, array $dados): bool {

            $stmt = $this->db->prepare("UPDATE alunos SET 
                numero_estudante = ?, nome = ?, curso = ?,
                turma = ?, telefone = ?, email = ?, foto = ?
                WHERE id = ?");
            return $stmt->execute([
                $dados["numero_estudante"],
                $dados["nome"],
                $dados["curso"],
                $dados["turma"]    ?? null,
                $dados["telefone"] ?? null,
                $dados["email"]    ?? null,
                $dados["foto"]     ?? null,
                $id

            ]);

        }

        public function alterarStatus(int $id, string $status): bool {

            $stmt = $this->db->prepare("UPDATE alunos SET `status` = ? WHERE id = ?");
            return $stmt->execute([$status, $id]);

        }

        public function actualizarSenha(int $id, string $senha): bool {

            $stmt = $this->db->prepare("UPDATE alunos SET 
                senha = ?, senha_temporaria = 1 WHERE id = ?");
            return $stmt->execute([password_hash($senha, PASSWORD_DEFAULT), $id]);
            
        }

       public function eliminar(int $id): bool {

            $stmt = $this->db->prepare("UPDATE alunos SET eliminado_em = NOW() WHERE id = ?");
            return $stmt->execute([$id]);

        }

        public function pesquisar(string $termo): array {

            $stmt = $this->db->prepare("SELECT * FROM alunos WHERE (nome LIKE ? OR numero_estudante LIKE ?) AND eliminado_em IS NULL ORDER BY nome ASC LIMIT 8");
            $stmt->execute(["%$termo%", "%$termo%"]);
            return $stmt->fetchAll();

        }

    }