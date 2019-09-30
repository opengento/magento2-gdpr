<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use DateTime;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime as DateTimeFormat;
use Magento\Framework\Stdlib\DateTime\DateTime as DateTimeManager;
use Opengento\Gdpr\Api\ActionEntityManagementInterface;
use Opengento\Gdpr\Api\ActionEntityRepositoryInterface;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;
use Opengento\Gdpr\Model\Action\ProcessorFactory;

final class ActionEntityManagement implements ActionEntityManagementInterface
{
    /**
     * @var ActionEntityRepositoryInterface
     */
    private $actionEntityRepository;

    /**
     * @var ProcessorFactory
     */
    private $processorFactory;

    /**
     * @var DateTimeManager
     */
    private $dateTime;

    public function __construct(
        ActionEntityRepositoryInterface $actionEntityRepository,
        ProcessorFactory $processorFactory,
        DateTimeManager $dateTime
    ) {
        $this->actionEntityRepository = $actionEntityRepository;
        $this->processorFactory = $processorFactory;
        $this->dateTime = $dateTime;
    }

    public function execute(ActionEntityInterface $actionEntity): ActionEntityInterface
    {
        $actionEntity->setPerformedAt($this->dateTime->date(DateTimeFormat::DATETIME_PHP_FORMAT));
        $actionEntity->setState(ActionEntityInterface::STATE_PROCESSING);
        $actionEntity->setMessage('');

        try {
            $this->actionEntityRepository->save($actionEntity);
            $actionEntity->setResult($this->processorFactory->get($actionEntity->getType())->execute($actionEntity));
            $actionEntity->setState(ActionEntityInterface::STATE_SUCCEEDED);
        } catch (LocalizedException $e) {
            $actionEntity->setMessage($e->getMessage());
            $actionEntity->setState(ActionEntityInterface::STATE_FAILED);
        }

        return $this->actionEntityRepository->save($actionEntity);
    }

    public function schedule(ActionEntityInterface $actionEntity, DateTime $scheduledAt): ActionEntityInterface
    {
        $actionEntity->setScheduledAt($scheduledAt->format(DateTimeFormat::DATETIME_PHP_FORMAT));
        $actionEntity->setState(ActionEntityInterface::STATE_PENDING);
        $actionEntity->setMessage('');

        return $this->actionEntityRepository->save($actionEntity);
    }
}
