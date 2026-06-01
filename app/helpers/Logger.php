<?php

    namespace App\Helpers;

    use App\Core\Database;

    class Logger {

        public static function registar(string $acao, string $modulo, string $descricao): void {

            try {

                $pdo  = Database::getInstance()->getConnection();
                $id   = $_SESSION["id"]   ?? null;
                $nome = $_SESSION["nome"] ?? "Sistema";

                $stmt = $pdo->prepare("INSERT INTO logs (utilizador_id, utilizador_nome, acao, modulo, descricao) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$id, $nome, $acao, $modulo, $descricao]);

            } catch (\Exception $e) {

                // Log em ficheiro se a BD falhar
                $linha = date("Y-m-d H:i:s") . " | {$acao} | {$modulo} | {$descricao}\n";
                file_put_contents(BASE_PATH . "/storage/logs/sistema.log", $linha, FILE_APPEND);

            }

        }
        
    }