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
interface ConsentInterface extends ExtensibleDataInterface
{
    /**
     * Constants for fields keys
     */
    public const ID = 'consent_id';
    public const AGREEMENT_IDENTIFIER = 'agreement_identifier';
    public const IS_APPROVED = 'is_approved';
    public const IS_OUTDATED = 'is_outdated';  //todo when it's outdated or the agreement has been modified
    public const SUBMITTED_AT = 'submitted_at';

    public function getConsentId(): int;

    public function setConsentId(int $consentId): ConsentInterface;

    public function getAgreementIdentifier(): string;

    public function setAgreementIdentifier(string $agreementIdentifier): ConsentInterface;

    public function isApproved(): bool;

    public function setIsApproved(bool $isApproved): ConsentInterface;

    public function isOutDated(): bool;

    public function setIsOutDated(bool $isOutDated): ConsentInterface;

    public function getSubmittedAt(): string;

    public function setSubmittedAt(string $submittedAt): ConsentInterface;
}
