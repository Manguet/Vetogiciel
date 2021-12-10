<?php

namespace App\Security\Login;

use App\Entity\Patients\Client;
use App\Entity\Structure\Employee;
use App\Entity\Structure\Veterinary;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * Authenticator for login User
 */
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'security_login';

    private UrlGeneratorInterface $urlGenerator;

    private CsrfTokenManagerInterface $csrfTokenManager;

    private EntityManagerInterface $entityManager;

    private FlashBagInterface $flashBag;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param EntityManagerInterface $entityManager
     * @param FlashBagInterface $flashBag
     */
    public function __construct(UrlGeneratorInterface $urlGenerator,
                                CsrfTokenManagerInterface $csrfTokenManager,
                                EntityManagerInterface $entityManager,
                                FlashBagInterface $flashBag)
    {
        $this->urlGenerator     = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->entityManager    = $entityManager;
        $this->flashBag         = $flashBag;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    /**
     * @param Request $request
     *
     * @return array|mixed
     */
    public function getCredentials(Request $request)
    {
        $credentials = [
            'email'      => $request->request->get('email'),
            'password'   => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);

        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException('Token expiré, merci de rafraichir la page. Si le problème persiste, contactez un administrateur');
        }

        $user = $this->loadUserByUsername($credentials['email']);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException('L\'email n\'a pas été trouvé.');
        }

        return $user;
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     *
     * @return bool
     *
     * @throws Exception
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $user->getEmail() === $credentials['email'];
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param $providerKey
     *
     * @return RedirectResponse|Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('index'));
    }

    /**
     * @return string
     */
    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    /**
     * @param string $email
     *
     * @return mixed
     */
    private function loadUserByUsername(string $email)
    {
        /**
         * Check email on client / employee / veterinary
         */
        $user = $this->entityManager->getRepository(Client::class)
            ->findOneBy(['email' => $email]);

        if (null === $user) {
            $user = $this->entityManager->getRepository(Employee::class)
                ->findOneBy(['email' => $email]);
        }

        if (null === $user) {
            $user = $this->entityManager->getRepository(Veterinary::class)
                ->findOneBy(['email' => $email]);
        }

        if (null === $user) {
            throw new UsernameNotFoundException(sprintf('L\'utilisateur "%s" n\'a pas été trouvé.', $email));
        }

        return $user;
    }
}
