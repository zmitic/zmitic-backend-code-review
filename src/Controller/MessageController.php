<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Message;
use App\Message\SendMessage;
use App\Repository\MessageRepository;
use Controller\MessageControllerTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use function array_map;

/**
 * @see MessageControllerTest
 * TODO: review both methods and also the `openapi.yaml` specification
 *       Add Comments for your Code-Review, so that the developer can understand why changes are needed.
 */
class MessageController extends AbstractController
{
    /**
     * TODO: cover this method with tests, and refactor the code (including other files that need to be refactored)
     */
    #[Route('/messages', name: 'app_message_list', methods: ['GET'])]
    public function list(Request $request, MessageRepository $messages): Response
    {
        $messages = $messages->by($request->query->all());
        $data = array_map($this->transformMessageToArray(...), $messages);

        return $this->json(['messages' => $data], Response::HTTP_OK);
    }

    #[Route('/messages/send', name: 'app_message_send', methods: ['GET'])]
    public function send(Request $request, MessageBusInterface $bus): Response
    {
        $text = $request->query->getString('text');

        if ('' === $text) {
            return new Response('Text is required', 400);
        }
        if (strlen($text) > 255) {
            return new Response('Text is too long', 400);
        }

        $bus->dispatch(new SendMessage($text));

        return new Response('Successfully sent', 204);
    }

    /**
     * @return array{
     *     uuid: ?string,
     *     text: ?string,
     *     status: ?string,
     * }
     */
    private function transformMessageToArray(Message $message): array
    {
        return [
            'uuid' => $message->getUuid(),
            'text' => $message->getText(),
            'status' => $message->getStatus(),
        ];
    }
}
