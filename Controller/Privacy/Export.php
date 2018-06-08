<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Privacy;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Archive\Zip;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Controller\AbstractPrivacy;
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Service\ExportManagement;

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
     * @var \Magento\Framework\File\Csv
     */
    private $csvWriter;

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
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Archive\Zip $zip
     * @param \Magento\Framework\File\Csv $csv
     * @param \Magento\Framework\Filesystem\Driver\File $file
     * @param \Opengento\Gdpr\Model\Config $config
     * @param \Opengento\Gdpr\Service\ExportManagement $exportManagement
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        Zip $zip,
        Csv $csv,
        File $file,
        Config $config,
        ExportManagement $exportManagement,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->fileFactory = $fileFactory;
        $this->zip = $zip;
        $this->csvWriter = $csv;
        $this->file = $file;
        $this->config = $config;
        $this->exportManagement = $exportManagement;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
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
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @deprecated
     * @todo debug
     */
    public function download(): ResponseInterface
    {
        $customer = $this->customerRepository->getById($this->customerSession->getCustomerId());
        $privacyData = $this->exportManagement->execute($customer->getEmail());

        $zipFileName = 'customer_privacy_data_' . $customer->getId() . '.zip';

        foreach ($privacyData as $key => $data) {
            $file = 'customer_privacy_data_' . $key . '_' . $customer->getId() . '.csv';
            $this->createCsv($file, $privacyData);
            $this->zip->pack($file, $zipFileName);
            $this->deleteCsv($file);
        }

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
     * Create .csv file.
     *
     * @param string $fileName
     * @param array $data
     *
     * @return void
     */
    private function createCsv($fileName, $data)
    {
        $this->csvWriter
            ->setEnclosure('"')
            ->setDelimiter(',')
            ->saveData($fileName, $data);
    }

    /**
     * Delete .csv file.
     *
     * @param string $fileName
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function deleteCsv($fileName)
    {
        if ($this->file->isExists($fileName)) {
            $this->file->deleteFile($fileName);
        }
    }
}
