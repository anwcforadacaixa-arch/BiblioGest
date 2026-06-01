<?php

    namespace App\Models;

    use App\Core\Database;
    use PDO;

    class Usuarios {

        private PDO $db;

        public function __construct() {

            $this->db = Database::getInstance()->getConnection();

        }

        public function encontrarPorEmail(string $email): array|false {

            $stmt = $this->db->prepare("SELECT * FROM utilizadores WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch();

        }

        public function criar(string $nome, string $email, string $senha, string $perfil): bool {

            $stmt = $this->db->prepare("INSERT INTO utilizadores (nome, email, senha, perfil) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$nome, $email, password_hash($senha, PASSWORD_DEFAULT), $perfil]);

        }
        
    }