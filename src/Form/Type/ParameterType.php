<?php

declare(strict_types=1);

namespace Spyck\SonataExtension\Form\Type;

use Spyck\SonataExtension\Form\Transformer\ParameterTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ParameterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $transformer = new ParameterTransformer();

        $formBuilder
            ->addModelTransformer($transformer)
            ->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event): void {
                $data = $event->getData();

                if (null === $data) {
                    return;
                }

                $returnData = [];

                foreach ($data as $key => $value) {
                    $returnData[] = [
                        'key' => $key,
                        'value' => $value,
                    ];
                }

                $event->setData($returnData);
            }, 1);
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'entry_type' => ParameterFormType::class,
            'allow_add' => true,
            'allow_delete' => true,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'sonata_type_native_collection';
    }

    public function getParent(): ?string
    {
        return CollectionType::class;
    }
}
