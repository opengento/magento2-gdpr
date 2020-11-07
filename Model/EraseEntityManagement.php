<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Stdlib\DateTime as DateTimeFormat;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\ScopeInterface;
use Opengento\Gdpr\Api\Data\EraseEntityInterface;
use Opengento\Gdpr\Api\Data\EraseEntityInterfaceFactory;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;
use Opengento\Gdpr\Api\EraseEntityRepositoryInterface;
use Opengento\Gdpr\Service\Erase\ProcessorFactory;

final class EraseEntityManagement implements EraseEntityManagementInterface
{
    private const CONFIG_PATH_ERASURE_DELAY = 'gdpr/erasure/delay';

    /**
     * @var EraseEntityInterfaceFactory
     */
    private $eraseEntityFactory;

    /**
     * @var EraseEntityRepositoryInterface
     */
    private $eraseRepository;

    /**
     * @var ProcessorFactory
     */
    private $processorFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var DateTime
     */
    private $localeDate;

    public function __construct(
        EraseEntityInterfaceFactory $eraseEntityFactory,
        EraseEntityRepositoryInterface $eraseRepository,
        ProcessorFactory $processorFactory,
        ScopeConfigInterface $scopeConfig,
        DateTime $localeDate
    ) {
        $this->eraseEntityFactory = $eraseEntityFactory;
        $this->eraseRepository = $eraseRepository;
        $this->processorFactory = $processorFactory;
        $this->scopeConfig = $scopeConfig;
        $this->localeDate = $localeDate;
    }

    public function create(int $entityId, string $entityType): EraseEntityInterface
    {
        /** @var EraseEntityInterface $entity */
        $entity = $this->eraseEntityFactory->create();
        $entity->setEntityId($entityId);
        $entity->setEntityType($entityType);
        $entity->setState(EraseEntityInterface::STATE_PENDING);
        $entity->setStatus(EraseEntityInterface::STATUS_READY);
        $entity->setScheduledAt($this->retrieveScheduledAt());

        return $this->eraseRepository->save($entity);
    }

    public function cancel(int $entityId, string $entityType): bool
    {
        return $this->eraseRepository->delete($this->eraseRepository->getByEntity($entityId, $entityType));
    }

    public function process(EraseEntityInterface $entity): EraseEntityInterface
    {
        $entity->setState(EraseEntityInterface::STATE_PROCESSING);
        $entity->setStatus(EraseEntityInterface::STATUS_RUNNING);
        $entity = $this->eraseRepository->save($entity);
        $eraser = $this->processorFactory->get($entity->getEntityType());

        try {
            if ($eraser->execute($entity->getEntityId())) {
                return $this->success($entity);
            }

            return $this->fail($entity);
        } catch (Exception $e) {
            $this->fail($entity, $e->getMessage());
            throw new LocalizedException(new Phrase('Impossible to process the erasure: %1', [$e->getMessage()]));
        }
    }

    /**
     * @param EraseEntityInterface $entity
     * @return EraseEntityInterface
     * @throws CouldNotSaveException
     */
    private function success(EraseEntityInterface $entity): EraseEntityInterface
    {
        $entity->setState(EraseEntityInterface::STATE_COMPLETE);
        $entity->setStatus(EraseEntityInterface::STATUS_SUCCEED);
        $entity->setErasedAt($this->localeDate->gmtDate());
        $entity->setMessage(null);

        return $this->eraseRepository->save($entity);
    }

    /**
     * @param EraseEntityInterface $entity
     * @param string|null $message [optional]
     * @return EraseEntityInterface
     * @throws CouldNotSaveException
     */
    private function fail(EraseEntityInterface $entity, ?string $message = null): EraseEntityInterface
    {
        $entity->setState(EraseEntityInterface::STATE_PROCESSING);
        $entity->setStatus(EraseEntityInterface::STATUS_FAILED);
        $entity->setMessage($message);

        return $this->eraseRepository->save($entity);
    }

    private function retrieveScheduledAt(): string
    {
        return $this->localeDate->gmtDate(
            DateTimeFormat::DATETIME_PHP_FORMAT,
            $this->resolveErasureDelay() * 60 + $this->localeDate->gmtTimestamp()
        );
    }

    public function resolveErasureDelay(): int
    {
        return (int) $this->scopeConfig->getValue(self::CONFIG_PATH_ERASURE_DELAY, ScopeInterface::SCOPE_STORE);
    }
}
