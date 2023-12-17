<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use App\Command\ImportPostsCommand;
use Doctrine\ORM\EntityManagerInterface;

class ImportPostsCommandTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;
    
    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Your teardown logic here
    }

    public function testExecute()
    {
        $command = new ImportPostsCommand($this->entityManager);
        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        // Add assertions based on the expected output or state after command execution
        $this->assertStringContainsString('Tabela użytkowników została wyczyszczona przed importem', $commandTester->getDisplay());
        $this->assertStringContainsString('Tabela postów została wyczyszczona przed importem', $commandTester->getDisplay());
        // Add more assertions as needed
    }

    // Add more test methods for other functionalities of the ImportPostsCommand class
    // ...

    // Optional: You may want to test the private methods individually by making them public or protected
    // and testing them separately.
}
