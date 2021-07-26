<?php

namespace App\Controller;

use App\Entity\Todolist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends AbstractController
{
  /**
   * @Route("/")
   */
  public function homepage()
  {
    return $this->render("Home/home.html.twig");
  }

  /**
   * @Route("/tasks", name="tasks")
   */
  public function taskspage(EntityManagerInterface $em)
  {
    $repo = $em->getRepository(Todolist::class);
    $tasks = $repo->findAll();

    return $this->render("Tasks/tasks.html.twig", ["tasks" => $tasks]);
  }

  /**
   * @Route("/addtask")
   */
  public function addpage(EntityManagerInterface $em, Request $req)
  {
    $task = $req->request->get("task");
    $finished = $req->request->get("finished");


    if ($task) {
      $newTask = new Todolist();
      $newTask->setTask($task);

      if (!$finished) {
        $newTask->setFinished(false);
      } else {
        $newTask->setFinished(true);
      }

      $em->persist($newTask);
      $em->flush();

      header('Location: ' . '/tasks');
    }

    return $this->render("Tasks/add.html.twig");
  }

  /**
   * @Route("/delete/{id}", name="delete")
   */
  public function deletetask($id, EntityManagerInterface $em)
  {
    $task = $em->getRepository(Todolist::class)->find($id);
    $em->remove($task);
    $em->flush();

    return $this->redirectToRoute("tasks");
  }
}
