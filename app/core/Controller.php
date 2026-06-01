<?php

    namespace App\Core;

    class Controller {

        protected function view(string $view, array $data = []): void {

            extract($data);
            $viewPath = BASE_PATH . "/views/{$view}.php";

            if (!file_exists($viewPath)) {

                die("View '{$view}' não encontrada.");

            }

            require $viewPath;
        }

        protected function json(array $data, int $status = 200): void {
            
            http_response_code($status);
            header('Content-Type: application/json');
            echo json_encode($data);
            exit;

        }
        
    }