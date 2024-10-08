<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DocumentoValido implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $documento = preg_replace('/\D/', '', $value);

        if (strlen($documento) == 11) {
            if (!$this->validarCPF($documento)) {
                $fail('O CPF é inválido.');
            }
        } elseif (strlen($documento) == 14) {
            if (!$this->validarCNPJ($documento)) {
                $fail('O CNPJ é inválido.');
            }
        } else {
            $fail('Não foi informado um CNPJ/CPF válido.');
        }
    }

    private function validarCPF($cpf): bool
    {
        if (preg_match('/(\d)\1{10}/', $cpf)) return false;

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) return false;
        }
        return true;
    }

    private function validarCNPJ($cnpj): bool
    {
        if (strlen($cnpj) != 14 || preg_match('/(\d)\1{13}/', $cnpj)) return false;

        $calculo = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $soma = 0;
        for ($i = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $calculo[$i];
        }
        $resto = $soma % 11;
        $d1 = ($resto < 2) ? 0 : 11 - $resto;

        if ($cnpj[12] != $d1) return false;

        $soma = 0;
        for ($i = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * ($calculo[$i + 1] ?? 2);
        }
        $resto = $soma % 11;
        $d2 = ($resto < 2) ? 0 : 11 - $resto;

        return $cnpj[13] == $d2;
    }
}
