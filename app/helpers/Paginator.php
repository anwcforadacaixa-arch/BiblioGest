<?php
namespace App\Helpers;

class Paginator {
    private int $total;
    private int $porPagina;
    private int $paginaActual;

    public function __construct(int $total, int $porPagina = 25, int $paginaActual = 1) {
        $this->total        = $total;
        $this->porPagina    = $porPagina;
        $this->paginaActual = max(1, $paginaActual);
    }

    public function offset(): int {
        return ($this->paginaActual - 1) * $this->porPagina;
    }

    public function limite(): int {
        return $this->porPagina;
    }

    public function totalPaginas(): int {
        return (int) ceil($this->total / $this->porPagina);
    }

    public function meta(): array {
        return [
            "total"         => $this->total,
            "por_pagina"    => $this->porPagina,
            "pagina_actual" => $this->paginaActual,
            "total_paginas" => $this->totalPaginas(),
            "tem_anterior"  => $this->paginaActual > 1,
            "tem_proxima"   => $this->paginaActual < $this->totalPaginas(),
        ];
    }
}