<?php

declare(strict_types=1);

namespace Message;

use App\Message\SendMessage;
use App\Repository\MessageRepository;
use Zenstruck\Messenger\Test\InteractsWithMessenger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * We are testing DB here as well, but without https://github.com/liip/LiipTestFixturesBundle/blob/2.x/doc/database.md
 *
 * So any counting must be memoized and later referred manually, simply because we don't know how many entities have been created in the previous run.
 */
class SendMessageHandlerTest extends KernelTestCase
{
    use InteractsWithMessenger;

    public function test_message_entity_has_been_created(): void
    {
        self::bootKernel();
        /** @phpstan-var MessageRepository $repository - PHPStan doesn't recognize test containers */
        $repository = self::getContainer()->get(MessageRepository::class);
        $transport = $this->transport('sync');
        $nrOfMessageEntities = $repository->count();

        $transport->send(new SendMessage('This is a test'));
        $transport->queue()->assertContains(SendMessage::class, 1);
        $transport->process();
        // 1 new entity has been persisted
        self::assertSame($nrOfMessageEntities + 1, $repository->count());
    }
}
