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
use ZipArchive;

use function basename;

/**
 * Zip compressed file archive with local file name.
 * @api
 */
class Zip implements ArchiveInterface
{
    public function __construct(
        private Filesystem $filesystem,
        private ArchiveZip $zip
    ) {}

    public function pack($source, $destination): string
    {
        $zip = new ZipArchive();
        $zip->open($destination, ZipArchive::CREATE);
        $zip->addFile(
            $source,
            $this->filesystem->getDirectoryReadByPath($source)->isDirectory($source) ? '' : basename($source)
        );
        $zip->close();

        return $destination;
    }

    public function unpack($source, $destination): string
    {
        return $this->zip->unpack($source, $destination);
    }
}
