<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class BookEditType extends BookType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('cover', FileType::class, [
                'label' => 'Обложка книги',
                'required' => false,
                'mapped' => false,
            ])
            ->add('delete_cover', CheckboxType::class, [
                'label' => 'Удалить обложку',
                'required' => false,
                'mapped' => false,
            ])
            ->add('file', FileType::class, [
                'label' => 'Файл книги',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5120k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Загружен некорректный PDF-документ',
                    ]),
                ],
            ])
            ->add('delete_file', CheckboxType::class, [
                'label' => 'Удалить файл книги',
                'required' => false,
                'mapped' => false,
            ]);
    }
}
