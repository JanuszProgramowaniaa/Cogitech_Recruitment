<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use App\Entity\User;

class ImportPostsCommand extends Command
{
    protected static $defaultName = 'app:import-posts';
    protected static $defaultDescription = 'Ta importuje użytkowników oraz posty </info>';
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        // Dodaj opcję --clear, która pozwoli na wyczyszczenie tabeli przed importem
        $this->addOption('clear', null, null, 'Wyczyść tabelę przed importem');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
       
        $this->clearTable('user');
        $this->resetAutoIncrement('user');
        $output->writeln('Tabela została wyczyszczona przed importem. Oraz inkrementacja została wyzerowana');
      

        $httpClient = HttpClient::create();
        $usersData = $httpClient->request('GET', 'https://jsonplaceholder.typicode.com/users')->toArray();

        // Inicjalizuj liczniki
        $importedUsersCount = 0;
        $skippedUsersCount = 0;

        foreach ($usersData as $userData) {
            $email = $userData['name'] . '.' . $userData['username'] . '@import.com';

            $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$existingUser) {
                $user = $this->importUser($userData);
                $importedUsersCount++;
                $output->writeln('<fg=green>Zaimportowano użytkownika: ' . $user->getUsername() . '</>');
            } else {
                $output->writeln('<fg=red>Użytkownik o e-mailu: ' . $email . ' już istnieje</>');
                $skippedUsersCount++;
            }
        }

        $output->writeln('Liczba zaimportowanych użytkowników: <fg=green>' . $importedUsersCount . '</>');
        $output->writeln('Liczba pominiętych użytkowników: <fg=red>' . $skippedUsersCount . '</>');

        return Command::SUCCESS;
    }

    private function importUser(array $userData): User
    {
        $user = new User();
        $user->setName($userData['name']);
        $user->setUsername($userData['username']);
        $user->setEmail($userData['name'] . '.' . $userData['username'] . '@import.com');
        $user->setPassword('import');

        $doctrine = $this->entityManager;
        $doctrine->persist($user);
        $doctrine->flush();

        return $user;
    }

  
    private function clearTable($tableName)
    {
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeStatement($platform->getTruncateTableSQL($tableName, true));
    }

    private function resetAutoIncrement($tableName)
    {
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
   
        $tableName = $connection->quoteIdentifier($tableName);
    
        $primaryKey = $connection->getDatabasePlatform()->getIdentifierQuoteCharacter() . 'id' . $connection->getDatabasePlatform()->getIdentifierQuoteCharacter();

        $query = "SELECT MAX($primaryKey) as max_id FROM $tableName";
        $maxId = $connection->fetchAssociative($query)['max_id'];
    
        $query = "ALTER TABLE $tableName AUTO_INCREMENT = " . ($maxId + 1);
        $connection->executeStatement($query);
    }


}
