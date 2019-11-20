<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * @api
 */
interface AgreementInterface extends ExtensibleDataInterface
{
    /**
     * Constants for fields keys
     */
    public const ID = 'agreement_id';
    public const IDENTIFIER = 'identifier';
    public const IS_ACTIVE = 'is_active';
    public const IS_MANDATORY = 'is_mandatory';
    public const TITLE = 'title';
    public const LABEL = 'label';
    public const CONTENT = 'content';

    public function getAgreementId(): int;

    public function setAgreementId(int $agreementId): AgreementInterface;

    public function getIdentifier(): string;

    public function setIdentifier(string $identifier): AgreementInterface;

    public function getIsActive(): bool;

    public function setIsActive(bool $isActive): AgreementInterface;

    public function getIsMandatory(): bool;

    public function setIsMandatory(bool $isMandatory): AgreementInterface;

    public function getTitle(): string;

    public function setTitle(string $title): AgreementInterface;

    public function getLabel(): string;

    public function setLabel(string $label): AgreementInterface;

    public function getContent(): string;

    public function setContent(string $content): AgreementInterface;
}
