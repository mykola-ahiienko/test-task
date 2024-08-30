<?php

declare(strict_types=1);

namespace App\Services\Files;

class InvalidJsonList extends File
{
    private array $list = [];

    public function __construct(private readonly string $path)
    {
        parent::__construct($this->path);
    }

    protected function isValid(): bool
    {
        if (!$this->content) {
            return false;
        }

        $contentAsArray = explode(PHP_EOL, $this->content);

        foreach ($contentAsArray as $item) {
            if (json_validate($item)) {
                $this->list[] = json_decode($item, true);
            }
        }

        return (bool)$this->list;
    }

    public function toArray(): array
    {
        return $this->list;
    }
}