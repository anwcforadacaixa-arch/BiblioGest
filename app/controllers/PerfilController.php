<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Helpers\Response;
use App\Helpers\Logger;
use App\Helpers\Upload;

class PerfilController extends Controller {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function pagina(): void {
        $this->view("perfil/index");
    }

    public function dados(): void {
        $stmt = $this->db->prepare("
            SELECT id, nome, email, numero_identificacao,
                   telefone, genero, foto, perfil, criado_em
            FROM utilizadores WHERE id = ?
        ");
        $stmt->execute([$_SESSION["id"]]);
        Response::success($stmt->fetch());
    }

    public function actualizar(): void {
        $dados = $_POST;
        $id    = $_SESSION["id"];

        // Verificar email duplicado
        if (!empty($dados["email"])) {

            $validator = new \App\Helpers\Validator();
            $validator->email("email", $dados["email"]);
            if (!$validator->valido()) {
                Response::error($validator->primeiroErro());
            }

            $check = $this->db->prepare("
                SELECT COUNT(*) FROM utilizadores
                WHERE email = ? AND id != ?
            ");
            $check->execute([$dados["email"], $id]);
            if (intval($check->fetchColumn()) > 0) {
                Response::error("Este email já está em uso.");
            }
        }

        // Upload de foto
        $fotoActual = $dados["foto_actual"] ?? null;
        $foto       = $fotoActual;

        if (!empty($_FILES["foto"]["name"])) {
            if (!empty($fotoActual)) {
                Upload::eliminar($fotoActual);
            }
            $upload = Upload::imagem($_FILES["foto"], "utilizadores");
            if (!$upload["sucesso"]) {
                Response::error($upload["erro"]);
            }
            $foto = $upload["caminho"];
        }

        $stmt = $this->db->prepare("UPDATE utilizadores SET
            nome = ?, email = ?, telefone = ?, genero = ?, foto = ?
            WHERE id = ?");
        $stmt->execute([
            $dados["nome"],
            $dados["email"]    ?? null,
            $dados["telefone"] ?? null,
            $dados["genero"]   ?? null,
            $foto,
            $id
        ]);

        // Actualizar sessão
        $_SESSION["nome"] = $dados["nome"];

        Logger::registar("EDITAR", "Perfil", "Perfil actualizado: " . $dados["nome"]);
        Response::success(["foto" => $foto], "Perfil actualizado com sucesso.");
    }

    public function alterarSenha(): void {
        $dados       = json_decode(file_get_contents("php://input"), true);
        $senhaActual = $dados["senha_actual"] ?? "";
        $novaSenha   = $dados["nova_senha"]   ?? "";
        $confirmar   = $dados["confirmar"]    ?? "";

        if (empty($senhaActual) || empty($novaSenha) || empty($confirmar)) {
            Response::error("Todos os campos são obrigatórios.");
        }

        if (strlen($novaSenha) < 6) {
            Response::error("A nova senha deve ter pelo menos 6 caracteres.");
        }

        if ($novaSenha !== $confirmar) {
            Response::error("As senhas não coincidem.");
        }

        // Verificar senha actual
        $stmt = $this->db->prepare("SELECT senha FROM utilizadores WHERE id = ?");
        $stmt->execute([$_SESSION["id"]]);
        $utilizador = $stmt->fetch();

        if (!password_verify($senhaActual, $utilizador["senha"])) {
            Response::error("A senha actual está incorrecta.");
        }

        $this->db->prepare("UPDATE utilizadores SET
            senha = ?, senha_temporaria = 0 WHERE id = ?")
            ->execute([
                password_hash($novaSenha, PASSWORD_DEFAULT),
                $_SESSION["id"]
            ]);

        Logger::registar("ALTERACAO", "Perfil", "Senha alterada: " . $_SESSION["nome"]);
        Response::success(null, "Senha alterada com sucesso.");
    }

    public function historico(): void {
        $pagina    = intval($_GET["pagina"] ?? 1);
        $limite    = 20;
        $offset    = ($pagina - 1) * $limite;

        $total = $this->db->prepare("
            SELECT COUNT(*) FROM logs WHERE utilizador_id = ?
        ");
        $total->execute([$_SESSION["id"]]);
        $totalRegistos = intval($total->fetchColumn());

        $paginator = new \App\Helpers\Paginator($totalRegistos, $limite, $pagina);
        $lim       = $paginator->limite();
        $off       = $paginator->offset();

        $stmt = $this->db->prepare("
            SELECT acao, modulo, descricao, data_hora
            FROM logs
            WHERE utilizador_id = ?
            ORDER BY data_hora DESC
            LIMIT $lim OFFSET $off
        ");
        $stmt->execute([$_SESSION["id"]]);

        Response::paginated($stmt->fetchAll(), $paginator->meta());
    }
}