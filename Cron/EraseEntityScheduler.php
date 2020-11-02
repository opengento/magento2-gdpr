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
use Magento\Store\Model\ScopeInterface;
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Model\Erase\EraseEntityScheduler as EraseEntitySchedulerService;
use Psr\Log\LoggerInterface;

/**
 * Schedule entities to erase
 */
final class EraseEntityScheduler
{
    private const CONFIG_PATH_ERASURE_MAX_AGE = 'gdpr/erasure/entity_max_age';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var EraseEntitySchedulerService
     */
    private $eraseEntityScheduler;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var string[]
     */
    private $entityTypes;

    public function __construct(
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        Config $config,
        EraseEntitySchedulerService $eraseEntityScheduler,
        FilterBuilder $filterBuilder,
        array $entityTypes
    ) {
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
        $this->eraseEntityScheduler = $eraseEntityScheduler;
        $this->filterBuilder = $filterBuilder;
        $this->entityTypes = $entityTypes;
    }

    public function execute(): void
    {
        if ($this->config->isModuleEnabled() && $this->config->isErasureEnabled()) {
            try {
                $this->filterBuilder->setField('created_at');
                $this->filterBuilder->setValue(new DateTime('-' . $this->resolveErasureMaxAge() . 'days'));
                $this->filterBuilder->setConditionType('lteq');
                $this->eraseEntityScheduler->schedule($this->entityTypes, $this->filterBuilder->create());
            } catch (Exception $e) {
                $this->logger->error($e->getMessage(), $e->getTrace());
            }
        }
    }

    private function resolveErasureMaxAge(): int
    {
        return (int) $this->scopeConfig->getValue(self::CONFIG_PATH_ERASURE_MAX_AGE, ScopeInterface::SCOPE_STORE);
    }
}
