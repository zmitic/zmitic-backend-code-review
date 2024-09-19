<?php
declare(strict_types=1);

namespace Repository;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MessageRepositoryTest extends KernelTestCase
{
    public function test_it_has_connection(): void
    {
        self::bootKernel();

        /** @phpstan-var MessageRepository $repository - PHPStan doesn't recognize test containers */
        $repository = self::getContainer()->get(MessageRepository::class);
        $repository->findAll();
        self::assertContainsOnlyInstancesOf(Message::class, $repository->findAll());
    }
}
