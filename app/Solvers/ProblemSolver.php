<?php

namespace App\Solvers;

use App\Solvers\Contracts\ProvidesSolution;
use Illuminate\Support\Collection;

/**
 * Class DataParser
 *
 * @package App\Services
 */
abstract class ProblemSolver implements ProvidesSolution
{
    /**
     * @var Collection
     */
    protected $dataSet;

    /**
     * ProblemSolver constructor.
     *
     * @param array $dataSet
     */
    public function __construct(array $dataSet)
    {
        $this->dataSet = $dataSet;
    }
}
