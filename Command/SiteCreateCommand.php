<?php

namespace Prodigious\MultisiteBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class SiteCreateCommand extends ContainerAwareCommand
{
    const MAX_ATTEMPTS = 5;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('site:create')
            ->setDescription('Create a new site')
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
        $helper = $this->getHelper('question');
        $sm = $this->getContainer()->get('multisite.manager');

        $question = new Question('Please enter the site name(a-zA-Z0-9_):', null);
        $question->setValidator(array('Prodigious\MultisiteBundle\Command\Validators', 'validateSiteName'));
        $question->setMaxAttempts(self::MAX_ATTEMPTS);
        
        $name = $helper->ask($input, $output, $question);

        $question = new Question('Please enter the host name without http or https(site.demo.com):', null);
        $question->setValidator(array('Prodigious\MultisiteBundle\Command\Validators', 'validateHost'));
        $question->setMaxAttempts(self::MAX_ATTEMPTS);

        $host = $helper->ask($input, $output, $question);

        $question = new Question('Please enter the default language (en):', 'en');

        $locale = $helper->ask($input, $output, $question);

        $sites = $sm->list();

        if(!$sm->checkHost($name, $host)) {
            $output->writeln('<error>Error : host '.$host.' already exists.</error>');
            return;
        }

        $question = new ConfirmationQuestion('Are you sure to create ? (yes/no) ', false);

        if(!$helper->ask($input, $output, $question)) {
            return;
        }
        
        $created = $sm->exists($name);

        // Create site app folder
        $sm->create($name, $host, $locale);

        if(!$created) {
            // Config parameters.yml
            // $question = new ConfirmationQuestion('Do you want to configure a new database ? (yes/no) ', false);
            // if($helper->ask($input, $output, $question)) {
            $sm->updateParameters($name);
            // }
        } else {
            $output->writeln('<fg=green>The site '.$name.' already exists, you can not update database configs.</>');
        }

        $question = new ConfirmationQuestion('Do you want to enable this site ? (yes/no) ', true);
        $status = $helper->ask($input, $output, $question);
        
        $sites[$name][] = ['host' => $host, 'locale' => $locale, 'active' => $status];
        // Write configs to sites.yml
        $sm->updateSiteConfigs($sites);

        $output->writeln('');
        $output->writeln('<fg=green>Success: The site has been created</>');
        $output->writeln('<fg=green>Site name:</> '.$name);
        $output->writeln('<fg=green>Host name:</> '.$host);
        $output->writeln('<fg=green>App directory:</> app_'.$name);
        $output->writeln('<fg=green>Public directory:</> web/public/'.$name);
        $output->writeln('<fg=green>Site status:</> '. ($status ? 'Enabled' : 'Disabled'));
        $output->writeln('');
    }

}
