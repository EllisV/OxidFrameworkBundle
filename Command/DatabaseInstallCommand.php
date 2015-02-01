<?php

/*
 * This file is part of the OXID package.
 *
 * (c) Eligijus Vitkauskas <eligijusvitkauskas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ellis\Oxid\Bundle\FrameworkBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Install OXID database.
 */
class DatabaseInstallCommand extends Command implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('database:install')
            ->setDescription('Install OXID database')
            ->setHelp('The <info>%command.name%</info> command creates OXID database and tables for it')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $installer = $this->getContainer()->get('oxid.database_installer');
        if ($installer->isInstalled()) {
            $output->isVerbose() and $output->writeln('  Database is already installed');
            return;
        }

        $databaseName = $this->getContainer()->getParameter('oxid.database.name');
        $output->writeln(sprintf('Creating <info>%s</info> database with OXID tables', $databaseName));
        $installer->install();

        $dialog = $this->getHelper('dialog');
        if ($dialog->askConfirmation($output, '<question>Install demodata?</question>', true)) {
            $output->isVerbose() and $output->writeln('  Installing demodata');
            $installer->installDemodata();
            $output->isVerbose() and $output->writeln('  Finished installing demodata');
        }
    }

    /**
     * Set Container
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Get Container
     *
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }
}
