<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\EntradaService;
use App\Helpers\Response;
use App\Middleware\AuthMiddleware;

class EntradaController extends Controller {
    private EntradaService $service;

    public function __construct() {
        $this->service = new EntradaService();
    }

    public function pagina(): void {
        
        $this->view("entradas/index");
    }

    public function listar(): void {

        $pagina    = intval($_GET["pagina"] ?? 1);
        $paginator = new \App\Helpers\Paginator($this->service->total(), 25, $pagina);

        $dados = $this->service->listar($paginator->limite(), $paginator->offset());
        Response::paginated($dados, $paginator->meta());
        
    }

    public function resumo(): void {
        
        Response::success($this->service->resumoDia());
    }

    public function pesquisarAlunos(): void {
        
        $termo = $_GET["termo"] ?? "";
        Response::success($this->service->pesquisarAlunos($termo));
    }

    public function registar(): void {
        
        $dados = json_decode(file_get_contents("php://input"), true);

        if (empty($dados["aluno_id"]) || empty($dados["tipo"])) {
            Response::error("Todos os campos são obrigatórios.");
        }

        $resultado = $this->service->registar(
            intval($dados["aluno_id"]),
            $dados["tipo"]
        );

        if (!$resultado["sucesso"]) {
            Response::error($resultado["erro"]);
        }

        Response::success(null, "Movimento registado com sucesso.", 201);
    }

    public function eliminar(string $id): void {
        
        $this->service->eliminar(intval($id));
        Response::success(null, "Registo eliminado com sucesso.");
    }
}