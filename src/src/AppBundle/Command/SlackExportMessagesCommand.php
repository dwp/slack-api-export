<?php

namespace AppBundle\Command;

use AppBundle\Document\Channel;
use AppBundle\Document\Team;
use AppBundle\Document\User;
use AppBundle\Service\ChannelService;
use AppBundle\Service\MessageService;
use AppBundle\Service\TeamService AS TeamService;
use AppBundle\Service\UserService AS UserService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SlackExportMessagesCommand which dumps the local mongo database in to a flat JSON file.
 *
 * @package AppBundle\Command
 */
class SlackExportMessagesCommand extends ContainerAwareCommand
{
    /**
     * Configuration information for the command.
     */
    protected function configure()
    {
        $this
            ->setName('slack:export:messages')
            ->setDescription('Command to export all local messages to a flat file')
            ->addArgument(
                'export-dir',
                InputArgument::REQUIRED,
                'The directory which the export files should be output.'
            )
            ->addOption(
                'no-bots',
                null,
                InputOption::VALUE_NONE,
                'Only export messages from real users (e.g. is_bot === false)'
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
        // get export directory
        $exportDir = realpath($input->getArgument('export-dir'));
        $output->writeln(sprintf("> Starting the export process to %s", $exportDir));
        /** @var Team $team */
        $teams = $this->getTeamService()->findAll();
        foreach ($teams AS $team) {
            // start team output
            $exportFile = $exportDir . DIRECTORY_SEPARATOR . $team->getDomain() . '-messages.json';
            $output->writeln(sprintf(">> Outputing %s for %s.", $exportFile, $team->getName()));
            $messages = [];
            // whip though outputting messages
            foreach ($team->getChannels() AS $channel) {
                foreach($channel->getMessages() AS $message) {
                    if (
                        $input->getOption('no-bots') &&
                        (
                            $message->getIsBot() ||
                            $message->getUser()->getIsBot()
                        )
                    ) continue;
                    $messages[] = [
                        'id' => $message->getId(),
                        'timestamp' => $message->getTimestampDateTime()->format(DATE_ISO8601),
                        'type' => $message->getType(),
                        'sub_type' => $message->getSubType(),
                        'channel' => $message->getChannel()->getName(),
                        'user' => is_null($message->getUser()) ? null : $message->getUser()->getName(),
                        'text' => $message->getText(),
                        'is_bot' => $message->getIsBot()
                    ];
                }
            }
            file_put_contents($exportFile, json_encode($messages));
        }
        $output->writeln(sprintf("> Export complete."));
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
