<?php

namespace OroInc\FooBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for test.
 */
class HelloCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('foo:hello')
            ->setDescription('Test command')
        ;
    }
    
    /**
     * Run command and print 'Hello from Foo!' on console.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hello from Foo!');
    }
}