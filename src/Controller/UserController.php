<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\User;

class UserController extends FOSRestController
{   

    /**
     * @Rest\Get("/users")
     */
    public function getUsersAction()
    {
        $users = $this->getDoctrine()->getRepository('App:User')->findAll();
        if ($users === null) {
          return new View("No users exist.", Response::HTTP_NOT_FOUND);
        }
        return $users;
    }

    /**
     * @Rest\Get("/users/{id}")
     */
    public function getUserAction($id)
    {
        $user = $this->getDoctrine()->getRepository('App:User')->find($id);
        if ($user === null) {
          return new View("No user exists with this id, try another.", Response::HTTP_NOT_FOUND);
        }
        return $user;
    }

     /**
     * @Rest\Post("/users/register")
     */
    public function registerAction(Request $request)
    {
        $user = new User;
        $name = $request->get('name');
        $password = $request->get('password');
        if(empty($name) || empty($password)) {
            return new View("Please specify the user name and password before proceeding.", Response::HTTP_NOT_ACCEPTABLE); 
        } else if ($this->getDoctrine()->getRepository('App:User')->findOneBy(['name' => $name])){
            return new View("A user already exists with this name! Try another.", Response::HTTP_NOT_ACCEPTABLE); 
        }
        
        $user->setName($name);
        $user->setPassword(password_hash($password, PASSWORD_BCRYPT));
        $this->getDoctrine()->getRepository('App:User')->persist($user);
        return $user;
    }

    /**
     * @Rest\Put("/users/{id}")
     */
    public function updateAction($id, Request $request)
    { 
        $data = new User;
        $name = $request->get('name');
        $en = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('App:User')->find($id);
        if (empty($user)) {
            return new View("This user does not exist, try another.", Response::HTTP_NOT_FOUND);
        } 
        elseif(!empty($name)){
            $user->setName($name);
            $en->flush();
            return new View("User name updated successfully.", Response::HTTP_OK);
        }
        else return new View("User name cannot be empty, try again.", Response::HTTP_NOT_ACCEPTABLE); 
    }

    /**
     * @Rest\Delete("/users/{id}")
     */
    public function deleteAction($id, Request $request)
    { 
        $user = $this->getDoctrine()->getRepository('App:User')->find($id);
        if ($user === null) {
          return new View("No user exists with this id, try another.", Response::HTTP_NOT_FOUND);
        }

        $this->getDoctrine()->getRepository('App:User')->delete($user);
        return new View("User deleted succesfully. ", Response::HTTP_OK);
    }
}
