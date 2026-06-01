<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\EmprestimoService;
use App\Helpers\Response;
use App\Middleware\AuthMiddleware;

class EmprestimoController extends Controller {

    private EmprestimoService $service;

    public function __construct() {

        $this->service = new EmprestimoService();

    }

    public function pagina(): void {

        
        $this->view("emprestimos/index");
    }


    public function listar(): void {

        $estado    = $_GET["estado"] ?? "activo";
        $pagina    = intval($_GET["pagina"] ?? 1);
        $paginator = new \App\Helpers\Paginator($this->service->totalPorEstado($estado), 25, $pagina);

        $this->service->actualizarAtrasados();
        
        $dados = $this->service->listarPorEstado($estado, $paginator->limite(), $paginator->offset());
        Response::paginated($dados, $paginator->meta());

    }

    public function contar(): void {
        
        Response::success($this->service->contarPorEstado());
    }

    public function criar(): void {

        
        $dados = json_decode(file_get_contents("php://input"), true);

        $resultado = $this->service->criar($dados);

        if (!$resultado["sucesso"]) {

            Response::error($resultado["erro"]);


        }

        Response::success(null, "Empréstimo criado com sucesso.", 201);
    }

    public function devolver(string $id): void {

        
        $dados = json_decode(file_get_contents("php://input"), true);

        $resultado = $this->service->devolver(intval($id), $dados["data_devolucao_real"]);

        if (!$resultado["sucesso"]) {

            Response::error($resultado["erro"]);

        }

        Response::success(null, "Devolução registada com sucesso.");

    }

    public function estatisticas(): void {

        
        Response::success($this->service->estatisticas());

    }
    
}