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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;
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
    private const INPUT_ARGUMENT_ENTITY_ID = 'entity_id';
    private const INPUT_ARGUMENT_ENTITY_TYPE = 'entity_type';
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
     * @var \Opengento\Gdpr\Api\EraseEntityManagementInterface
     */
    private $eraseEntityManagement;

    /**
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\Registry $registry
     * @param \Opengento\Gdpr\Api\EraseEntityManagementInterface $eraseEntityManagement
     * @param string $name
     */
    public function __construct(
        State $appState,
        Registry $registry,
        EraseEntityManagementInterface $eraseEntityManagement,
        string $name = 'gdpr:entity:erase'
    ) {
        $this->appState = $appState;
        $this->registry = $registry;
        $this->eraseEntityManagement = $eraseEntityManagement;
        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Erase the entity\'s related data.');
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

        $entityIds = $input->getArgument(self::INPUT_ARGUMENT_ENTITY_ID);
        $entityType = $input->getArgument(self::INPUT_ARGUMENT_ENTITY_TYPE);

        try {
            foreach ($entityIds as $entityId) {
                $this->eraseEntityManagement->process(
                    $this->eraseEntityManagement->create((int) $entityId, $entityType)
                );
                $output->writeln(
                    '<info>Entity\'s (' . $entityType . ') with ID "' . $entityId . '" has been erased.</info>'
                );
            }
        } catch (LocalizedException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            $returnCode = Cli::RETURN_FAILURE;
        }

        $this->registry->register('isSecureArea', $oldValue, true);

        return $returnCode;
    }
}
