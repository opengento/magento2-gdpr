<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

/**
 * Class AbstractRenderer
 */
abstract class AbstractRenderer implements RendererInterface
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        Filesystem $filesystem
    ) {
        $this->fileSystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function saveData(string $fileName, array $data): string
    {
        $tmpWrite = $this->fileSystem->getDirectoryWrite(DirectoryList::TMP);
        $tmpWrite->writeFile($fileName, $this->render($data));

        return $tmpWrite->getAbsolutePath($fileName);
    }
}
