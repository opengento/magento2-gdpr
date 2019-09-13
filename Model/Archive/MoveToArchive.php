<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Archive;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Archive\ArchiveInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Filesystem;
use Magento\Framework\Phrase;

/**
 * Class MoveToArchive
 */
final class MoveToArchive
{
    /**
     * @var \Magento\Framework\Archive\ArchiveInterface
     */
    private $archive;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @param \Magento\Framework\Archive\ArchiveInterface $archive
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        ArchiveInterface $archive,
        Filesystem $filesystem
    ) {
        $this->archive = $archive;
        $this->filesystem = $filesystem;
    }

    /**
     * Pack the source
     *
     * @param string $source
     * @param string $destination
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function prepareArchive(string $source, string $destination): string
    {
        $tmpWrite = $this->filesystem->getDirectoryWrite(DirectoryList::TMP);
        $fileDriver = $tmpWrite->getDriver();

        if (!$fileDriver->isExists($source)) {
            throw new NotFoundException(new Phrase('File "%1" does not exists.', [$source]));
        }

        return $this->archive->pack($source, $tmpWrite->getAbsolutePath($destination));
    }
}
