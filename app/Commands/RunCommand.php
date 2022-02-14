<?php

namespace App\Commands;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Solvers\Contracts\ProvidesSolution;
use LaravelZero\Framework\Commands\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use App\Solvers\Contracts\CalculatesPreliminaryScore;

class RunCommand extends Command
{
    protected $signature = 'run {filepath} {solver}';

    protected $description = 'Run HashCode solution';

    protected string $inFilePath;

    protected array|string|null $solverName;

    public function __construct(protected Filesystem $filesystem)
    {
        parent::__construct();
    }

    protected function validateFilePath(): bool
    {
        if (! file_exists($this->inFilePath)) {
            $this->error('Please, provide a valid file path');

            return false;
        }

        return true;
    }

    protected function resolveSolverClass(): ?string
    {
        $solverClassName = 'App\\Solvers\\' . Str::studly($this->solverName);

        if (! class_exists($solverClassName)) {
            $solverClassName = $solverClassName . 'Solver';

            if (! class_exists($solverClassName)) {
                $this->error('Please, provide a valid solver name');

                return null;
            }
        }

        if (! is_subclass_of($solverClassName, ProvidesSolution::class)) {
            $this->error("{$solverClassName} must implement interface " . ProvidesSolution::class);

            return null;
        }

        return $solverClassName;
    }

    protected function getDataSet(): array
    {
        $inFileStream = fopen($this->inFilePath, 'r');
        flock($inFileStream, LOCK_EX);

        $dataSet = [];

        while (! feof($inFileStream)) {
            $line = trim(fgets($inFileStream));
            if (empty($line)) {
                continue;
            }

            $dataSet[] = explode(' ', $line);
        }

        flock($inFileStream, LOCK_UN);
        fclose($inFileStream);

        return $dataSet;
    }

    public function handle(): int
    {
        $this->inFilePath = $this->argument('filepath');
        $this->solverName = $this->argument('solver');

        if (! $this->validateFilePath() || ! $solverClassName = $this->resolveSolverClass()) {
            return static::INVALID;
        }

        /** @var ProvidesSolution $solver */
        $solver = app($solverClassName, [
            'dataSet' => $this->getDataSet(),
        ]);

        $result = $solver->solutionResult();

        $this->alert("All done! Please, see the result in {$this->writeOutFile($result)}");

        if ($solver instanceof CalculatesPreliminaryScore) {
            $this->info("Preliminary score: {$solver->preliminaryScore()}");
        }

        return static::SUCCESS;
    }

    protected function writeOutFile(array $result): string
    {
        $result = array_values($result);

        $outFilePath = 'out' . DIRECTORY_SEPARATOR . $this->solverName . DIRECTORY_SEPARATOR . str_replace(
                'in',
                'out',
                Arr::last(explode('/', $this->inFilePath))
            );

        if ($this->filesystem->exists($outFilePath)) {
            $this->filesystem->delete($outFilePath);
        }

        $stream = tmpfile();

        foreach ($result as $index => $item) {
            $value = $item;
            if (is_array($item)) {
                $value = implode(' ', $item);
            }

            if ($index < count($result) - 1) {
                $value .= PHP_EOL;
            }

            fwrite($stream, $value);
        }

        fsync($stream);

        $this->filesystem->writeStream($outFilePath, $stream);
        fclose($stream);

        return app_path($outFilePath);
    }
}
