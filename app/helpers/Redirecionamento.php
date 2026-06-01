<?php
namespace App\Helpers;

class Redirecionamento {
    public static function destino(string $perfil): string {
        return match($perfil) {
            "aluno" => "/painel-aluno",
            default => "/dashboard"
        };
    }

    public static function ir(string $perfil): void {
        header("Location: " . self::destino($perfil));
        exit;
    }
}