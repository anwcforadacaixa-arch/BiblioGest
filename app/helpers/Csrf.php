<?php
namespace App\Helpers;

class Csrf {
    private const CHAVE = "csrf_token";

    public static function gerar(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION[self::CHAVE])) {
            $_SESSION[self::CHAVE] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::CHAVE];
    }

    public static function token(): string {
        return self::gerar();
    }

    public static function verificar(string $token): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $tokenSessao = $_SESSION[self::CHAVE] ?? "";
        return hash_equals($tokenSessao, $token);
    }

    public static function metaTag(): string {
        return '<meta name="csrf-token" content="' . self::token() . '">';
    }
}