<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class,['label'=>false,'attr'=>['placeholder'=>'Lastname','class'=>'form-control']])
            ->add('prenom',TextType::class,['label'=>false,'attr'=>['placeholder'=>'Firstname','class'=>'form-control']])
            ->add('date_naissance',DateType::class,[ 'placeholder' => [
                'Year' => 'Year', 'Month' => 'Month', 'Day' => 'Day'],'years' => range(date('Y') - 100, date('Y'))])
            ->add('numTel',TextType::class,['label'=>false,'attr'=>['placeholder'=>'Phone','class'=>'form-control']])
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
                    'Client' => 'Client',
                    'Freelancer' => 'Freelancer'
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
