<?php
namespace App\Services;

use App\Models\Aluno;
use App\Helpers\Logger;

class AlunoService {

    private Aluno $aluno;

    public function __construct() {

        $this->aluno = new Aluno();

    }

    public function total(): int {

        return $this->aluno->total();

    }

    public function listar(int $limite = 25, int $offset = 0): array {

        return $this->aluno->listar($limite, $offset);
        
    }

    public function pesquisar(string $termo): array {

        return $this->aluno->pesquisar($termo);

    }

    public function criar(array $dados, array $ficheiros = []): array {

        $pdo = \App\Core\Database::getInstance()->getConnection();

        // Validar duplicados
        if ($this->aluno->encontrarPorNumero($dados["numero_estudante"])) {
            return ["sucesso" => false, "erro" => "Já existe um aluno com este número de estudante."];
        }
        if (!empty($dados["email"]) && $this->aluno->encontrarPorEmail($dados["email"])) {
            return ["sucesso" => false, "erro" => "Já existe um aluno com este email."];
        }
        if (!empty($dados["telefone"]) && $this->aluno->encontrarPorTelefone($dados["telefone"])) {
            return ["sucesso" => false, "erro" => "Já existe um aluno com este telefone."];
        }

        try {
            $pdo->beginTransaction();

            // Gerar senha temporária
            $senhaTemporaria = $dados["numero_estudante"] . "@BG";

            // Criar utilizador
            $stmtUser = $pdo->prepare("INSERT INTO utilizadores 
                (nome, email, senha, perfil, `status`, senha_temporaria) 
                VALUES (?, ?, ?, 'aluno', 'activo', 1)");
            $stmtUser->execute([
                $dados["nome"],
                $dados["email"] ?? null,
                password_hash($senhaTemporaria, PASSWORD_DEFAULT),
            ]);
            $dados["utilizador_id"] = intval($pdo->lastInsertId());

            // Upload de foto
            $dados["foto"] = null;
            if (!empty($ficheiros["foto"]["name"])) {
                $upload = \App\Helpers\Upload::imagem($ficheiros["foto"], "alunos");
                if (!$upload["sucesso"]) {
                    $pdo->rollBack();
                    return ["sucesso" => false, "erro" => $upload["erro"]];
                }
                $dados["foto"] = $upload["caminho"];
            }

            // Criar aluno
            $this->aluno->criar($dados);

            Logger::registar("CRIAR", "Alunos", "Aluno criado: " . $dados["nome"]);
            $pdo->commit();

            return ["sucesso" => true, "senha_temporaria" => $senhaTemporaria];

        } catch (\Exception $e) {
            $pdo->rollBack();
            return ["sucesso" => false, "erro" => "Erro ao criar aluno: " . $e->getMessage()];
        }

    }

    public function actualizar(int $id, array $dados, array $ficheiros = []): array {

        if ($this->aluno->encontrarPorNumero($dados["numero_estudante"], $id)) {
            return ["sucesso" => false, "erro" => "Já existe um aluno com este número de estudante."];
        }
        if (!empty($dados["email"]) && $this->aluno->encontrarPorEmail($dados["email"], $id)) {
            return ["sucesso" => false, "erro" => "Já existe um aluno com este email."];
        }
        if (!empty($dados["telefone"]) && $this->aluno->encontrarPorTelefone($dados["telefone"], $id)) {
            return ["sucesso" => false, "erro" => "Já existe um aluno com este telefone."];
        }

        // Upload de nova foto
        if (!empty($ficheiros["foto"]["name"])) {
            if (!empty($dados["foto_actual"])) {
                \App\Helpers\Upload::eliminar($dados["foto_actual"]);
            }
            $upload = \App\Helpers\Upload::imagem($ficheiros["foto"], "alunos");
            if (!$upload["sucesso"]) return ["sucesso" => false, "erro" => $upload["erro"]];
            $dados["foto"] = $upload["caminho"];
        } else {
            $dados["foto"] = $dados["foto_actual"] ?? null;
        }

        $this->aluno->actualizar($id, $dados);
        Logger::registar("EDITAR", "Alunos", "Aluno editado: " . $dados["nome"]);
        return ["sucesso" => true];

    }

    public function alterarStatus(int $id, string $status): array {

        if (!in_array($status, ["activo", "inactivo", "bloqueado", "suspenso"])) {
            return ["sucesso" => false, "erro" => "Status inválido."];
        }
        $this->aluno->alterarStatus($id, $status);
        Logger::registar("ALTERACAO", "Alunos", "Status alterado para '$status': ID $id");
        return ["sucesso" => true];

    }

    public function redefinirSenha(int $id): array {

        $aluno = $this->aluno->encontrarPorId($id);
        if (!$aluno) {
            return ["sucesso" => false, "erro" => "Aluno não encontrado."];
        }

        if (empty($aluno["utilizador_id"])) {
            return ["sucesso" => false, "erro" => "Este aluno não tem conta de acesso."];
        }

        $senhaTemporaria = $aluno["numero_estudante"] . "@BG";
        $pdo  = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("UPDATE utilizadores SET 
            senha = ?, senha_temporaria = 1 WHERE id = ?");
        $stmt->execute([
            password_hash($senhaTemporaria, PASSWORD_DEFAULT),
            $aluno["utilizador_id"]
        ]);

        Logger::registar("ALTERACAO", "Alunos", "Senha redefinida: " . $aluno["nome"]);
        return ["sucesso" => true, "senha_temporaria" => $senhaTemporaria];
        
    }

    public function eliminar(int $id): array {

        $aluno = $this->aluno->encontrarPorId($id);
        $this->aluno->eliminar($id);
        Logger::registar("ELIMINAR", "Alunos", "Aluno eliminado: " . ($aluno["nome"] ?? "ID $id"));
        return ["sucesso" => true];

    }
    
}