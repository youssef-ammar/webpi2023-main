<?php

namespace App\Form;

use App\Entity\Evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Intervention\Image\ImageManagerStatic as Image;

class Evenement1Type extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('nom_ev')
            ->add('date_ev')
            ->add('heure_ev')
            ->add('emplacement')
            ->add('region')
            ->add('id_type')
            //   on ajout un champ poour tapperla recherche

            ->add('image', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
      
        ]);
    }
}
