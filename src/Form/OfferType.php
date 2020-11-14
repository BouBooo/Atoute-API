<?php

namespace App\Form;

use App\Entity\Offer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'empty_data' => ''
            ])
            ->add('description', null, [
                'empty_data' => ''
            ])
            ->add('start_at', null, [
                'required' => false,
                'mapped' => false
            ])
            ->add('end_at', null, [
                'required' => false,
                'mapped' => false
            ])
            ->add('city')
            ->add('postal_code', null, [
                'empty_data' => ''
            ])
            ->add('salary', null, [
                'required' => false
            ])
            ->add('type')
            ->add('activity')
            ->add('status')
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'onPostSubmit']);
    }

    public function onPostSubmit(FormEvent $event): void
    {
        /** @var Offer $offer */
        $offer = $event->getData();
        $form = $event->getForm();

        if ($startAt = $form->get('start_at')->getData()) {
            $offer->setStartAt((new \DateTime())->setTimestamp($startAt));
        }

        if ($endAt = $form->get('end_at')->getData()) {
            $offer->setEndAt((new \DateTime())->setTimestamp($endAt));
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offer::class,
            'csrf_protection' => false, // Api context
            'allow_extra_fields' => true
        ]);
    }
}