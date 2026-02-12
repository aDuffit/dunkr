<?php

namespace App\Command;

use App\Entity\League;
use App\Entity\Player;
use App\Service\ImportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'import:player:information',
    description: 'Import des informations joueur',
)]
class ImportPlayerInformationCommand extends Command
{
    protected EntityManagerInterface $em;

    protected HttpClientInterface $client;
    public function __construct(EntityManagerInterface $em, HttpClientInterface $client)
    {
        parent::__construct();
        $this->em = $em;
        $this->client = $client;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $leagues = $this->em->getRepository(League::class)->findAll();

        $io->title('Import des informations joueurs');

        ProgressBar::setFormatDefinition('custom', ' %current%/%max% %bar% %estimated% -- %message%');

        foreach ($leagues as $league) {
            if (!$league instanceof League) {
                continue;
            }

            $io->comment(sprintf('Début import joueur League %s', $league->getName()));

            foreach ($league->getTeams() as $team) {
                $io->comment(sprintf('Début import joueur équipe %s', $team->getName()));
                $progressBar = new ProgressBar($output, $team->getPlayers()->count());
                $progressBar->setFormat('custom');
                $progressBar->start();

                foreach ($team->getPlayers() as $player) {
                    if (!$player instanceof Player) {
                        continue;
                    }

                    ImportService::importPlayerInformation($player, $this->client, $this->em);

                    $progressBar->setMessage(sprintf('Import joueur "%s"', $player->getName()));
                    $progressBar->advance();

                    // Attend entre 3 et 4 secondes de manière aléatoire pour ne pas être bannis du site via leur firewall
                    usleep(rand(3000, 4000) * 1000);
                }

                $this->em->flush();
                $progressBar->finish();
                $io->newLine(2);
            }
        }

        $io->newLine(2);
        $io->success('Création des joueurs terminé.');

        return Command::SUCCESS;
    }
}
