<?php
declare(strict_types=1);

namespace Controller;

use Generator;
use Faker\Factory;
use App\Message\SendMessage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Messenger\Test\InteractsWithMessenger;
use Gertjuhh\SymfonyOpenapiValidator\OpenApiValidator;

class MessageControllerTest extends WebTestCase
{
    use InteractsWithMessenger;
    use OpenApiValidator;

    public function test_list(): void
    {
        $client = static::createClient();
        $client->request('GET', '/messages');
        self::assertResponseIsSuccessful();
        self::assertResponseAgainstOpenApiSchema('openapi.yaml', $client);
    }

    public function test_list_filtered(): void
    {
        $client = static::createClient();
        $client->request('GET', '/messages', [
            'status' => 'sent'
        ]);
        self::assertResponseIsSuccessful();
        self::assertResponseAgainstOpenApiSchema('openapi.yaml', $client);
    }

    /**
     * @dataProvider provideInvalidData
     */
    public function test_that_it_does_not_send_a_message(string $text): void
    {
        $client = static::createClient();
        $client->request('GET', '/messages/send', [
            'text' => $text,
        ]);
        $this->assertResponseStatusCodeSame(400);
        // assert that controller didn't create a message, even with 400 status
        $this->transport('sync')
            ->queue()
            ->assertContains(SendMessage::class, 0);
    }

    /**
     * @dataProvider provideValidData
     */
    public function test_that_it_sends_a_message(string $text): void
    {
        $client = static::createClient();
        $client->request('GET', '/messages/send', [
            'text' => $text,
        ]);
        $this->assertResponseIsSuccessful();
        // This is using https://packagist.org/packages/zenstruck/messenger-test
        $transport = $this->transport('sync');
        $transport->queue()->assertContains(SendMessage::class, 1);
    }

    /**
     * PHPStan doesn't properly detect falsy strings. The following values must be allowed by controller.
     */
    public function provideValidData(): Generator
    {
        $faker = Factory::create();

        yield ['0'];
        yield ['false'];
        yield [$faker->text(maxNbChars: 255)];
    }

    /**
     * We do not allow empty strings, nor those longer than 255 bytes. Generate more exclusion use-cases if needed.
     */
    public function provideInvalidData(): Generator
    {
        $faker = Factory::create();

        yield [''];
        yield [$faker->realTextBetween(minNbChars: 256, maxNbChars: 300)];
    }
}
