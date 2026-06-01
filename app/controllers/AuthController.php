<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;
use App\Helpers\Response;
use App\Helpers\Validator;
use App\Helpers\Logger;

class AuthController extends Controller {

    private AuthService $authService;

    public function __construct() {

        $this->authService = new AuthService();

    }

    // Página de login
    public function loginPage(): void {

        if (isset($_SESSION["id"])) {

            \App\Helpers\Redirecionamento::ir($_SESSION["perfil"]);

        }

        $this->view("auth/login");

    }

    // Processar login
    public function processarLogin(): void {

        $dados = json_decode(file_get_contents("php://input"), true);

        $validator = new Validator();
        $validator
            ->obrigatorio("identificador", $dados["identificador"] ?? "")
            ->obrigatorio("senha",         $dados["senha"]         ?? "");

        if (!$validator->valido()) {

            Response::error($validator->primeiroErro());

        }

        $resultado = $this->authService->login(

            $dados["identificador"],
            $dados["senha"]

        );

        if (!$resultado["sucesso"]) {

            Response::error($resultado["erro"], 401);

        }

        // Destino centralizado
        $destino = $resultado["senha_temporaria"]

            ? "/auth/alterar-senha"
            : \App\Helpers\Redirecionamento::destino($resultado["perfil"]);

        Response::success([
            "perfil"           => $resultado["perfil"],
            "senha_temporaria" => $resultado["senha_temporaria"],
            "destino"          => $destino
        ], "Login realizado com sucesso.");

    }

    public function alterarSenhaPagina(): void {

        if (!isset($_SESSION["id"])) {
            header("Location: /");
            exit;
        }
        $this->view("auth/alterar_senha");

    }

    public function alterarSenha(): void {

        if (!isset($_SESSION["id"])) {
            Response::unauthorized();
        }

        $dados     = json_decode(file_get_contents("php://input"), true);
        $novaSenha = $dados["nova_senha"] ?? "";

        if (strlen($novaSenha) < 6) {
            Response::error("A senha deve ter pelo menos 6 caracteres.");
        }

        $pdo  = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("UPDATE utilizadores SET 
            senha = ?, senha_temporaria = 0 WHERE id = ?");
        $stmt->execute([
            password_hash($novaSenha, PASSWORD_DEFAULT),
            $_SESSION["id"]
        ]);

        $_SESSION["senha_temporaria"] = 0;

        Logger::registar("ALTERACAO", "Autenticação", "Senha alterada por: " . $_SESSION["nome"]);

        // Destino centralizado após alterar senha
        $destino = \App\Helpers\Redirecionamento::destino($_SESSION["perfil"]);
        Response::success(["destino" => $destino], "Senha alterada com sucesso.");

    }

    public function logout(): void {

        $this->authService->logout();
        header("Location: /");
        exit;
        
    }
    
}