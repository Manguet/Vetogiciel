<?php

namespace App\Service\User;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Service to encode password on entities
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class PasswordEncoderServices
{
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param $form
     * @param $entity
     */
    public function encodePassword($form, $entity): void
    {
        $password        = $form->getData()->getPassword();
        $encodedPassword = $this->encoder->encodePassword($entity, $password);

        $entity->setPassword($encodedPassword);
    }
}