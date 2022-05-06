<?php

namespace App\Form;

use App\Entity\Equipe;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id_equipe',TextType::class, ['label' => "Code CIO pays"])
            ->add('nat_equipe',TextType::class, ['label' => "Nationalité"])
            ->add('nbr_victoire', NumberType::class, ['label' => "Nombre de victoires"])
            ->add('nbr_defaite', NumberType::class, ['label' => "Nombre de défaites"])
            ->add('save', SubmitType::class);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
        ]);
    }
}
