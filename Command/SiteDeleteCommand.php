<?php

namespace Prodigious\MultisiteBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class SiteDeleteCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('site:delete')
            ->setDescription('Delete a site')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Which site do you want to delete?', null)
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
            $output->writeln('<error>Please enter the name of site you want to delete. Ex: site:delete --name=demo</error>');
            return;
        }

        $sm = $this->getContainer()->get('multisite.manager');

        $sites = $sm->list();

        $table = new Table($output);

        $helper = $this->getHelper('question');

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

                $question = new Question('Please enter a host you want to delete (all):', null);
                $host = $helper->ask($input, $output, $question);
                if($host != 'all' && !in_array($host, $hosts)) {
                    $output->writeln('<error>Error : the host '.$host.' does not exist.</error>');
                    return;
                }
            }

            $table->setHeaders(['#', 'Name', 'Host', 'Locale', 'Public Directory', 'Status']);

            $rows = [];

            $deleted = false;

            $count = 1;

            foreach ($sites[$name] as $key => $site) {
                if($site['host'] == $host || $host == 'all') {
                    $rows[] = [$count, $name, $site['host'], $site['locale'], '/web/public/'.$name, $site['active'] ? 'Activated' : 'Deactivated'];
                    $count++;
                }
            }

            $table->setRows($rows);
            $table->setStyle('borderless');
            $table->render();

            $question = new ConfirmationQuestion('Are you sure to delete ? (yes/no) ', false);

            if($helper->ask($input, $output, $question)) {

                if($host == 'all') {
                    
                    // Remove extra config in composer.json and directory
                    $sm->removeComposerExtraConfig($name);
                    $sm->deleteAppDirectory($name);
                    $sm->deletePublicDirectory($name);
                    foreach ($sites[$name] as $instance) {
                        $sm->deleteRobotsFile($instance['host']);
                    }

                    // Remove config in sites.yml
                    unset($sites[$name]);

                } else {
                    foreach ($sites[$name] as $key => $site) {
                        if($site['host'] == $host) {
                            // Remove config in sites.yml
                            unset($sites[$name][$key]);
                            // Remove robots.txt
                            $sm->deleteRobotsFile($host);

                            // if all locales have been deleted, remove extra config in composer.json and directory
                            if(count($sites[$name]) == 0) {
                                unset($sites[$name]);
                                $sm->removeComposerExtraConfig($name);
                                $sm->deleteAppDirectory($name);
                                $sm->deletePublicDirectory($name);
                            }
                        }
                    }
                }

                // Write configs to sites.yml
                $sm->updateSiteConfigs($sites);

                $output->writeln('');
                $output->writeln('<fg=green>Success: deleted</>');
                $output->writeln('');
            }

        } else {
            $output->writeln('<error>Error : the site '.$name.' does not exist.</error>');
            return;
        }       
    }
}
