<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Privacy;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\EraseCustomerManagementInterface;
use Opengento\Gdpr\Controller\AbstractPrivacy;

/**
 * Action Index Delete
 */
class Delete extends AbstractPrivacy implements HttpGetActionInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $session;

    /**
     * @var \Opengento\Gdpr\Api\EraseCustomerManagementInterface
     */
    private $eraseCustomerManagement;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Opengento\Gdpr\Api\EraseCustomerManagementInterface $eraseCustomerManagement
     */
    public function __construct(
        Context $context,
        Session $session,
        EraseCustomerManagementInterface $eraseCustomerManagement
    ) {
        parent::__construct($context);
        $this->session = $session;
        $this->eraseCustomerManagement = $eraseCustomerManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->eraseCustomerManagement->exists((int) $this->session->getCustomerId())) {
            $this->messageManager->addErrorMessage(new Phrase('Your account is already being removed.'));
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setRefererOrBaseUrl();

            return $resultRedirect;
        }

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
