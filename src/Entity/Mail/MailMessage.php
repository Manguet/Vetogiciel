<?php

namespace App\Entity\Mail;

use App\Repository\Mail\MailMessageRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PrePersist;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @ORM\Entity(repositoryClass=MailMessageRepository::class)
 * @ORM\Table(
 *     name="mail_messenger",
 *     indexes={
 *      @ORM\Index(name="ix_queue_name", columns={"queue_name"}),
 *      @ORM\Index(name="ix_available_at", columns={"available_at"}),
 *      @ORM\Index(name="ix_delivered_at", columns={"delivered_at"})
 *  }
 * )
 *
 * @ORM\HasLifecycleCallbacks
 */
class MailMessage
{
    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    private int $id;

    /**
     * @ORM\Column(name="body", type="text", nullable=false)
     */
    private string $body;

    /**
     * @ORM\Column(name="headers", type="text", nullable=false)
     */
    private string $headers;

    /**
     * @ORM\Column(name="queue_name", type="string", length=190, nullable=false)
     */
    private string $queueName;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected DateTimeInterface $createdAt;

    /**
     * @ORM\Column(name="available_at", type="datetime", nullable=false)
     */
    protected DateTimeInterface $availableAt;

    /**
     * @ORM\Column(name="delivered_at", type="datetime", nullable=true)
     */
    protected DateTimeInterface $deliveredAt;

    /**
     * @ORM\Column(name="email_id", type="string", nullable=false)
     */
    protected string $emailId;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getHeaders(): string
    {
        return $this->headers;
    }

    /**
     * @param string $headers
     */
    public function setHeaders(string $headers): void
    {
        $this->headers = $headers;
    }

    /**
     * @return string
     */
    public function getQueueName(): string
    {
        return $this->queueName;
    }

    /**
     * @param string $queueName
     */
    public function setQueueName(string $queueName): void
    {
        $this->queueName = $queueName;
    }

    /**
     * @return DateTimeInterface
     */
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @PrePersist
     */
    public function setCreatedAt(): void
    {
        $this->createdAt = new DateTime();
    }

    /**
     * @return DateTimeInterface
     */
    public function getAvailableAt(): DateTimeInterface
    {
        return $this->availableAt;
    }

    /**
     * @PrePersist
     */
    public function setAvailableAt(): void
    {
        $this->availableAt = new DateTime();
    }

    /**
     * @return DateTimeInterface
     */
    public function getDeliveredAt(): DateTimeInterface
    {
        return $this->deliveredAt;
    }

    /**
     * @param DateTimeInterface $deliveredAt
     */
    public function setDeliveredAt(DateTimeInterface $deliveredAt): void
    {
        $this->deliveredAt = $deliveredAt;
    }

    /**
     * @return string
     */
    public function getEmailId(): string
    {
        return $this->emailId;
    }

    /**
     * @param string $emailId
     */
    public function setEmailId(string $emailId): void
    {
        $this->emailId = $emailId;
    }
}