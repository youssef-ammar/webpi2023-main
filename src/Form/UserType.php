<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
/*
Cette ligne définit une nouvelle classe UserType,
qui étend AbstractType et sert à définir un type de formulaire personnalisé pour l'entité User.
*/
class UserType extends AbstractType
{
/*
Ce code est une méthode appelée buildForm() de la classe "UserType" qui étend la classe AbstractType de Symfony.
Cette méthode permet de construire un formulaire pour l'entité "User".
*/ 

    public function buildForm(FormBuilderInterface $builder, array $options): void
//La méthode prend deux arguments: $builder et $options. 
//La méthode ajoute des champs au formulaire en utilisant la méthode add() du $builder.
    {
        $builder
            ->add('nom',TextType::class,['label'=>false,'attr'=>['placeholder'=>'Lastname','class'=>'form-control']])
            ->add('prenom',TextType::class,['label'=>false,'attr'=>['placeholder'=>'Firstname','class'=>'form-control']])
            ->add('date_naissance',DateType::class,[ 'placeholder' => [
                'Year' => 'Year', 'Month' => 'Month', 'Day' => 'Day'],'years' => range(date('Y') - 100, date('Y'))])
            ->add('numTel',TextType::class,['label'=>false,'attr'=>['placeholder'=>'Phone','class'=>'form-control']])
            //changement le type d'adresse de texte  a liste des roulants ("choice type")
            ->add('adresse',ChoiceType::class,[
                'choices'=>[
                    'Tunis'=>'Tunis',
                    'Sfax'=>'Sfax',
                    'Sousse'=>'Sousse',
                    'Kairouan'=>'Kairouan',
                    'Métouia'=>'Métouia',
                    'Kebili'=>'Kebili',
                    'Bizerte'=>'Bizerte',
                    'Sidi Bouzid'=>'Sidi Bouzid',
                    'Gabés'=>'Gabés',
                    'Ariana'=>'Ariana',
                    'Béja'=>'Béja',

                ]
            ])
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Freelancer' => 'Freelancer',
                    'Client' => 'client',
                ],
                'expanded' => true
            ])
            ->add('email',TextType::class,['label'=>false,'attr'=>['placeholder'=>'Email','class'=>'form-control']])

            ->add('password',PasswordType::class,['label'=>false,'attr'=>['placeholder'=>'Password','class'=>'form-control']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
