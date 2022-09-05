<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Erase;

use DateTimeImmutable;
use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\ScopeInterface;
use Opengento\Gdpr\Api\Data\EraseEntityInterface;
use Opengento\Gdpr\Api\Data\EraseEntityInterfaceFactory;
use Opengento\Gdpr\Api\EraseEntityRepositoryInterface;
use Opengento\Gdpr\Api\EraseSalesInformationInterface;

final class EraseSalesInformation implements EraseSalesInformationInterface
{
    private const CONFIG_PATH_ERASURE_SALES_MAX_AGE = 'gdpr/erasure/sales_max_age';

    private EraseEntityInterfaceFactory $eraseEntityFactory;

    private EraseEntityRepositoryInterface $eraseRepository;

    private ScopeConfigInterface $scopeConfig;

    public function __construct(
        EraseEntityInterfaceFactory $eraseEntityFactory,
        EraseEntityRepositoryInterface $eraseRepository,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->eraseEntityFactory = $eraseEntityFactory;
        $this->eraseRepository = $eraseRepository;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     * @throws CouldNotSaveException
     */
    public function scheduleEraseEntity(int $entityId, string $entityType, \DateTime $lastActive): EraseEntityInterface
    {
        $dateTime = DateTimeImmutable::createFromMutable($lastActive);
        $scheduleAt = $dateTime->modify('+' . $this->resolveErasureSalesMaxAge() . ' days');

        /** @var EraseEntityInterface $eraseEntity */
        $eraseEntity = $this->eraseEntityFactory->create();
        $eraseEntity->setEntityId($entityId);
        $eraseEntity->setEntityType($entityType);
        $eraseEntity->setState(EraseEntityInterface::STATE_PENDING);
        $eraseEntity->setStatus(EraseEntityInterface::STATUS_READY);
        $eraseEntity->setScheduledAt($scheduleAt->format(DateTime::DATETIME_PHP_FORMAT));

        $this->eraseRepository->save($eraseEntity);

        return $eraseEntity;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isAlive(\DateTime $dateTime): bool
    {
        return $dateTime > new \DateTime('-' . $this->resolveErasureSalesMaxAge() . 'days');
    }

    private function resolveErasureSalesMaxAge(): int
    {
        return (int) $this->scopeConfig->getValue(self::CONFIG_PATH_ERASURE_SALES_MAX_AGE, ScopeInterface::SCOPE_STORE);
    }
}
