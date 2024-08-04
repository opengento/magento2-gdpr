<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Privacy;

use Magento\Customer\Controller\AccountInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;
use Opengento\Gdpr\Controller\AbstractAction;

class Settings extends AbstractAction implements HttpGetActionInterface, AccountInterface
{
    protected function executeAction(): Page
    {
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
