<?php

namespace App\Service\Mail\Factory;

use App\Entity\Mail\MailMessage;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use LogicException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Messenger\Stamp\SentStamp;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

/**
 * @author Benjamin Manguet
 */
class MailCommandTransport implements TransportInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $db;

    /**
     * @var PhpSerializer|SerializerInterface
     */
    protected $serializer;

    /**
     * @param EntityManagerInterface $db
     * @param SerializerInterface|null $serializer
     */
    public function __construct(EntityManagerInterface $db, SerializerInterface $serializer = null)
    {
        $this->db         = $db;
        $this->serializer = $serializer ?? new PhpSerializer();
    }

    /**
     * @return iterable
     */
    public function get(): iterable
    {
        $redeliverTimeout = new DateTimeImmutable('-5minutes');

        $mailMessage = $this->db->getRepository(MailMessage::class)
            ->findAvailableMessage($redeliverTimeout);

        if (null === $mailMessage) {
            return [];
        }

        $envelope = $this->serializer->decode([
            'body' => $mailMessage->getBody(),
        ]);

        return [$envelope->with(new TransportMessageIdStamp($mailMessage->getId()))];
    }

    /**
     * @param Envelope $envelope
     *
     * @return void
     */
    public function ack(Envelope $envelope): void
    {
        $stamp = $envelope->last(TransportMessageIdStamp::class);

        if (!$stamp instanceof TransportMessageIdStamp) {
            throw new LogicException('Aucun TransportMessageIdStamp n\'a été trouvé dans l\'envelope.');
        }

        $mailMessage = $this->db->getRepository(MailMessage::class)
            ->find($stamp->getId());

        $mailMessage->setDeliveredAt(new DateTime());

        $this->db->flush();

        $this->reject($envelope);
    }

    /**
     * @param Envelope $envelope
     *
     * @return void
     */
    public function reject(Envelope $envelope): void
    {
        $stamp = $envelope->last(TransportMessageIdStamp::class);

        if (!$stamp instanceof TransportMessageIdStamp) {
            throw new LogicException('Aucun TransportMessageIdStamp n\'a été trouvé dans l\'envelope.');
        }

        $mailMessage = $this->db->getRepository(MailMessage::class)
            ->find($stamp->getId());

        if ($mailMessage) {
            $this->db->remove($mailMessage);

            $this->db->flush();
        }
    }

    /**
     * @param Envelope $envelope
     *
     * @return Envelope
     *
     * @throws Exception
     */
    public function send(Envelope $envelope): Envelope
    {
        $encodedMessage = $this->serializer->encode($envelope);

        $mailMessage = new MailMessage();

        $mailMessage->setHeaders('');
        $mailMessage->setEmailId($envelope->getMessage()->getMailTitle());
        $mailMessage->setQueueName($this->getQueue($envelope));
        $mailMessage->setBody($encodedMessage['body']);

        $this->db->persist($mailMessage);
        $this->db->flush();

        return $envelope->with(new TransportMessageIdStamp($mailMessage->getId()));
    }

    /**
     * @param Envelope $envelope
     *
     * @return string
     *
     * @throws Exception
     */
    protected function getQueue(Envelope $envelope): string
    {
        foreach ($envelope->all() as $stamp) {

            if (isset($stamp[0]) && !empty($stamp[0])) {

                if ($stamp[0] instanceof SentStamp) {

                    return $stamp[0]->getSenderAlias();
                }

                if ($stamp[0] instanceof ReceivedStamp) {

                    return $stamp[0]->getTransportName();
                }
            }
        }

        throw new Exception(
            'Une erreur est survenu lors de la récupération de la queue au niveau du transporteur',
            500
        );
    }
}