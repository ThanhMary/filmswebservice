<?php

namespace App\Form;

use App\Entity\Film;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('released')
            ->add('note')
            ->add('category')
            ->add('Image', FileType::class,[
                'label' => false,
                'mapped' => false,
                'required' => false,
                'contraints'=>[
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/x-png,'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image en jpg ou en png',
                    ])
                ]
              
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Film::class,
        ]);
    }
}
