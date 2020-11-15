<?php

namespace App\Manager;

use App\Entity\Company;
use App\Entity\Particular;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager
{
    private EntityManagerInterface $manager;
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->manager = $manager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function update(array $data, $user): void
    {
        if ($user->isParticular()) {
            empty($data['firstName']) ?: $user->setFirstName($data['firstName']);
            empty($data['lastName']) ?: $user->setLastName($data['lastName']);
            empty($data['civility']) ?: $user->setCivility($data['civility']);
        }

        if ($user->isCompany()) {
            empty($data['companyName']) ?: $user->setCompanyName($data['companyName']);
        }

        empty($data['password']) ?: $user->setPassword($this->passwordEncoder->encodePassword($user, $data['password']));

        $this->manager->flush();
    }

    public function delete($user): void
    {
        $this->manager->remove($user);
        $this->manager->flush();
    }
}