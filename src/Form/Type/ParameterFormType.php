<?php

declare(strict_types=1);

namespace Spyck\SonataExtension\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ParameterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('key', TextType::class)
            ->add('value', TextType::class);
    }
}
