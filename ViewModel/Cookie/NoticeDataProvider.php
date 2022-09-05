<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\ViewModel\Cookie;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Opengento\Gdpr\Model\Config\PrivacyMessage;

final class NoticeDataProvider implements ArgumentInterface
{
    private PrivacyMessage $privacyMessage;

    public function __construct(PrivacyMessage $privacyMessage)
    {
        $this->privacyMessage = $privacyMessage;
    }

    public function getTemplate(string $defaultTemplate, string $customTemplate): string
    {
        return $this->privacyMessage->isEnabled() ? $customTemplate : $defaultTemplate;
    }

    public function getLearnMoreUrl(): ?string
    {
        return $this->privacyMessage->getLearnMoreUrl();
    }

    public function getNoticeHtml(): string
    {
        return $this->privacyMessage->getDisclosureInformationHtml();
    }
}
