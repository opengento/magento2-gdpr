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
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Model\ExportEntity;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ExportCommand
 */
class ExportCommand extends Command
{
    /**#@+
     * Input Variables Names
     */
    private const INPUT_ARGUMENT_ENTITY_ID = 'entity_id';
    private const INPUT_ARGUMENT_ENTITY_TYPE = 'entity_type';
    private const INPUT_OPTION_FILENAME = 'filename';
    /**#@-*/

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var \Opengento\Gdpr\Api\ExportEntityManagementInterface
     */
    private $exportManagement;

    /**
     * @param \Magento\Framework\App\State $appState
     * @param \Opengento\Gdpr\Api\ExportEntityManagementInterface $exportManagement
     * @param string $name
     */
    public function __construct(
        State $appState,
        ExportEntityManagementInterface $exportManagement,
        string $name = 'gdpr:entity:export'
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

        $this->setDescription('Export the entity\'s related data.');
        $this->setDefinition([
            new InputArgument(
                self::INPUT_ARGUMENT_ENTITY_TYPE,
                InputArgument::REQUIRED,
                'Entity Type'
            ),
            new InputArgument(
                self::INPUT_ARGUMENT_ENTITY_ID,
                InputArgument::REQUIRED + InputArgument::IS_ARRAY,
                'Entity ID'
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
        $entityIds = $input->getArgument(self::INPUT_ARGUMENT_ENTITY_ID);
        $entityType = $input->getArgument(self::INPUT_ARGUMENT_ENTITY_TYPE);
        $fileName = $input->getOption(self::INPUT_OPTION_FILENAME);

        try {
            foreach ($entityIds as $entityId) {
                $out = $this->exportManagement->export(
                    new ExportEntity((int) $entityId, $entityType, $fileName . '_' . $entityId)
                );
                $output->writeln('<info>Entity\'s related data have been exported to: ' . $out . '.</info>');
            }
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            $resultCode = Cli::RETURN_FAILURE;
        }

        return $resultCode;
    }
}
