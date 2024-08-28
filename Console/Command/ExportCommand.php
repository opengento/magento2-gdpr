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
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCommand extends Command
{
    private const INPUT_ARGUMENT_ENTITY_ID = 'entity_id';
    private const INPUT_ARGUMENT_ENTITY_TYPE = 'entity_type';

    public function __construct(
        private State $appState,
        private ExportEntityRepositoryInterface $exportEntityRepository,
        private ExportEntityManagementInterface $exportEntityManagement,
        string $name = 'gdpr:entity:export'
    ) {
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

        $progressBar = new ProgressBar($output, count($entityIds));
        $progressBar->start();

        $files = [];

        try {
            foreach ($entityIds as $entityId) {
                $exportEntity = $this->fetchEntity((int)$entityId, $entityType);
                $this->exportEntityManagement->export($exportEntity);
                $files[] = $exportEntity->getFilePath();
                $progressBar->advance();
            }
            $progressBar->finish();
            $output->writeln('');
            $output->writeln('<info>Entities data have been exported to:</info>');
            foreach ($files as $file) {
                $output->writeln($file);
            }
        } catch (Exception $e) {
            $output->writeln('');
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            $resultCode = Cli::RETURN_FAILURE;
        }

        return $resultCode;
    }

    /**
     * @throws AlreadyExistsException
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    private function fetchEntity(int $entityId, string $entityType): ExportEntityInterface
    {
        try {
            return $this->exportEntityRepository->getByEntity($entityId, $entityType);
        } catch (NoSuchEntityException) {
            return $this->exportEntityManagement->create($entityId, $entityType);
        }
    }
}
