<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\Joueur;
use App\Entity\Poste;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class JoueurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class, ["label" => "Nom"])
            ->add('prenom',TextType::class, ['label' => "Prénom"])
            ->add('age', NumberType::class, ['label' => "Âge"])
            ->add('taille', NumberType::class, ['label' => "Taille du joueur"])
            ->add('equipe',EntityType::class,[
                'class' => Equipe::class,
                'choice_label' => function($equipe) {
                    return $equipe->getIdEquipe();
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.id_equipe', 'ASC');
                },
                'label' =>'Équipe',
])
            ->add('poste' ,EntityType::class,[
                'class' => Poste::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.nom', 'ASC');
                },
                'choice_label' => function($poste) {
                    return $poste->getNom();
                },
                'label'=>'Poste',
            ])
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Joueur::class,
        ]);
    }
}
