<?php
/**
 * This file is part of the Flurrybox EnhancedPrivacy package.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Flurrybox EnhancedPrivacy
 * to newer versions in the future.
 *
 * @copyright Copyright (c) 2018 Flurrybox, Ltd. (https://flurrybox.com/)
 * @license   GNU General Public License ("GPL") v3.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flurrybox\EnhancedPrivacy\Cron;

use Exception;
use Flurrybox\EnhancedPrivacy\Api\DataDeleteInterface;
use Flurrybox\EnhancedPrivacy\Helper\Data;
use Flurrybox\EnhancedPrivacy\Model\ResourceModel\CronSchedule\CollectionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;
use Flurrybox\EnhancedPrivacy\Model\ReasonsFactory;
use RuntimeException;

/**
 * Scheduler to clean accounts marked to be deleted or anonymized.
 */
class Schedule
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var ReasonsFactory
     */
    protected $reasonFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var array|DataDeleteInterface[]
     */
    protected $processors;

    /**
     * AccountCleaner constructor.
     *
     * @param LoggerInterface $logger
     * @param CollectionFactory $collectionFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param Registry $registry
     * @param ReasonsFactory $reasonFactory
     * @param Data $helper
     * @param DateTime $dateTime
     * @param DataDeleteInterface[] $processors
     */
    public function __construct(
        LoggerInterface $logger,
        CollectionFactory $collectionFactory,
        CustomerRepositoryInterface $customerRepository,
        Registry $registry,
        ReasonsFactory $reasonFactory,
        Data $helper,
        DateTime $dateTime,
        array $processors = []
    ) {
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->customerRepository = $customerRepository;
        $this->registry = $registry;
        $this->reasonFactory = $reasonFactory;
        $this->helper = $helper;
        $this->dateTime = $dateTime;
        $this->processors = $processors;
    }

    /**
     * Check for accounts which need to be deleted and delete them.
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->helper->isModuleEnabled() || !$this->helper->isAccountDeletionEnabled()) {
            return;
        }

        $cronSchedule = $this->collectionFactory
            ->create()
            ->addFieldToFilter('scheduled_at', ['lteq' => date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp())]);

        if (!$cronSchedule->getItems()) {
            return;
        }

        try {
            $this->registry->register('isSecureArea', true);
        } catch (RuntimeException $e) {
            // area is already set
        }

        foreach ($cronSchedule->getItems() as $item) {
            try {
                $this->process(
                    $this->customerRepository->getById($item->getData('customer_id')),
                    $item->getData('type')
                );

                $this->saveReason($item->getData('reason'));
                $item->getResource()->delete($item);
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }

    /**
     * Process data deletion or anonymization.
     *
     * @param CustomerInterface $customer
     * @param string $type
     *
     * @return void
     */
    protected function process(CustomerInterface $customer, string $type)
    {
        foreach ($this->processors as $processor) {
            if (!$processor instanceof DataDeleteInterface) {
                continue;
            }

            switch ($type) {
                case Data::SCHEDULE_TYPE_DELETE:
                    $processor->delete($customer);
                    break;

                case Data::SCHEDULE_TYPE_ANONYMIZE:
                    $processor->anonymize($customer);
                    break;
            }
        }
    }

    /**
     * Save reason why account was deleted or anonymized.
     *
     * @param string $reason
     * @throws Exception
     */
    public function saveReason($reason)
    {
        $model = $this->reasonFactory
            ->create()
            ->setData('reason', $reason);

        $model->getResource()->save($model);
    }
}