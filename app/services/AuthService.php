<?php
namespace App\Services;

use App\Models\Usuarios;
use App\Core\Database;
use App\Helpers\Logger;

class AuthService {
    private Usuarios $usuario;
    private $db;

    public function __construct() {
        $this->usuario = new Usuarios();
        $this->db      = Database::getInstance()->getConnection();
    }

    public function login(string $identificador, string $senha): array {
        if (empty($identificador) || empty($senha)) {
            return ["sucesso" => false, "erro" => "Identificador e senha são obrigatórios."];
        }

        // Verificar se é email ou número de estudante
        if (filter_var($identificador, FILTER_VALIDATE_EMAIL)) {
            // Login com email — admin ou bibliotecário
            $utilizador = $this->usuario->encontrarPorEmail($identificador);

            if (!$utilizador || !password_verify($senha, $utilizador["senha"])) {
                return ["sucesso" => false, "erro" => "Credenciais incorrectas."];
            }

            if ($utilizador["status"] !== "activo") {
                return ["sucesso" => false, "erro" => "Conta inactiva ou bloqueada."];
            }

        } else {
            // Login com número de estudante — aluno
            $stmt = $this->db->prepare("
                SELECT u.*, a.numero_estudante, a.nome as nome_aluno,
                       a.senha_temporaria as aluno_senha_temporaria
                FROM alunos a
                JOIN utilizadores u ON a.utilizador_id = u.id
                WHERE a.numero_estudante = ?
                AND a.eliminado_em IS NULL
            ");
            $stmt->execute([$identificador]);
            $utilizador = $stmt->fetch();

            if (!$utilizador || !password_verify($senha, $utilizador["senha"])) {
                return ["sucesso" => false, "erro" => "Número de estudante ou senha incorrectos."];
            }

            if ($utilizador["status"] !== "activo") {
                return ["sucesso" => false, "erro" => "Conta inactiva ou bloqueada."];
            }
        }

        $_SESSION["id"]              = $utilizador["id"];
        $_SESSION["nome"]            = $utilizador["nome"];
        $_SESSION["perfil"]          = $utilizador["perfil"];
        $_SESSION["senha_temporaria"] = $utilizador["senha_temporaria"];

        Logger::registar("LOGIN", "Autenticação", "Login realizado por " . $utilizador["nome"]);

        return [
            "sucesso"          => true,
            "perfil"           => $utilizador["perfil"],
            "senha_temporaria" => (bool) $utilizador["senha_temporaria"]
        ];
    }

    public function logout(): void {
        Logger::registar("LOGOUT", "Autenticação", "Logout realizado por " . ($_SESSION["nome"] ?? "Desconhecido"));
        session_destroy();
    }
}