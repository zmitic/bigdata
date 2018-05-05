<?php

namespace App\Command;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

class StopwatchProgressBar
{
    /** @var Stopwatch */
    private $stopWatch;

    /** @var ProgressBar */
    private $progressBar;

    /** @var string  */
    private $key;

    public function __construct(SymfonyStyle $io, string $key, int $limit)
    {
        $this->stopWatch = new Stopwatch();
        $this->createProgressBar($io, $key, $limit);
        $this->stopWatch->start($key);
        $this->key = $key;
        $this->setProgress(10);
    }

    public function __destruct()
    {
        $this->progressBar->clear();
    }

    public function setProgress(int $progress): void
    {
        $event = $this->stopWatch->lap($this->key);
        $duration = $event->getDuration();
        if (!$duration) {
            $duration = 1;
        }
        $speed = round($progress / $duration * 1000);
        $this->progressBar->setProgress($progress);
        $this->progressBar->setMessage(sprintf('Speed %d/sec', $speed), 'speed');
    }

    public function clear(): void
    {
        $this->progressBar->clear();
        $this->stopWatch->stop($this->key);
    }

    private function createProgressBar(SymfonyStyle $io, string $key, int $limit): void
    {
        $progressBar = $io->createProgressBar($limit);
        $progressBar->setBarCharacter('<fg=green>⚬</>');
        $progressBar->setEmptyBarCharacter('<fg=red>⚬</>');
        $progressBar->setProgressCharacter('<fg=green>➤</>');

        $formats = [
            '',
            "<fg=white;bg=cyan>Importing $key </>",
            '',
            '[%bar%]%current%/%max% ',
            '',
            '%speed: -21s% ETA: %estimated% %memory:21s%',
        ];

        $progressBar->setFormat(implode("\n", $formats));
        $progressBar->setRedrawFrequency(5000);

        $this->progressBar = $progressBar;
    }
}
