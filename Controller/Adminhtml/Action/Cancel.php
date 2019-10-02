<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Adminhtml\Action;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\ActionEntityRepositoryInterface;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;

class Cancel extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::gdpr_actions_cancel';

    /**
     * @var ActionEntityRepositoryInterface
     */
    private $actionEntityRepository;

    public function __construct(
        Context $context,
        ActionEntityRepositoryInterface $actionEntityRepository
    ) {
        $this->actionEntityRepository = $actionEntityRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $this->cancel();
            $this->messageManager->addSuccessMessage(new Phrase('The selected action have been canceled.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('An error occurred on the server.'));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    private function cancel(): void
    {
        $actionEntity = $this->actionEntityRepository->getById((int) $this->getRequest()->getParam('action_id'));

        if ($actionEntity->getState() !== ActionEntityInterface::STATE_PENDING) {
            throw new LocalizedException(new Phrase('The selected action cannot be canceled.'));
        }

        $actionEntity->setState(ActionEntityInterface::STATE_CANCELED);
        $this->actionEntityRepository->save($actionEntity);
    }
}
