<?php

namespace OroInc\ChainCommandBundle\Tests\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use OroInc\ChainCommandBundle\EventListener\CommandListener;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use OroInc\BarBundle\Command\HiCommand;
use OroInc\FooBundle\Command\HelloCommand;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

class CommandListenerTest extends KernelTestCase {
    
    private $event;
    private $listener;
    private $logger;
    private $output;
    private $input;
    private $application;

    public function setUp()
    {
        $this->logger = $this->createMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $this->logger
            //->expects($this->once())
            ->method('info');

        $this->output = $this->createMock('Symfony\Component\Console\Output\OutputInterface');
        $this->input = $this->createMock('Symfony\Component\Console\Input\InputInterface');
        $this->listener = new CommandListener($this->logger);
        
        $kernel = $this->createKernel();
        $kernel->boot();
        $this->application = new Application($kernel);
        
        $command = new Command("not:exsist");
        $this->event = new ConsoleCommandEvent($command,$this->input,$this->output);
    }
    
    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    public function testRunCommandsChain()
    {
        $this->application->add(new HiCommand());
        $this->application->add(new HelloCommand());
             
        $GLOBALS['application'] = $this->application;
        
        $commands = array( 'foo:hello','bar:hi');
        $this->invokeMethod($this->listener, 'runCommandsChain', array($commands, $this->output));
    }
    
    /**
     * @expectedException Symfony\Component\Console\Exception\CommandNotFoundException
     */
    public function testRunCommandsChainWhenCommandDontExsist()
    {
        $this->application->add(new HiCommand());
        $this->application->add(new HelloCommand());
             
        $GLOBALS['application'] = $this->application;
        
        $commands = array( 'foo:Nothing','bar:DontExsist');
       
        $this->invokeMethod($this->listener, 'runCommandsChain', array($commands, $this->output));
    }
    
    public function testCheckChainElements()
    {
        $commands = array( 'foo:hello','bar:hi');
        $commandName = 'bar:DontExsist';
             
        $this->invokeMethod($this->listener,'checkChainElements',array($commands,$commandName,$this->output));
    }
    
    public function testOnConsoleCommand()
    {
     $this->listener->onConsoleCommand($this->event);
    }
}