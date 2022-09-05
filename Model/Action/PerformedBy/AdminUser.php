<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action\PerformedBy;

use Magento\Backend\Model\Auth\Session;
use Opengento\Gdpr\Model\Action\PerformedByInterface;

final class AdminUser implements PerformedByInterface
{
    private const PERFORMED_BY = 'Admin: ';

    /**
     * @var Session
     */
    private Session $authSession;

    private string $attributeName;

    public function __construct(
        Session $authSession,
        string $attributeName = 'username'
    ) {
        $this->authSession = $authSession;
        $this->attributeName = $attributeName;
    }

    public function get(): string
    {
        return self::PERFORMED_BY . $this->resolveUserName();
    }

    private function resolveUserName(): string
    {
        return $this->authSession->getUser() ? $this->authSession->getUser()->getData($this->attributeName) : 'Unknown';
    }
}
