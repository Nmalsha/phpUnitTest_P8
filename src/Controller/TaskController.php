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

            $task->setCreatedAt(new \DateTimeImmutable())
                ->setIsDone(false);

            if ($this->getUser()->getRoles()[0] == "ROLE_USER" || $this->getUser()->getRoles()[0] == "ROLE_ADMIN") {
                $task->setUser($this->getUser());
            } elseif ($this->getuser()->getRoles()[0] == "ROLE_ANONYMOUS") {
                $anonymous = $this->userRepository->findOneBy([
                    'username' => 'Anonyme',
                ]);
                $task->setUser($anonymous);
            }
            // dd($task);
            $this->em->persist($task);
            $this->em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée à la liste.');
            return $this->redirectToRoute('task_list');
        }
        return $this->render('task/create_update.html.twig', [
            'form' => $form->createView()]);
    }
    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     */
    public function editTask(Request $request, $id, Task $task)
    {
        //check the log user is the owner of the task
        //get the task owner id
        $taskOwnerId = $this->taskRepository->findOneBy(['id' => $id])->getUser()->getId();
        //get the log user id
        $connectedUserId = $this->getUser()->getId();

        $tasks = $this->taskRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(TaskType::class, $tasks);
        $form->handleRequest($request);
        if ($connectedUserId === $taskOwnerId) {
            if ($form->isSubmitted() && $form->isValid()) {

                $task->setCreatedAt(new \DateTimeImmutable());
                $this->em->flush();
                return $this->redirectToRoute('task_list');
            }

        } else {
            $this->addFlash('error', "Vous n'avez pas le doit de editer le tache ");
        }
        return $this->render('task/create_update.html.twig', [
            'task' => $tasks,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/tasks/{id}/delete", name="delete")
     */
    public function deleteTask($id, Task $task)
    {
        //connected user id
        $connectedUserId = $this->getUser()->getId();
        //user id of the task owner
        $taskOwnerId = $this->taskRepository->findOneBy(['id' => $id])->getUser()->getId();
//check if the owner of the task is 'ANONYME'
        // dd($this->taskRepository->findOneBy(['id' => $id])->getUser()->getUsername());
        $userAnonyme = $this->taskRepository->findOneBy(['id' => $id])->getUser()->getUsername();

        //  if (!$userAnonyme == "anonyme") {
        // TODO if the username is anonyme the admin can delete the task
        if ($userAnonyme == "anonyme" && $this->getUser()->getRoles()[0] == "ROLE_ADMIN") {
            $tasks = $this->taskRepository->findOneBy(['id' => $id]);
            $this->em->remove($tasks);
            $this->em->flush();

            $this->addflash(
                'success',
                "La tâche {$tasks->getTitle()} a été supprimé avec succès !"
            );
        }
        if ($connectedUserId === $taskOwnerId) {
            // dump($taskOwnerId, $connectedUserId);
            $tasks = $this->taskRepository->findOneBy(['id' => $id]);
            $this->em->remove($tasks);
            $this->em->flush();

            $this->addflash(
                'success',
                "La tâche {$tasks->getTitle()} a été supprimé avec succès !"
            );

        } else {
            $this->addFlash('error', "Vous n'avez pas le doit de supprimer le tache ");
        }
        //  }
        return $this->redirectToRoute('task_list');
    }
    /**
     * @Route("/tasks/{id}/toggle", name="toggle")
     */
    public function toggleTaskAction($id, Task $task)
    {
        $tasks = $this->taskRepository->findOneBy(['id' => $id]);

        if ($tasks->isIsDone() === false) {
            $tasks->setIsDone(true);
        } elseif ($tasks->isIsDone() === true) {
            $tasks->setIsDone(false);
        }
        $this->em->flush();

        $this->addFlash('success', "Tâche mise à jour");

        return $this->redirectToRoute('task_list');
    }
}
