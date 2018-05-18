<?php

namespace Prodigious\MultisiteBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class SiteListCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('site:list')
            ->setDescription('List of sites')
        ;
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @return null|int null or 0 if everything went fine, or an error code
     *
     * @throws LogicException When this abstract method is not implemented
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);

        $sm = $this->getContainer()->get('multisite.manager');

        $sites = $sm->list();

        $table->setHeaders(['#', 'Name', 'Host', 'Locale', 'Public Directory', 'Status']);

        $rows = [];

        if(!empty($sites)) {
            $count = 1;
            foreach ($sites as $key => $site) {
                foreach ($site as $v) {
                    $rows[] = [$count, $key, $v['host'], $v['locale'], '/web/public/'.$key, $v['active'] ? 'Enabled' : 'Disabled'];
                    $count++;
                }
            }
        }

        $table->setRows($rows);
        $table->setStyle('borderless');
        $table->render();
    }

}
