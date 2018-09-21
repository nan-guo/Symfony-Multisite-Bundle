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

class SiteSyncConfigCommand extends ContainerAwareCommand
{
    const MAX_ATTEMPTS = 5;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('site:config:sync')
            ->setDescription('Synchronize configurations')
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
        $projectDir = $this->getContainer()->get('kernel')->getProjectDir();
        $helper = $this->getHelper('question');

        $question = new Question('Please enter origin directory name(a-zA-Z0-9_):', null);
        $question->setValidator(['Prodigious\MultisiteBundle\Command\Validators', 'validateSiteFolder']);
        $question->setMaxAttempts(self::MAX_ATTEMPTS);

        $originDir = $helper->ask($input, $output, $question);

        if(is_null($originDir) || !is_dir($projectDir.DIRECTORY_SEPARATOR.$originDir)) {
            $output->writeln('<error>Error : The origin directory does not exist.</error>');
            return;
        }

        $question = new Question('Please enter target directory name(all):', 'all');
        $question->setValidator(['Prodigious\MultisiteBundle\Command\Validators', 'validateSiteFolder']);
        $question->setMaxAttempts(self::MAX_ATTEMPTS);

        $targetDir = $helper->ask($input, $output, $question);

        $sm = $this->getContainer()->get('multisite.manager');
       
        if($targetDir != 'all' && !is_dir($projectDir.DIRECTORY_SEPARATOR.$targetDir)) {
            $output->writeln('<error>Error : The target directory does not exist.</error>');
            return;
        }

        if($targetDir == 'all') {

            $question = new ConfirmationQuestion('Are you sure to synchronize configurations for all sites ? (yes/no) ', true);

            if(!$helper->ask($input, $output, $question)) {
                return;
            }

            $sites = $sm->list();

            if($originDir != 'app') {
                $sm->syncToGlobalConfig($originDir);
                $output->writeln('');
                $output->writeln('<fg=green>Synchronization: '.$originDir.' ===> app</>');
                $output->writeln('');
            }
            
            foreach ($sites as $folder => $site) {
                if($originDir != 'app_'.$folder) {
                    $sm->syncConfig($originDir, 'app_'.$folder);
                    $output->writeln('');
                    $output->writeln('<fg=green>Synchronization: '.$originDir.' ===> app_'.$folder.' </>');
                    $output->writeln('');
                }
            }
        }else {
            $sm->syncConfig($originDir, $targetDir);
            $output->writeln('');
            $output->writeln('<fg=green>Synchronization: '.$originDir.' ===> '.$targetDir.' </>');
            $output->writeln('');
        }

        $output->writeln('');
        $output->writeln('<fg=green>Success: Configurations have been synchronized</>');
        $output->writeln('');

    }

}
