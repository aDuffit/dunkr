<?php

namespace App\Form;

use App\Entity\Player;
use App\Form\Autocomplete\PlayerAutocompleteField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

class ComparisonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = new DynamicFormBuilder($builder);
        $builder
            ->add('player1', PlayerAutocompleteField::class, [
                'required' => false,
            ])
            ->addDependent('player2', 'player1', function (DependentField $field, ?Player $player) {
                if (!$player instanceof Player) {
                    return;
                }

                $field->add(PlayerAutocompleteField::class);
            });
    }
}
