<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

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
            ])
            ->add('cover', FileType::class, [
                'label' => 'Обложка книги',
                'required' => true,
                'mapped' => false,
            ])
            ->add('file', FileType::class, [
                'label' => 'Файл книги',
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5120k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Загружен некорректный PDF-документ',
                    ])
                ],
            ])
            ->add('allow_download', CheckboxType::class, [
                'label' => 'Разрешить скачивание',
                'required' => false,
            ])
            ->add('read_at', DateTimeType::class, [
                'label' => 'Дата прочтения',
                'required' => true,
                'widget' => 'single_text',
            ])
            ->add('author', TextType::class, [
                'label' => 'Автор книги',
                'required' => true,
                'mapped' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Добавить',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
