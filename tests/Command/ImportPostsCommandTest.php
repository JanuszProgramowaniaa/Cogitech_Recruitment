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
    
        $this->assertStringContainsString('Tabela użytkowników została wyczyszczona przed importem', $commandTester->getDisplay());
        $this->assertStringContainsString('Tabela postów została wyczyszczona przed importem', $commandTester->getDisplay());
    
        $this->assertStringContainsString('Zaimportowano użytkownika:', $commandTester->getDisplay());
        $this->assertStringContainsString('Zaimportowano post:', $commandTester->getDisplay());
    
        $this->assertStringContainsString('Liczba zaimportowanych użytkowników:', $commandTester->getDisplay());
        $this->assertStringContainsString('Liczba zaimportowanych postów:', $commandTester->getDisplay());
    
        $this->assertStringContainsString('inkrementacja została wyzerowana', $commandTester->getDisplay());
    
    }


}
