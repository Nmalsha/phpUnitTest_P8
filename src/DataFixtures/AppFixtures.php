<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    // public function load(ObjectManager $manager): void
    // {
    //     // $product = new Product();
    //     // $manager->persist($product);

    //     $manager->flush();
    // }
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                "password"
            );
            $user->setEmail($faker->email)
                ->setUsername($faker->firstName)
                ->setRoles(["ROLE_USER"])
                ->setPassword($hashedPassword);

            $manager->persist($user);

            for ($a = 0; $a < 5; $a++) {
                $task = new Task();
                $task->setTitle($faker->text(15))
                    ->setContent($faker->paragraph(1))
                    ->setUser($user)
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setIsDone(false);

                $manager->persist($task);
            }
        }

        for ($u = 0; $u < 1; $u++) {
            $admin = new User();
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                "admin"
            );
            $admin->setUserName($faker->firstName)
                ->setEmail($faker->email)
                ->setPassword($hashedPassword)
                ->setRoles(["ROLE_ADMIN"]);

            $manager->persist($admin);

            $anonyme = new User();
            $anonyme->setUserName("Anonyme")
                ->setEmail($faker->email)
                ->setPassword($hashedPassword)
                ->setRoles(["ROLE_USER"]);

            $manager->persist($anonyme);

            for ($a = 0; $a < 5; $a++) {
                $task = new Task();
                $task->setTitle($faker->text(15))
                    ->setContent($faker->paragraph(1))
                    ->setUser($anonyme)
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setIsDone(false);
                for ($a = 0; $a < 5; $a++) {
                    $task = new Task();
                    $task->setTitle($faker->text(15))
                        ->setContent($faker->paragraph(1))
                        ->setUser($admin)
                        ->setCreatedAt(new \DateTimeImmutable())
                        ->setIsDone(false);

                    $manager->persist($task);
                }
            }
        }

        $manager->flush();
    }
}
