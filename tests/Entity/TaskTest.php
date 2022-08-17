<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @covers \App\Entity\Task
 */

class TaskTest extends WebTestCase
{

    public function setUp(): void
    {
        $this->user = new User();
        $this->task = new Task();
        $this->date = new \DateTimeImmutable();
        $this->task->setUser($this->user)
            ->setTitle('testTaskTitle')
            ->setContent('testTaskContent')
            ->setIsDone(1)
            ->setCreatedAt($this->date);
    }

    public function testTaskIsTrue(): void
    {

        $this->assertTrue($this->user === $this->task->getUser());
        $this->assertTrue('testTaskTitle' === $this->task->getTitle());
        $this->assertTrue('testTaskContent' === $this->task->getContent());
        $this->assertTrue(true === $this->task->isIsDone());
        $this->assertTrue($this->date === $this->task->getCreatedAt());
    }

    public function testTaskIsFalse(): void
    {
        $this->assertFalse(new User === $this->task->getUser());
        $this->assertFalse('testTaskfalseTitle' === $this->task->getTitle());
        $this->assertFalse('testTaskfalseContent' === $this->task->getContent());
        $this->assertFalse(false === $this->task->isIsDone());
        $this->assertFalse(new \DateTimeImmutable() === $this->task->getCreatedAt());
    }

    public function testTaskIsEmpty(): void
    {
        $task = new Task;

        $this->assertEmpty($task->getId());
        $this->assertEmpty($task->getTitle());
        $this->assertEmpty($task->getContent());
        $this->assertEmpty($task->getCreatedAt());
        $this->assertEmpty($task->getUser());
        $this->assertEmpty($task->isIsDone());

    }

}
