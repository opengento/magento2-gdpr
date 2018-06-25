<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Cron;

use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Model\CronSchedule;
use Opengento\Gdpr\Model\ReasonsFactory;
use Opengento\Gdpr\Model\ResourceModel\CronSchedule as CronScheduleResource;
use Opengento\Gdpr\Model\ResourceModel\CronSchedule\CollectionFactory;
use Opengento\Gdpr\Model\ResourceModel\Reasons as ResourceReasons;
use Opengento\Gdpr\Service\ErasureStrategy;
use Psr\Log\LoggerInterface;

/**
 * Scheduler to clean accounts marked to be deleted or anonymized
 */
class Erasure
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
     * @var \Opengento\Gdpr\Service\ErasureStrategy
     */
    private $erasureStrategy;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Opengento\Gdpr\Model\ReasonsFactory
     */
    private $reasonFactory;

    /**
     * @var \Opengento\Gdpr\Model\ResourceModel\Reasons
     */
    private $reasonsResource;

    /**
     * @var \Opengento\Gdpr\Model\ResourceModel\CronSchedule\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Opengento\Gdpr\Model\ResourceModel\CronSchedule
     */
    private $schedulerResource;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Opengento\Gdpr\Model\Config $config
     * @param \Opengento\Gdpr\Service\ErasureStrategy $erasureStrategy
     * @param \Magento\Framework\Registry $registry
     * @param \Opengento\Gdpr\Model\ReasonsFactory $reasonFactory
     * @param \Opengento\Gdpr\Model\ResourceModel\Reasons $reasonsResource
     * @param \Opengento\Gdpr\Model\ResourceModel\CronSchedule\CollectionFactory $collectionFactory
     * @param \Opengento\Gdpr\Model\ResourceModel\CronSchedule $scheduleResource
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     */
    public function __construct(
        LoggerInterface $logger,
        Config $config,
        ErasureStrategy $erasureStrategy,
        Registry $registry,
        ReasonsFactory $reasonFactory,
        ResourceReasons $reasonsResource,
        CollectionFactory $collectionFactory,
        CronScheduleResource $scheduleResource,
        DateTime $dateTime
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->erasureStrategy = $erasureStrategy;
        $this->registry = $registry;
        $this->reasonFactory = $reasonFactory;
        $this->reasonsResource = $reasonsResource;
        $this->collectionFactory = $collectionFactory;
        $this->schedulerResource = $scheduleResource;
        $this->dateTime = $dateTime;
    }

    /**
     * Check for accounts which need to be erased
     *
     * @return void
     */
    public function execute()
    {
        if ($this->config->isModuleEnabled() && $this->config->isErasureEnabled()) {
            /** @var \Opengento\Gdpr\Model\ResourceModel\CronSchedule\Collection $cronScheduleCollection */
            $cronScheduleCollection = $this->collectionFactory->create();
            $cronScheduleCollection->addFieldToFilter('scheduled_at', ['lteq' => $this->dateTime->gmtDate()]);

            if ($cronScheduleCollection->count()) {
                $oldValue = $this->registry->registry('isSecureArea');
                $this->registry->register('isSecureArea', true, true);

                /** @var \Opengento\Gdpr\Model\CronSchedule $cronSchedule */
                foreach ($cronScheduleCollection as $cronSchedule) {
                    try {
                        $this->erasureStrategy->execute((int) $cronSchedule->getData('customer_id'));
                        $this->proceedReason($cronSchedule);
                    } catch (\Exception $e) {
                        $this->logger->error($e->getMessage());
                    }
                }

                $this->registry->register('isSecureArea', $oldValue, true);
            }
        }
    }

    /**
     * Proceed reason and update the cron schedule eraser
     *
     * @param \Opengento\Gdpr\Model\CronSchedule $cronSchedule
     * @return void
     * @throws \Exception
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    private function proceedReason(CronSchedule $cronSchedule)
    {
        $this->reasonsResource->save(
            $this->reasonFactory->create(['reason' => $cronSchedule->getData('reason')])
        );
        $this->schedulerResource->delete($cronSchedule);
    }
}
