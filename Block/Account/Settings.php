<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Block\Account;

use Opengento\Gdpr\Block\Account;
use Opengento\Gdpr\Model\Config\Source\Schema;

/**
 * Customer privacy settings block.
 */
class Settings extends Account
{
    /**
     * Get delete page url.
     *
     * @return string
     */
    public function getDeletingPageUrl()
    {
        return $this->getUrl('privacy/delete');
    }

    /**
     * Get undo delete page url.
     *
     * @return string
     */
    public function getUndoDeletePageURL()
    {
        return $this->getUrl('privacy/delete/undodelete');
    }

    /**
     * Get Anonymise page url.
     */
    public function getAnonymisePageURL()
    {
        return $this->getUrl('privacy/anonymise');
    }

    /**
     * Get export controller.
     *
     * @return string
     */
    public function getExportAction()
    {
        return $this->getUrl('privacy/export/export');
    }

    /**
     * Get export page url.
     *
     * @return string
     */
    public function getExportPageUrl()
    {
        return $this->getUrl('privacy/export');
    }

    /**
     * Check if account should be anonymized instead of deleted.
     *
     * @return bool
     */
    public function shouldAnonymize()
    {
        return ($this->helper->getDeletionSchema() === Schema::ANONYMIZE || $this->hasOrders()) &&
            $this->helper->isAnonymizationMessageEnabled();
    }
}
