<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Processor\Entity;

/**
 * Class Document
 */
final class Document implements DocumentInterface
{
    /**
     * @var array
     */
    private $data;

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function addData(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): array
    {
        $data = $this->data;
        $this->data = [];

        return $data;
    }
}
