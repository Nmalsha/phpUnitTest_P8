<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    public function __construct(TaskRepository $taskRepository, EntityManagerInterface $em, UserRepository $userRepository)
    {
        $this->taskRepository = $taskRepository;
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/tasks", name="task_list")
     */
    public function listAction()
    {
        return $this->render('task/list.html.twig', ['tasks' => $this->taskRepository->findBy(array(), array('isDone' => 'ASC', 'createdAt' => 'DESC'))]);
    }
    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function createAction(Request $request)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $task->setCreatedAt(new \DateTimeImmutable())
            //     ->setIsDone(false);

            // if ($this->getUser()->getRoles()[0] == "ROLE_USER" || $this->getUser()->getRoles()[0] == "ROLE_ADMIN") {
            //     $task->setUser($this->getUser());
            // } elseif ($this->getuser()->getRoles()[0] == "ROLE_ANONYMOUS") {
            //     $anonymous = $this->userRepository->findOneBy([
            //         'username' => 'Anonyme',
            //     ]);
            //     $task->setUser($anonymous);
            // }
            // dd($task);
            // $this->em->persist($task);
            // $this->em->flush();

            // $this->addFlash('success', 'La tâche a été bien été ajoutée à la liste.');
            // return $this->redirectToRoute('task_list');
        }
        return $this->render('task/create_update.html.twig', [
            'form' => $form->createView()]);
    }
    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     */
    public function editTask(Request $request)
    {

    }
}
