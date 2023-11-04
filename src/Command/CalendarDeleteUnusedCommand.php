<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use App\Repository\Calendar\CalendarRepository;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'calendar:delete-unused',
    description: 'Add a short description for your command',
)]
class CalendarDeleteUnusedCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private CalendarRepository $calendarRepository;

    public function __construct(EntityManagerInterface $entityManager, CalendarRepository $calendarRepository)
    {

        $this->entityManager = $entityManager;
        $this->calendarRepository = $calendarRepository;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $unusedCalendars = $this->calendarRepository->getUnusedCalendars();

        foreach ($unusedCalendars as $calendar) {
            $this->entityManager->remove($calendar);
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
