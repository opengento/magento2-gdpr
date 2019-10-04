<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action\PerformedBy;

use Opengento\Gdpr\Model\Action\PerformedByInterface;

final class FrontUser implements PerformedByInterface
{
    private const PERFORMED_BY = 'Unknown';

    /**
     * @var Customer
     */
    private $performedByCustomer;

    /**
     * @var Guest
     */
    private $performedByGuest;

    public function __construct(
        Customer $performedByCustomer,
        Guest $performedByGuest
    ) {
        $this->performedByCustomer = $performedByCustomer;
        $this->performedByGuest = $performedByGuest;
    }

    public function get(): string
    {
        return $this->performedByCustomer->get() ?: $this->performedByGuest->get() ?: self::PERFORMED_BY;
    }
}
