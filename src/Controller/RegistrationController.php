<?php

namespace App\Controller;

use App\Entity\Structure\Veterinary;
use App\Security\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Security\Login\LoginFormAuthenticator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * Class for registration and send email verification
 *
 * @Route("/security/register", name="app_register")
 */
class RegistrationController extends AbstractController
{
    /**
     * @var EmailVerifier
     */
    private $emailVerifier;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @param EmailVerifier $emailVerifier
     * @param FlashBagInterface $flashBag
     */
    public function __construct(EmailVerifier $emailVerifier, FlashBagInterface $flashBag)
    {
        $this->emailVerifier = $emailVerifier;
        $this->flashBag      = $flashBag;
    }

    /**
     * @Route("", name="")
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     *
     * @return Response
     *
     * @throws TransportExceptionInterface
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        $user = new Veterinary();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * Encode password
             */
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            /**
             * Generate a signed url and email it to the user
             */
            $this->emailVerifier->sendEmailConfirmation('app_register_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('benjamin.manguet@gmail.com', 'Vetogiciel'))
                    ->to($user->getEmail())
                    ->subject('Confirmation d\'adresse : Vetogiciel')
                    ->htmlTemplate('email/confirmation_email.html.twig')
            );

            $this->flashBag->add('warning', 'Merci de confirmer votre adresse mail : ' . $user->getEmail() . ' afin d\'accéder aux fonctionnalités.');

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main'
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify/email", name="_verify_email")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function verifyUserEmail(Request $request): Response
    {
        /**
         * Validate email confirmation link, sets User::isVerified=true and persists
         */
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Votre adresse mail a bien été vérifiée.');

        return $this->redirectToRoute('index');
    }
}
