<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Privacy;

use Magento\Framework\Controller\ResultFactory;
use Opengento\Gdpr\Controller\AbstractPrivacy;

class Settings extends AbstractPrivacy
{
    protected function executeAction()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
