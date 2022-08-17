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

        $this->task = new Task;
    }

    public function testGetIsTrue(): void
    {

        $this->assertTrue('testEntityUser@gmail.com' === $this->user->getEmail());
        $this->assertTrue('testEntityUser' === $this->user->getUsername());
        $this->assertTrue('testEntityUser' === $this->user->getPassword());
        $this->assertTrue(['ROLE_USER'] === $this->user->getRoles());

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

}
