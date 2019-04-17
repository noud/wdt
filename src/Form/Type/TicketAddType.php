<?php

namespace App\Form\Type;

use App\Form\Data\TicketAddData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketAddType extends AbstractType
{
    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contactName', null, [
                'label' => 'ticket.add.field.contact_name',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'user.add.field.email',
                'required' => true,
            ])
            ->add('subject', null, [
                'label' => 'user.add.field.subject',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'user.add.field.description',
                'required' => true,
            ])
            ->add('priority', ChoiceType::class, [
                'label' => 'user.add.field.priority',
                'required' => true,
                'choices' => [
                    'High' => 'High',
                    'Low' => 'Low',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TicketAddData::class,
            'translation_domain' => 'ticket',
        ]);
    }
}
