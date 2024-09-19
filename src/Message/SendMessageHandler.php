<?php
declare(strict_types=1);

namespace App\Message;

use DateTime;
use App\Entity\Message;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
/**
 * TODO: Cover with a test
 */
class SendMessageHandler
{
    public function __construct(private EntityManagerInterface $manager)
    {
    }
    
    public function __invoke(SendMessage $sendMessage): void
    {
        $message = new Message();
        $message->setUuid(Uuid::v6()->toRfc4122());
        $message->setText($sendMessage->text);
        $message->setStatus('sent');
        $message->setCreatedAt(new DateTime());

        $this->manager->persist($message);
        $this->manager->flush();
    }
}
