<?php

namespace App\Tests\Traits;

use App\Entity\Patients\Client;
use App\Entity\Structure\Employee;
use App\Entity\Structure\Veterinary;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
trait LoginTrait
{
    /**
     * @var null|KernelBrowser
     */
    protected $client;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * @param string $userType
     *
     * @return void
     */
    private function logIn(string $userType = 'veterinary'): void
    {
        $session = self::$container->get('session');

        switch ($userType) {

            case 'veterinary':
                $userType = Veterinary::class;
                break;

            case 'employee':
                $userType = Employee::class;
                break;

            case 'client':
                $userType = Client::class;
                break;

            default:
                throw new InvalidArgumentException('La classe ' . $userType . ' n\'est pas supporté', 400);
        }

        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');

        $user = $entityManager->getRepository($userType)
            ->findOneBy([]);

        $firewallName    = 'main';
        $firewallContext = 'main';

        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());

        $this->client->getCookieJar()->set($cookie);
    }
}