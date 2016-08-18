<?php

namespace AppBundle\Command;

use AppBundle\Document\Team;
use AppBundle\Document\User;
use AppBundle\Service\ChannelService;
use AppBundle\Service\TeamService AS TeamService;
use AppBundle\Service\UserService AS UserService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SlackSyncCommand which syncronises the local mongodb data set with the remote api.
 *
 * @package AppBundle\Command
 */
class SlackSyncCommand extends ContainerAwareCommand
{
    /**
     * Configuration information for the command.
     */
    protected function configure()
    {
        $this
            ->setName('slack:sync')
            ->setDescription('Command to sync local data with remote slack installs - will sync all teams that are authenticated with the bot..')
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
        $output->writeln(sprintf("> Starting the syncronisation process."));
        $teams = $this->getTeamService()->findAll();
        /** @var Team $team */
        foreach ($teams AS $team) {

            $output->writeln(sprintf("> Syncing team: %s", $team->getName()));

            $output->writeln(">> Retrieving user data...");
            $users = $this->getUserService()->syncTeam($team);
            /** @var User $user */
            foreach($users AS $user) {
                $output->writeln(sprintf(">>> Syncing user - %s", $user->getName()));
            }
            unset($users, $user);

            $output->writeln(">> Retrieving channel data...");
            $channels = $this->getChannelService()->syncTeam($team);
            /** @var Channel $channel */
            foreach($channels AS $channel) {
                $output->writeln(sprintf(">>> Syncing channel history - %s", $channel->getName()));
                $this->getChannelService()->syncHistory($channel);
            }
            unset($channels, $channel);

        }
        $output->writeln(sprintf("> Syncronisation complete."));
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

}
