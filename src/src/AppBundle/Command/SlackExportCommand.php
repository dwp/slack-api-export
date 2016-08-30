<?php

namespace AppBundle\Command;

use AppBundle\Document\Channel;
use AppBundle\Document\Team;
use AppBundle\Document\User;
use AppBundle\Service\ChannelService;
use AppBundle\Service\MessageService;
use AppBundle\Service\TeamService AS TeamService;
use AppBundle\Service\UserService AS UserService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SlackExportCommand which dumps the local mongo database in to a flat JSON file.
 *
 * @package AppBundle\Command
 */
class SlackExportCommand extends ContainerAwareCommand
{
    /**
     * Configuration information for the command.
     */
    protected function configure()
    {
        $this
            ->setName('slack:export')
            ->setDescription('Command to export the local database to a flat file.')
            ->addArgument(
                'export-dir',
                InputArgument::REQUIRED,
                'The directory which the export files should be output.'
            )->addOption(
                'since',
                null,
                InputOption::VALUE_OPTIONAL,
                'A timestamp to export messages since in a YYYY-MM-DD.',
                '2010-12-31 00:00'
            );
        ;
    }

    /**
     * Execute the main routine of the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // try and parse since date
        $since = new \DateTime($input->getOption('since'));
        $output->writeln(sprintf("> Exporting data since %s", $since->format(\DateTime::ISO8601)));
        // get export directory
        $exportDir = realpath($input->getArgument('export-dir'));
        $output->writeln(sprintf("> Starting the export process to %s", $exportDir));
        /** @var Team $team */
        $teams = $this->getTeamService()->findAll();
        foreach ($teams AS $team) {
            // start team output
            $output->writeln(sprintf(">> Outputing %s.json for %s.", $team->getDomain(), $team->getName()));
            $exportFile = $exportDir . DIRECTORY_SEPARATOR . $team->getDomain() . '.json';
            file_put_contents(
                $exportFile,
                json_encode(
                    $this->prepareTeamData($team, $since)
                )
            );
        }
        $output->writeln(sprintf("> Export complete."));
    }

    /**
     * Prepare team information and ensure the correct messages are hydrated.
     *
     * @param Team $team
     * @param \DateTime $since
     * @return array
     */
    private function prepareTeamData(Team $team, \DateTime $since)
    {
        /** @var User $user */
        $data = $team->jsonSerialize();
        unset($data['users']);
        foreach($team->getUsers() AS $user) {
            $userData = $user->jsonSerialize();
            $userData['messages'] = $this->getMessageService()->findByUserSince($user, $since);
            $data['users'][] = $userData;
        }
        return $data;
    }

    /**
     * @return ChannelService
     */
    protected function getChannelService()
    {
        return $this->getContainer()->get('app.service.channel');
    }

    /**
     * @return TeamService
     */
    protected function getTeamService()
    {
        return $this->getContainer()->get('app.service.team');
    }

    /**
     * @return UserService
     */
    protected function getUserService()
    {
        return $this->getContainer()->get('app.service.user');
    }

    /**
     * @return MessageService
     */
    protected function getMessageService()
    {
        return $this->getContainer()->get('app.service.message');
    }

}
