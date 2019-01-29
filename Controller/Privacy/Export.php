<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Privacy;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Archive\Zip;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Filesystem;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Controller\AbstractPrivacy;
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Service\ExportManagement;
use Opengento\Gdpr\Service\ExportStrategy;

/**
 * Action Export Export
 */
class Export extends AbstractPrivacy implements ActionInterface
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    private $fileFactory;

    /**
     * @var \Magento\Framework\Archive\Zip
     */
    private $zip;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @var \Opengento\Gdpr\Service\ExportManagement
     */
    private $exportManagement;

    /**
     * @var \Opengento\Gdpr\Service\ExportStrategy
     */
    private $exportStrategy;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Archive\Zip $zip
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Opengento\Gdpr\Model\Config $config
     * @param \Opengento\Gdpr\Service\ExportManagement $exportManagement
     * @param \Opengento\Gdpr\Service\ExportStrategy $exportStrategy
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        Zip $zip,
        Filesystem $filesystem,
        Config $config,
        ExportManagement $exportManagement,
        ExportStrategy $exportStrategy,
        Session $customerSession
    ) {
        $this->fileFactory = $fileFactory;
        $this->zip = $zip;
        $this->filesystem = $filesystem;
        $this->config = $config;
        $this->exportManagement = $exportManagement;
        $this->exportStrategy = $exportStrategy;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (!$this->config->isExportEnabled()) {
            return $this->forwardNoRoute();
        }

        try {
            return $this->download();
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('Something went wrong, please try again later!'));

            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setRefererOrBaseUrl();

            return $resultRedirect;
        }
    }

    /**
     * Download zip of a csv with customer privacy data
     *
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Exception
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function download(): ResponseInterface
    {
        $privacyData = $this->exportManagement->execute((int) $this->customerSession->getCustomerId());
        $fileName = $this->exportStrategy->saveData('personal_data', $privacyData);
        $zipFileName = 'customer_privacy_data_' . $this->customerSession->getCustomerId() . '.zip';

        return $this->fileFactory->create(
            $zipFileName,
            [
                'type' => 'filename',
                'value' => $this->prepareArchive($fileName, $zipFileName),
                'rm' => true,
            ],
            DirectoryList::TMP
        );
    }

    /**
     * Prepare the archive
     *
     * @param string $source
     * @param string $destination
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    private function prepareArchive(string $source, string $destination): string
    {
        $tmpWrite = $this->filesystem->getDirectoryWrite(DirectoryList::TMP);
        $fileDriver = $tmpWrite->getDriver();

        if (!$fileDriver->isExists($source)) {
            throw new NotFoundException(new Phrase('File "%1" not found.', [$source]));
        }

        $zipFile = $this->zip->pack($source, $tmpWrite->getAbsolutePath($destination));
        $fileDriver->deleteFile($source);

        return $zipFile;
    }
}
