<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\User;
use App\Service\FileUploader;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $users = $userRepository->findAll();

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users
        ]);
    }

    /**
     * @Route("/user/info/{id}", name="user_info")
     */
    public function info($id)
    {
        // die('info');
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->find($id);

        return $this->render('user/edit.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user
        ]);
    }

    /**
     * @Route("/user/add", name="user_add")
     */
    public function add(Request $request)
    {
        // die('user_add');
        return $this->render('user/add.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/user/add_action", name="user_add_action")
     */
    public function addAction(Request $request) : Response
    {
        if ($request->getMethod() == 'POST') {
            $full_name = $request->request->get('full_name');
            $username = $request->request->get('username');
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $avatar = $request->files->get('avatar');

            if($full_name == '' || $username == '' || $email == '' || $password == '' || $avatar == NULL) {
                $msg = 'Please enter full info';
                echo $msg;

                return $this->render('user/add.html.twig', [
                    'controller_name' => 'UserController',
                    'msg' => $msg
                ]);
            }

            $uploadDir = 'uploads/avatar/';
            
            $uploader = new FileUploader();
            $filename = $avatar->getClientOriginalName();
            $uploader->upload($uploadDir, $avatar, $filename);

            $user = new User();
            $user->setFullName($full_name);
            $user->setUserName($username);
            $user->setEmail($email);
            $user->setPassword($password);
            $user->setAvatar($uploadDir . $filename);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('user'));
        }
        else {
            $msg = 'Please enter full info';
            echo $msg;

            return $this->render('user/add.html.twig', [
                'controller_name' => 'UserController',
                'msg' => $msg
            ]);
        }
    }

    /**
     * @Route("/user/edit/{id}", name="user_edit")
     */
    public function edit($id)
    {
        // die('edit ' . $id);
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->find($id);

        return $this->render('user/edit.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user
        ]);
    }

    /**
     * @Route("/user/update/{id}", name="user_update")
     */
    public function updateAction($id, Request $request = null)
    {
        if($request == null) {
            return $this->redirect($this->generateUrl('user'));
        }

        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->find($id);

        if($user) {
            if ($request->getMethod() == 'POST') {
                $full_name = $request->request->get('full_name');
                $username = $request->request->get('username');
                $email = $request->request->get('email');
                $password = $request->request->get('password');
    
                if($full_name == '' || $username == '' || $email == '' || $password == '') {
                    $msg = 'Please enter full info';
                    echo $msg;
    
                    return $this->render('user/add.html.twig', [
                        'controller_name' => 'UserController',
                        'msg' => $msg
                    ]);
                }

                $user->setFullName($full_name);
                $user->setUserName($username);
                $user->setEmail($email);
                $user->setPassword($password);
    
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
    
                return $this->redirect($this->generateUrl('user'));
            }
        }

        return $this->redirect($this->generateUrl('user'));
    }

    /**
     * @Route("/user/delete/{id}", name="user_delete")
     */
    public function delete($id)
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->find($id);

        if($user) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        // return $this->index();

        return $this->redirect($this->generateUrl('user'));
    }

}
