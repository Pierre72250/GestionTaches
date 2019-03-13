<?php

/**
 * @author  Even-Mind
 */


namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', EmailType::class, array(
            'error_bubbling'=>true,
            'required' => true,
            'trim' => true,
            'label' => "Registration.Label.Email",
            'attr' => array('maxlength' => 150)));

        $builder->add('username', null, array(
            'label' => "Registration.Label.UserName",
            'attr' => array('maxlength' => 150)));

        $builder->add('plainPassword', RepeatedType::class, array(
            'error_bubbling'=>true,
            'required' => true,
            'type' => PasswordType::class,
            'options' => array('attr' => array('class' => 'password-field')),
            'invalid_message' => 'Invalide_Password',
            'first_options'  => array('label' => "Registration.Label.Password", 'attr' => array('maxlength' => '40')),
            'second_options' => array('label' => "Registration.Label.ConfirmPassword", 'attr' => array('maxlength' => '40'))));

        $builder->add('validation', SubmitType::class, array('label' => "Créer le compte"));
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'locale' => 'fr',
            'data_class' => User::class
        ));
    }
}