<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{

    /**
     * @Route("/admin/", name="login")
     * @Template()
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return array(
                'error' => $error,
                'last_username' => $lastUsername,
                );
    }

    /**
     * @Route("/admin/login_check", name="login_check")
     */
    public function loginCheckAction(Request $request)
    {
        $email    = $request->request->get('_email');
        $password = $request->request->get('_password');


        $em = $this->getDoctrine()->getManager();
        $userData = $em->getRepository('UserBundle:User')->findOneByEmail($email);
        $result   = $this->validateLogin($password, $userData->getPassword(),
            $userData->getSalt());
        if ($result) {
            echo "Login Success!!";
            die;
        } else {
             throw new \Exception('Invalid Credentials!');
        }
    }

    function validateLogin($pass, $hashed_pass, $salt, $hash_method = 'sha1')
    {
        if (function_exists('hash') && in_array($hash_method, hash_algos())) {
            return ($hashed_pass === hash($hash_method, $salt.$pass));
        }
        return ($hashed_pass === sha1($salt.$pass));
    }
}