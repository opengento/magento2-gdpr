<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Cron;

use DateTime;
use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Stdlib\DateTime as DateTimeFormat;
use Opengento\Gdpr\Api\ActionEntityManagementInterface;
use Opengento\Gdpr\Api\ActionEntityRepositoryInterface;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;
use Psr\Log\LoggerInterface;

final class ActionEntity
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ActionEntityManagementInterface
     */
    private $actionEntityManagement;

    /**
     * @var ActionEntityRepositoryInterface
     */
    private $actionEntityRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        LoggerInterface $logger,
        ActionEntityManagementInterface $actionEntityManagement,
        ActionEntityRepositoryInterface $actionEntityRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->logger = $logger;
        $this->actionEntityRepository = $actionEntityRepository;
        $this->actionEntityManagement = $actionEntityManagement;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function execute(): void
    {
        $this->searchCriteriaBuilder->addFilter(ActionEntityInterface::STATE, ActionEntityInterface::STATE_PENDING);
        $this->searchCriteriaBuilder->addFilter(
            ActionEntityInterface::SCHEDULED_AT,
            (new DateTime())->format(DateTimeFormat::DATETIME_PHP_FORMAT),
            'lteq'
        );

        try {
            $actionEntityList = $this->actionEntityRepository->getList($this->searchCriteriaBuilder->create());

            /** @var ActionEntityInterface $actionEntity */
            foreach ($actionEntityList->getItems() as $actionEntity) {
                $this->actionEntityManagement->execute($actionEntity);
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
    }
}
