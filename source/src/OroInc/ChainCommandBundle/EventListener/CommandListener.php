<?php

namespace OroInc\ChainCommandBundle\EventListener;

use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

/**
 * Listner that run when execute ConsoleCommand, 
 * and implements command chaining functionality.
 */
class CommandListener {
    
    private $logger;
    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    /**
     * Called up when run ConsoleCommand.
     *
     * @param ConsoleCommandEvent $event
     */
    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
       $command = $event->getCommand();

       $configValues = Yaml::parse(file_get_contents( __DIR__ . '/../Resources/config/config.yml'));
       
       foreach ( $configValues['ChainCommand'] as $chain) {
           if (is_array($chain)) {
               if($this->checkChainElements($chain["chain"],$command->getName(),$event->getOutput())) 
               {
                   exit();
               }
           }
       }
    }
       
    /**
     * Check that command belongs to chain, 
     * and support chain if yes.
     *
     * @param array $chain          Array of command in chain
     * @param string $commandName   Name on induced command
     * @param OutputInterface $output
     * 
     * @return bool Return true when induced command belongs to some chain
     */
   private function checkChainElements(array $chain, string $commandName, OutputInterface $output) 
   {
       $chainPosition = 0;
       
       foreach ($chain as $chainElement) {
           if ($chainElement == $commandName) {
               if ($chainPosition == 0) {
                   $this->logger->info("$commandName is a master command of a command chain that has registered member commands");
                   
                   for($x = 1; $x < count($chain); $x++) {
                       $this->logger->info("$chain[$x] registered as a member of $commandName command chain");
                   }
                   
                   $this->runCommandsChain($chain,$output);
               } else {
                   $errorInformation = "Error: $commandName command is a member of $chain[0] command chain and cannot be executed on its own.";
                   $this->logger->error($errorInformation);
               }
               return true;
            }
            $chainPosition++;
        }
        
        return false;
    }
   
   /**
    * Run commands in chain    
    * 
    *
    * @param array $chain          Array of command in chain
    * @param OutputInterface $output
    */
   private function runCommandsChain(array $commands, OutputInterface $output) 
   {
       $element = 0;
       
       foreach ( $commands as $commandName) {
           if ($element == 0) {
              $this->logger->info("Executing $commandName command itself first:");
           } else if ($element == 1){
              $this->logger->info("Executing $commands[0] chain members:");
           }
           
           $command = $GLOBALS['application']->find($commandName);
           $arguments = array();
           
           $bufferedOutput = new BufferedOutput();
           $command->run(new ArrayInput($arguments),$bufferedOutput);
           
           $commandOut = $bufferedOutput->fetch();
           
           $output->write($commandOut);
           $this->logger->info($commandOut);
           
           $element++;
       }
       
       $this->logger->info("Execution of $commands[0] chain completed.");
   }
}