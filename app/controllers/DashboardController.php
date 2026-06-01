<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Emprestimo;
use App\Helpers\Response;

class DashboardController extends Controller {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index(): void {
        $this->view("dashboard/index");
    }

    public function verificarAtrasos(): void {
        $emprestimo = new Emprestimo();
        $emprestimo->actualizarAtrasados();
        Response::success(null, "Verificação concluída.");
    }

    public function graficosEmprestimosMensais(): void {
        $stmt = $this->db->query("SELECT DATE_FORMAT(data_emprestimo, '%Y-%m') AS mes, COUNT(*) AS total FROM emprestimos WHERE data_emprestimo >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) GROUP BY mes ORDER BY mes ASC");
        Response::success($stmt->fetchAll());
    }

    public function graficosCursosFrequentes(): void {
        $stmt = $this->db->query("SELECT a.curso, COUNT(e.id) AS total FROM entradas_saidas e JOIN alunos a ON e.aluno_id = a.id WHERE e.tipo = 'entrada' GROUP BY a.curso ORDER BY total DESC LIMIT 8");
        Response::success($stmt->fetchAll());
    }

    public function graficosCategoriasEmprestadas(): void {
        $stmt = $this->db->query("SELECT l.categoria, COUNT(e.id) AS total FROM emprestimos e JOIN livros l ON e.livro_id = l.id WHERE l.categoria IS NOT NULL GROUP BY l.categoria ORDER BY total DESC");
        Response::success($stmt->fetchAll());
    }

    public function graficosFluxoHorario(): void {
        $stmt = $this->db->query("SELECT HOUR(data_hora) AS hora, COUNT(*) AS total FROM entradas_saidas WHERE tipo = 'entrada' AND data_hora >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY hora ORDER BY hora ASC");
        Response::success($stmt->fetchAll());
    }
}