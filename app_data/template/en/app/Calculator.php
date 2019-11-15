<?php
// This is a PHP Class file. If you are creating additional classes in the
// playground you can use this file as a template.
//
// When creating classes in the playground site you will want to use the
// [App] namespace and match the class name with the file name.

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
