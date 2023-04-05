<?php

namespace App\Command;

use App\Entity\Auth\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'auth:create-admin',
    description: 'Add a short description for your command',
)]
class AuthCreateAdminCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;

        parent::__construct();
    }



    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');
        $user = new User();

        $email = new Question("Renseignez l'email de l'administrateur : ", false);
        $email = $helper->ask($input, $output, $email);
        if (!$email) {
            $io->error('Vous devez renseigner un email');
            return Command::FAILURE;
        }

        $password = new Question("Renseignez le mot de passe de l'administrateur : ", false);
        $password = $helper->ask($input, $output, $password);
        if (!$password) {
            $io->error('Vous devez renseigner un mot de passe');
            return Command::FAILURE;
        }

        $password = $this->passwordHasher->hashPassword($user, $password);

        $firstname = new Question("Renseignez le prénom de l'administrateur : ", false);
        $firstname = $helper->ask($input, $output, $firstname);
        if (!$firstname) {
            $io->error('Vous devez renseigner un prénom');
            return Command::FAILURE;
        }

        $lastname = new Question("Renseignez le nom de l'administrateur : ", false);
        $lastname = $helper->ask($input, $output, $lastname);
        if (!$lastname) {
            $io->error('Vous devez renseigner un nom');
            return Command::FAILURE;
        }

        $user->setEmail($email);
        $user->setPassword($password);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setRoles(['ROLE_ADMIN']);
        $this->entityManager->persist($user);
        $this->entityManager->flush();  
        
        $io->success('Administrateur créé avec succès');

        return Command::SUCCESS;
    }
}
