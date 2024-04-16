<?php

declare(strict_types=1);

namespace Spyck\SonataExtension\Security;

interface SecurityInterface
{
    public function getRole(): ?string;
}
