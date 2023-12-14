<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Post;

class ImportPostsCommand extends Command
{
    protected static $defaultName = 'app:import-posts';
    protected static $defaultDescription = 'Importuje użytkowników i posty';

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
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

    private function importUsers(OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $usersData = $httpClient->request('GET', 'https://jsonplaceholder.typicode.com/users')->toArray();

        $importedUsersCount = 0;

        foreach ($usersData as $userData) {
            $email = $userData['name'] . '.' . $userData['username'] . '@import.com';

            $user = $this->importUser($userData);
            $importedUsersCount++;
            $output->writeln('<fg=green>Zaimportowano użytkownika: ' . $user->getUsername() . '</>');
            
        }

        $output->writeln('Liczba zaimportowanych użytkowników: <fg=green>' . $importedUsersCount . '</>');
    }

    private function importPosts(OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $postsData = $httpClient->request('GET', 'https://jsonplaceholder.typicode.com/posts')->toArray();

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

    private function deleteUsersAndPosts()
    {
        $connection = $this->entityManager->getConnection();
        
        $connection->executeStatement('DELETE FROM post');
        
        $connection->executeStatement('DELETE FROM user');
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
