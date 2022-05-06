<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\MembrePersonnel;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MembrePersonnelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, ['label' => "Nom"])
            ->add('prenom', TextType::class, ['label' => "Prénom"])
            ->add('role', TextType::class, ['label' => "Rôle"])
            ->add('equipe', EntityType::class, [
                'class'=>Equipe::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.id_equipe', 'ASC');
                },
                'choice_label' => function($equipe) {
                    return $equipe->getIdEquipe();
                },
                'label' => 'equipe'
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MembrePersonnel::class,
        ]);
    }
}
