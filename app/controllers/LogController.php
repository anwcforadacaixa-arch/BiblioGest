<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Helpers\Response;
use App\Middleware\AuthMiddleware;

class LogController extends Controller {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function pagina(): void {
        
        $this->view("logs/index");
    }

    public function listar(): void {
        $utilizador = "%" . ($_GET["utilizador"] ?? "") . "%";
        $acao       = $_GET["filtro_acao"] ?? "";
        $modulo     = $_GET["modulo"]      ?? "";
        $dataInicio = $_GET["data_inicio"] ?? "";
        $dataFim    = $_GET["data_fim"]    ?? "";
        $pagina     = intval($_GET["pagina"] ?? 1);

        $condicoes = ["utilizador_nome LIKE ?"];
        $params    = [$utilizador];

        if ($acao)       { $condicoes[] = "acao = ?";             $params[] = $acao; }
        if ($modulo)     { $condicoes[] = "modulo = ?";           $params[] = $modulo; }
        if ($dataInicio) { $condicoes[] = "DATE(data_hora) >= ?"; $params[] = $dataInicio; }
        if ($dataFim)    { $condicoes[] = "DATE(data_hora) <= ?"; $params[] = $dataFim; }

        $where = implode(" AND ", $condicoes);

        // Contar total
        $stmtTotal = $this->db->prepare("SELECT COUNT(*) FROM logs WHERE $where");
        $stmtTotal->execute($params);
        $total = intval($stmtTotal->fetchColumn());

        $paginator = new \App\Helpers\Paginator($total, 25, $pagina);
        $limite    = $paginator->limite();
        $offset    = $paginator->offset();

        $stmt = $this->db->prepare("
            SELECT id, utilizador_nome, acao, modulo, descricao, data_hora
            FROM logs
            WHERE $where
            ORDER BY data_hora DESC
            LIMIT $limite OFFSET $offset
        ");
        $stmt->execute($params);

        Response::paginated($stmt->fetchAll(), $paginator->meta());

    }

}