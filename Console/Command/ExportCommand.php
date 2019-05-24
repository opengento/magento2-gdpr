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
use Opengento\Gdpr\Api\ExportInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ExportCommand
 * @package Opengento\Gdpr\Console\Command
 */
final class ExportCommand extends Command
{
    /**#@+
     * Input Variables Names
     */
    private const INPUT_ARGUMENT_CUSTOMER = 'customer';
    private const INPUT_OPTION_FILENAME = 'filename';
    /**#@-*/

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var \Opengento\Gdpr\Api\ExportInterface
     */
    private $exportManagement;

    /**
     * @param \Magento\Framework\App\State $appState
     * @param \Opengento\Gdpr\Api\ExportInterface $exportManagement
     * @param string $name
     */
    public function __construct(
        State $appState,
        ExportInterface $exportManagement,
        string $name = 'gdpr:customer:export'
    ) {
        $this->appState = $appState;
        $this->exportManagement = $exportManagement;
        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Export the customer\'s personal data.');
        $this->setDefinition([
            new InputArgument(
                self::INPUT_ARGUMENT_CUSTOMER,
                InputArgument::REQUIRED + InputArgument::IS_ARRAY,
                'Customer ID'
            ),
            new InputOption(
                self::INPUT_OPTION_FILENAME,
                '-f',
                InputOption::VALUE_OPTIONAL,
                'Export file name',
                'personal_data'
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

        $resultCode = Cli::RETURN_SUCCESS;
        $customerIds = $input->getArgument(self::INPUT_ARGUMENT_CUSTOMER);
        $fileName = $input->getOption(self::INPUT_OPTION_FILENAME);

        try {
            foreach ($customerIds as $customerId) {
                $fileName = $this->exportManagement->exportToFile((int) $customerId, $fileName . '_' . $customerId);
                $output->writeln('<info>Customer\'s personal data have been exported to: ' . $fileName . '.</info>');
            }
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            $resultCode = Cli::RETURN_FAILURE;
        }

        return $resultCode;
    }
}
