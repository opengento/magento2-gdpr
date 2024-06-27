<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Cron;

use DateTime;
use Exception;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Model\Entity\EntityTypeList;
use Opengento\Gdpr\Model\Erase\EraseEntityScheduler as EraseEntitySchedulerService;
use Psr\Log\LoggerInterface;

/**
 * Schedule entities to erase
 */
class EraseEntityScheduler
{
    private const CONFIG_PATH_ERASURE_MAX_AGE = 'gdpr/erasure/entity_max_age';

    public function __construct(
        private LoggerInterface $logger,
        private ScopeConfigInterface $scopeConfig,
        private Config $config,
        private EraseEntitySchedulerService $eraseEntityScheduler,
        private FilterBuilder $filterBuilder,
        private EntityTypeList $entityTypeList
    ) {
    }

    public function execute(): void
    {
        if ($this->config->isModuleEnabled() && $this->config->isErasureEnabled()) {
            try {
                $this->scheduleEntitiesErasure();
            } catch (Exception $e) {
                $this->logger->error($e->getMessage(), $e->getTrace());
            }
        }
    }

    /**
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws Exception
     */
    private function scheduleEntitiesErasure(): void
    {
        $this->filterBuilder->setField('created_at');
        $this->filterBuilder->setValue(new DateTime('-' . $this->resolveErasureMaxAge() . 'days'));
        $this->filterBuilder->setConditionType('lteq');
        $this->eraseEntityScheduler->schedule($this->entityTypeList->getEntityTypes(), $this->filterBuilder->create());
    }

    private function resolveErasureMaxAge(): int
    {
        return (int)$this->scopeConfig->getValue(self::CONFIG_PATH_ERASURE_MAX_AGE, ScopeInterface::SCOPE_STORE);
    }
}
