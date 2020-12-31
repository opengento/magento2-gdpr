<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Plugin;

use Magento\Customer\Controller\AccountInterface;
use Magento\Customer\Controller\Address;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\AbstractAction;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Opengento\Gdpr\Api\Data\EraseEntityInterface;
use Opengento\Gdpr\Model\ResourceModel\EraseEntity\CollectionFactory;
use Psr\Log\LoggerInterface;

final class SessionChecker
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        CollectionFactory $collectionFactory,
        Session $session,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        ManagerInterface $messageManager,
        LoggerInterface $logger
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->session = $session;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
    }

    public function aroundExecute(ActionInterface $action, callable $proceed, ...$arguments)
    {
        if ($this->session->isLoggedIn() && $this->isErased()) {
            $this->messageManager->addNoticeMessage(
                new Phrase('Your account have been erased and you have signed out.')
            );
            $this->logout();

            if ($action instanceof AccountInterface) {
                return $this->session->authenticate();
            }
            if ($action instanceof AbstractAction) {
                $action->dispatch($action->getRequest());
            }
        }

        return $proceed(...$arguments);
    }

    private function logout(): void
    {
        $this->session->logout();
        $metadata = $this->cookieMetadataFactory->createCookieMetadata();
        $metadata->setPath('/');

        try {
            $this->cookieManager->deleteCookie('mage-cache-sessid', $metadata);
        } catch (LocalizedException $e) {
            $this->logger->error($e->getLogMessage(), $e->getTrace());
        }
    }

    private function isErased(): bool
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(EraseEntityInterface::ENTITY_ID, $this->session->getCustomerId());
        $collection->addFieldToFilter(EraseEntityInterface::ENTITY_TYPE, 'customer');
        $collection->addFieldToFilter(EraseEntityInterface::STATE, EraseEntityInterface::STATE_COMPLETE);

        return (bool) $collection->getSize();
    }
}
