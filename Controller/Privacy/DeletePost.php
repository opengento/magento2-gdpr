<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Privacy;

use Magento\Customer\Model\AuthenticationInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\Phrase;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Opengento\Gdpr\Controller\AbstractPrivacy;
use Opengento\Gdpr\Helper\AccountData;
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Model\CronSchedule;
use Opengento\Gdpr\Model\CronScheduleFactory;
use Opengento\Gdpr\Model\ResourceModel\CronSchedule as CronScheduleResource;

/**
 * Action Delete Delete
 */
class DeletePost extends AbstractPrivacy implements ActionInterface
{
    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $formKeyValidator;

    /**
     * @var \Magento\Customer\Model\AuthenticationInterface
     */
    private $authentication;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $session;

    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @var \Opengento\Gdpr\Model\CronScheduleFactory
     */
    private $scheduleFactory;

    /**
     * @var \Opengento\Gdpr\Model\ResourceModel\CronSchedule
     */
    private $scheduleResource;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \Opengento\Gdpr\Helper\AccountData
     */
    private $accountData;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Customer\Model\AuthenticationInterface $authentication
     * @param \Magento\Customer\Model\Session $session
     * @param \Opengento\Gdpr\Model\Config $config
     * @param \Opengento\Gdpr\Model\CronScheduleFactory $scheduleFactory
     * @param \Opengento\Gdpr\Model\ResourceModel\CronSchedule $scheduleResource
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Opengento\Gdpr\Helper\AccountData $accountData
     */
    public function __construct(
        Context $context,
        Validator $formKeyValidator,
        AuthenticationInterface $authentication,
        Session $session,
        Config $config,
        CronScheduleFactory $scheduleFactory,
        CronScheduleResource $scheduleResource,
        DateTime $dateTime,
        AccountData $accountData
    ) {
        parent::__construct($context);
        $this->formKeyValidator = $formKeyValidator;
        $this->authentication = $authentication;
        $this->session = $session;
        $this->config = $config;
        $this->scheduleFactory = $scheduleFactory;
        $this->scheduleResource = $scheduleResource;
        $this->dateTime = $dateTime;
        $this->accountData = $accountData;
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
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('privacy/settings');

        if ($this->getRequest()->getParams() && $this->formKeyValidator->validate($this->getRequest())) {
            try {
                $customerId = (int) $this->session->getCustomerId();
                $this->authentication->authenticate($customerId, $this->getRequest()->getParam('password'));
                $this->scheduleResource->save($this->createCronSchedule($customerId));
                $this->messageManager->addWarningMessage(new Phrase('Your account is processed to be deleted.'));
            } catch (InvalidEmailOrPasswordException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (UserLockedException $e) {
                $this->session->logout();
                $this->session->start();
                $this->messageManager->addErrorMessage(
                    new Phrase('You did not sign in correctly or your account is temporarily disabled.')
                );
                $resultRedirect->setPath('customer/account/login');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(new Phrase('Something went wrong, please try again later!'));
            }
        }

        return $resultRedirect;
    }

    /**
     * Create a new cron schedule object
     *
     * @param int $customerId
     * @return \Opengento\Gdpr\Model\CronSchedule
     */
    private function createCronSchedule(int $customerId): CronSchedule
    {
        return $this->scheduleFactory->create(['data' => [
            'scheduled_at' => date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp() + $this->config->getErasureTimeLapse()),
            'customer_id' => $customerId,
            'reason' => $this->getRequest()->getParam('reason')
        ]]);
    }
}
