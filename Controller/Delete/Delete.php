<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Delete;

use Opengento\Gdpr\Helper\AccountData;
use Opengento\Gdpr\Model\CronScheduleFactory;
use Opengento\Gdpr\Model\Config\Source\Schema;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\AuthenticationInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\Order\Config;
use Opengento\Gdpr\Helper\Data;

/**
 * Customer account delete action.
 * @refactor
 */
class Delete extends Action
{
    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var Config
     */
    protected $orderConfig;

    /**
     * @var AuthenticationInterface
     */
    protected $authentication;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var AccountData
     */
    protected $accountData;

    /**
     * @var CronScheduleFactory
     */
    protected $scheduleFactory;

    /**
     * Delete constructor.
     *
     * @param Context $context
     * @param Validator $formKeyValidator
     * @param CustomerRepositoryInterface $customerRepository
     * @param Config $orderConfig
     * @param AuthenticationInterface $authentication
     * @param DateTime $dateTime
     * @param Session $session
     * @param Data $helper
     * @param AccountData $accountData
     * @param CronScheduleFactory $scheduleFactory
     */
    public function __construct(
        Context $context,
        Validator $formKeyValidator,
        CustomerRepositoryInterface $customerRepository,
        Config $orderConfig,
        AuthenticationInterface $authentication,
        DateTime $dateTime,
        Session $session,
        Data $helper,
        AccountData $accountData,
        CronScheduleFactory $scheduleFactory
    ) {
        parent::__construct($context);

        $this->formKeyValidator = $formKeyValidator;
        $this->customerRepository = $customerRepository;
        $this->orderConfig = $orderConfig;
        $this->authentication = $authentication;
        $this->dateTime = $dateTime;
        $this->session = $session;
        $this->helper = $helper;
        $this->accountData = $accountData;
        $this->scheduleFactory = $scheduleFactory;
    }

    /**
     * Dispatch controller.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->session->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }

        if (
            !$this->helper->isModuleEnabled() ||
            !$this->helper->isAccountDeletionEnabled() ||
            $this->accountData->isAccountToBeDeleted()
        ) {
            $this->_forward('no_route');
        }

        return parent::dispatch($request);
    }

    /**
     * Execute controller.
     *
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\SessionException
     */
    public function execute()
    {
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
                    date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp() + $this->helper->getDeletionTime())
                )
                ->setData('customer_id', $customerId)
                ->setData('reason', $this->getRequest()->getPost('reason'));

            switch ($this->helper->getDeletionSchema()) {
                case Schema::DELETE:
                    $schedule->setData('type', Data::SCHEDULE_TYPE_DELETE);
                    break;

                case Schema::ANONYMIZE:
                    $schedule->setData('type', Data::SCHEDULE_TYPE_ANONYMIZE);
                    break;

                case Schema::DELETE_ANONYMIZE:
                    $schedule->setData(
                        'type',
                        $this->accountData->hasOrders() ? Data::SCHEDULE_TYPE_ANONYMIZE : Data::SCHEDULE_TYPE_DELETE
                    );
                    break;
            }

            $schedule->getResource()->save($schedule);

            $this->messageManager->addWarningMessage(__($this->helper->getSuccessMessage()));

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
