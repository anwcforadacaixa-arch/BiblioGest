<?php
namespace App\Helpers;

use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Exportador {

    // ─── PDF ──────────────────────────────────────────────────
    public static function pdf(string $titulo, array $colunas, array $dados, string $filename = "relatorio"): void {
        $html = self::gerarHtmlTabela($titulo, $colunas, $dados);

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream($filename . ".pdf", ["Attachment" => true]);
        exit;
    }

    // ─── EXCEL ────────────────────────────────────────────────
    public static function excel(string $titulo, array $colunas, array $dados, string $filename = "relatorio"): void {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle($titulo);

        // Título
        $sheet->setCellValue('A1', $titulo);
        $sheet->mergeCells('A1:' . self::letraColuna(count($colunas)) . '1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E5496']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Cabeçalhos
        foreach ($colunas as $i => $coluna) {
            $letra = self::letraColuna($i + 1);
            $sheet->setCellValue($letra . '2', $coluna);
            $sheet->getStyle($letra . '2')->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->getColumnDimension($letra)->setAutoSize(true);
        }

        // Dados
        foreach ($dados as $linha => $registo) {
            $valores = array_values($registo);
            foreach ($valores as $i => $valor) {
                $letra = self::letraColuna($i + 1);
                $sheet->setCellValue($letra . ($linha + 3), $valor ?? "—");

                // Alternar cor das linhas
                if ($linha % 2 === 0) {
                    $sheet->getStyle($letra . ($linha + 3))->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EEF2FB']],
                    ]);
                }
            }
        }

        // Data de geração
        $ultimaLinha = count($dados) + 4;
        $sheet->setCellValue('A' . $ultimaLinha, 'Gerado em: ' . date('d/m/Y H:i'));
        $sheet->getStyle('A' . $ultimaLinha)->getFont()->setItalic(true)->setSize(9);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // ─── HELPERS PRIVADOS ─────────────────────────────────────
    private static function gerarHtmlTabela(string $titulo, array $colunas, array $dados): string {
        $linhas = "";
        foreach ($dados as $i => $registo) {
            $cor    = $i % 2 === 0 ? "#ffffff" : "#EEF2FB";
            $cells  = "";
            foreach (array_values($registo) as $valor) {
                $cells .= "<td style='padding:6px 10px;border:1px solid #ddd;font-size:11px;'>" . htmlspecialchars($valor ?? "—") . "</td>";
            }
            $linhas .= "<tr style='background:{$cor};'>{$cells}</tr>";
        }

        $cabecalhos = "";
        foreach ($colunas as $col) {
            $cabecalhos .= "<th style='padding:8px 10px;background:#2E5496;color:white;font-size:11px;text-align:left;'>{$col}</th>";
        }

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                h2   { color: #2E5496; margin-bottom: 4px; }
                p    { color: #888; font-size: 10px; margin-top: 0; }
                table { width: 100%; border-collapse: collapse; margin-top: 12px; }
            </style>
        </head>
        <body>
            <h2>{$titulo}</h2>
            <p>BiblioGest | Gerado em: " . date('d/m/Y H:i') . "</p>
            <table>
                <thead><tr>{$cabecalhos}</tr></thead>
                <tbody>{$linhas}</tbody>
            </table>
        </body>
        </html>";
    }

    private static function letraColuna(int $numero): string {
        $letra = '';
        while ($numero > 0) {
            $numero--;
            $letra  = chr(65 + ($numero % 26)) . $letra;
            $numero = intval($numero / 26);
        }
        return $letra;
    }
}