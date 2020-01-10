<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Privacy;

use Exception;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Opengento\Gdpr\Controller\AbstractPrivacy;
use Opengento\Gdpr\Model\Config;
use function get_class;
use function var_dump;

class Download extends AbstractPrivacy implements HttpGetActionInterface
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var ExportEntityRepositoryInterface
     */
    private $exportRepository;

    /**
     * @var Session
     */
    private $customerSession;

    public function __construct(
        Context $context,
        Config $config,
        FileFactory $fileFactory,
        ExportEntityRepositoryInterface $exportRepository,
        Session $customerSession
    ) {
        $this->fileFactory = $fileFactory;
        $this->exportRepository = $exportRepository;
        $this->customerSession = $customerSession;
        parent::__construct($context, $config);
    }

    protected function isAllowed(): bool
    {
        return parent::isAllowed() && $this->config->isExportEnabled();
    }

    protected function executeAction()
    {
        try {
            $customerId = (int) $this->customerSession->getCustomerId();

            return $this->fileFactory->create(
                'customer_privacy_data_' . $customerId . '.zip',
                [
                    'type' => 'filename',
                    'value' => $this->exportRepository->getByEntity($customerId, 'customer')->getFilePath(),
                ],
                DirectoryList::TMP
            );
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(
                new Phrase('The document does not exists and may have expired. Please renew your demand.')
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('Something went wrong, please try again later!'));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setRefererOrBaseUrl();
    }
}
