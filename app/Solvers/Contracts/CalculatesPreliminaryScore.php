<?php

namespace App\Solvers\Contracts;

interface CalculatesPreliminaryScore
{
    public function preliminaryScore(): int;
}
