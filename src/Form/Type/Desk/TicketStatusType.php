<?php

namespace App\Form\Type\Desk;

use App\Form\Data\Desk\TicketStatusData;
use App\Zoho\Enum\TicketStatusEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketStatusType extends AbstractType
{
    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'label' => 'ticket.field.status',
                'required' => false,
                'choices' => TicketStatusEnum::getChoices(),
                'placeholder' => 'ticket.placeholder.status',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TicketStatusData::class,
            'translation_domain' => 'ticket',
        ]);
    }
}
