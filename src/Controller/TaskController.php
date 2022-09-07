<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

class TaskController extends AbstractController
{
    private $taskRepository;
    private $userRepository;
    private $em;
    private $cache;

    public function __construct(TaskRepository $taskRepository,
        EntityManagerInterface $em,
        UserRepository $userRepository,
        CacheInterface $cache
    ) {

        $this->taskRepository = $taskRepository;
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->cache = $cache;

    }

    /**
     * @Route("/tasks", name="task_list")
     */
    public function listAction(Request $request, PaginatorInterface $paginator)
    {
        $this->cache->delete('isDone');
        $tasks = $this->cache->get('isDone', function () {
            // return $this->taskRepository->findBy(['isDone' => "0"]);
            return $this->taskRepository->findBy(array('isDone' => '0'), array('createdAt' => 'DESC'));
            // return $this->taskRepository->findBy(array(), array('isDone' => 'ASC', 'createdAt' => 'DESC'));

        });

        // $listTasks = $this->taskRepository->findBy(array(), array('isDone' => 'ASC', 'createdAt' => 'DESC'));
        $taskspag = $paginator->paginate(
            $tasks,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('task/list.html.twig', ['tasks' => $taskspag]);
    }

    /**
     * @Route("/tasksdone", name="task_treated")
     */
    public function listTaskIsDone(Request $request, PaginatorInterface $paginator)
    {
        $this->cache->delete('isDone');
        $tasks = $this->cache->get('isDone', function () {
            return $this->taskRepository->findBy(array('isDone' => '1'), array('createdAt' => 'DESC'));

        });

        $taskspag = $paginator->paginate(
            $tasks,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('task/list.html.twig', ['tasks' => $taskspag]);
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
            }
            // dd($task);
            $this->em->persist($task);
            $this->em->flush();
            //delete cache key
            $this->cache->delete('isDone');
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

        //check if the connected user is admin
        $isadmin = $this->getUser()->getRoles()[0] == "ROLE_ADMIN";

        $form = $this->createForm(TaskType::class, $tasks);
        $form->handleRequest($request);
        if ($connectedUserId === $taskOwnerId || $isadmin && $this->taskRepository->findOneBy(['id' => $id])->getUser()->getUsername() == "Anonyme") {
            if ($form->isSubmitted() && $form->isValid()) {

                $task->setCreatedAt(new \DateTimeImmutable());
                $this->em->flush();

                //delete cache key
                $this->cache->delete('isDone');

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
    public function deleteTask($id)
    {
        //connected user id
        $connectedUser = $this->getUser();
        // dd($connectedUser);
        $connectedUserId = $connectedUser->getId();

        //user id of the task owner
        $taskOwnerId = $this->taskRepository->findOneBy(['id' => $id])->getUser()->getId();
//check if the owner of the task is 'ANONYME'

        $isadmin = $this->getUser()->getRoles()[0] == "ROLE_ADMIN";

        //  if the username is anonyme the admin can delete the task
        if ($connectedUserId === $taskOwnerId && $isadmin && $this->taskRepository->findOneBy(['id' => $id])->getUser()->getUsername() == "Anonyme") {
            $tasks = $this->taskRepository->findOneBy(['id' => $id]);
            $this->em->remove($tasks);
            $this->em->flush();
            //delete cache key
            $this->cache->delete('task_list');
            $this->addflash(
                'success',
                "La tâche {$tasks->getTitle()} a été supprimé avec succès !"
            );
        }
        if ($connectedUserId === $taskOwnerId) {

            $tasks = $this->taskRepository->findOneBy(['id' => $id]);
            $this->em->remove($tasks);
            $this->em->flush();

            //delete cache key
            $this->cache->delete('isDone');

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
    public function toggleTaskAction($id)
    {
        $tasks = $this->taskRepository->findOneBy(['id' => $id]);

        if ($tasks->isIsDone() === false) {
            $tasks->setIsDone(true);
        } elseif ($tasks->isIsDone() === true) {
            $tasks->setIsDone(false);
        }
        $this->em->flush();

        //delete cache key
        $this->cache->delete('task_list');

        $this->addFlash('success', "Tâche mise à jour");

        return $this->redirectToRoute('task_list');
    }

}
