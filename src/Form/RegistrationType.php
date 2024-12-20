<?php

// src/Form/RegistrationType.php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control mb-2'],
                'required' => true,
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'attr' => ['class' => 'form-control mb-2'],
                'required' => true,
            ])
            ->add('firstname', Type\TextType::class, [
                'label' => 'Prénom',
                'attr' => ['class' => 'form-control mb-2'],
                'required' => true,
            ])
            ->add('lastname', Type\TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control mb-2'],
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => "S'enregistrer",
                'attr' => ['class' => 'btn btn-primary w-100']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
