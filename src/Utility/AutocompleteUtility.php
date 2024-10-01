<?php

declare(strict_types=1);

namespace Spyck\SonataExtension\Utility;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Filter\Model\FilterData;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQueryInterface as ProxyQueryOrmInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class AutocompleteUtility
{
    public static function callbackFilter(ProxyQueryOrmInterface $proxyQuery, string $alias, string $field, FilterData $filterData): bool
    {
        if (null === $filterData->getValue()) {
            return false;
        }

        $andX = $proxyQuery->expr()->andX();

        $keywords = self::getKeywords($filterData->getValue());

        foreach ($keywords as $keyword) {
            $andX->add($proxyQuery->expr()->like(sprintf('%s.%s', $alias, $field), $proxyQuery->expr()->literal(sprintf('%%%s%%', $keyword))));
        }

        $proxyQuery->andWhere($andX);

        return true;
    }

    public static function callbackFilterInJson(ProxyQueryOrmInterface $proxyQuery, string $alias, string $field, FilterData $filterData): bool
    {
        if (null === $filterData->getValue()) {
            return false;
        }

        $proxyQuery
            ->andWhere(sprintf('JSON_SEARCH(%s.%s, \'one\', :value) IS NOT NULL', $alias, $field))
            ->setParameter('value', sprintf('%%%s%%', $filterData->getValue()));

        return true;
    }

    public static function callbackForm(AdminInterface $admin, array $properties, string $value): void
    {
        $datagrid = $admin->getDatagrid();
        $query = $datagrid->getQuery();

        $keywords = self::getKeywords($value);

        foreach ($keywords as $index => $keyword) {
            $orX = $query->expr()->orX();

            foreach ($properties as $property) {
                if (false === $datagrid->hasFilter($property)) {
                    throw new BadRequestHttpException(sprintf('Filter "%s" not found', $property));
                }

                $filter = $datagrid->getFilter($property);

                $alias = $query->entityJoin($filter->getParentAssociationMappings());

                $key = sprintf('%s_%d', $filter->getFormName(), $index + 1);

                $orX->add(sprintf('%s.%s LIKE :%s', $alias, $filter->getFieldName(), $key));

                $query->setParameter($key, sprintf('%%%s%%', $keyword));
            }

            $query->andWhere($orX);
        }
    }

    public static function getKeywords(string $data): array
    {
        $keywords = preg_split('/\s{1,}|\t{1,}/is', $data);

        if (false === $keywords) {
            return [];
        }

        return $keywords;
    }
}
