<?php

declare(strict_types=1);

namespace Spyck\SonataExtension\Filter;

use Sonata\DoctrineORMAdminBundle\Filter\AbstractDateFilter;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\DateType;

#[AutoconfigureTag(name: 'sonata.admin.filter.type', attributes: [
    'alias' => 'doctrine_orm_date_range',
])]
final class DateRangeFilter extends AbstractDateFilter
{
    /**
     * This is a range filter.
     *
     * @var bool
     */
    protected $range = true;

    /**
     * This filter has time.
     *
     * @var bool
     */
    protected $time = false;

    public function getDefaultOptions(): array
    {
        return [
            'field_options' => [
                'field_options' => [
                    'format' => DateType::HTML5_FORMAT,
                ],
            ],
            'field_type' => DateRangePickerType::class,
        ];
    }
}
