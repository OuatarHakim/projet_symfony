<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\Match;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'input' => 'string'
            ])
            ->add('score_equipe1', NumberType::class, ['label' => "Score équipe 1"])
            ->add('score_equipe2', NumberType::class, ['label' => "Score équipe 2"])
            ->add('equipe1', EntityType::class, [
                'class'=>Equipe::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.id_equipe', 'ASC');
                },
                'choice_label' => function($equipe) {
                    return $equipe->getIdEquipe();
                },
                'label' => 'Équipe 1'
            ])
            ->add('equipe2', EntityType::class, [
                'class'=>Equipe::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.id_equipe', 'ASC');
                },
                'choice_label' => function($equipe) {
                    return $equipe->getIdEquipe();
                },
                'label' => 'Équipe 2 :'
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Match::class,
        ]);
    }
}
