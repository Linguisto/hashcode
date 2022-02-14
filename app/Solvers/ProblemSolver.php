<?php

namespace App\Solvers;

use Illuminate\Support\Collection;
use App\Solvers\Contracts\ProvidesSolution;
use Illuminate\Contracts\Support\Arrayable;

abstract class ProblemSolver implements ProvidesSolution
{
    protected Collection $dataSet;

    public function __construct(iterable|Arrayable $dataSet)
    {
        $this->dataSet = collect($dataSet);
    }
}
