<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskRepositoryTest extends WebTestCase
{

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->taskRepository = static::getContainer()->get(TaskRepository::class);
        $this->user = $this->userRepository->findOneByEmail('constance.gros@live.com');
        $this->adminUser = $this->userRepository->findOneByEmail('admin12@gmail.com');
        $this->urlGenerator = $this->client->getContainer()->get('router.default');
        $this->testAnonymous = $this->userRepository->findOneByEmail('anonyme@gmail.com');

    }

    /**
     * @covers TaskRepository::remove
     */
    // public function testTaskRepositoryAdd(): void
    // {
    //     $user = $this->client->loginUser($this->adminUser);
    //     // $task = new Task();
    //     // $task->setTitle("test-taskRepos")
    //     //     ->setIsDone(1)
    //     //     ->setContent("test dvdvgfgfg")

    //     // ;

    //     // $this->em->persist($task);
    //     // $this->em->flush();
    //     $data = ["test-taskRepos", "test", "1"];
    //     // dd($data);

    //     $form = $this->createTaskForm($user, $data);

    //     $taskTest = $this->taskRepository->findOneByTitle('test-taskRepos');
    //     // dd($taskTest);
    //     $this->taskRepository->remove($taskTest);
    //     $taskTest = $this->taskRepository->findOneByTitle('test-taskRepos');
    //     dd($taskTest);
    //     $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list'));
    //     $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    //     // $this->assertEquals('test', $taskTest->getContent());
    //     // $this->assertEquals('test-taskRepos', $taskTest->getTitle());

    // }

    // /**
    //  * @covers TaskRepository::remove
    //  */
    // public function testTaskRepositoryRemove(): void
    // {
    //     $this->client->loginUser($this->adminUser);
    //     $id = $this->taskRepository->findOneByTitle('test-taskRepos');
    //     $this->client->request('GET', '/tasks/' . $id->getId() . '/delete');
    //     $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

    //     $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list'));
    //     $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    // }

    // public function createTaskForm($user, $data)
    // {
    //     $this->client->loginUser($this->adminUser);
    //     $crawler = $this->client->request('GET', '/tasks/create');
    //     $createButton = $crawler->selectButton("Ajouter");
    //     $form = $createButton->form();
    //     $form["task[title]"] = $data[0];

    //     $form["task[content]"] = $data[1];
    //     $form["task[isDone]"] = $data[2];

    //     return $this->client->submit($form);

    // }
}
