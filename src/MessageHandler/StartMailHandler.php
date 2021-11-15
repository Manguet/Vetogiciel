<?php

namespace App\MessageHandler;

use App\Entity\Mail\Email;
use App\Entity\Structure\Veterinary;
use App\Interfaces\Message\StartMailInterface;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Message;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class StartMailHandler implements MessageHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EmailVerifier
     */
    private $emailVerifier;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @param EntityManagerInterface $entityManager
     * @param EmailVerifier $emailVerifier
     * @param KernelInterface $kernel
     */
    public function __construct(EntityManagerInterface $entityManager, EmailVerifier $emailVerifier,
                                KernelInterface $kernel)
    {
        $this->entityManager = $entityManager;
        $this->emailVerifier = $emailVerifier;
        $this->kernel        = $kernel;
    }

    /**
     * @param StartMailInterface $mailMessage
     *
     * @throws TransportExceptionInterface
     */
    public function __invoke(StartMailInterface $mailMessage)
    {
        if ($mailMessage->getMailTitle() === 'confirmation_email.html.twig') {
            $user = $this->entityManager->getRepository(Veterinary::class)
                ->find($mailMessage->getUserId());

            $this->sendConfirmationMessage($user);
        }

        $mail = $this->entityManager->getRepository(Email::class)
            ->findOneBy(['title' => $mailMessage->getMailTitle()]);

        if ($mailMessage->getUserId()) {
            $user = $this->entityManager->getRepository(Veterinary::class)
                ->find($mailMessage->getUserId());
        }

        dd($mail);


    }

    /**
     * @throws TransportExceptionInterface
     */
    private function sendConfirmationMessage(UserInterface $user): void
    {
        $logo = $this->kernel->getProjectDir() . '/assets/images/vetogiciel-logo.png';
        $css  = $this->kernel->getProjectDir() . '/assets/css/email.css';

        $this->emailVerifier->sendEmailConfirmation('app_register_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('benjamin.manguet@gmail.com', 'Vetogiciel'))
                ->to($user->getEmail())
                ->subject('Confirmation d\'adresse : Vetogiciel')
                ->htmlTemplate('email/confirmation_email.html.twig')
                ->context([
                    'logo' => $logo,
                    'css'  => $css
                ])
        );
    }
}