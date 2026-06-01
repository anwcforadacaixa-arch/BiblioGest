<?php

    namespace App\Helpers;

    class Validator {

        private array $erros = [];

        public function obrigatorio(string $campo, mixed $valor): self {

            if (empty($valor) && $valor !== '0') {

                $this->erros[] = "O campo '{$campo}' é obrigatório.";

            }

            return $this;

        }

        public function email(string $campo, string $valor): self {

            if (!empty($valor) && !filter_var($valor, FILTER_VALIDATE_EMAIL)) {

                $this->erros[] = "O campo '{$campo}' deve ser um email válido.";

            }

            return $this;

        }

        public function minimo(string $campo, string $valor, int $min): self {

            if (strlen($valor) < $min) {

                $this->erros[] = "O campo '{$campo}' deve ter pelo menos {$min} caracteres.";

            }

            return $this;

        }

        public function numerico(string $campo, mixed $valor): self {

            if (!is_numeric($valor)) {

                $this->erros[] = "O campo '{$campo}' deve ser numérico.";

            }

            return $this;

        }

        public function valido(): bool {

            return empty($this->erros);

        }

        public function erros(): array {

            return $this->erros;

        }

        public function primeiroErro(): string {

            return $this->erros[0] ?? "";

        }
        
    }