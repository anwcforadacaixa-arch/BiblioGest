<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Helpers\Response;
use App\Helpers\Logger;

class BackupController extends Controller {
    private $db;
    private string $pastaBackup;

    public function __construct() {
        $this->db          = Database::getInstance()->getConnection();
        $this->pastaBackup = BASE_PATH . "/storage/backups/";

        if (!is_dir($this->pastaBackup)) {
            mkdir($this->pastaBackup, 0755, true);
        }
    }

    public function pagina(): void {
        $this->view("backup/index");
    }

    public function criar(): void {
        try {
            $config   = require BASE_PATH . '/config/database.php';
            $nome     = "backup_" . date("Y-m-d_H-i-s") . ".sql";
            $caminho  = $this->pastaBackup . $nome;

            $sql  = "-- BiblioGest Backup\n";
            $sql .= "-- Gerado em: " . date("d/m/Y H:i:s") . "\n";
            $sql .= "-- Base de dados: " . $config['dbname'] . "\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            // Buscar todas as tabelas
            $tabelas = $this->db->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);

            foreach ($tabelas as $tabela) {
                // Estrutura da tabela
                $create = $this->db->query("SHOW CREATE TABLE `$tabela`")->fetch();
                $sql   .= "-- Tabela: $tabela\n";
                $sql   .= "DROP TABLE IF EXISTS `$tabela`;\n";
                $sql   .= $create['Create Table'] . ";\n\n";

                // Dados da tabela
                $rows = $this->db->query("SELECT * FROM `$tabela`")->fetchAll(\PDO::FETCH_ASSOC);

                if (count($rows) > 0) {
                    $colunas = implode("`, `", array_keys($rows[0]));
                    $sql    .= "INSERT INTO `$tabela` (`$colunas`) VALUES\n";

                    $linhas = [];
                    foreach ($rows as $row) {
                        $valores = array_map(function($v) {
                            if ($v === null) return "NULL";
                            return "'" . addslashes($v) . "'";
                        }, array_values($row));
                        $linhas[] = "(" . implode(", ", $valores) . ")";
                    }

                    $sql .= implode(",\n", $linhas) . ";\n\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            // Guardar no servidor
            file_put_contents($caminho, $sql);

            Logger::registar("BACKUP", "Sistema", "Backup criado: $nome");

            // Devolver para download
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $nome . '"');
            header('Content-Length: ' . strlen($sql));
            echo $sql;
            exit;

        } catch (\Exception $e) {
            Response::error("Erro ao criar backup: " . $e->getMessage());
        }
    }

    public function listar(): void {
        $ficheiros = glob($this->pastaBackup . "*.sql");
        $backups   = [];

        if ($ficheiros) {
            rsort($ficheiros);
            foreach ($ficheiros as $f) {
                $backups[] = [
                    "nome"     => basename($f),
                    "tamanho"  => $this->formatarTamanho(filesize($f)),
                    "data"     => date("d/m/Y H:i:s", filemtime($f)),
                ];
            }
        }

        Response::success($backups);
    }

    public function download(): void {
        $nome    = $_GET["ficheiro"] ?? "";
        $caminho = $this->pastaBackup . basename($nome);

        if (!file_exists($caminho) || !str_ends_with($nome, '.sql')) {
            Response::error("Ficheiro não encontrado.");
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($caminho) . '"');
        header('Content-Length: ' . filesize($caminho));
        readfile($caminho);
        exit;
    }

    public function eliminar(): void {
        $dados   = json_decode(file_get_contents("php://input"), true);
        $nome    = $dados["ficheiro"] ?? "";
        $caminho = $this->pastaBackup . basename($nome);

        if (!file_exists($caminho) || !str_ends_with($nome, '.sql')) {
            Response::error("Ficheiro não encontrado.");
        }

        unlink($caminho);
        Logger::registar("ELIMINAR", "Backup", "Backup eliminado: $nome");
        Response::success(null, "Backup eliminado com sucesso.");
    }

    private function formatarTamanho(int $bytes): string {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . " MB";
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . " KB";
        return $bytes . " B";
    }
}