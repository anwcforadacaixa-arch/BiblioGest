<?php
namespace App\Services;

use App\Models\Emprestimo;
use App\Models\Livro;
use App\Helpers\Logger;

class EmprestimoService {

    private Emprestimo $emprestimo;
    private Livro $livro;

    public function __construct() {

        $this->emprestimo = new Emprestimo();
        $this->livro      = new Livro();

    }

    public function totalPorEstado(string $estado): int {

        return $this->emprestimo->totalPorEstado($estado);

    }

    public function actualizarAtrasados(): void {

        $this->emprestimo->actualizarAtrasados();

    }

    public function listarPorEstado(string $estado, int $limite = 25, int $offset = 0): array {

        return $this->emprestimo->listarPorEstado($estado, $limite, $offset);

    }

    public function contarPorEstado(): array {

        return $this->emprestimo->contarPorEstado();

    }

    public function criar(array $dados): array {

        $pdo = \App\Core\Database::getInstance()->getConnection();

        try {

            $pdo->beginTransaction();

            $livro = $this->livro->encontrarPorId(intval($dados["livro_id"]));

            if (!$livro || $livro["quantidade_disponivel"] < 1) {

                $pdo->rollBack();
                return ["sucesso" => false, "erro" => "Este livro não tem exemplares disponíveis."];

            }

            if ($this->emprestimo->alunoTemEmprestimoActivo(intval($dados["aluno_id"]))) {

                $pdo->rollBack();
                return ["sucesso" => false, "erro" => "Este aluno já tem um empréstimo activo."];

            }

            $this->emprestimo->criar($dados);
            $this->livro->decrementarDisponivel(intval($dados["livro_id"]));

            Logger::registar("CRIAR", "Empréstimos", "Empréstimo criado para aluno ID: " . $dados["aluno_id"]);

            $pdo->commit();
            return ["sucesso" => true];

        } catch (\Exception $e) {

            $pdo->rollBack();
            return ["sucesso" => false, "erro" => "Erro ao criar empréstimo. Tente novamente."];

        }

    }

    public function devolver(int $id, string $dataReal): array {

        $pdo = \App\Core\Database::getInstance()->getConnection();

        try {
            $pdo->beginTransaction();

            $emprestimo = $this->emprestimo->encontrarPorId($id);

            if (!$emprestimo) {

                $pdo->rollBack();
                return ["sucesso" => false, "erro" => "Empréstimo não encontrado."];

            }

            $this->emprestimo->devolver($id, $dataReal);
            $this->livro->incrementarDisponivel(intval($emprestimo["livro_id"]));

            Logger::registar("DEVOLVER", "Empréstimos", "Devolução registada para empréstimo ID: $id");

            $pdo->commit();
            return ["sucesso" => true];

        } catch (\Exception $e) {

            $pdo->rollBack();
            return ["sucesso" => false, "erro" => "Erro ao registar devolução. Tente novamente."];

        }
        
    }

    public function estatisticas(): array {

        return $this->emprestimo->estatisticas();

    }
    
}