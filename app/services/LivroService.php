<?php
namespace App\Services;

use App\Models\Livro;
use App\Helpers\Logger;

class LivroService {

    private Livro $livro;

    public function __construct() {

        $this->livro = new Livro();

    }

   public function total(): int {

        return $this->livro->total();

    }

    public function listar(int $limite = 25, int $offset = 0): array {

        return $this->livro->listar($limite, $offset);

    }

    public function pesquisar(string $termo): array {

        return $this->livro->pesquisar($termo);

    }

    public function criar(array $dados): array {
        $this->livro->criar($dados);
        Logger::registar("CRIAR", "Acervo", "Livro criado: " . $dados["titulo"]);
        return ["sucesso" => true];
    }

    public function actualizar(int $id, array $dados): array {
        $this->livro->actualizar($id, $dados);
        Logger::registar("EDITAR", "Acervo", "Livro editado: " . $dados["titulo"]);
        return ["sucesso" => true];
    }

    public function eliminar(int $id): array {

        $livro = $this->livro->encontrarPorId($id);
        $this->livro->eliminar($id);
        Logger::registar("ELIMINAR", "Acervo", "Livro eliminado: " . ($livro["titulo"] ?? "ID $id"));
        return ["sucesso" => true];

    }
        
}