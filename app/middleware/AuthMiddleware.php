<?php
namespace App\Middleware;

use App\Helpers\Response;

class AuthMiddleware {
    public static function handle(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION["id"])) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || 
                str_starts_with($_SERVER['REQUEST_URI'], '/api/')) {
                Response::unauthorized();
            }
            header("Location: /");
            exit;
        }
    }

    // Retorna callable para usar nas rotas
    public static function auth(): callable {
        return function() { self::handle(); };
    }

    public static function admin(): callable {
        return function() {
            self::handle();
            if ($_SESSION["perfil"] !== "admin") {
                if (str_starts_with($_SERVER['REQUEST_URI'], '/api/')) {
                    Response::forbidden();
                }
                header("Location: /dashboard");
                exit;
            }
        };
    }

    public static function bibliotecario(): callable {
        return function() {
            self::handle();
            if (!in_array($_SESSION["perfil"], ["admin", "bibliotecario"])) {
                if (str_starts_with($_SERVER['REQUEST_URI'], '/api/')) {
                    Response::forbidden();
                }
                header("Location: /dashboard");
                exit;
            }
        };
    }

    public static function csrf(): callable {
    return function() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? "";
            if (!\App\Helpers\Csrf::verificar($token)) {
                \App\Helpers\Response::error("Token CSRF inválido.", 403);
            }
        }
    };
}
}