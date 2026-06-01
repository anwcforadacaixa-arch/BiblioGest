<?php

    namespace App\Core;

    use PDO;
    use PDOException;

    class Database {

        private static ?Database $instance = null;
        private PDO $connection;

        private function __construct() {

            $config = require BASE_PATH . '/config/database.php';

            try {

                $this->connection = new PDO(
                    "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4",
                    $config['username'],
                    $config['password']

                );

                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            } catch (PDOException $e) {

                die(json_encode(["erro" => "Falha na ligação: " . $e->getMessage()]));

            }

        }

        public static function getInstance(): Database {

            if (self::$instance === null) {

                self::$instance = new Database();

            }

            return self::$instance;

        }

        public function getConnection(): PDO {

            return $this->connection;

        }
        
    }