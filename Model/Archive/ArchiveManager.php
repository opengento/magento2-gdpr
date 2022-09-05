<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Archive;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Archive\ArchiveInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Filesystem;
use Magento\Framework\Phrase;

final class ArchiveManager
{
    private ArchiveInterface $archive;

    private Filesystem $filesystem;

    public function __construct(
        ArchiveInterface $archive,
        Filesystem $filesystem
    ) {
        $this->archive = $archive;
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $source
     * @param string $destination
     * @param bool $remove [optional] Remove the source files from the file system.
     * @return string
     * @throws FileSystemException
     * @throws NotFoundException
     */
    public function addToArchive(string $source, string $destination, bool $remove = true): string
    {
        $tmpWrite = $this->filesystem->getDirectoryWrite(DirectoryList::TMP);
        $fileDriver = $tmpWrite->getDriver();

        if (!$fileDriver->isExists($source)) {
            throw new NotFoundException(new Phrase('File "%1" does not exists.', [$source]));
        }

        $archive = $this->archive->pack($source, $tmpWrite->getAbsolutePath($destination));

        if ($remove) {
            $fileDriver->deleteFile($source);
        }

        return $archive;
    }
}
