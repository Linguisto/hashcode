<?php

namespace App\Solvers;

use App\Solvers\Contracts\ProvidesSolution;
use Illuminate\Support\Collection;

abstract class ProblemSolver implements ProvidesSolution
{
    protected Collection $dataSet;

    public function __construct(array|Collection $dataSet)
    {
        $this->dataSet = collect($dataSet);
    }
}
