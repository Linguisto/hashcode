<?php

namespace App\Solvers\Contracts;

/**
 * Interface CalculatesPreliminaryScore
 *
 * @package App\Solvers\Contracts
 */
interface CalculatesPreliminaryScore
{
    /**
     * @return int
     */
    public function preliminaryScore(): int;
}
