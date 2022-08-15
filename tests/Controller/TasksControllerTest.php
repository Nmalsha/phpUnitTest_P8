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

    //check  create task

    /**
     * @covers TaskController::createAction
     */
    public function testTaskCreate(): void
    {
        $user = $this->client->loginUser($this->adminUser);
        $data = ["test-task", "test", "1"];
        // dd($data);
        $form = $this->createTaskForm($user, $data);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    }

    //check  edit task

    /**
     * @covers TaskController::editAction
     */
    public function testTaskEdit(): void
    {
        $user = $this->client->loginUser($this->adminUser);
        $TestTaskId = $this->taskRepository->findOneByTitle('test-task');
        $crowler = $this->client->request('GET', '/tasks/' . $TestTaskId->getId() . '/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crowler->selectButton('Editer')->form();
        $form['task[title]'] = 'test-task - modify';
        $form['task[content]'] = 'test -modify';
        $form["task[isDone]"] = '1';

        $this->client->submit($form);

        $this->client->followRedirects();
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    }

    public function createTaskForm($user, $data)
    {
        $this->client->loginUser($this->adminUser);
        $crawler = $this->client->request('GET', '/tasks/create');
        $createButton = $crawler->selectButton("Ajouter");
        $form = $createButton->form();
        $form["task[title]"] = $data[0];

        $form["task[content]"] = $data[1];
        $form["task[isDone]"] = $data[2];

        return $this->client->submit($form);

    }

}
