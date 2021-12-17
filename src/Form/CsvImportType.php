<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CsvImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                    'label' => 'CSV File',
                    'mapped' => false,
                    'required' => true,
//                    'constraints' => [
//                        new File([
//                            'mimeTypes' => [
//                                'text/csv',
//                            ],
//                            'mimeTypesMessage' => 'Veuillez uploader un fichier CSV valide',
//                        ])
//                    ],
                ]
            )
        ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
