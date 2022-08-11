<?php

namespace App\Tests\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TasksControllerTest extends WebTestCase
{
    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->taskRepository = static::getContainer()->get(TaskRepository::class);
        $this->user = $this->userRepository->findOneByEmail('constance.gros@live.com');
        $this->adminUser = $this->userRepository->findOneByEmail('admin12@gmail.com');
        $this->urlGenerator = $this->client->getContainer()->get('router.default');

    }
    //check redirection of the page when user/admin click on create task

    /**
     * @covers TaskController::listAction
     */

    public function testTaskListPageRedirectionIfAUserConnected(): void
    {

        $this->client->loginUser($this->user);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
    /**
     * @covers TaskController::listAction
     */

    public function testTaskListPageRedirectionIfAUserNotConnected(): void
    {

        $this->client->loginUser($this->user);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_login'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
