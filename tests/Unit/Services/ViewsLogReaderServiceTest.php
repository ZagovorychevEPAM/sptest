<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Exceptions\FileException;
use App\Services\FileService;
use App\Services\ViewsLogReaderService;
use Tests\TestCase;

class ViewsLogReaderServiceTest extends TestCase
{
    private $viewsLogReaderService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->viewsLogReaderService = new ViewsLogReaderService();
    }

    /**
     * @throws FileException
     */
    public function testGetViews(): void
    {
        $self = $this;
        $this->mock(FileService::class, static function ($mock) use ($self) {
            $mock->shouldReceive('fileMap')->once()->withArgs(static function ($path, $callback) use($self) {
                $self->assertSame('/mockery/path', $path);
                $callback('/path/to?anything 127.0.0.1');
                $callback('/path/to?anything 127.0.0.1');
                $callback('/path/to?anything 0.0.0.0');
                $callback('/path/to?anythingElse 0.0.0.0');
                return true;
            });
        });

        $data = $this->viewsLogReaderService->getViews('/mockery/path');
        self::assertSame([
            '/path/to?anything' => 3,
            '/path/to?anythingElse' => 1
        ], $data);
    }

    public function testGetEmptyViews(): void
    {
        $self = $this;
        $this->mock(FileService::class, static function ($mock) use ($self) {
            $mock->shouldReceive('fileMap')->once()->withArgs(static function ($path) use($self) {
                $self->assertSame('/mockery/path', $path);
                return true;
            });
        });

        $data = $this->viewsLogReaderService->getViews('/mockery/path');
        self::assertSame([], $data);
    }

    /**
     * @throws FileException
     */
    public function testGetUniqueViews(): void
    {
        $self = $this;
        $this->mock(FileService::class, static function ($mock) use ($self) {
            $mock->shouldReceive('fileMap')->once()->withArgs(static function ($path, $callback) use($self) {
                $self->assertSame('/mockery/path', $path);
                $callback('/path/to?anything 127.0.0.1');
                $callback('/path/to?anything 127.0.0.1');
                $callback('/path/to?anything 0.0.0.0');
                $callback('/path/to?anythingElse 0.0.0.0');
                return true;
            });
        });

        $data = $this->viewsLogReaderService->getUniqueViews('/mockery/path');
        self::assertSame([
            '/path/to?anything' => 2,
            '/path/to?anythingElse' => 1
        ], $data);
    }
}
