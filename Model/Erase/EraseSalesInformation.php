<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Erase;

use Magento\Framework\Stdlib\DateTime;
use Opengento\Gdpr\Api\Data\EraseEntityInterface;
use Opengento\Gdpr\Api\Data\EraseEntityInterfaceFactory;
use Opengento\Gdpr\Api\EraseEntityRepositoryInterface;
use Opengento\Gdpr\Api\EraseSalesInformationInterface;
use Opengento\Gdpr\Model\Config;

/**
 * Class EraseSalesInformation
 */
final class EraseSalesInformation implements EraseSalesInformationInterface
{
    /**
     * @var \Opengento\Gdpr\Api\Data\EraseEntityInterfaceFactory
     */
    private $eraseEntityFactory;

    /**
     * @var \Opengento\Gdpr\Api\EraseEntityRepositoryInterface
     */
    private $eraseEntityRepository;

    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @param \Opengento\Gdpr\Api\Data\EraseEntityInterfaceFactory $eraseEntityFactory
     * @param \Opengento\Gdpr\Api\EraseEntityRepositoryInterface $eraseEntityRepository
     * @param \Opengento\Gdpr\Model\Config $config
     */
    public function __construct(
        EraseEntityInterfaceFactory $eraseEntityFactory,
        EraseEntityRepositoryInterface $eraseEntityRepository,
        Config $config
    ) {
        $this->eraseEntityFactory = $eraseEntityFactory;
        $this->eraseEntityRepository = $eraseEntityRepository;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function scheduleEraseEntity(int $entityId, string $entityType, \DateTime $lastActive): EraseEntityInterface
    {
        $dateTime = \DateTimeImmutable::createFromMutable($lastActive);
        $scheduleAt = $dateTime->modify('+' . $this->config->getErasureSalesMaxAge() . + 'days');

        /** @var \Opengento\Gdpr\Api\Data\EraseEntityInterface $eraseEntity */
        $eraseEntity = $this->eraseEntityFactory->create();
        $eraseEntity->setEntityId($entityId);
        $eraseEntity->setEntityType($entityType);
        $eraseEntity->setState(EraseEntityInterface::STATE_PENDING);
        $eraseEntity->setStatus(EraseEntityInterface::STATUS_READY);
        $eraseEntity->setScheduledAt($scheduleAt->format(DateTime::DATETIME_PHP_FORMAT));

        $this->eraseEntityRepository->save($eraseEntity);

        return $eraseEntity;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function isAlive(\DateTime $dateTime): bool
    {
        return $dateTime > new \DateTime('-' . $this->config->getErasureSalesMaxAge() . 'days');
    }
}
