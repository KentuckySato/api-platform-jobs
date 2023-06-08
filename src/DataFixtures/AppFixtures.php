<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Job;
use App\Entity\Recruiter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $cmpArray = [];
        $recArray = [];


        // Create Companies
        for ($cmp = 0; $cmp < 9; $cmp++) {
            $company = new Company();
            $company
                ->setName($faker->company())
                ->setReference($faker->randomNumber(9))
                ->setDescription($faker->text())
                ->setSiren($faker->siren())
                ->setSiret($faker->siret())
                ->setCreatedAt(new \DateTimeImmutable());

            $cmpArray[] = $company;

            $manager->persist($company);

        }

        // Create Recruiter
        for ($r = 0; $r < 4; $r++) {
            $rec = new Recruiter();

            $rec
                ->setCompany($faker->randomElement($cmpArray))
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setEmail($faker->email())
                ->setPhone($faker->phoneNumber())
                ->setReference($faker->randomNumber(9))
                ->setCreatedAt(new \DateTimeImmutable());

            $recArray[] = $rec;

            $manager->persist($rec);
        }

        for ($j = 0; $j < 100; $j++) {
            $job = new Job();

            /** @var Recruiter $jobRec */
            $jobRec = $faker->randomElement($recArray);

            $job
                ->setCompany($jobRec->getCompany())
                ->setRecruiter($jobRec)
                ->setName($faker->jobTitle())
                ->setDescription($faker->text())
                ->setStatus($faker->randomElement([1, 2, 3]))
                ->setSalaryLow($faker->numberBetween(20000, 30000))
                ->setSalaryHigh($faker->numberBetween(30000, 50000))
                ->setSalaryPrivacy($faker->boolean())
                ->setProfile($faker->text())
                ->setFullRemote($faker->boolean())
                ->setReference($faker->randomNumber(9))
                ->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($job);
        }

        $manager->flush();
    }
}
