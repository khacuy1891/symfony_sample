<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\User;

class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="login_show")
     */
    public function index(Request $request)
    {
        $msg = null;
        if($request) {
            $msg = $request->query->get('msg');
        }
        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
            'msg' => $msg
        ]);
    }

    /**
     * @Route("/loginAction", name="login_login_action")
     */
    public function login(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $username = $request->request->get('username');
            $password = $request->request->get('password');

            $userRepository = $this->getDoctrine()->getRepository(User::class);
            $user = $userRepository->findOneBy([
                'user_name' => $username,
                'password' => $password,
            ]);

            if($user) {
                $msg['type'] = 'success';
                $msg['content'] = 'Login success';
                return $this->redirect($this->generateUrl('login_show', ['msg' => $msg]));
            }
        }

        $msg['type'] = 'danger';
        $msg['content'] = 'Login fail';

        return $this->redirectToRoute('login_show', ['msg' => $msg]);
    }
}
