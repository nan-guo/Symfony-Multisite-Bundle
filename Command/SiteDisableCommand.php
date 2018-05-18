<?php

namespace Prodigious\MultisiteBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Helper\Table;

class SiteDisableCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('site:disable')
            ->setDescription('Disable a site')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Which site do you want to disable?', null)
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
        $name = $input->getOption('name');

        if(is_null($name)) {
            $output->writeln('<error>Please enter the name of site you want to disable. Ex: site:disable --name=demo</error>');
            return;
        }

        $sm = $this->getContainer()->get('multisite.manager');

        $helper = $this->getHelper('question');

        $table = new Table($output);

        $sites = $sm->list();

        if(isset($sites[$name])) {
            $hosts = [];
            foreach ($sites[$name] as $site) {
                $hosts[]= $site['host'];
            }

            $host = $hosts[0];

            if(count($sites[$name]) > 1) {
                $table->setHeaders(['#', 'Name', 'Host', 'Locale', 'Public Directory', 'Status']);
                $rows = [];
                $count = 1;

                foreach ($sites[$name] as $key => $site) {
                    $rows[] = [$count, $name, $site['host'], $site['locale'], '/web/public/'.$name, $site['active'] ? 'Enabled' : 'Disabled'];
                    $count++;
                }

                $table->setRows($rows);
                $table->setStyle('borderless');
                $table->render();

                $output->writeln('<info>There are '.count($sites[$name]).' hosts for '. $name .' </info>');
                $question = new Question('Please enter the host you want to disable:', null);
                $host = $helper->ask($input, $output, $question);
                if(!in_array($host, $hosts)) {
                    $output->writeln('<error>Error : the host '.$host.' does not exist.</error>');
                    return;
                }
            }

            $disabled = false;

            foreach ($sites[$name] as $key => $site) {
                if($site['host'] == $host) {
                    $sites[$name][$key]['active'] = false;
                    
                    $output->writeln('');
                    $rows = [];
                    $rows[] = [1, $name, $site['host'], $site['locale'], '/web/public/'.$name, $site['active'] ? 'Enabled' : 'Disabled'];
                    $table->setRows($rows);
                    $table->setStyle('borderless');
                    $table->render();
                    $output->writeln('');
                    
                    $question = new ConfirmationQuestion('Do you want to disable ? (yes/no) ', false);
                    if($helper->ask($input, $output, $question)) {
                        $disabled = true;
                        $sm->updateSiteConfigs($sites);
                        $output->writeln('');
                        $output->writeln('<fg=green>Success: The host '.$host.' has been disabled</>');
                        $output->writeln('');
                    }
                }
            }

            if(!$disabled) {
                $output->writeln('');
                $output->writeln('<fg=red>The host '.$host.' has not been disabled</>');
                $output->writeln('');
            }
        } else {
            $output->writeln('<error>Error : the site '.$name.' does not exist.</error>');
            return;
        }

    }

}
