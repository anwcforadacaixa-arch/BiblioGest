<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Helpers\Response;
use App\Helpers\Logger;
use App\Helpers\Upload;

class UtilizadorController extends Controller {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function pagina(): void {
        $this->view("utilizadores/index");
    }

    public function listar(): void {
        $stmt = $this->db->query("SELECT id, nome, email, numero_identificacao, telefone, genero, foto, `status`, perfil, criado_em FROM utilizadores WHERE perfil = 'bibliotecario' ORDER BY nome ASC");
        Response::success($stmt->fetchAll());
    }

    public function criar(): void {
        // Suporta multipart/form-data para foto
        $dados = $_POST;

        if (empty($dados["nome"]) || empty($dados["email"])) {
            Response::error("Nome e email são obrigatórios.");
        }

        // Verificar email duplicado
        $check = $this->db->prepare("SELECT COUNT(*) FROM utilizadores WHERE email = ?");
        $check->execute([$dados["email"]]);
        if (intval($check->fetchColumn()) > 0) {
            Response::error("Já existe um utilizador com este email.");
        }

        // Gerar senha temporária
        $senhaTemporaria = strtoupper(substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, 4))
                         . rand(100, 999)
                         . strtoupper(substr($dados["nome"], 0, 2));

        // Upload de foto
        $foto = null;
        if (!empty($_FILES["foto"]["name"])) {
            $resultado = Upload::imagem($_FILES["foto"], "utilizadores");
            if (!$resultado["sucesso"]) {
                Response::error($resultado["erro"]);
            }
            $foto = $resultado["caminho"];
        }

        $stmt = $this->db->prepare("INSERT INTO utilizadores 
            (nome, email, numero_identificacao, telefone, genero, foto, senha, perfil, `status`, senha_temporaria)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'bibliotecario', 'activo', 1)");
        $stmt->execute([
            $dados["nome"],
            $dados["email"],
            $dados["numero_identificacao"] ?? null,
            $dados["telefone"]             ?? null,
            $dados["genero"]               ?? null,
            $foto,
            password_hash($senhaTemporaria, PASSWORD_DEFAULT),
        ]);

        Logger::registar("CRIAR", "Utilizadores", "Bibliotecário criado: " . $dados["nome"]);

        Response::success(
            ["senha_temporaria" => $senhaTemporaria],
            "Bibliotecário criado com sucesso."
        );
    }

    public function actualizar(string $id): void {
        $dados = $_POST;
        $id    = intval($id);

        // Upload de nova foto
        $foto = $dados["foto_actual"] ?? null;
        if (!empty($_FILES["foto"]["name"])) {
            // Eliminar foto antiga
            if (!empty($dados["foto_actual"])) {
                Upload::eliminar($dados["foto_actual"]);
            }
            $resultado = Upload::imagem($_FILES["foto"], "utilizadores");
            if (!$resultado["sucesso"]) {
                Response::error($resultado["erro"]);
            }
            $foto = $resultado["caminho"];
        }

        $stmt = $this->db->prepare("UPDATE utilizadores SET
            nome = ?, email = ?, numero_identificacao = ?,
            telefone = ?, genero = ?, foto = ?
            WHERE id = ?");
        $stmt->execute([
            $dados["nome"],
            $dados["email"],
            $dados["numero_identificacao"] ?? null,
            $dados["telefone"]             ?? null,
            $dados["genero"]               ?? null,
            $foto,
            $id
        ]);

        Logger::registar("EDITAR", "Utilizadores", "Bibliotecário editado: ID $id");
        Response::success(null, "Dados actualizados com sucesso.");
    }

    public function alterarStatus(string $id): void {
        $dados  = json_decode(file_get_contents("php://input"), true);
        $status = $dados["status"] ?? "";

        if (!in_array($status, ["activo", "inactivo", "bloqueado"])) {
            Response::error("Status inválido.");
        }

        $stmt = $this->db->prepare("UPDATE utilizadores SET `status` = ? WHERE id = ?");
        $stmt->execute([$status, intval($id)]);

        Logger::registar("ALTERACAO", "Utilizadores", "Status alterado para '$status': ID $id");
        Response::success(null, "Status actualizado com sucesso.");
    }

    public function redefinirSenha(string $id): void {
        $utilizador = $this->db->prepare("SELECT nome FROM utilizadores WHERE id = ?");
        $utilizador->execute([intval($id)]);
        $u = $utilizador->fetch();

        if (!$u) {
            Response::error("Utilizador não encontrado.");
        }

        $novaSenha = strtoupper(substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, 4))
                   . rand(100, 999)
                   . "@BG";

        $stmt = $this->db->prepare("UPDATE utilizadores SET 
            senha = ?, senha_temporaria = 1 WHERE id = ?");
        $stmt->execute([password_hash($novaSenha, PASSWORD_DEFAULT), intval($id)]);

        Logger::registar("ALTERACAO", "Utilizadores", "Senha redefinida para: " . $u["nome"]);
        Response::success(["senha_temporaria" => $novaSenha], "Senha redefinida com sucesso.");
    }
}