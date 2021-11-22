<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class BookType extends AbstractType
{
    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Название книги',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('cover', FileType::class, [
                'label' => 'Обложка книги',
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
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
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
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
            ->add('downloadable', CheckboxType::class, [
                'label' => 'Разрешить скачивание',
                'required' => false,
            ])
            ->add('read_at', DateTimeType::class, [
                'label' => 'Дата прочтения',
                'required' => true,
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(),
                    new DateTime(),
                ],
            ])
            ->add('author', TextType::class, [
                'label' => 'Автор книги',
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Сохранить',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
