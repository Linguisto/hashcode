<?php

namespace App\Solvers\Contracts;

interface ProvidesSolution
{
    public function solutionResult(): array;
}
