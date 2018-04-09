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

namespace Flurrybox\EnhancedPrivacy\Observers;

use Flurrybox\EnhancedPrivacy\Model\ResourceModel\CronSchedule\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

/**
 * Customer logout observer.
 */
class CustomerLogout implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var RedirectFactory
     */
    protected $redirect;

    /**
     * @var RedirectInterface
     */
    protected $redirectInterface;

    /**
     * @var CollectionFactory
     */
    protected $scheduleCollectionFactory;

    /**
     * CustomerLogout constructor.
     *
     * @param LoggerInterface $logger
     * @param Context $context
     * @param Session $session
     * @param RedirectFactory $redirect
     * @param RedirectInterface $redirectInterface
     * @param CollectionFactory $scheduleCollectionFactory
     */
    public function __construct(
        LoggerInterface $logger,
        Context $context,
        Session $session,
        RedirectFactory $redirect,
        RedirectInterface $redirectInterface,
        CollectionFactory $scheduleCollectionFactory
    ) {
        $this->logger = $logger;
        $this->context = $context;
        $this->session = $session;
        $this->redirect = $redirect;
        $this->redirectInterface = $redirectInterface;
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
    }

    /**
     * Execute observer.
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $loggedIn = $this->context->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);

        if (!$loggedIn) {
            return;
        }

        $customer = $this->session->getCustomerData();

        if (!($isAnonymized = $customer->getCustomAttribute('is_anonymized'))) {
            return;
        }

        if ($isAnonymized->getValue()) {
            $this->session
                ->logout()
                ->setBeforeAuthUrl($this->redirectInterface->getRefererUrl());
        }
    }
}
