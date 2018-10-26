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
use Magento\Framework\Registry;
use Opengento\Gdpr\Service\ErasureStrategy;
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
     * @var \Opengento\Gdpr\Service\ErasureStrategy
     */
    private $erasureStrategy;

    /**
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\Registry $registry
     * @param \Opengento\Gdpr\Service\ErasureStrategy $erasureStrategy
     * @param null|string $name
     */
    public function __construct(
        State $appState,
        Registry $registry,
        ErasureStrategy $erasureStrategy,
        string $name = 'gdpr:customer:erase'
    ) {
        $this->appState = $appState;
        $this->registry = $registry;
        $this->erasureStrategy = $erasureStrategy;
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->appState->setAreaCode(Area::AREA_GLOBAL);
        $oldValue = $this->registry->registry('isSecureArea');
        $this->registry->register('isSecureArea', true, true);

        $returnCode = Cli::RETURN_SUCCESS;

        try {
            foreach ($input->getArgument(self::INPUT_ARGUMENT_CUSTOMER) as $customerId) {
                $this->erasureStrategy->execute((int) $customerId);
                $output->writeln('<info>Customer\'s ("' . $customerId . '") personal data has been erased.</info>');
            }
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            $returnCode = Cli::RETURN_FAILURE;
        }

        $this->registry->register('isSecureArea', $oldValue, true);

        return $returnCode;
    }
}
