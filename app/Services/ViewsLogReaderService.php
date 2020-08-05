<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\FileException;
use Illuminate\Support\Facades\Log;

class ViewsLogReaderService
{
    /**
     * @param string $path
     * @return array
     * @throws FileException
     */
    public function getViews(string $path): array
    {
        $stat = [];
        $this->readViewsLog($path, static function (array $data) use (&$stat) {
            if (count($data) !== 2) {
                Log::info('Unexpected data format', $data);
                return;
            }

            if (!array_key_exists($data[0], $stat)) {
                $stat[$data[0]] = 1;
            } else {
                $stat[$data[0]]++;
            }
        });
        return $stat;
    }

    /**
     * Returns unique visits count for the page
     * @param string $path
     * @return array
     * @throws FileException
     */
    public function getUniqueViews(string $path): array
    {
        $stat = [];
        $hashStorage = []; // to filter already counted
        $this->readViewsLog($path, static function (array $data) use (&$stat, &$hashStorage) {

            if (count($data) !== 2) {
                Log::info('Unexpected data format', $data);
                return;
            }

            $hash = $data[0] . '_' . $data[1];

            if (!array_key_exists($data[0], $stat)) {
                $stat[$data[0]] = 1;
                $hashStorage[] = $hash;
            } elseif(!in_array($hash, $hashStorage, true)) {
                $hashStorage[] = $hash;
                $stat[$data[0]]++;
            }
        });
        return $stat;
    }

    /**
     * @param $path
     * @param callable $callback
     * @throws FileException
     */
    protected function readViewsLog($path, callable $callback): void
    {
        $self = $this;
        $this->getFileService()->fileMap($path, static function ($line) use ($self, $callback) {
            $callback($self->parseLine($line));
        });
    }

    private function getFileService(): FileService
    {
        return resolve(FileService::class);
    }

    /**
     * Prepared data with information about the page and IP of visitor
     * @param string $line
     * @return array
     * @example
     *  [
     *      0 => '/',
     *      1 => '0.0.0.0',
     *  ]
     */
    protected function parseLine(string $line): array
    {
        // it can be improved to detect if the ip is correct IP or IPv6 and if uri is correct
        // not sure that this is needed by the acceptance criteria of the task

        return preg_split('/ /', $line, -1, PREG_SPLIT_NO_EMPTY);
    }
}
