<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use UserBundle\Entity\User;
use UserBundle\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends Controller
{

    /**
     * @Route("/admin/register", name="register")
     */
    public function registerAction(Request $req)
    {

        $em   = $this->getDoctrine()->getManager();
        $form = $this->createForm(new RegistrationType(), new User());
        $form->handleRequest($req);

        if ($form->isValid()) {

            $user = new User();
            $user = $form->getData();

            $user->setCreatedOn(new \DateTime());
            $user->setUpdatedOn(new \DateTime());
            $user->setlocked(new \DateTime());
            $user->setRoles($user->getRoles());
            $user->setEnabled(true);

            $password = $user->getPassword();
            $salt     = $this->randomSalt();
            $user->setPassword(
                $this->create_hash($salt.$password)
            );
            $user->setSalt($salt);
            $user->setFirstName($user->getFirstName());
            $user->setLastName($user->getLastName());
            $user->setRecordId($user->getRecordId());
            $user->setStatusId(1);
            $user->setCreatedBy(1);
            $em->persist($user);
            $em->flush();

            $url = $this->generateUrl('login');
            return $this->redirect($url);
        }

        return $this->render('UserBundle:Registration:register.html.twig',
                ['form' => $form->createView()]);
    }

    public function randomSalt($len = 16)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789`~!@#$%^&*()-=_+';
        $l     = strlen($chars) - 1;
        $str   = '';
        for ($i = 0; $i < $len; ++$i) {
            $str .= $chars[rand(0, $l)];
        }
        return $str;
    }

    public function create_hash($string, $hash_method = 'sha1')
    {
        if (function_exists('hash') && in_array($hash_method, hash_algos())) {
            return hash($hash_method, $string);
        }
        return sha1($string);
    }
}