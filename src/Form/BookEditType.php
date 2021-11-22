<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Загружено некорректное изображение обложки',
                    ]),
                ],
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
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var Book $book */
            $book = $event->getData();
            $form = $event->getForm();

            if ($book->getCover() !== null) {
                $form->add('delete_cover', CheckboxType::class, [
                    'label' => 'Удалить обложку',
                    'required' => false,
                    'mapped' => false,
                ]);
            }

            if ($book->getFile() !== null) {
                $form->add('delete_file', CheckboxType::class, [
                    'label' => 'Удалить файл книги',
                    'required' => false,
                    'mapped' => false,
                ]);
            }
        });
    }
}
