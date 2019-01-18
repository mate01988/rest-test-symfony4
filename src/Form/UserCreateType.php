<?php declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserCreateType extends AbstractType
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

        $builder->add('name', TextType::class, [
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

        $builder->add('lastname', TextType::class, [
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

        $builder->add('password', PasswordType::class, [
            'constraints' => [
                new Assert\NotBlank([
                    'message' => "The field can not be empty."
                ]),
                new Assert\Length([
                    'min' => 8,
                    'minMessage' => "The value is too short, min. 8 chars.",
                    'max' => 255,
                    'maxMessage' => "The value is too long, max. 255 chars."
                ])
            ]
        ]);


    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'csrf_protection' => false,
            'constraints' => array(
                new UniqueEntity(array('fields' => array('email')))
            )
        ));
    }
}