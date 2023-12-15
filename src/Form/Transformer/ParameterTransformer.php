<?php

declare(strict_types=1);

namespace Spyck\SonataExtension\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @phpstan-implements DataTransformerInterface<array, array>
 */
final class ParameterTransformer implements DataTransformerInterface
{
    public function transform(mixed $data): ?array
    {
        return $data;
    }

    public function reverseTransform(mixed $data): array
    {
        $returnData = [];

        foreach ($data as $row) {
            if (false === array_key_exists('key', $row) || false === array_key_exists('value', $row)) {
                throw new TransformationFailedException();
            }

            $key = $row['key'];
            $value = $row['value'];

            if (false !== array_key_exists($key, $returnData)) {
                throw new TransformationFailedException('Duplicate key detected');
            }

            $returnData[$key] = $value;
        }

        return $returnData;
    }
}
