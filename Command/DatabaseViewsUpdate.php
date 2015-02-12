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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Ellis\Oxid\Bundle\FrameworkBundle\OxidAwareInterface;

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
            ->setName('database:views:update')
            ->setDescription('Update OXID database views')
            ->setHelp('The <info>%command.name%</info> command updates OXID database views')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $handler = $container->get('oxid.database_metadata_handler');

        $output->writeln(sprintf(
            'Updating views for <info>%s</info> database',
            $container->getParameter('oxid.database.name')
        ));

        if (!$handler->updateViews()) {
            $output->writeLn('Failed updating views');
            return;
        }

        if ($output->isVerbose()) {
            $output->writeln('  Done updating database views');
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
