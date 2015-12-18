<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RegistrationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', 'text', ['label' => 'First Name'])
            ->add('lastName', 'text', ['label' => 'Last Name'])
            ->add('email', 'email', ['label' => 'email'])
            ->add('password', 'repeated',
                array(
                'type' => 'password', 'invalid_message' => 'Passwords do not match'
            ))
            ->add('recordId', 'text', ['label' => 'Record ID'])
            ->add('roles', 'choice',
                array(
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
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'registration';
    }
}
