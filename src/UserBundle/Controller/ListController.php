<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use UserBundle\Entity\User;
use UserBundle\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListController extends Controller
{

    
    /**
     * @Route("/admin/users", name="users")
     */
    public function userAction()
    {
        $repository = $this->getDoctrine()
            ->getRepository('UserBundle:User');
        $users = $repository->findAll();

        return $this->render('UserBundle:List:list.html.twig', array('users' => $users));
    }

    /**
    * @Route("admin/users/delete/{id}", requirements={"id" = "\d+"}, defaults={"id" = 0})
    * @Template()
    */
    public function deleteAction($id)
    {
        if ($id == 0){ // no user id entered
            return $this->redirect($this->generateUrl('users'), 301);
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->find($id);

        if (!$user) { // no user in the system
            throw $this->createNotFoundException(
                'No user found for id '.$id
            );
        } else {
            $em->remove($user);
            $em->flush();
            return $this->redirect($this->generateUrl('users'), 301);
        }
    }   

    /**
    * @Route("admin/users/edit/{id}", requirements={"id" = "\d+"}, defaults={"id" = 0}, name="edit")
    * @Template()
    */
    public function editAction($id, Request $request)
    {        
        if ($id == 0){ // no user id entered
            return $this->redirect($this->generateUrl('users'), 301);
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->find($id);
        // echo '<pre>';print_r($user);exit;

        if (!$user) { // no user in the system
            throw $this->createNotFoundException(
                'No user found for id '.$id
            );
        } else {
            $originalPassword = $user->getPassword();
            $user->setPassword($originalPassword);                
            $form = $this->createFormBuilder($user)                
                ->add('firstName', 'text', ['label' => 'First Name'])
                ->add('lastName', 'text', ['label' => 'Last Name'])
                ->add('email', 'email', ['label' => 'email'])
                ->add('recordId', 'text', ['label' => 'Record ID'])
                ->add('roles', 'choice', array(
                    'choices' => array(
                        'Admin' => 'ROLE_ADMIN',
                        'Super Admin' => 'ROLE_SUPER_ADMIN',
                    ),
                    'choices_as_values' => true,
                    'choice_label' => function ($allChoices, $currentChoiceKey) {
                        return $currentChoiceKey;
                    },
                ))
                ->add('save', 'submit', ['label' => 'Register'])
                ->getForm();                
                
                $form->handleRequest($request);
                 if ($request->getMethod() == 'POST') {
                    if ($form->isValid()) {
                        $em->flush();
                        $this->redirect($this->generateUrl('users'), 301);
                        // return new Response('News updated successfully');
                        return $this->redirect($this->generateUrl('users'), 301);
                    } 
                }
            $build['form'] = $form->createView();
            $build['user'] = array('id' => $id );

            return $this->render('UserBundle:List:edit.html.twig', $build);
        }
    }

    /**
    * @Route("admin/users/active/{id}", requirements={"id" = "\d+"}, defaults={"id" = 0})
    * @Template()
    */
    public function activeAction($id)
    {
        if ($id == 0){ // no user id entered
            return $this->redirect($this->generateUrl('users'), 301);
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->find($id);

        if (!$user) { // no user in the system
            throw $this->createNotFoundException(
                'No user found for id '.$id
            );
        } else {
            $originalPassword = $user->getPassword();
            $user->setPassword($originalPassword);
            $enabled = $user->getEnabled();
            if($enabled == 1) {
                $user->setEnabled(0);
            } else {
                $user->setEnabled(1);
            }
            $em->flush();
            return $this->redirect($this->generateUrl('users'), 301);
        }
    }
}