<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Post;

/**
 * Komenda do importu użytkowników i postów z zewnętrznego źródła.
 */
class ImportPostsCommand extends Command
{
    protected static $defaultName = 'app:import-posts';
    protected static $defaultDescription = 'Importuje użytkowników i posty';

    /**
     * @var EntityManagerInterface $entityManager Manager encji Doctrine.
     */
    private EntityManagerInterface $entityManager;

    /**
     * Konstruktor komendy.
     *
     * @param EntityManagerInterface $entityManager Manager encji Doctrine.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    /**
     * Wykonuje import użytkowników i postów.
     *
     * @param InputInterface $input Interfejs wejściowy konsoli.
     * @param OutputInterface $output Interfejs wyjściowy konsoli.
     *
     * @return int Kod zakończenia komendy.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {   
        $this->deleteUsersAndPosts();

        $this->resetAutoIncrement('user');
        $output->writeln('Tabela użytkowników została wyczyszczona przed importem. Oraz inkrementacja została wyzerowana');

        $this->importUsers($output);

        $this->resetAutoIncrement('post');
        $output->writeln('Tabela postów została wyczyszczona przed importem. Oraz inkrementacja została wyzerowana');

        $this->importPosts($output);

        return Command::SUCCESS;
    }

    /**
     * Importuje użytkowników z zewnętrznego źródła.
     *
     * @param OutputInterface $output Interfejs wyjściowy konsoli.
     */
    private function importUsers(OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $usersData = $httpClient->request('GET', $_ENV['API_BASE_PLACEHOLDER_URL'].'/users')->toArray();

        $importedUsersCount = 0;

        foreach ($usersData as $userData) {
            $email = $userData['name'] . '.' . $userData['username'] . '@import.com';

            $user = $this->importUser($userData);
            $importedUsersCount++;
            $output->writeln('<fg=green>Zaimportowano użytkownika: ' . $user->getUsername() . '</>');
            
        }

        $output->writeln('Liczba zaimportowanych użytkowników: <fg=green>' . $importedUsersCount . '</>');
    }

    /**
     * Importuje posty z zewnętrznego źródła.
     *
     * @param OutputInterface $output Interfejs wyjściowy konsoli.
     */
    private function importPosts(OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $postsData = $httpClient->request('GET', $_ENV['API_BASE_PLACEHOLDER_URL'].'/posts')->toArray();

        $importedPostsCount = 0;

        foreach ($postsData as $postData) {
            $userId = $postData['userId'];
            $user = $this->entityManager->getRepository(User::class)->find($userId);

            if ($user) {
                $post = $this->importPost($postData, $user);
                $importedPostsCount++;
                $output->writeln('<fg=green>Zaimportowano post: ' . $post->getTitle() . '</>');
            } else {
                $output->writeln('<fg=red>Nie znaleziono użytkownika o ID: ' . $userId . ' dla postu o ID: ' . $postData['id'] . '</>');
            }
        }

        $output->writeln('Liczba zaimportowanych postów: <fg=green>' . $importedPostsCount . '</>');
    }

    /**
     * Importuje pojedynczego użytkownika.
     *
     * @param array $userData Dane użytkownika.
     *
     * @return User Utworzony użytkownik.
     */
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

    /**
     * Importuje pojedynczego posta.
     *
     * @param array $postData Dane posta.
     * @param User $user Użytkownik przypisany do posta.
     *
     * @return Post Utworzony post.
     */
    private function importPost(array $postData, User $user): Post
    {
        $post = new Post();
        $post->setTitle($postData['title']);
        $post->setBody($postData['body']);
        $post->setUser($user);

        $doctrine = $this->entityManager;
        $doctrine->persist($post);
        $doctrine->flush();

        return $post;
    }

    /**
     * Usuwa wszystkich użytkowników i posty z bazy danych.
     */
    private function deleteUsersAndPosts()
    {
        $connection = $this->entityManager->getConnection();
        
        $connection->executeStatement('DELETE FROM post');
        
        $connection->executeStatement('DELETE FROM user');
    }

    /**
     * Resetuje inkrementację dla określonej tabeli.
     *
     * @param string $tableName Nazwa tabeli.
     */
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
