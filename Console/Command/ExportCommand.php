<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Console\Command;

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Framework\Exception\LocalizedException;
use Opengento\Gdpr\Api\ActionInterface;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Model\Action\ArgumentReader;
use Opengento\Gdpr\Model\Action\ContextBuilder;
use Opengento\Gdpr\Model\Action\Export\ArgumentReader as ExportArgumentReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCommand extends Command
{
    private const INPUT_ARGUMENT_ENTITY_ID = 'entity_id';
    private const INPUT_ARGUMENT_ENTITY_TYPE = 'entity_type';
    private const INPUT_OPTION_FILENAME = 'filename';

    private State $appState;

    private ActionInterface $action;

    private ContextBuilder $actionContextBuilder;

    public function __construct(
        State $appState,
        ActionInterface $action,
        ContextBuilder $actionContextBuilder,
        string $name = 'gdpr:entity:export'
    ) {
        $this->appState = $appState;
        $this->action = $action;
        $this->actionContextBuilder = $actionContextBuilder;
        parent::__construct($name);
    }

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
     * @throws LocalizedException
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
                $this->actionContextBuilder->setParameters([
                    ArgumentReader::ENTITY_ID => $entityId,
                    ArgumentReader::ENTITY_TYPE => $entityType,
                    ExportArgumentReader::EXPORT_FILE_NAME => $fileName . '_' . $entityId
                ]);
                $result = $this->action->execute($this->actionContextBuilder->create())->getResult();
                /** @var ExportEntityInterface $exportEntity */
                $exportEntity = $result[ExportArgumentReader::EXPORT_ENTITY];

                $output->writeln(
                    '<info>Entity\'s related data have been exported to: ' . $exportEntity->getFilePath() . '.</info>'
                );
            }
        } catch (Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            $resultCode = Cli::RETURN_FAILURE;
        }

        return $resultCode;
    }
}
