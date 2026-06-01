<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\AlunoService;
use App\Helpers\Response;
use App\Helpers\Validator;
use App\Middleware\AuthMiddleware;

class AlunoController extends Controller {

    private AlunoService $service;

    public function __construct() {

        $this->service = new AlunoService();

    }

    public function pagina(): void {

        
        $this->view("alunos/index");

    }

    public function listar(): void {

        $pagina    = intval($_GET["pagina"] ?? 1);
        $paginator = new \App\Helpers\Paginator($this->service->total(), 25, $pagina);

        $dados = $this->service->listar($paginator->limite(), $paginator->offset());
        Response::paginated($dados, $paginator->meta());
        
    }

    public function pesquisar(): void {

        
        $termo = $_GET["termo"] ?? "";
        Response::success($this->service->pesquisar($termo));

    }

    public function criar(): void {

        $dados = $_POST;

        $validator = new Validator();
        $validator
            ->obrigatorio("numero_estudante", $dados["numero_estudante"] ?? "")
            ->obrigatorio("nome", $dados["nome"] ?? "")
            ->obrigatorio("curso", $dados["curso"] ?? "")
            ->email("email", $dados["email"]?? "");

        if (!$validator->valido()) {
            Response::error($validator->primeiroErro());
        }

        $resultado = $this->service->criar($dados, $_FILES);
        if (!$resultado["sucesso"]) {
            Response::error($resultado["erro"]);
        }

        Response::success(
            ["senha_temporaria" => $resultado["senha_temporaria"]],
            "Aluno criado com sucesso.",
            201
        );

    }

    public function actualizar(string $id): void {

        $dados = $_POST;

        $resultado = $this->service->actualizar(intval($id), $dados, $_FILES);
        if (!$resultado["sucesso"]) {
            Response::error($resultado["erro"]);
        }

        Response::success(null, "Aluno actualizado com sucesso.");

    }

    public function alterarStatus(string $id): void {

        $dados     = json_decode(file_get_contents("php://input"), true);
        $resultado = $this->service->alterarStatus(intval($id), $dados["status"] ?? "");

        if (!$resultado["sucesso"]) {
            Response::error($resultado["erro"]);
        }

        Response::success(null, "Status actualizado com sucesso.");

    }

    public function redefinirSenha(string $id): void {

        $resultado = $this->service->redefinirSenha(intval($id));
        if (!$resultado["sucesso"]) {
            Response::error($resultado["erro"]);
        }
        Response::success(
            ["senha_temporaria" => $resultado["senha_temporaria"]],
            "Senha redefinida com sucesso."
        );
        
    }

    public function eliminar(string $id): void {

        
        $resultado = $this->service->eliminar(intval($id));
        Response::success(null, "Aluno eliminado com sucesso.");

    }
    
}