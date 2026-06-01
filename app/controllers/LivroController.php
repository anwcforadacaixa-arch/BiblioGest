<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\LivroService;
use App\Helpers\Response;
use App\Helpers\Validator;
use App\Middleware\AuthMiddleware;

class LivroController extends Controller {

    private LivroService $service;

    public function __construct() {

        $this->service = new LivroService();

    }

    public function pagina(): void {

        
        $this->view("acervo/index");

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
            ->obrigatorio("titulo",          $dados["titulo"]          ?? "")
            ->obrigatorio("autor",           $dados["autor"]           ?? "")
            ->obrigatorio("quantidade_total", $dados["quantidade_total"] ?? "")
            ->numerico("quantidade_total",    $dados["quantidade_total"] ?? "");

        if (!$validator->valido()) {
            Response::error($validator->primeiroErro());
        }

        // Upload de capa
        if (!empty($_FILES["capa"]["name"])) {
            $upload = \App\Helpers\Upload::imagem($_FILES["capa"], "livros");
            if (!$upload["sucesso"]) {
                Response::error($upload["erro"]);
            }
            $dados["capa"] = $upload["caminho"];
        }

        $resultado = $this->service->criar($dados);
        Response::success(null, "Livro criado com sucesso.", 201);

    }

    public function actualizar(string $id): void {

        $dados = $_POST;

        // Upload de nova capa
        if (!empty($_FILES["capa"]["name"])) {
            // Eliminar capa antiga
            if (!empty($dados["capa_actual"])) {
                \App\Helpers\Upload::eliminar($dados["capa_actual"]);
            }
            $upload = \App\Helpers\Upload::imagem($_FILES["capa"], "livros");
            if (!$upload["sucesso"]) {
                Response::error($upload["erro"]);
            }
            $dados["capa"] = $upload["caminho"];
        } else {
            $dados["capa"] = $dados["capa_actual"] ?? null;
        }

        $this->service->actualizar(intval($id), $dados);
        Response::success(null, "Livro actualizado com sucesso.");
        
    }

    public function eliminar(string $id): void {

        
        $this->service->eliminar(intval($id));
        Response::success(null, "Livro eliminado com sucesso.");

    }
    
}