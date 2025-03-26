<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin_user',
    description: 'Create a new admin user',
)]
class CreateAdminUserCommand extends Command
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    )
    {
        parent::__construct();
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure(): void
    {
//        $this
//            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user')
//            ->addArgument('password', InputArgument::REQUIRED, 'The password of the user')
//        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $io->ask('Email');
        $password = $io->askHidden('Password');


        $existingUser = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($existingUser) {
            $output->writeln('<error>User with this email already exists.</error>');

            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setIsVerified(true);

        $this->em->persist($user);
        $this->em->flush();

        $io->success('Admin user created successfully.');

        return Command::SUCCESS;
    }
}
