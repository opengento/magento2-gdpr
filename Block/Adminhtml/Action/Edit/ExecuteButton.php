<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Block\Adminhtml\Action\Edit;

use Magento\Framework\Phrase;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

final class ExecuteButton implements ButtonProviderInterface
{
    public function getButtonData(): array
    {
        return [
            'label' => new Phrase('Execute Action'),
            'class' => 'primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'on_click' => 'location.reload();',
            'sort_order' => 30,
            'aclResource' => 'Opengento_Gdpr::gdpr_actions_execute',
        ];
    }
}
