<?php

namespace App\Solvers\Contracts;

/**
 * Interface SolvesHashCodeProblems
 *
 * @package App\Solvers
 */
interface ProvidesSolution
{
    /**
     * @return array
     */
    public function solutionResult(): array;
}
