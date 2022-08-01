<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
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
    public function __construct(UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository)
    {
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
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
                ->setEmail("admin12@gmail.com")
                ->setPassword($hashedPassword)
                ->setRoles(["ROLE_ADMIN"]);

            $manager->persist($admin);

        }
        for ($a = 0; $a < 5; $a++) {
            $task = new Task();
            $task->setTitle($faker->text(15))
                ->setContent($faker->paragraph(1))
                ->setUser($admin)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setIsDone(false);

            $manager->persist($task);
        }
        for ($u = 0; $u < 1; $u++) {
            $anonyme = new User();
            $anonyme->setUserName("Anonyme")
                ->setEmail("anonyme@gmail.com")
                ->setPassword($hashedPassword)
                ->setRoles(["ROLE_USER"]);

            $manager->persist($anonyme);
            $manager->flush();

        }

        $anonymous = $this->userRepository->findOneBy([
            'username' => 'Anonyme',
        ]);

        for ($a = 0; $a < 5; $a++) {

            $task = new Task();
            $task->setTitle($faker->text(15))
                ->setContent($faker->paragraph(1))
                ->setUser($anonymous)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setIsDone(false);
            $manager->persist($task);

        }

        $manager->flush();
    }

}
