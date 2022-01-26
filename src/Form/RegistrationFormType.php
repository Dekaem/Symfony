<?php

namespace App\Form;

use App\Entity\Association;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', null, ['label'=>'Prénom'])
            ->add('lastname', null, ['label'=>'Nom'])
            ->add('phone', null, ['label'=>'Téléphone'])
            ->add('association', EntityType::class, [
                'label'=>'Association',
                'class' => Association::class,
                'choice_label' => 'name',
                'expanded' => false,
                'multiple' => false,
                'placeholder' => '------',
                'required' => true
            ])
            ->add('email', null, ['label'=>'Email'])
            ->add('plainPassword', PasswordType::class, [ 
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label'=>'Mot de passe',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe est trop court ({{ limit }} caractères minimum).',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                        'maxMessage' => 'Votre mot de passe ne doit pas excéder {{ limit }} caractères.'
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label'=>"J'accepte les termes et les conditions.",
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('register', SubmitType::class, ['label'=>"S'inscrire"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
