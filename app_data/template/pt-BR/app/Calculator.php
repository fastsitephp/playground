<?php
// Este é um arquivo de Classe PHP. Se você for criar classes adicionais no
// Code Playground, você pode utilizar este arquivo como um modelo.
//
// Ao criar classes no site do Code Playground você deve utilizar o namespace
// [App] e corresponder o nome da classe com o nome do arquivo.

namespace App;

class Calculator
{
    public function calculate($x, $op, $y)
    {
        switch ($op) {
            case '+':
                return $this->add($x, $y);
            case '-':
                return $this->subtract($x, $y);
            case '*':
                return $this->multiply($x, $y);
            case '/':
                return $this->divide($x, $y);
        }
    }

    public function add($x, $y) { return $x + $y; }
    public function subtract($x, $y) { return $x - $y; }
    public function multiply($x, $y) { return $x * $y; }
    public function divide($x, $y) { return $x / $y; }
}
