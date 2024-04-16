<?php

declare(strict_types=1);

namespace Spyck\SonataExtension\Security\Handler;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Security\Handler\SecurityHandlerInterface;
use Spyck\SonataExtension\Security\SecurityInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

final class RoleSecurityHandler implements SecurityHandlerInterface
{
    public function __construct(#[Autowire(service: 'security.authorization_checker')] private readonly AuthorizationCheckerInterface $authorizationChecker, #[Autowire(value: '%sonata.admin.configuration.security.role_super_admin%')] private readonly string $roleSuperAdmin)
    {
    }

    public function isGranted(AdminInterface $admin, $role, object $object = null): bool
    {
        if (false === str_starts_with($role, 'ROLE_')) {
            $role = sprintf($this->getBaseRole($admin), $role);
        }

        $roleForAll = sprintf($this->getBaseRole($admin), 'ALL');

        try {
            return $this->authorizationChecker->isGranted($this->roleSuperAdmin) || $this->authorizationChecker->isGranted($role, $object) || $this->authorizationChecker->isGranted($roleForAll, $object);
        } catch (AuthenticationCredentialsNotFoundException) {
            return false;
        }
    }

    public function getBaseRole(AdminInterface $admin): string
    {
        $code = null;

        if ($admin instanceof SecurityInterface) {
            $code = $admin->getRole();
        }

        if (null === $code) {
            return sprintf('ROLE_%s_%%s', str_replace('.', '_', strtoupper($admin->getCode())));
        }

        return sprintf('ROLE_%s_%%s', $code);
    }

    public function buildSecurityInformation(AdminInterface $admin): array
    {
        return [];
    }

    public function createObjectSecurity(AdminInterface $admin, object $object): void
    {
    }

    public function deleteObjectSecurity(AdminInterface $admin, object $object): void
    {
    }
}
