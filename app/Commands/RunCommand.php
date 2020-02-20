<?php

namespace App\Commands;

use App\Solvers\Contracts\CalculatesPreliminaryScore;
use App\Solvers\Contracts\ProvidesSolution;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\{Arr, Str};
use LaravelZero\Framework\Commands\Command;
use League\Flysystem\Util;

/**
 * Class RunCommand
 *
 * @package App\Commands
 */
class RunCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'run {filepath} {solver}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Run HashCode solution';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $inFilePath;

    /**
     * @var array|string|null
     */
    protected $solverName;

    /**
     * RunCommand constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
    }

    /**
     * Handle
     */
    public function handle()
    {
        $this->inFilePath = Util::normalizePath($this->argument('filepath'));
        $this->solverName = $this->argument('solver');

        if (! file_exists($this->inFilePath)) {
            $this->error('Please, provide a valid file path');
            die;
        }

        $solverClassName = 'App\\Solvers\\' . Str::studly($this->solverName . 'Solver');
        if (! class_exists($solverClassName)) {
            $this->error('Please, provide a valid solver name');
            die;
        }

        if (! is_subclass_of($solverClassName, ProvidesSolution::class)) {
            $this->error("$solverClassName must implement interface " . ProvidesSolution::class);
            die;
        }

        $inFile = fopen($this->inFilePath, 'r');
        $dataSet = [];

        while (! feof($inFile)) {
            $line = trim(fgets($inFile));
            if (empty($line)) {
                continue;
            }

            $dataSet[] = explode(' ', $line);
        }

        /** @var ProvidesSolution $solver */
        $solver = app($solverClassName, [
            'dataSet' => $dataSet,
        ]);

        $result = $solver->solutionResult();

        $this->alert("All done! Please, see the result in {$this->writeOutFile($result)}");

        if ($solver instanceof CalculatesPreliminaryScore) {
            $this->info("Preliminary score: {$solver->preliminaryScore()}");
        }
    }

    /**
     * @param array $result
     *
     * @return string
     */
    protected function writeOutFile(array $result): string
    {
        $outFilePath = 'out' . DIRECTORY_SEPARATOR . $this->solverName . DIRECTORY_SEPARATOR . str_replace(
                'in',
                'out',
                Arr::last(explode('/', $this->inFilePath))
            );

        if ($this->filesystem->has($outFilePath)) {
            $this->filesystem->delete($outFilePath);
        }

        foreach ($result as $item) {
            $value = $item;
            if (is_array($item)) {
                $value = implode(' ', $item);
            }

            $this->filesystem->append($outFilePath, $value);
        }

        return storage_path($outFilePath);
    }
}
