<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity;

final class Document implements DocumentInterface
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function addData(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
