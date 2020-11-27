<?php

namespace App\DataFixtures;

use App\Entity\Application;
use App\Entity\Company;
use App\Entity\Offer;
use App\Entity\Particular;
use App\Entity\Resume;
use App\Enum\EntityEnum;
use App\Service\TokenGeneratorService;
use App\Utils\FixturesUtils;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private const PASSWORD = 'password';

    private Generator $faker;
    private UserPasswordEncoderInterface $passwordEncoder;
    private TokenGeneratorService $tokenGeneratorService;
    private FixturesUtils $fixturesUtils;

    private array $users = [];
    private array $offers = [];
    private array $applications = [];
    private array $resumes = [];

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        TokenGeneratorService $tokenGeneratorService,
        FixturesUtils $fixturesUtils
    ) {
        $this->faker = Factory::create('fr_FR');
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenGeneratorService = $tokenGeneratorService;
        $this->fixturesUtils = $fixturesUtils;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadResumes($manager);
        $this->loadOffers($manager);
        $this->loadApplications($manager);

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager): void
    {
        for ($u = 0; $u < 20; ++$u) {
            $user = $u < 10 ? new Company() : new Particular();
            $user->setPassword($this->passwordEncoder->encodePassword($user, self::PASSWORD))
                ->setIsVerified(true);

            if ($user->isCompany()) {
                $user->setEmail(sprintf('company%s@test.com', $u))
                    ->setCompanyName($this->faker->company);
            } else {
                $user->setEmail(sprintf('particular%s@test.com', $u))
                    ->setCivility($this->fixturesUtils->getRandomItem(Particular::$civilities))
                    ->setFirstName($this->faker->firstName)
                    ->setLastName($this->faker->lastName);
            }

            $this->tokenGeneratorService->generateAuthToken($user);

            $this->users[] = $user;

            $manager->persist($user);
        }
    }

    private function loadResumes(ObjectManager $manager): void
    {
        $particulars = $this->getParticulars();

        for ($r = 0, $rMax = count($particulars); $r < $rMax; ++$r) {
            $resume = (new Resume())
                ->setTitle($this->faker->jobTitle)
                ->setCv(sprintf('/no-cv-%s', $r))
                ->setContractType($this->fixturesUtils->getRandomItem(EntityEnum::$types))
                ->setActivityArea($this->fixturesUtils->getRandomItem(EntityEnum::$activities))
                ->setOwner($this->fixturesUtils->getRandomItem($particulars))
                ->setDescription($r < count($particulars) / 2 ? $this->fixturesUtils->generateParagraph(2) : null)
                ->setIsPublic($r < $rMax / 2)
            ;

            $this->resumes[] = $resume;

            $manager->persist($resume);
        }
    }

    private function loadOffers(ObjectManager $manager): void
    {
        $companies = $this->getCompanies();

        for ($o = 0; $o < 30; ++$o) {
            $offer = (new Offer())
                ->setTitle($this->faker->jobTitle)
                ->setDescription($this->fixturesUtils->generateParagraph())
                ->setCity($this->faker->city)
                ->setPostalCode($this->faker->postcode)
                ->setStartAt($o < 20 ? $this->faker->dateTimeBetween('- 6 months') : null)
                ->setEndAt($o < 20 ? $this->faker->dateTimeBetween('- 3 months') : null)
                ->setType($this->fixturesUtils->getRandomItem(EntityEnum::$types))
                ->setActivity($this->fixturesUtils->getRandomItem(EntityEnum::$activities))
                ->setSalary($o < 20 ? $this->faker->numberBetween(300, 7000) : null)
                ->setStatus($this->fixturesUtils->getRandomItem(Offer::$offerStatus))
                ->setOwner($this->fixturesUtils->getRandomItem($companies))
            ;

            $this->offers[] = $offer;

            $manager->persist($offer);
        }
    }

    private function loadApplications(ObjectManager $manager): void
    {
        $particulars = $this->getParticulars();

        for ($a = 0; $a < 60; ++$a) {
            $application = (new Application())
                ->setMessage($a < 30 ? $this->fixturesUtils->generateParagraph() : null)
                ->setCandidate($this->fixturesUtils->getRandomItem($particulars))
                ->setOffer($this->fixturesUtils->getRandomItem($this->offers))
                ->setStatus($this->fixturesUtils->getRandomItem(Application::$applicationStatus))
            ;

            $this->applications[] = $application;

            $manager->persist($application);
        }
    }

    private function getParticulars(): array
    {
        return array_filter($this->users, static fn ($user) => $user->isParticular());
    }

    private function getCompanies(): array
    {
        return array_filter($this->users, static fn ($user) => $user->isCompany());
    }
}
