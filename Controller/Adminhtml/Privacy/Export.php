<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Adminhtml\Privacy;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Opengento\Gdpr\Controller\Adminhtml\AbstractAction;
use Opengento\Gdpr\Model\Config;

class Export extends AbstractAction
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::customer_export';

    public function __construct(
        Context $context,
        Config $config,
        private FileFactory $fileFactory,
        private ExportEntityManagementInterface $exportEntityManagement,
        private ExportEntityRepositoryInterface $exportEntityRepository,
    ) {
        parent::__construct($context, $config);
    }

    protected function executeAction(): ResultInterface|ResponseInterface
    {
        try {
            $exportEntity = $this->exportEntityManagement->export(
                $this->exportEntityRepository->getByEntity((int)$this->getRequest()->getParam('id'), 'customer')
            );

            return $this->fileFactory->create(
                'customer_privacy_data_' . $exportEntity->getEntityId() . '.zip',
                [
                    'type' => 'filename',
                    'value' => $exportEntity->getFilePath(),
                ],
                DirectoryList::TMP
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('An error occurred on the server.'));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setRefererOrBaseUrl();
    }
}
