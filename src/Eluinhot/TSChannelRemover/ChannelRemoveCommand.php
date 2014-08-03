<?php
namespace Eluinhot\TSChannelRemover;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TeamSpeak3_Node_Server;

class ChannelRemoveCommand extends Command {

    private $server;

    public function __construct(TeamSpeak3_Node_Server $server)
    {
        $this->server = $server;
        parent::__construct('run_script');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //TODO everything
    }
} 