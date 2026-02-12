<?php

namespace App\Form;

use AllowDynamicProperties;
use App\Form\Autocomplete\PlayerAutocompleteField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AllowDynamicProperties]
class SearchPlayerType extends AbstractType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('player', PlayerAutocompleteField::class, [
                'tom_select_options' => [
                    'openOnFocus' => false,
                    'loadingClass' => 'loading loading-spinner loading-xs',
                    'loadThrottle' => 500,
                    'placeholder' => ucfirst($this->translator->trans('form.search_player_placeholder', [], 'player')),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
