<?php
namespace Netdudes\Branchio\Tool\Commands;

use Netdudes\Branchio\Sites;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshCommand extends Command
{

    /**
     * @var Sites
     */
    private $sites;

    /**
     * @param Sites $sites
     */
    public function __construct(Sites $sites)
    {
        $this->sites = $sites;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('refresh')
            ->setDescription('Refreshes the installed site for a branch')
            ->addArgument(
                'branch',
                InputArgument::REQUIRED,
                'Branch of the site to refresh'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $branch = $input->getArgument('branch');
        $this->sites->updateSite($branch);
    }
}