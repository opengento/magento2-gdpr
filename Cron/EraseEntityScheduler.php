<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Cron;

use Exception;
use Magento\Store\Model\StoreManagerInterface;
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Model\Entity\EntityTypeList;
use Opengento\Gdpr\Model\Erase\EraseEntityScheduler as EraseEntitySchedulerService;
use Psr\Log\LoggerInterface;

/**
 * Schedule entities to erase
 */
class EraseEntityScheduler
{

    public function __construct(
        private LoggerInterface $logger,
        private Config $config,
        private EraseEntitySchedulerService $eraseEntityScheduler,
        private EntityTypeList $entityTypeList,
        private StoreManagerInterface $storeManager
    ) {}

    public function execute(): void
    {
        foreach ($this->storeManager->getWebsites() as $website) {
            $websiteId = $website->getId();
            if ($this->config->isErasureEnabled($websiteId)) {
                try {
                    $this->eraseEntityScheduler->schedule($this->entityTypeList->getEntityTypes(), $website);
                } catch (Exception $e) {
                    $this->logger->error($e->getMessage(), ['exception' => $e]);
                }
            }
        }
    }
}
