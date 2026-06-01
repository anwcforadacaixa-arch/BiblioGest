<?php
namespace App\Helpers;

class Upload {
    private static array $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp'];
    private static int   $tamanhoMaximo   = 2097152; // 2MB

    public static function imagem(array $ficheiro, string $pasta): array {
        if ($ficheiro['error'] !== UPLOAD_ERR_OK) {
            return ["sucesso" => false, "erro" => "Erro no upload do ficheiro."];
        }

        if ($ficheiro['size'] > self::$tamanhoMaximo) {
            return ["sucesso" => false, "erro" => "Ficheiro demasiado grande. Máximo 2MB."];
        }

        $tipo = mime_content_type($ficheiro['tmp_name']);
        if (!in_array($tipo, self::$tiposPermitidos)) {
            return ["sucesso" => false, "erro" => "Tipo de ficheiro não permitido. Use JPG, PNG ou WEBP."];
        }

        $extensao = pathinfo($ficheiro['name'], PATHINFO_EXTENSION);
        $nome     = uniqid() . "_" . time() . "." . strtolower($extensao);
        $destino  = BASE_PATH . "/public/assets/uploads/{$pasta}/" . $nome;

        if (!is_dir(dirname($destino))) {
            mkdir(dirname($destino), 0755, true);
        }

        if (!move_uploaded_file($ficheiro['tmp_name'], $destino)) {
            return ["sucesso" => false, "erro" => "Erro ao guardar o ficheiro."];
        }

        return ["sucesso" => true, "nome" => $nome, "caminho" => "/assets/uploads/{$pasta}/" . $nome];
    }

    public static function eliminar(string $caminho): void {
        $ficheiro = BASE_PATH . "/public" . $caminho;
        if (file_exists($ficheiro)) {
            unlink($ficheiro);
        }
    }
}