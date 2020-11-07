<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Anonymize;

use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\ResourceModel\Visitor\Collection;
use Magento\Customer\Model\ResourceModel\Visitor\CollectionFactory as VisitorCollectionFactory;
use Magento\Customer\Model\Visitor;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Intl\DateTimeFactory;
use Magento\Framework\Math\Random;
use Magento\Framework\Session\Config;
use Magento\Framework\Session\SaveHandlerInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\ScopeInterface;

final class AccountBlocker
{
    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var SaveHandlerInterface
     */
    private $saveHandler;

    /**
     * @var VisitorCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var DateTimeFactory
     */
    private $dateTimeFactory;

    /**
     * @var Random
     */
    private $mathRandom;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        CustomerRegistry $customerRegistry,
        EncryptorInterface $encryptor,
        SaveHandlerInterface $saveHandler,
        VisitorCollectionFactory $collectionFactory,
        DateTimeFactory $dateTimeFactory,
        Random $mathRandom,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->customerRegistry = $customerRegistry;
        $this->saveHandler = $saveHandler;
        $this->collectionFactory = $collectionFactory;
        $this->encryptor = $encryptor;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->mathRandom = $mathRandom;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param int $customerId
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function invalid(int $customerId): bool
    {
        return $this->resetPassword($customerId) && $this->closeSessions($customerId);
    }

    /**
     * @param int $customerId
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
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
     * @param int $customerId
     * @return bool
     */
    private function closeSessions(int $customerId): bool
    {
        $sessionLifetime = $this->scopeConfig->getValue(Config::XML_PATH_COOKIE_LIFETIME, ScopeInterface::SCOPE_STORE);
        $dateTime = $this->dateTimeFactory->create();
        $time = $dateTime->setTimestamp($dateTime->getTimestamp() - $sessionLifetime);

        /** @var Collection $visitorCollection */
        $visitorCollection = $this->collectionFactory->create();
        $visitorCollection->addFieldToFilter('customer_id', ['eq' => $customerId]);
        $visitorCollection->addFieldToFilter('last_visit_at', ['from' => $time->format(DateTime::DATETIME_PHP_FORMAT)]);

        /** @var Visitor $visitor */
        foreach ($visitorCollection->getItems() as $visitor) {
            $this->saveHandler->destroy($visitor->getData('session_id'));
        }

        return true;
    }
}
