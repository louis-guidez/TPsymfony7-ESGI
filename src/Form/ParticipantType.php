<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Participant;
use Dom\Text;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
            ])
            ->add('city', TextType::class, [
                'label' => 'Location',
                'required' => true,
            ])
            ->add('event', EntityType::class, [
                'class' => Event::class,
                'label' => 'Événement associé',
                'choice_label' => 'name', // Indique à Symfony de montrer la propriété "name" de l'objet Event
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
