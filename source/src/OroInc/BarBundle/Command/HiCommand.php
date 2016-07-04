<?php

namespace OroInc\BarBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for test.
 */
class HiCommand extends ContainerAwareCommand
{
    
    protected function configure()
    {
        $this
            ->setName('bar:hi')
            ->setDescription('Test command')
        ;
    }
    
    /**
     * Run command and print 'Hi from Bar!' on console.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hi from Bar!');
    }
}