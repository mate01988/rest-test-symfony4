<?php declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('email', TextType::class, [
            'constraints' => [
                new Assert\NotBlank([
                    'message' => "The field can not be empty."
                ]),
                new Assert\Length([
                    'min' => 3,
                    'max' => 255,
                    'minMessage' => "The value is too short, min. 3 chars.",
                    'maxMessage' => "The value is too long, max. 255 chars."
                ]),
                new Assert\Email([
                    'message' => "The email '{{ value }}' is not a valid email.",
                ])
            ]
        ]);

        $builder->add('password', TextType::class, [
            'constraints' => [
                new Assert\NotBlank([
                    'message' => "The field can not be empty."
                ]),
                new Assert\Length([
                    'min' => 8,
                    'max' => 255,
                    'minMessage' => "The value is too short, min. 8 chars.",
                    'maxMessage' => "The value is too long, max. 255 chars."
                ])
            ]
        ]);


    }

    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults(array(
            'data_class' => User::class,
            'csrf_protection' => false
        ));
    }
}