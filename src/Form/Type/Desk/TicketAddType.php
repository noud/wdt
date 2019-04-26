<?php

namespace App\Form\Type\Desk;

use App\Form\Data\Desk\TicketAddData;
use App\Zoho\Enum\TicketPriorityEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('contactName', TextType::class, [
                'label' => 'ticket.field.contact_name',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'ticket.field.email',
                'required' => true,
            ])
            ->add('subject', TextType::class, [
                'label' => 'ticket.field.subject',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'ticket.field.description',
                'required' => true,
            ])
            ->add('priority', ChoiceType::class, [
                'label' => 'ticket.field.priority',
                'required' => false,
                'choices' => TicketPriorityEnum::getChoices(),
                'placeholder' => 'ticket.placeholder.priority',
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
