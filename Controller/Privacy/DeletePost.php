<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Privacy;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AuthenticationInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\Order\Config as orderConfig;
use Opengento\Gdpr\Controller\AbstractPrivacy;
use Opengento\Gdpr\Helper\AccountData;
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Model\CronScheduleFactory;

/**
 * Action Delete Delete
 */
class DeletePost extends AbstractPrivacy implements ActionInterface
{
    /**
     * @var Validator
     */
    private $formKeyValidator;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    private $orderConfig;

    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var \Opengento\Gdpr\Helper\AccountData
     */
    private $accountData;

    /**
     * @var CronScheduleFactory
     */
    private $scheduleFactory;

    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Customer\Model\AuthenticationInterface $authentication
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Customer\Model\Session $session
     * @param \Opengento\Gdpr\Helper\AccountData $accountData
     * @param \Opengento\Gdpr\Model\CronScheduleFactory $scheduleFactory
     * @param \Opengento\Gdpr\Model\Config $config
     */
    public function __construct(
        Context $context,
        Validator $formKeyValidator,
        CustomerRepositoryInterface $customerRepository,
        OrderConfig $orderConfig,
        AuthenticationInterface $authentication,
        DateTime $dateTime,
        Session $session,
        AccountData $accountData,
        CronScheduleFactory $scheduleFactory,
        Config $config
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerRepository = $customerRepository;
        $this->orderConfig = $orderConfig;
        $this->authentication = $authentication;
        $this->dateTime = $dateTime;
        $this->session = $session;
        $this->accountData = $accountData;
        $this->scheduleFactory = $scheduleFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->accountData->isAccountToBeDeleted()) {
            return $this->forwardNoRoute();
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $validFormKey = $this->formKeyValidator->validate($this->getRequest());
        if ($this->getRequest()->isPost() && !$validFormKey) {
            return $resultRedirect->setPath('privacy/settings');
        }

        $customerId = $this->session->getCustomerId();
        $currentCustomerDataObject = $this->getCustomerDataObject($customerId);

        try {
            $this->authenticate($currentCustomerDataObject);

            /** @var \Opengento\Gdpr\Model\CronSchedule $schedule */
            $schedule = $this->scheduleFactory->create()
                ->setData(
                    'scheduled_at',
                    date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp() + $this->config->getErasureTimeLapse())
                )
                ->setData('customer_id', $customerId)
                ->setData('reason', $this->getRequest()->getPost('reason'));

            $schedule->getResource()->save($schedule);

            $this->messageManager->addWarningMessage(__('Your account is processed to be deleted.'));

            return $resultRedirect->setPath('privacy/settings');
        } catch (InvalidEmailOrPasswordException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (UserLockedException $e) {
            $this->session->logout();
            $this->session->start();
            $this->messageManager
                ->addErrorMessage(__('You did not sign in correctly or your account is temporarily disabled.'));

            return $resultRedirect->setPath('customer/account/login');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong, please try again later!'));
        }

        return $resultRedirect->setPath('privacy/settings');
    }

    /**
     * Get customer data object
     *
     * @param int $customerId
     *
     * @return CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCustomerDataObject($customerId)
    {
        return $this->customerRepository->getById($customerId);
    }

    /**
     * Authenticate user.
     *
     * @param CustomerInterface $currentCustomerDataObject
     *
     * @return void
     * @throws InvalidEmailOrPasswordException
     * @throws \Magento\Framework\Exception\State\UserLockedException
     */
    private function authenticate(CustomerInterface $currentCustomerDataObject)
    {
        try {
            $this->authentication
                ->authenticate($currentCustomerDataObject->getId(), $this->getRequest()->getPost('password'));
        } catch (InvalidEmailOrPasswordException $e) {
            throw new InvalidEmailOrPasswordException(__('Password you typed does not match this account.'));
        }
    }
}
