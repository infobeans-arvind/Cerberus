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
    public function loginAction(Request $request)
    {
        $error = "";
        if ($this->getRequest()->isMethod('POST')) {
            $email    = $request->request->get('_email');
            $password = $request->request->get('_password');
            if (!empty($email && $password)) {
                $em       = $this->getDoctrine()->getManager();
                $userData = $em->getRepository('UserBundle:User')->findOneByEmail($email);
                if (isset($userData)) {
                    $result = $this->validateLogin($password,
                        $userData->getPassword(), $userData->getSalt());
                    if (isset($result)) {
                        $url = $this->generateUrl('users');
                        return $this->redirect($url);
                    } else {
                        $error = "Invalid Email or Password!";
                    }
                } else {
                    $error = "Invalid Email or Password!";
                }
            } else {
                $error = "Email or Password should not be blank!";
            }
        }
        return array('error' => $error);
    }

    function validateLogin($pass, $hashed_pass, $salt, $hash_method = 'sha1')
    {
        if (function_exists('hash') && in_array($hash_method, hash_algos())) {
            return ($hashed_pass === hash($hash_method, $salt.$pass));
        }
        return ($hashed_pass === sha1($salt.$pass));
    }
}