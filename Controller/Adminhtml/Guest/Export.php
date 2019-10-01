<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Adminhtml\Guest;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\User\Model\User;
use Opengento\Gdpr\Api\ActionInterface;
use Opengento\Gdpr\Controller\Adminhtml\AbstractAction;
use Opengento\Gdpr\Model\Action\ArgumentReader;
use Opengento\Gdpr\Model\Action\ContextBuilder;
use Opengento\Gdpr\Model\Config;
use function reset;

class Export extends AbstractAction
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::order_export';

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var ActionInterface
     */
    private $action;

    /**
     * @var ContextBuilder
     */
    private $actionContextBuilder;

    public function __construct(
        Context $context,
        Config $config,
        FileFactory $fileFactory,
        ActionInterface $action,
        ContextBuilder $actionContextBuilder
    ) {
        $this->fileFactory = $fileFactory;
        $this->action = $action;
        $this->actionContextBuilder = $actionContextBuilder;
        parent::__construct($context, $config);
    }

    protected function executeAction()
    {
        /** @var User $user */
        $user = $this->_auth->getUser();
        $entityId = (int) $this->getRequest()->getParam('id');

        $this->actionContextBuilder->setPerformedBy('Admin: ' . $user->getUserName());
        $this->actionContextBuilder->setParameters([
            ArgumentReader::ENTITY_ID => $entityId,
            ArgumentReader::ENTITY_TYPE => 'order'
        ]);

        try {
            $result = $this->action->execute($this->actionContextBuilder->create());

            return $this->fileFactory->create(
                'guest_privacy_data_' . $entityId . '.zip',
                [
                    'type' => 'filename',
                    'value' => reset($result),
                    'rm' => true,
                ],
                DirectoryList::TMP
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('An error occurred on the server.'));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setRefererOrBaseUrl();
    }
}
