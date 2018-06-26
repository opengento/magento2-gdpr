<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Opengento\Gdpr\Service\ExportManagement;
use Opengento\Gdpr\Service\ExportStrategy;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ExportCommand
 * @package Opengento\Gdpr\Console\Command
 */
class ExportCommand extends Command
{
    /**#@+
     * Input Variables Names
     */
    const INPUT_ARGUMENT_CUSTOMER = 'customer';
    const INPUT_OPTION_FILENAME = 'filename';
    /**#@-*/

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var \Opengento\Gdpr\Service\ExportManagement
     */
    private $exportManagement;

    /**
     * @var \Opengento\Gdpr\Service\ExportStrategy
     */
    private $exportStrategy;

    /**
     * @param \Magento\Framework\App\State $appState
     * @param \Opengento\Gdpr\Service\ExportManagement $exportManagement
     * @param \Opengento\Gdpr\Service\ExportStrategy $exportStrategy
     * @param null|string $name
     */
    public function __construct(
        State $appState,
        ExportManagement $exportManagement,
        ExportStrategy $exportStrategy,
        ?string $name = null
    ) {
        $this->appState = $appState;
        $this->exportManagement = $exportManagement;
        $this->exportStrategy = $exportStrategy;
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('gdpr:customer:export');
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
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->appState->setAreaCode(Area::AREA_GLOBAL);

        $resultCode = Cli::RETURN_SUCCESS;
        $customerIds = $input->getArgument(self::INPUT_ARGUMENT_CUSTOMER);
        $fileName = $input->getOption(self::INPUT_OPTION_FILENAME);

        try {
            foreach ($customerIds as $customerId) {
                $personalData = $this->exportManagement->execute((int) $customerId);
                $fileName = $this->exportStrategy->saveData($fileName . '_' . $customerId, $personalData);
                $output->writeln('<info>Customer\'s personal data have been exported to: ' . $fileName . '.</info>');
            }
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            $resultCode = Cli::RETURN_FAILURE;
        }

        return $resultCode;
    }
}
