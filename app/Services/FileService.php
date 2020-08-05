<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\FileException;

class FileService
{
    /**
     * Read the file line by line and run an action with that data
     * @param string $path
     * @param callable $callback ($line) from the file
     * @throws FileException
     */
    public function fileMap(string $path, callable $callback): void
    {
        $handle = @fopen($path, 'rb');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                // process the line read.
                $callback($line);
            }

            fclose($handle);
        } else {
            throw new FileException('Unable to open the file');
        }
    }
}
