<?php

namespace App\Form;

use App\Entity\Resume;
use App\Uploader\Uploader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResumeType extends AbstractType
{
    private Uploader $uploader;

    public function __construct(Uploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setAttribute('cv', $options['cv']);

        $builder
            ->add('title')
            ->add('description', null, [
                'required' => false
            ])
            ->add('cv', FileType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('contractType')
            ->add('activityArea')
            ->add('isPublic', null, [
                'required' => false
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'onPostSubmit']);
    }

    public function onPostSubmit(FormEvent $event): void
    {
        /** @var Resume $resume */
        $resume = $event->getData();
        $form = $event->getForm();

        if ($cv = $form->getConfig()->getAttribute('cv')) {
            $resume->setCv($this->uploader->upload($cv));
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Resume::class,
            'csrf_protection' => false, // Api context
            'allow_extra_fields' => true,
            'cv' => null
        ]);
    }
}
