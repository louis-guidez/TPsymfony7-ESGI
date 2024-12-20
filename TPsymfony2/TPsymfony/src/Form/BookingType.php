<?php

// src/Form/BookingType.php
namespace App\Form;

use App\Entity\Booking;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date',
                'html5' => false,
                'attr' => [
                    'class' => 'date-picker',
                    'data-disable-weekends' => 'true',
                    'data-min-date' => 'today'
                ],
            ])
            ->add('time', TimeType::class, [
                'widget' => 'choice',
                'hours' => range(8, 17), // De 08h à 17h
                'minutes' => [0, 30],    // Options pour 00 et 30 minutes
                'label' => 'Heure',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }

    public function validateWeekday($date, ExecutionContextInterface $context)
    {
        $dayOfWeek = (new \DateTime($date))->format('N');
        if ($dayOfWeek >= 6) {
            $context->buildViolation('Veuillez sélectionner un jour de semaine.')
                ->addViolation();
        }
    }

    private function getAvailableTimes(): array
    {
        return [
            '08:00' => '08:00',
            '08:30' => '08:30',
            '09:00' => '09:00',
            '09:30' => '09:30',
            '10:00' => '10:00',
            '10:30' => '10:30',
            '11:00' => '11:00',
            '11:30' => '11:30',
            '12:00' => '12:00',
            '13:00' => '13:00',
            '13:30' => '13:30',
            '14:00' => '14:00',
            '14:30' => '14:30',
            '15:00' => '15:00',
            '15:30' => '15:30',
            '16:00' => '16:00',
            '16:30' => '16:30',
            '17:00' => '17:00',
        ];
    }
}