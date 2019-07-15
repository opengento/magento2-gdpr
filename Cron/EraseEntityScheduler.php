<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Cron;

use Magento\Framework\Api\FilterBuilder;
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Model\Erase\EraseEntityScheduler as EraseEntitySchedulerService;
use Psr\Log\LoggerInterface;

/**
 * Class EraseEntityScheduler
 */
final class EraseEntityScheduler
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @var \Opengento\Gdpr\Model\Erase\EraseEntityScheduler
     */
    private $eraseEntityScheduler;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var string[]
     */
    private $entityTypes;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Opengento\Gdpr\Model\Config $config
     * @param \Opengento\Gdpr\Model\Erase\EraseEntityScheduler $eraseEntityScheduler
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param string[] $entityTypes
     */
    public function __construct(
        LoggerInterface $logger,
        Config $config,
        EraseEntitySchedulerService $eraseEntityScheduler,
        FilterBuilder $filterBuilder,
        array $entityTypes
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->eraseEntityScheduler = $eraseEntityScheduler;
        $this->filterBuilder = $filterBuilder;
        $this->entityTypes = $entityTypes;
    }

    /**
     * Schedule entities to erase
     *
     * @return void
     */
    public function execute(): void
    {
        if ($this->config->isModuleEnabled() && $this->config->isErasureEnabled()) {
            try {
                $this->filterBuilder->setField('created_at');
                $this->filterBuilder->setValue(new \DateTime('-' . $this->config->getErasureMaxAge() . 'days'));
                $this->filterBuilder->setConditionType('lteq');
                $this->eraseEntityScheduler->schedule($this->entityTypes, $this->filterBuilder->create());
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
