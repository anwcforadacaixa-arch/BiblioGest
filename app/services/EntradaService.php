<?php
namespace App\Services;

use App\Models\Entrada;
use App\Helpers\Logger;

class EntradaService {
    private Entrada $entrada;

    public function __construct() {
        $this->entrada = new Entrada();
    }

    public function total(): int {

        return $this->entrada->total();

    }

    public function listar(int $limite = 25, int $offset = 0): array {

        return $this->entrada->listar($limite, $offset);

    }

    public function resumoDia(): array {
        return $this->entrada->resumoDia();
    }

    public function pesquisarAlunos(string $termo): array {
        return $this->entrada->pesquisarAlunos($termo);
    }

    public function registar(int $alunoId, string $tipo): array {
        $ultimo = $this->entrada->ultimoMovimento($alunoId);

        if (!$ultimo && $tipo === "saida") {
            return ["sucesso" => false, "erro" => "Este aluno não tem entrada registada para dar saída."];
        }

        if ($ultimo === $tipo) {
            $msg = $tipo === "entrada"
                ? "Este aluno já tem uma entrada registada sem saída correspondente."
                : "Este aluno não tem entrada registada para dar saída.";
            return ["sucesso" => false, "erro" => $msg];
        }

        $this->entrada->registar($alunoId, $tipo);
        Logger::registar(
            strtoupper($tipo),
            "Entradas/Saídas",
            "Movimento '$tipo' registado para aluno ID: $alunoId"
        );

        return ["sucesso" => true];
    }

    public function eliminar(int $id): array {
        $this->entrada->eliminar($id);
        Logger::registar("ELIMINAR", "Entradas/Saídas", "Movimento eliminado ID: $id");
        return ["sucesso" => true];
    }
}