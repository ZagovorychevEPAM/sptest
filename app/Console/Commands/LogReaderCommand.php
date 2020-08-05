<?php

namespace App\Console\Commands;

use App\Exceptions\FileException;
use App\Services\ViewsLogReaderService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Exception\RuntimeException;

/**
 * @example
 * $ php artisan log:reader -p /path/to/file.log
 *
 * Class LogReaderCommand
 * @package App\Console\Commands
 */
class LogReaderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:reader
        {--p|path= : Path to the file which contains page views statistic}
        {--m|mode=views : Mode ["views" (pages views) or "unique" (unique pages views)] }
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Page views statistic based on the application logs';

    /**
     * Execute the console command.
     *
     * @param ViewsLogReaderService $viewsLogReader
     * @return int
     */
    public function handle(ViewsLogReaderService $viewsLogReader): int
    {
        $path = $this->option('path');
        if (!$path) {
            throw new InvalidOptionException('-p or --path should be provided [-h for more info]');
        }
        try {
            switch ($this->option('mode')) {
                case 'views':
                    $data = $viewsLogReader->getViews($this->option('path'));
                    break;
                case 'unique':
                    $data = $viewsLogReader->getUniqueViews($this->option('path'));
                    break;
                default:
                    throw new InvalidOptionException('Supported modes: "views" and "unique"');
            }
        } catch (FileException $e) {
            throw new RuntimeException($e->getMessage());
        }

        $this->showData($data);

        return 0;
    }

    private function showData($data): void
    {
        $headers = ['Path', $this->option('mode') === 'views' ? 'Views' : 'Unique Views'];
        $rows = [];
        foreach ($data as $key => $datum) {
            $rows[] = [$key, $datum];
        }
        $this->table($headers, $rows);
    }
}
