<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Exceptions\FileException;
use App\Services\FileService;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class FileServiceTest extends TestCase
{
    private $fileService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileService = new FileService();
    }

    public function testFileMapNoFileException(): void
    {
        $this->expectExceptionMessage('Unable to open the file');
        $this->expectException(FileException::class);

        $this->fileService->fileMap('wrongpath', static function(){});
    }

    public function testFileMap(): void
    {
        $file = UploadedFile::fake()->createWithContent('phpUnitFile.name', "s1\ns2");
        $pos = 1;
        $self = $this;
        $this->fileService->fileMap($file->path(), static function ($line) use (&$pos, $self) {
            $line = trim($line, "\n");
            if ($pos === 1) {
                $pos++;
                $self->assertSame('s1', $line);
            } else {
                $self->assertSame('s2', $line);
            }
        });
    }
}
