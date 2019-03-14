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
     * @Rest\Post("/users")
     */
    public function postAction(Request $request)
    {
        $data = new User;
        $name = $request->get('name');
        if(empty($name)) {
            return new View("Please specify the user name before proceeding.", Response::HTTP_NOT_ACCEPTABLE); 
        } 
        $data->setName($name);
        $this->getDoctrine()->getRepository('App:User')->persist($data);
        return new View("User Added Successfully.", Response::HTTP_OK);
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
