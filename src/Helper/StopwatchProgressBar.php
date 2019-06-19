<?php

declare(strict_types=1);

namespace App\Helper;

use DateTime;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;

class StopwatchProgressBar
{
    /** @var ProgressBar */
    private $progressBar;

    private $progressCounter;
    private $lastUpdate;
    private $lastProgress = 0;

    public function __construct(SymfonyStyle $io, string $key, int $limit)
    {
        $this->createProgressBar($io, $key, $limit);
        $this->setProgress(0);
        $this->lastUpdate = new DateTime();
    }

    public function __destruct()
    {
        $this->progressBar->clear();
    }

    public function setProgress(int $progress): void
    {
        ++$this->progressCounter;
        if ($this->progressCounter < 1000) {
            return;
        }
        $this->progressCounter = 0;
        $lastProgress = $this->lastProgress;
        $advancedFor = $progress - $lastProgress;

        $now = new DateTime();
        $duration = $now->diff($this->lastUpdate)->s ?? 1;
        if (!$duration) {
            return;
        }

        $speedPerSecond = round($advancedFor / $duration);
        $this->progressBar->setProgress($progress);
        $this->progressBar->setMessage(sprintf('Speed %s/sec', number_format($speedPerSecond)), 'speed');
        $this->progressBar->setMessage(number_format($progress), 'current_num');
        $this->progressBar->setMessage(number_format($this->progressBar->getMaxSteps()), 'max_num');

        $this->lastProgress = $progress;
        $this->lastUpdate = $now;
    }

    public function clear(): void
    {
        $this->progressBar->clear();
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
            '[%bar%]    %current_num% / %max_num% (%percent%%)',
            '',
            '%speed: -21s% ETA: %estimated% %memory:21s%',
        ];

        $progressBar->setFormat(implode("\n", $formats));
        $progressBar->setRedrawFrequency(10000);

        $this->progressBar = $progressBar;
    }
}
