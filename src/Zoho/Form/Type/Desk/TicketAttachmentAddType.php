<?php

namespace App\Zoho\Form\Type\Desk;

use App\Zoho\Form\Data\Desk\TicketAttachmentAddData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketAttachmentAddType extends AbstractType
{
    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isPublic', null, [
                'label' => 'ticket_comment.field.content',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TicketAttachmentAddData::class,
            'translation_domain' => 'ticket',
        ]);
    }
}
