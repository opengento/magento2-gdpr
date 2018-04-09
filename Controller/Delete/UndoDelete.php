<?php
/**
 * This file is part of the Flurrybox EnhancedPrivacy package.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Flurrybox EnhancedPrivacy
 * to newer versions in the future.
 *
 * @copyright Copyright (c) 2018 Flurrybox, Ltd. (https://flurrybox.com/)
 * @license   GNU General Public License ("GPL") v3.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flurrybox\EnhancedPrivacy\Controller\Delete;

use Exception;
use Flurrybox\EnhancedPrivacy\Helper\AccountData;
use Flurrybox\EnhancedPrivacy\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Flurrybox\EnhancedPrivacy\Model\ResourceModel\CronSchedule\CollectionFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Undo customer delete controller.
 */
class UndoDelete extends Action
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var AccountData
     */
    protected $accountData;

    /**
     * UndoDelete constructor.
     *
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param Session $session
     * @param Data $helper
     * @param AccountData $accountData
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        Session $session,
        Data $helper,
        AccountData $accountData
    ) {
        parent::__construct($context);

        $this->collectionFactory = $collectionFactory;
        $this->session = $session;
        $this->helper = $helper;
        $this->accountData = $accountData;
    }

    /**
     * Dispatch controller.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->session->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }

        if (
            !$this->helper->isModuleEnabled() ||
            !$this->helper->isAccountDeletionEnabled() ||
            !$this->accountData->isAccountToBeDeleted()
        ) {
            $this->_forward('no_route');
        }

        return parent::dispatch($request);
    }

    /**
     * Execute controller.
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            /** @var \Flurrybox\EnhancedPrivacy\Model\CronSchedule $model */
            $model = $this->collectionFactory->create()
                ->getItemByColumnValue('customer_id', $this->session->getId());

            $model->getResource()->delete($model);

            $this->messageManager->addSuccessMessage(__('You canceled your account deletion.'));
        } catch (Exception $e) {
            $this->messageManager->addWarningMessage(__('Something went wrong, please try again later!'));
        }

        return $resultRedirect->setPath('privacy/settings');
    }
}
