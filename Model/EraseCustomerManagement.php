<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Stdlib\DateTime as DateTimeFormat;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Opengento\Gdpr\Api\Data\EraseCustomerInterface;
use Opengento\Gdpr\Api\Data\EraseCustomerInterfaceFactory;
use Opengento\Gdpr\Api\EraseCustomerCheckerInterface;
use Opengento\Gdpr\Api\EraseCustomerManagementInterface;
use Opengento\Gdpr\Api\EraseCustomerRepositoryInterface;
use Opengento\Gdpr\Api\EraseInterface;

/**
 * Class EraseCustomerManagement
 */
final class EraseCustomerManagement implements EraseCustomerManagementInterface
{
    /**
     * @var \Opengento\Gdpr\Api\Data\EraseCustomerInterfaceFactory
     */
    private $eraseCustomerFactory;

    /**
     * @var \Opengento\Gdpr\Api\EraseCustomerRepositoryInterface
     */
    private $eraseCustomerRepository;

    /**
     * @var \Opengento\Gdpr\Api\EraseCustomerCheckerInterface
     */
    private $eraseCustomerChecker;

    /**
     * @var \Opengento\Gdpr\Api\EraseInterface
     */
    private $eraseManagement;

    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $localeDate;

    /**
     * @param \Opengento\Gdpr\Api\Data\EraseCustomerInterfaceFactory $eraseCustomerFactory
     * @param \Opengento\Gdpr\Api\EraseCustomerRepositoryInterface $eraseCustomerRepository
     * @param \Opengento\Gdpr\Api\EraseCustomerCheckerInterface $eraseCustomerChecker
     * @param \Opengento\Gdpr\Api\EraseInterface $eraseManagement
     * @param \Opengento\Gdpr\Model\Config $config
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $localeDate
     */
    public function __construct(
        EraseCustomerInterfaceFactory $eraseCustomerFactory,
        EraseCustomerRepositoryInterface $eraseCustomerRepository,
        EraseCustomerCheckerInterface $eraseCustomerChecker,
        EraseInterface $eraseManagement,
        Config $config,
        DateTime $localeDate
    ) {
        $this->eraseCustomerFactory = $eraseCustomerFactory;
        $this->eraseCustomerRepository = $eraseCustomerRepository;
        $this->eraseCustomerChecker = $eraseCustomerChecker;
        $this->eraseManagement = $eraseManagement;
        $this->config = $config;
        $this->localeDate = $localeDate;
    }

    /**
     * @inheritdoc
     */
    public function create(int $customerId): EraseCustomerInterface
    {
        if (!$this->eraseCustomerChecker->canCreate($customerId)) {
            throw new LocalizedException(
                new Phrase(
                    'Impossible to initiate the erasure, it\'s already processing or there is still pending orders.'
                )
            );
        }

        /** @var \Opengento\Gdpr\Api\Data\EraseCustomerInterface $entity */
        $entity = $this->eraseCustomerFactory->create();
        $entity->setCustomerId($customerId);
        $entity->setState(EraseCustomerInterface::STATE_PENDING);
        $entity->setStatus(EraseCustomerInterface::STATUS_READY);
        $entity->setScheduledAt($this->retrieveScheduledAt());

        return $this->eraseCustomerRepository->save($entity);
    }

    /**
     * @inheritdoc
     */
    public function cancel(int $customerId): bool
    {
        if (!$this->eraseCustomerChecker->canCancel($customerId)) {
            throw new LocalizedException(new Phrase('The erasure process is running and cannot be undone.'));
        }

        return $this->eraseCustomerRepository->delete($this->eraseCustomerRepository->getByCustomerId($customerId));
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function process(EraseCustomerInterface $entity): EraseCustomerInterface
    {
        if (!$this->eraseCustomerChecker->canProcess($entity->getCustomerId())) {
            throw new LocalizedException(
                new Phrase('Impossible to process the erasure, there is still pending orders.')
            );
        }

        $entity->setState(EraseCustomerInterface::STATE_PROCESSING);
        $entity->setStatus(EraseCustomerInterface::STATUS_RUNNING);
        $entity = $this->eraseCustomerRepository->save($entity);

        try {
            if ($this->eraseManagement->erase($entity->getCustomerId())) {
                return $this->success($entity);
            }

            return $this->fail($entity);
        } catch (\Exception $e) {
            $this->fail($entity, $e->getMessage());
            throw $e;
        }
    }

    /**
     * Erasure has succeeded
     *
     * @param \Opengento\Gdpr\Api\Data\EraseCustomerInterface $entity
     * @return \Opengento\Gdpr\Api\Data\EraseCustomerInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function success(EraseCustomerInterface $entity): EraseCustomerInterface
    {
        $entity->setState(EraseCustomerInterface::STATE_COMPLETE);
        $entity->setStatus(EraseCustomerInterface::STATUS_SUCCEED);
        $entity->setErasedAt($this->localeDate->gmtDate());
        $entity->setMessage(null);

        return $this->eraseCustomerRepository->save($entity);
    }

    /**
     * Erasure has failed
     *
     * @param \Opengento\Gdpr\Api\Data\EraseCustomerInterface $entity
     * @param string|null $message
     * @return \Opengento\Gdpr\Api\Data\EraseCustomerInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function fail(EraseCustomerInterface $entity, ?string $message = null): EraseCustomerInterface
    {
        $entity->setState(EraseCustomerInterface::STATE_PROCESSING);
        $entity->setStatus(EraseCustomerInterface::STATUS_FAILED);
        $entity->setMessage($message);

        return $this->eraseCustomerRepository->save($entity);
    }

    /**
     * Retrieve the final scheduled at date from config
     *
     * @return string
     */
    private function retrieveScheduledAt(): string
    {
        return $this->localeDate->gmtDate(
            DateTimeFormat::DATETIME_PHP_FORMAT,
            $this->config->getErasureDelay() * 60 + $this->localeDate->gmtTimestamp()
        );
    }
}
