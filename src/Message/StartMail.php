<?php

namespace App\Message;

use App\Interfaces\Message\StartMailInterface;

/**
 * @author Benjamin Manguet
 */
class StartMail implements StartMailInterface
{
    private string $mailTitle;

    private ?int $userId;

    /**
     * @param $mailTitle
     * @param int|null $userId
     */
    public function __construct($mailTitle, ?int $userId = null)
    {
        $this->mailTitle = $mailTitle;
        $this->userId    = $userId;
    }

    /**
     * @return string
     */
    public function getMailTitle(): string
    {
        return $this->mailTitle;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }
}