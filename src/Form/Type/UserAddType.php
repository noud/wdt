<?php

namespace App\Form\Type;

use App\Form\Data\UserAddData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAddType extends AbstractType
{
    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('companyName', null, [
                'label' => 'user.add.field.company_name',
                'required' => true,
            ])
            ->add('firstName', null, [
                'label' => 'user.add.field.first_name',
                'required' => true,
            ])
            ->add('lastName', null, [
                'label' => 'user.add.field.last_name',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'user.add.field.email',
                'required' => true,
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'user.add.error.password_not_matching',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => ['label' => 'user.add.field.password'],
                'second_options' => ['label' => 'user.add.field.password_repeat'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserAddData::class,
            'translation_domain' => 'user',
        ]);
    }
}
