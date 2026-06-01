<?php

    namespace App\Helpers;

    class Response {

        public static function success(mixed $data = null, string $mensagem = "Operação realizada com sucesso.", int $status = 200): void {

            http_response_code($status);
            header('Content-Type: application/json');
            echo json_encode([
                "sucesso"  => true,
                "mensagem" => $mensagem,
                "dados"    => $data
            ]);

            exit;

        }

        public static function error(string $mensagem, int $status = 400): void {

            http_response_code($status);
            header('Content-Type: application/json');
            echo json_encode([
                "sucesso"  => false,
                "erro"     => $mensagem
            ]);

            exit;
            
        }

        public static function unauthorized(): void {

            self::error("Não autorizado.", 401);

        }

        public static function forbidden(): void {

            self::error("Acesso negado.", 403);

        }

        public static function notFound(string $mensagem = "Recurso não encontrado."): void {

            self::error($mensagem, 404);

        }

        public static function paginated(array $dados, array $meta, string $mensagem = "OK"): void {

            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode([
                "sucesso"  => true,
                "mensagem" => $mensagem,
                "dados"    => $dados,
                "meta"     => $meta
            ]);
            exit;
            
        }
        
    }