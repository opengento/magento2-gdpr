<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Framework\Registry;
use Opengento\Gdpr\Api\EraseCustomerManagementInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class EraseCommand
 */
final class EraseCommand extends Command
{
    /**#@+
     * Input Variables Names
     */
    private const INPUT_ARGUMENT_CUSTOMER = 'customer';
    /**#@-*/

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Opengento\Gdpr\Api\EraseCustomerManagementInterface
     */
    private $eraseCustomerManagement;

    /**
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\Registry $registry
     * @param \Opengento\Gdpr\Api\EraseCustomerManagementInterface $eraseCustomerManagement
     * @param string $name
     */
    public function __construct(
        State $appState,
        Registry $registry,
        EraseCustomerManagementInterface $eraseCustomerManagement,
        string $name = 'gdpr:customer:erase'
    ) {
        $this->appState = $appState;
        $this->registry = $registry;
        $this->eraseCustomerManagement = $eraseCustomerManagement;
        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Erase the customer\'s personal data.');
        $this->setDefinition([
            new InputArgument(
                self::INPUT_ARGUMENT_CUSTOMER,
                InputArgument::REQUIRED + InputArgument::IS_ARRAY,
                'Customer ID'
            )
        ]);
    }

    /**
     * @inheritdoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->appState->setAreaCode(Area::AREA_GLOBAL);
        $oldValue = $this->registry->registry('isSecureArea');
        $this->registry->register('isSecureArea', true, true);

        $returnCode = Cli::RETURN_SUCCESS;

        try {
            foreach ($input->getArgument(self::INPUT_ARGUMENT_CUSTOMER) as $customerId) {
                $output->writeln(
                    $this->eraseCustomer((int) $customerId)
                    ? '<info>Customer\'s ("' . $customerId . '") personal data has been erased.</info>'
                    : '<comment>Customer\'s ("' . $customerId . '") personal data is already being erased.</comment>'
                );
            }
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            $returnCode = Cli::RETURN_FAILURE;
        }

        $this->registry->register('isSecureArea', $oldValue, true);

        return $returnCode;
    }

    /**
     * Erase the customer data by its ID
     *
     * @param int $customerId
     * @return bool
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function eraseCustomer(int $customerId): bool
    {
        if ($this->eraseCustomerManagement->exists($customerId)) {
            $this->eraseCustomerManagement->process($this->eraseCustomerManagement->create($customerId));

            return true;
        }

        return false;
    }
}
