<?php
// Este es un archivo de clase PHP. Si está creando clases adicionales en el
// patio de recreo, puede usar este archivo como plantilla.
//
// Al crear clases en el sitio del patio de recreo, querrá usar el espacio
// de nombres [App] y unir el nombre de la clase con el nombre del archivo.

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
