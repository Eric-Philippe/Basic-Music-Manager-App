<?php

namespace App\Form;

use App\Entity\Chanson;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Artiste;

class ChansonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        // Put a default value of today's date in the date field
        $builder
            ->add('titre', TextType::class, ['required' => true])
            ->add('dateSortie', DateType::class, ['required' => true, 'years' => range(1900, 2023), 'data' => new \DateTime()])
            ->add('genre', TextType::class, ['required' => true])
            ->add('langue', TextType::class, ['required' => true])
            ->add('photoCouverture', UrlType::class, ['required' => false])
            ->add('artistes', EntityType::class, [
                'class' => Artiste::class,
                'choice_label' => 'FullTitle', // Supposant que vous avez une méthode getFullTitle dans votre entité Artiste
                'multiple' => true,
                'expanded' => false,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Chanson::class,
        ]);
    }
}


