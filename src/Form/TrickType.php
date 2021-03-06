<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('picture', FileType::class, [
                'required' => false,
                'label' => 'add images',
                'mapped' => false,
                'multiple' => true
            ])
            ->add('video', UrlType::class, [
                'required' => false,
                'label' => 'add video',
                'mapped' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '#^(http|https)://(www.dailymotion.com|dai.ly|www.youtube.com|youtu.be)/#',
                        'match'   => True,
                        'message' => 'Url must be  Dailymotion or youtube'
                    ])
            ]
            ])
            ->add('name', TextType::class, [
                "label" => "Name :"
            ])
            ->add('content', TextareaType::class, [
                "label" => "Your description :"
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                "choice_label" => function ($category) {
                    return ($category->getName());
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
