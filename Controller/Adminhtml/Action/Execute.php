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
use Opengento\Gdpr\Api\ActionInterface;
use Opengento\Gdpr\Api\ActionInterfaceFactory;
use Opengento\Gdpr\Api\Data\ActionResultInterface;
use Opengento\Gdpr\Model\Action\ContextBuilder;
use Opengento\Gdpr\Model\Config\Source\ActionStates;

class Execute extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::gdpr_actions_execute';

    /**
     * @var ActionInterfaceFactory
     */
    private $actionFactory;

    /**
     * @var ContextBuilder
     */
    private $contextBuilder;

    /**
     * @var ActionStates
     */
    private $actionStates;

    public function __construct(
        Context $context,
        ActionInterfaceFactory $actionFactory,
        ContextBuilder $contextBuilder,
        ActionStates $actionStates
    ) {
        $this->actionFactory = $actionFactory;
        $this->contextBuilder = $contextBuilder;
        $this->actionStates = $actionStates;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/new');

        try {
            $result = $this->proceed();
            $resultRedirect->setPath('*/*/');
            $this->messageManager->addSuccessMessage(
                new Phrase('The action state is now: %1.', [$this->actionStates->getOptionText($result->getState())])
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('An error occurred on the server.'));
        }

        return $resultRedirect;
    }

    /**
     * @return ActionResultInterface
     * @throws LocalizedException
     * @throws Exception
     */
    private function proceed(): ActionResultInterface
    {
        $parameters = [];
        /** @var array $param */
        foreach ((array) $this->getRequest()->getParam('parameters', []) as $param) {
            $parameters[$param['name']] = $param['value'];
        }

        $this->contextBuilder->setParameters($parameters);

        /** @var ActionInterface $action */
        $action = $this->actionFactory->create(['type' => $this->getRequest()->getParam('type')]);

        return $action->execute($this->contextBuilder->create());
    }
}
