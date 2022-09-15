<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @covers \App\Entity\User
 */

class UserTest extends WebTestCase
{

    public function setUp(): void
    {
        $this->user = new User;
        $this->user->setEmail('testEntityUser@gmail.com')
            ->setUsername('testEntityUser')
            ->setPassword('testEntityUser')
            ->setRoles(['ROLE_USER']);

        $this->task = new Task();
        $this->date = new \DateTimeImmutable();
        $this->task->setUser($this->user)
            ->setTitle('testTaskTitle')
            ->setContent('testTaskContent')
            ->setIsDone(1)
            ->setCreatedAt($this->date);

    }

    public function testGetIsTrue(): void
    {

        $this->assertTrue('testEntityUser@gmail.com' === $this->user->getEmail());
        $this->assertTrue('testEntityUser' === $this->user->getUsername());
        $this->assertTrue('testEntityUser' === $this->user->getPassword());
        $this->assertTrue(['ROLE_USER'] === $this->user->getRoles());

        $this->task = new Task();
        $this->user = new User;

        $this->user->addTask($this->task);
        $this->user->removeTask($this->task);

    }

    public function testGetIsFalse(): void
    {

        $this->assertFalse('testEntityUserFalse@gmail.com' === $this->user->getEmail());
        $this->assertFalse('testEntityUserFalse' === $this->user->getUsername());
        $this->assertFalse('testEntityUserFalse' === $this->user->getPassword());
        $this->assertFalse(['ROLE_ADMIN'] === $this->user->getRoles());

    }

    public function testGetIsEmpty(): void
    {
        $user = new User;

        $this->assertEmpty($user->getId());
        $this->assertEmpty($user->getEmail());
        $this->assertEmpty($user->getUsername());
        $this->assertEmpty($user->getUserIdentifier());
        $this->assertEmpty($user->getTask());
        $this->assertEmpty($user->getRoles());
    }
    // public function testAddTask(): void
    // {
    //     $user = new User;
    //     $task = new Task;
    //     $this->user->addTask();
    // }

}
