<?php

declare(strict_types=1);

namespace App\Services\Files;

use Exception;

abstract class File
{
    abstract protected function isValid(): bool;
    abstract public function toArray(): array;

    protected ?string $content;

    /**
     * @throws Exception
     */
    public function __construct(private readonly string $path)
    {
        if (!$this->exists()) {
            throw new Exception('File '. $this->path . ' does not exist.');
        }

        $this->setContent();

        if (!$this->isValid()) {
            throw new Exception('File does not valid.');
        }
    }

    private function exists(): bool
    {
        return file_exists($this->path);
    }

    private function setContent(): void
    {
        $contentFromFile = file_get_contents($this->path);
        $this->content = is_string($contentFromFile) ? $contentFromFile : null;
    }
}