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
use Opengento\Gdpr\Api\Data\EraseEntityInterface;
use Opengento\Gdpr\Api\Data\EraseEntityInterfaceFactory;
use Opengento\Gdpr\Api\EraseEntityCheckerInterface;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;
use Opengento\Gdpr\Api\EraseEntityRepositoryInterface;
use Opengento\Gdpr\Service\Erase\ProcessorFactory;

/**
 * Class EraseEntityManagement
 */
final class EraseEntityManagement implements EraseEntityManagementInterface
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
     * @var \Opengento\Gdpr\Api\EraseEntityCheckerInterface
     */
    private $eraseEntityChecker;

    /**
     * @var \Opengento\Gdpr\Service\Erase\ProcessorFactory
     */
    private $eraseProcessorFactory;

    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $localeDate;

    /**
     * @param \Opengento\Gdpr\Api\Data\EraseEntityInterfaceFactory $eraseEntityFactory
     * @param \Opengento\Gdpr\Api\EraseEntityRepositoryInterface $eraseEntityRepository
     * @param \Opengento\Gdpr\Api\EraseEntityCheckerInterface $eraseEntityChecker
     * @param \Opengento\Gdpr\Service\Erase\ProcessorFactory $eraseProcessorFactory
     * @param \Opengento\Gdpr\Model\Config $config
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $localeDate
     */
    public function __construct(
        EraseEntityInterfaceFactory $eraseEntityFactory,
        EraseEntityRepositoryInterface $eraseEntityRepository,
        EraseEntityCheckerInterface $eraseEntityChecker,
        ProcessorFactory $eraseProcessorFactory,
        Config $config,
        DateTime $localeDate
    ) {
        $this->eraseEntityFactory = $eraseEntityFactory;
        $this->eraseEntityRepository = $eraseEntityRepository;
        $this->eraseEntityChecker = $eraseEntityChecker;
        $this->eraseProcessorFactory = $eraseProcessorFactory;
        $this->config = $config;
        $this->localeDate = $localeDate;
    }

    /**
     * @inheritdoc
     */
    public function create(int $entityId, string $entityType): EraseEntityInterface
    {
        if (!$this->eraseEntityChecker->canCreate($entityId, $entityType)) {
            throw new LocalizedException(
                new Phrase(
                    'Impossible to initiate the erasure, it\'s already processing or there is still pending orders.'
                )
            );
        }

        /** @var \Opengento\Gdpr\Api\Data\EraseEntityInterface $entity */
        $entity = $this->eraseEntityFactory->create();
        $entity->setEntityId($entityId);
        $entity->setEntityType($entityType);
        $entity->setState(EraseEntityInterface::STATE_PENDING);
        $entity->setStatus(EraseEntityInterface::STATUS_READY);
        $entity->setScheduledAt($this->retrieveScheduledAt());

        return $this->eraseEntityRepository->save($entity);
    }

    /**
     * @inheritdoc
     */
    public function cancel(int $entityId, string $entityType): bool
    {
        if (!$this->eraseEntityChecker->canCancel($entityId, $entityType)) {
            throw new LocalizedException(new Phrase('The erasure process is running and cannot be undone.'));
        }

        return $this->eraseEntityRepository->delete($this->eraseEntityRepository->getByEntity($entityId, $entityType));
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function process(EraseEntityInterface $entity): EraseEntityInterface
    {
        if (!$this->eraseEntityChecker->canProcess($entity->getEntityId(), $entity->getEntityType())) {
            throw new LocalizedException(
                new Phrase('Impossible to process the erasure, there is still pending orders.')
            );
        }

        $entity->setState(EraseEntityInterface::STATE_PROCESSING);
        $entity->setStatus(EraseEntityInterface::STATUS_RUNNING);
        $entity = $this->eraseEntityRepository->save($entity);
        $eraser = $this->eraseProcessorFactory->get($entity->getEntityType());

        try {
            if ($eraser->execute($entity->getEntityId())) {
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
     * @param \Opengento\Gdpr\Api\Data\EraseEntityInterface $entity
     * @return \Opengento\Gdpr\Api\Data\EraseEntityInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function success(EraseEntityInterface $entity): EraseEntityInterface
    {
        $entity->setState(EraseEntityInterface::STATE_COMPLETE);
        $entity->setStatus(EraseEntityInterface::STATUS_SUCCEED);
        $entity->setErasedAt($this->localeDate->gmtDate());
        $entity->setMessage(null);

        return $this->eraseEntityRepository->save($entity);
    }

    /**
     * Erasure has failed
     *
     * @param \Opengento\Gdpr\Api\Data\EraseEntityInterface $entity
     * @param string|null $message
     * @return \Opengento\Gdpr\Api\Data\EraseEntityInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function fail(EraseEntityInterface $entity, ?string $message = null): EraseEntityInterface
    {
        $entity->setState(EraseEntityInterface::STATE_PROCESSING);
        $entity->setStatus(EraseEntityInterface::STATUS_FAILED);
        $entity->setMessage($message);

        return $this->eraseEntityRepository->save($entity);
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
