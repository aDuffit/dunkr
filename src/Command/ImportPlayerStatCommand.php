<?php

namespace App\Command;

use App\Entity\League;
use App\Entity\Player;
use App\Entity\Team;
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
    name: 'import-player-stat',
    description: 'Import des statistiques de joueurs',
)]
class ImportPlayerStatCommand extends Command
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

        $io->title('Import des joueurs');

        ProgressBar::setFormatDefinition('custom', ' %current%/%max% %bar% %estimated% -- %message%');
        $progressBar = new ProgressBar($output, count($leagues));

        $progressBar->setFormat('custom');
        $progressBar->setMessage('Début de l\'import des joueurs');
        $progressBar->start();

        foreach ($leagues as $league) {
            ImportService::importPlayerStatsByLeague(
                $league,
                $this->client,
                $this->em,
                $this->em->getRepository(Player::class),
                $this->em->getRepository(Team::class),
            );

            $progressBar->setMessage(sprintf('Import joueur de la league "%s"', $league->getName()));
            $progressBar->advance();
        }

        $progressBar->finish();

        $io->newLine(2);
        $io->success('Création des joueurs terminé.');

        return Command::SUCCESS;
    }
}
