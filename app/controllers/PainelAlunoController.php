<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Helpers\Response;
use App\Middleware\AuthMiddleware;
use App\Helpers\Logger;

class PainelAlunoController extends Controller {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index(): void {
        $this->view("painel_aluno/index");
    }

    public function dados(): void {
        $utilizadorId = $_SESSION["id"];

        // Buscar dados do aluno
        $stmt = $this->db->prepare("SELECT a.*, u.email, u.status FROM alunos a JOIN utilizadores u ON a.utilizador_id = u.id WHERE u.id = ?");
        $stmt->execute([$utilizadorId]);
        $aluno = $stmt->fetch();

        if (!$aluno) {
            Response::error("Aluno não encontrado.");
        }

        // Empréstimos activos
        $emprestimos = $this->db->prepare("SELECT e.id, l.titulo, l.autor, l.categoria, e.data_emprestimo, e.data_devolucao_prevista, e.estado,
                   DATEDIFF(e.data_devolucao_prevista, CURDATE()) AS dias_restantes FROM emprestimos e JOIN livros l ON e.livro_id = l.id
            WHERE e.aluno_id = ? AND e.estado IN ('activo', 'atrasado') ORDER BY e.data_devolucao_prevista ASC");
        $emprestimos->execute([$aluno["id"]]);

        // Histórico de empréstimos
        $historico = $this->db->prepare("SELECT e.id, l.titulo, l.autor, e.data_emprestimo, e.data_devolucao_prevista, e.data_devolucao_real, e.estado
            FROM emprestimos e JOIN livros l ON e.livro_id = l.id WHERE e.aluno_id = ? ORDER BY e.data_emprestimo DESC LIMIT 10");
        $historico->execute([$aluno["id"]]);

        Response::success([
            "aluno"       => $aluno,
            "emprestimos" => $emprestimos->fetchAll(),
            "historico"   => $historico->fetchAll(),
        ]);
    }

    public function acervo(): void {

        $termo    = "%" . ($_GET["termo"] ?? "") . "%";
        $categoria = $_GET["categoria"] ?? "";

        $condicoes = ["eliminado_em IS NULL", "(titulo LIKE ? OR autor LIKE ?)"];
        $params = [$termo, $termo];

        if ($categoria) {
            $condicoes[] = "categoria = ?";
            $params[]    = $categoria;
        }

        $where = implode(" AND ", $condicoes);

        $stmt = $this->db->prepare("SELECT id, titulo, autor, isbn, categoria, quantidade_total, quantidade_disponivel FROM livros WHERE $where ORDER BY titulo ASC LIMIT 50");
        $stmt->execute($params);

        Response::success($stmt->fetchAll());
    }

    public function categorias(): void {
        $stmt = $this->db->query("SELECT DISTINCT categoria FROM livros WHERE categoria IS NOT NULL AND eliminado_em IS NULL ORDER BY categoria ASC");
        Response::success($stmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    public function solicitarEmprestimo(): void {

        $dados    = json_decode(file_get_contents("php://input"), true);
        $livroId  = intval($dados["livro_id"] ?? 0);
        $utilizadorId = $_SESSION["id"];

        // Buscar aluno
        $stmt = $this->db->prepare("SELECT a.* FROM alunos a JOIN utilizadores u ON a.utilizador_id = u.id WHERE u.id = ?");
        $stmt->execute([$utilizadorId]);
        $aluno = $stmt->fetch();

        if (!$aluno) {
            Response::error("Aluno não encontrado.");
        }

        // Verificar se aluno tem empréstimo activo
        $check = $this->db->prepare("SELECT COUNT(*) FROM emprestimos WHERE aluno_id = ? AND estado IN ('activo', 'atrasado')");
        $check->execute([$aluno["id"]]);
        if (intval($check->fetchColumn()) > 0) {
            Response::error("Já tens um livro em teu poder. Devolve-o antes de solicitar outro.");
        }

        // Verificar se já tem solicitação pendente
        $checkSolic = $this->db->prepare("SELECT COUNT(*) FROM solicitacoes WHERE aluno_id = ? AND estado = 'pendente' AND valido_ate > NOW()");
        $checkSolic->execute([$aluno["id"]]);
        if (intval($checkSolic->fetchColumn()) > 0) {
            Response::error("Já tens uma solicitação pendente. Aguarda a aprovação ou dirije-te à biblioteca.");
        }

        // Verificar disponibilidade do livro
        $livro = $this->db->prepare("SELECT * FROM livros WHERE id = ? AND eliminado_em IS NULL");
        $livro->execute([$livroId]);
        $livro = $livro->fetch();

        if (!$livro) {
            Response::error("Livro não encontrado.");
        }

        // Gerar código único
        $codigo = strtoupper(substr(md5(uniqid()), 0, 8));

        // Válido por 48 horas
        $stmt = $this->db->prepare("INSERT INTO solicitacoes (aluno_id, livro_id, codigo, estado, valido_ate) VALUES (?, ?, ?, 'pendente', DATE_ADD(NOW(), INTERVAL 48 HOUR))");
        $stmt->execute([$aluno["id"], $livroId, $codigo]);

        Logger::registar("CRIAR", "Solicitações", 
            "Solicitação criada: {$aluno['nome']} → {$livro['titulo']} | Código: $codigo");

        Response::success([
            "codigo"    => $codigo,
            "livro"     => $livro["titulo"],
            "valido_ate" => date("d/m/Y H:i", strtotime("+48 hours"))
        ], "Solicitação criada com sucesso.");

        $stmt->execute([$utilizadorId]);
        $aluno = $stmt->fetch();

        // DEBUG — remove depois
        error_log("Aluno encontrado: " . json_encode($aluno));
        error_log("Utilizador ID da sessão: " . $utilizadorId);
    }

    public function minhasSolicitacoes(): void {
        $utilizadorId = $_SESSION["id"];

        $stmt = $this->db->prepare("SELECT s.id, s.codigo, s.estado, s.criado_em, s.valido_ate, l.titulo, l.autor, l.capa
            FROM solicitacoes s JOIN livros l ON s.livro_id = l.id JOIN alunos a ON s.aluno_id = a.id JOIN utilizadores u ON a.utilizador_id = u.id
            WHERE u.id = ? ORDER BY s.criado_em DESC LIMIT 20");
        $stmt->execute([$utilizadorId]);
        Response::success($stmt->fetchAll());
    }

    public function actualizarPerfil(): void {
        $utilizadorId = $_SESSION["id"];
        $dados        = $_POST;

        // Buscar aluno
        $stmt = $this->db->prepare("
            SELECT a.* FROM alunos a
            JOIN utilizadores u ON a.utilizador_id = u.id
            WHERE u.id = ?
        ");
        $stmt->execute([$utilizadorId]);
        $aluno = $stmt->fetch();

        if (!$aluno) {
            Response::error("Aluno não encontrado.");
        }

        // Upload de nova foto
        $foto = $aluno["foto"];
        if (!empty($_FILES["foto"]["name"])) {
            if (!empty($aluno["foto"])) {
                \App\Helpers\Upload::eliminar($aluno["foto"]);
            }
            $upload = \App\Helpers\Upload::imagem($_FILES["foto"], "alunos");
            if (!$upload["sucesso"]) {
                Response::error($upload["erro"]);
            }
            $foto = $upload["caminho"];
        }

        // Verificar telefone duplicado
        if (!empty($dados["telefone"]) && $dados["telefone"] !== $aluno["telefone"]) {
            $check = $this->db->prepare("
                SELECT COUNT(*) FROM alunos 
                WHERE telefone = ? AND id != ?
            ");
            $check->execute([$dados["telefone"], $aluno["id"]]);
            if (intval($check->fetchColumn()) > 0) {
                Response::error("Este telefone já está em uso.");
            }
        }

        // Actualizar aluno
        $stmt = $this->db->prepare("UPDATE alunos SET 
            telefone = ?, foto = ?
            WHERE id = ?");
        $stmt->execute([
            $dados["telefone"] ?? null,
            $foto,
            $aluno["id"]
        ]);

        // Actualizar sessão
        $_SESSION["nome"] = $aluno["nome"];

        Logger::registar("EDITAR", "Alunos", "Perfil actualizado: " . $aluno["nome"]);
        Response::success(["foto" => $foto], "Perfil actualizado com sucesso.");
    }
}