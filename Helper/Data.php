<?php


namespace Opengento\Gdpr\Helper;

use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\ScopeInterface;
use Opengento\Gdpr\Model\Config\PrivacyMessage;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {


    private const CONFIG_PATH_GENERAL_INFORMATION_PAGE = 'gdpr/general/page_id';
    private const CONFIG_PATH_GENERAL_NEWSLETTER_CHECKBOX_LABEL = 'gdpr/general/newsletter_checkbox_label';

    private $_helperPage;


    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Cms\Helper\Page $page
    )
    {
        $this->_helperPage = $page;
        parent::__construct($context);
    }

    public function getInformationPageUrl() {

        return $this->_helperPage->getPageUrl((string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_GENERAL_INFORMATION_PAGE,
            ScopeInterface::SCOPE_STORE
        )) ?? '#';
    }

    public function getNewsletterCheckboxLabelConfig() {

        return $this->scopeConfig->getValue(self::CONFIG_PATH_GENERAL_NEWSLETTER_CHECKBOX_LABEL,ScopeInterface::SCOPE_STORE);
    }

    public function getCheckboxLabel() {
        return __($this->getNewsletterCheckboxLabelConfig(), $this->getInformationPageUrl());
    }



}
