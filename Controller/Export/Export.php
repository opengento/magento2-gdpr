<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Export;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Opengento\Gdpr\Helper\Data;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Archive\Zip;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Export customer data.
 * @refactor
 */
class Export extends Action
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var Csv
     */
    protected $csvWriter;

    /**
     * @var Zip
     */
    protected $zip;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var array
     */
    protected $processors;

    /**
     * Export constructor.
     *
     * @param Context $context
     * @param DateTime $dateTime
     * @param FileFactory $fileFactory
     * @param File $file
     * @param Csv $csvWriter
     * @param Zip $zip
     * @param Data $helper
     * @param CustomerRepositoryInterface $customerRepository
     * @param Session $session
     * @param array $processors
     */
    public function __construct(
        Context $context,
        DateTime $dateTime,
        FileFactory $fileFactory,
        File $file,
        Csv $csvWriter,
        Zip $zip,
        Data $helper,
        CustomerRepositoryInterface $customerRepository,
        Session $session,
        array $processors = []
    ) {
        parent::__construct($context);

        $this->context = $context;
        $this->dateTime = $dateTime;
        $this->fileFactory = $fileFactory;
        $this->file = $file;
        $this->csvWriter = $csvWriter;
        $this->zip = $zip;
        $this->helper = $helper;
        $this->customerRepository = $customerRepository;
        $this->session = $session;
        $this->processors = $processors;
    }

    /**
     * Dispatch controller.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->session->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }

        if (!$this->helper->isModuleEnabled() || !$this->helper->isAccountExportEnabled()){
            $this->_forward('no_route');
        }

        return parent::dispatch($request);
    }

    /**
     * Execute export action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $this->downloadZip();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong. Try again later.'));

            return $resultRedirect->setPath('privacy/export');
        }

        return $this->resultFactory->create(ResultFactory::TYPE_RAW);
    }

    /**
     * This function download .zip file with customer data.
     *
     * @return void
     * @throws \Exception
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function downloadZip()
    {
        $customer = $this->customerRepository->getById($this->session->getCustomerId());
        $date = $this->getDateStamp();

        $zipFileName = 'customer_data_' . $date . '.zip';

        foreach ($this->processors as $name => $processor) {
            if (!$processor instanceof DataExportInterface) {
                continue;
            }

            $file = $name . '_' . $date . '.csv';

            $this->createCsv($file, $processor->export($customer));
            $this->zip->pack($file, $zipFileName);
            $this->deleteCsv($file);
        }

        $this->fileFactory->create(
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
        if (!$data) {
            return null;
        }

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

    /**
     * Return current date.
     *
     * @return false|string
     */
    private function getDateStamp()
    {
        return date('Y-m-d_H-i-s', $this->dateTime->gmtTimestamp());
    }
}
