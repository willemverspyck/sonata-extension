<?php

declare(strict_types=1);

namespace Spyck\SonataExtension\Form\Type;

use Sonata\AdminBundle\Admin\Pool;
use Spyck\SonataExtension\Form\Transformer\RoleTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RoleType extends AbstractType
{
    public function __construct(private readonly Pool $pool)
    {
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $transformer = new RoleTransformer($this->getRoles());

        $formBuilder
            ->addModelTransformer($transformer)
            ->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event) use ($transformer): void {
                $transformer->setRoles($event->getData());
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, static function (FormEvent $event) use ($transformer): void {
                $transformer->setRoles($event->getForm()->getData());
            });
    }

    public function configureOptions(OptionsResolver $optionResolver): void
    {
        $optionResolver->setDefaults([
            'multiple' => true,
            'choices' => $this->getRoles(),
        ]);
    }

    public function getParent(): ?string
    {
        return ChoiceType::class;
    }

    private function getRoles(): array
    {
        $data = [];

        foreach ($this->pool->getAdminServiceCodes() as $code) {
            $admin = $this->pool->getInstance($code);

            $baseRole = $admin->getSecurityHandler()->getBaseRole($admin);

            foreach (['EDIT', 'LIST', 'CREATE', 'VIEW', 'DELETE', 'EXPORT', 'ALL'] as $role) {
                $data[] = sprintf($baseRole, $role);
            }
        }

        return array_combine($data, $data);
    }
}
