<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\ViewModel;

use Magento\Cms\Helper\Page as HelperPage;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Opengento\Gdpr\Model\Config;

/**
 * Class UrlsDataProvider
 */
final class UrlsDataProvider implements ArgumentInterface
{
    /**#@+
     * Routes Path Data
     */
    public const ROUTE_PATH_SETTINGS = 'customer/privacy/settings';
    public const ROUTE_PATH_DELETE = 'customer/privacy/delete';
    public const ROUTE_PATH_DELETE_POST = 'customer/privacy/deletepost';
    public const ROUTE_PATH_UNDO_DELETE = 'customer/privacy/undodelete';
    public const ROUTE_PATH_EXPORT = 'customer/privacy/export';
    /**#@-*/

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Cms\Helper\Page
     */
    private $helperPage;

    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Cms\Helper\Page $helperPage
     * @param \Opengento\Gdpr\Model\Config $config
     */
    public function __construct(
        UrlInterface $urlBuilder,
        HelperPage $helperPage,
        Config $config
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->helperPage = $helperPage;
        $this->config = $config;
    }

    /**
     * Retrieve the information page url
     *
     * @return string
     */
    public function getInformationPageUrl(): string
    {
        return (string) $this->helperPage->getPageUrl($this->config->getPrivacyInformationPageId());
    }

    /**
     * Retrieve the settings page url
     *
     * @return string
     */
    public function getSettingsPageUrl(): string
    {
        return $this->urlBuilder->getUrl(self::ROUTE_PATH_SETTINGS);
    }

    /**
     * Retrieve the delete page url
     *
     * @return string
     */
    public function getDeletePageUrl(): string
    {
        return $this->urlBuilder->getUrl(self::ROUTE_PATH_DELETE);
    }

    /**
     * Retrieve the delete action url
     *
     * @return string
     */
    public function getDeleteActionUrl(): string
    {
        return $this->urlBuilder->getUrl(self::ROUTE_PATH_DELETE_POST);
    }

    /**
     * Retrieve the undo delete action url
     *
     * @return string
     */
    public function getUndoDeleteActionUrl(): string
    {
        return $this->urlBuilder->getUrl(self::ROUTE_PATH_UNDO_DELETE);
    }

    /**
     * Retrieve the export action url
     *
     * @return string
     */
    public function getExportActionUrl(): string
    {
        return $this->urlBuilder->getUrl(self::ROUTE_PATH_EXPORT);
    }
}
