<?php declare(strict_types=1);

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PostCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('title', TextType::class, [
            'constraints' => [
                new Assert\NotBlank([
                    'message' => "The field can not be empty."
                ]),
                new Assert\Length([
                    'min' => 3,
                    'max' => 255,
                    'minMessage' => "The value is too short, min. 3 chars.",
                    'maxMessage' => "The value is too long, max. 255 chars."
                ])
            ]
        ]);

        $builder->add('content', TextType::class);

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Post::class,
            'csrf_protection' => false
        ));
    }
}