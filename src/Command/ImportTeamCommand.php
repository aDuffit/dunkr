<?php

namespace App\Command;

use App\Entity\League;
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
    name: 'import:team',
    description: 'Add a short description for your command',
)]
class ImportTeamCommand extends Command
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

        $io->title('Import des équipes');

        ProgressBar::setFormatDefinition('custom', ' %current%/%max% %bar% %estimated% -- %message%');
        $progressBar = new ProgressBar($output, count($leagues));

        $progressBar->setFormat('custom');
        $progressBar->setMessage('Début de l\'import des équipes');
        $progressBar->start();

        foreach ($leagues as $league) {
            ImportService::importTeamByLeague(
                $league,
                $this->client,
                $this->em,
                $this->em->getRepository(Team::class),
            );

            $progressBar->setMessage(sprintf('Import équipes de la league "%s"', $league->getName()));
            $progressBar->advance();
        }

        $io->newLine(2);
        $io->success('Création des équipes terminé.');

        return Command::SUCCESS;
    }
}
