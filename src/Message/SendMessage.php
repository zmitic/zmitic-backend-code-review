<?php
declare(strict_types=1);

namespace App\Message;

class SendMessage
{
    /**
     * @param non-empty-string $text
     */
    public function __construct(
        public string $text,
    )
    {
    }
}
