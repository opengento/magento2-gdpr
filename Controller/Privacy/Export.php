<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
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
use Magento\Framework\Filesystem\Driver\File;
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
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $file;

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
     * @param \Magento\Framework\Filesystem\Driver\File $file
     * @param \Opengento\Gdpr\Model\Config $config
     * @param \Opengento\Gdpr\Service\ExportManagement $exportManagement
     * @param \Opengento\Gdpr\Service\ExportStrategy $exportStrategy
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        Zip $zip,
        File $file,
        Config $config,
        ExportManagement $exportManagement,
        ExportStrategy $exportStrategy,
        Session $customerSession
    ) {
        $this->fileFactory = $fileFactory;
        $this->zip = $zip;
        $this->file = $file;
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
        if (!$this->config->isExportEnabled()){
            return $this->forwardNoRoute();
        }

        try {
            return $this->download();
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('Something went wrong. Try again later.'));

            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('customer/privacy/settings');
        }
    }

    /**
     * Download zip of a csv with customer privacy data
     *
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Exception
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @deprecated
     * @todo debug
     */
    public function download(): ResponseInterface
    {
        $privacyData = $this->exportManagement->execute($this->customerSession->getCustomerId());

        $fileName = $this->exportStrategy->saveData('personalData.html', $privacyData);//todo fix file name

        $zipFileName = 'customer_privacy_data_' . $this->customerSession->getCustomerId() . '.zip';

        $this->zip->pack($fileName, $zipFileName);

        $this->unlinkFile($fileName);

        return $this->fileFactory->create(
            $zipFileName,
            [
                'type' => 'filename',
                'value' => $zipFileName,
                'rm' => true,
            ],
            DirectoryList::PUB,
            'zip',
            null
        );
    }

    /**
     * Unlink a file
     *
     * @param string $fileName
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function unlinkFile(string $fileName)
    {
        if ($this->file->isExists($fileName)) {
            $this->file->deleteFile($fileName);
        }
    }
}
