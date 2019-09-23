<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Archive;

use Magento\Framework\Archive\ArchiveInterface;
use Magento\Framework\Archive\Zip as ArchiveZip;
use Magento\Framework\Filesystem;

/**
 * Zip compressed file archive with local file name.
 * @api
 */
final class Zip implements ArchiveInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ArchiveZip
     */
    private $zip;

    public function __construct(
        Filesystem $filesystem,
        ArchiveZip $zip
    ) {
        $this->filesystem = $filesystem;
        $this->zip = $zip;
    }

    public function pack($source, $destination): string
    {
        $directoryRead = $this->filesystem->getDirectoryReadByPath($source);

        $zip = new \ZipArchive();
        $zip->open($destination, \ZipArchive::CREATE);
        $zip->addFile($source, $directoryRead->isDirectory($source) ? '' : \basename($source));
        $zip->close();

        return $destination;
    }

    public function unpack($source, $destination): string
    {
        return $this->zip->unpack($source, $destination);
    }
}
