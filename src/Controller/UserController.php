<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\User;

class UserController extends AbstractController
{
    /**
     * @Route("/user/new", name="user_new")
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {   
        $name = $request->query->get('name');
        $user = new User();
        if (!$name) {
            $user->setName('Default Name');
        }
        else {
            $user->setName($name);
        }

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($user);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return $this->redirectToRoute('user_show', [
            'id' => $user->getId()
        ]);   
    }

    /**
     * @Route("/user/{id}", name="user_show")
     */
    public function show($id)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$id
            );
        }

        return new Response('You have reached the user with the name: '.$user->getName());
    }

    /**
     * @Route("/user/edit/{id}")
     */
    public function update($id, EntityManagerInterface $entityManager)
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$id
            );
        }

        $user->setName('New product name!');
        $entityManager->flush();

        return $this->redirectToRoute('user_show', [
            'id' => $user->getId()
        ]);
    }

    /**
     * @Route("/user/remove/{id}")
     */
    public function remove($id, EntityManagerInterface $entityManager)
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$id
            );
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return new Response('You have removed the user with name: '.$user->getName());
    }
}
