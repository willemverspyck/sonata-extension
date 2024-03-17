<?php

declare(strict_types=1);

namespace Spyck\SonataExtension\Form\Transformer;

use RuntimeException;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @phpstan-implements DataTransformerInterface<array, array>
 */
final class RoleTransformer implements DataTransformerInterface
{
    private ?array $roles = null;

    public function __construct(private readonly array $data)
    {
    }

    public function setRoles(?array $roles = null): void
    {
        $this->roles = null === $roles ? [] : $roles;
    }

    public function transform(mixed $value): ?array
    {
        if (null === $value) {
            return null;
        }

        if (null === $this->roles) {
            throw new RuntimeException('Roles not found');
        }

        return $value;
    }

    public function reverseTransform(mixed $value): array
    {
        if (null === $this->roles) {
            throw new RuntimeException('Roles not found');
        }

        $data = array_merge($value, array_diff($this->roles, $this->data));

        sort($data);

        return $data;
    }
}
