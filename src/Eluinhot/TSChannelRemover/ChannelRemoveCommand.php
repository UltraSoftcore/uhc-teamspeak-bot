<?php
namespace Eluinhot\TSChannelRemover;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChannelRemoveCommand extends Command {

    protected function configure()
    {
        $this->setName('run_script');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //TODO everything
    }
} 