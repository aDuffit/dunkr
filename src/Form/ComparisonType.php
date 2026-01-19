<?php

namespace App\Form;

use App\Entity\Player;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ComparisonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('player1', EntityType::class, [
                'class' => Player::class,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'autocomplete' => true,
                'required' => false,
            ])
            ->add('player2', EntityType::class, [
                'class' => Player::class,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'autocomplete' => true,
                'required' => false,
            ]);
    }
}
