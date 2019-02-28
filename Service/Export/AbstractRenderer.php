<?php
/**
 * Copyright © OpenGento, All rights reserved.
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
     * @var string
     */
    protected $fileExtension;

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param string $fileExtension
     */
    public function __construct(
        Filesystem $filesystem,
        string $fileExtension
    ) {
        $this->fileSystem = $filesystem;
        $this->fileExtension = $fileExtension;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function saveData(string $fileName, array $data): string
    {
        $fileName .= '.' . $this->fileExtension;
        $tmpWrite = $this->fileSystem->getDirectoryWrite(DirectoryList::TMP);
        $tmpWrite->writeFile($fileName, $this->render($data));

        return $tmpWrite->getAbsolutePath($fileName);
    }
}
