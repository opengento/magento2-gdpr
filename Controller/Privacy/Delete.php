<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Privacy;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Controller\AbstractPrivacy;
use Opengento\Gdpr\Helper\Data;

/**
 * Action Index Delete
 */
class Delete extends AbstractPrivacy implements ActionInterface
{
    /**
     * @var \Opengento\Gdpr\Helper\Data
     */
    private $helperData;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Opengento\Gdpr\Helper\Data $helperData
     */
    public function __construct(
        Context $context,
        Data $helperData
    ) {
        parent::__construct($context);
        $this->helperData = $helperData;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->helperData->isAccountToBeDeleted()) {
            $this->messageManager->addErrorMessage(new Phrase('Your account is already currently being removed.'));
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('customer/privacy/settings');
        }

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
