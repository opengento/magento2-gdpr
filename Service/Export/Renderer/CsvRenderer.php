<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Renderer;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\CsvFactory;
use Magento\Framework\Filesystem;
use Opengento\Gdpr\Service\Export\AbstractRenderer;

/**
 * Class CsvRenderer
 */
final class CsvRenderer extends AbstractRenderer
{
    /**
     * @var \Magento\Framework\File\CsvFactory
     */
    private $csvFactory;

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\File\CsvFactory $csvFactory
     */
    public function __construct(
        Filesystem $filesystem,
        CsvFactory $csvFactory
    ) {
        $this->csvFactory = $csvFactory;
        parent::__construct($filesystem, 'csv');
    }

    /**
     * {@inheritdoc}
     */
    public function saveData(string $fileName, array $data): string
    {
        $tmpWrite = $this->fileSystem->getDirectoryWrite(DirectoryList::TMP);
        $fileName = $tmpWrite->getAbsolutePath($fileName . '.' . $this->fileExtension);

        /** @var \Magento\Framework\File\Csv $csv */
        $csv = $this->csvFactory->create();
        $csv->setDelimiter(',');
        $csv->setEnclosure('""');
        $csv->appendData($fileName, $data);

        return $fileName;
    }

    /**
     * {@inheritdoc}
     * @note Not safe, dot not use it directly.
     */
    public function render(array $data): string
    {
        $csv = '';

        foreach ($data as $key => $value) {
            $csv .= \is_array($value) ? \trim($this->render($value), ',') . \PHP_EOL : '"' . $value . '",';
        }

        return $csv;
    }
}
