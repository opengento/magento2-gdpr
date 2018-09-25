<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize;

use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\ResourceModel\Visitor\CollectionFactory as VisitorCollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Intl\DateTimeFactory;
use Magento\Framework\Math\Random;
use Magento\Framework\Session\Config;
use Magento\Framework\Session\SaveHandlerInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\ScopeInterface;

/**
 * Class AccountBlocker
 */
final class AccountBlocker
{
    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @var \Magento\Framework\Session\SaveHandlerInterface
     */
    private $saveHandler;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Visitor\CollectionFactory
     */
    private $visitorCollectionFactory;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    /**
     * @var \Magento\Framework\Intl\DateTimeFactory
     */
    private $dateTimeFactory;

    /**
     * @var \Magento\Framework\Math\Random
     */
    private $mathRandom;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
     * @param \Magento\Framework\Session\SaveHandlerInterface $saveHandler
     * @param \Magento\Customer\Model\ResourceModel\Visitor\CollectionFactory $visitorCollectionFactory
     * @param \Magento\Framework\Intl\DateTimeFactory $dateTimeFactory
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CustomerRegistry $customerRegistry,
        EncryptorInterface $encryptor,
        SessionManagerInterface $sessionManager,
        SaveHandlerInterface $saveHandler,
        VisitorCollectionFactory $visitorCollectionFactory,
        DateTimeFactory $dateTimeFactory,
        Random $mathRandom,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->customerRegistry = $customerRegistry;
        $this->sessionManager = $sessionManager;
        $this->saveHandler = $saveHandler;
        $this->visitorCollectionFactory = $visitorCollectionFactory;
        $this->encryptor = $encryptor;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->mathRandom = $mathRandom;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Invalid a customer account
     *
     * @param int $customerId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function invalid(int $customerId): bool
    {
        return $this->resetPassword($customerId) && $this->closeSessions($customerId);
    }

    /**
     * Reset the customer password to unknown password
     *
     * @param int $customerId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function resetPassword(int $customerId): bool
    {
        $customerSecure = $this->customerRegistry->retrieveSecureData($customerId);
        $customerSecure->setRpToken('');
        $customerSecure->setRpTokenCreatedAt('');
        $customerSecure->setPasswordHash($this->encryptor->getHash($this->mathRandom->getUniqueHash(), true));

        return true;
    }

    /**
     * Close all sessions related to the customer
     *
     * @param int $customerId
     * @return bool
     */
    private function closeSessions(int $customerId): bool
    {
        $sessionLifetime = $this->scopeConfig->getValue(Config::XML_PATH_COOKIE_LIFETIME, ScopeInterface::SCOPE_STORE);
        $dateTime = $this->dateTimeFactory->create();
        $time = $dateTime->setTimestamp($dateTime->getTimestamp() - $sessionLifetime);

        /** @var \Magento\Customer\Model\ResourceModel\Visitor\Collection $visitorCollection */
        $visitorCollection = $this->visitorCollectionFactory->create();
        $visitorCollection->addFieldToFilter('customer_id', $customerId);
        $visitorCollection->addFieldToFilter('last_visit_at', ['from' => $time->format(DateTime::DATETIME_PHP_FORMAT)]);

        /** @var \Magento\Customer\Model\Visitor $visitor */
        foreach ($visitorCollection->getItems() as $visitor) {
            $this->sessionManager->start();
            $this->saveHandler->destroy($visitor->getData('session_id'));
            $this->sessionManager->writeClose();
        }

        return true;
    }
}
