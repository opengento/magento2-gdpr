<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Adminhtml\Action;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Phrase;

class NewAction extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::gdpr_actions_execute';

    public function execute(): Page
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Opengento_Gdpr::gdpr_actions');
        $resultPage->getConfig()->getTitle()->set(new Phrase('Execute New Action'));
        $resultPage->addBreadcrumb(new Phrase('GDPR'), new Phrase('GDPR'));
        $resultPage->addBreadcrumb(new Phrase('Execute New Action'), new Phrase('Execute New Action'));

        return $resultPage;
    }
}
