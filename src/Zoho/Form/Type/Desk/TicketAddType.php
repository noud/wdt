<?php

namespace App\Zoho\Form\Type\Desk;

use App\Zoho\Form\Data\Desk\TicketAddData;
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
                'label' => 'ticket.field.contact_name',
                'disabled' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'ticket.field.email',
                'disabled' => true,
            ])
            ->add('subject', null, [
                'label' => 'ticket.field.subject',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'ticket.field.description',
                'required' => true,
            ])
            ->add('priority', ChoiceType::class, [
                'label' => 'ticket.field.priority',
                'required' => true,
                'choices' => [
                    '-Geen-' => '',
                    'Hoog' => 'High',
                    'Gemiddeld' => 'Medium',
                    'Laag' => 'Low',
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
