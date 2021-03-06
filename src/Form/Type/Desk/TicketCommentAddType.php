<?php

namespace App\Form\Type\Desk;

use App\Form\Data\Desk\TicketCommentAddData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketCommentAddType extends AbstractType
{
    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'ticket_comment.field.content',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TicketCommentAddData::class,
            'translation_domain' => 'ticket',
        ]);
    }
}
