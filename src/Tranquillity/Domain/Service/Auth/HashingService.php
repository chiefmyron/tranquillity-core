<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Service\Auth;

use Tranquillity\Domain\Model\Auth\Password;
use Tranquillity\Domain\Model\Auth\HashedPassword;

interface HashingService
{
    public function hash(Password $password): HashedPassword;

    public function verify(Password $password, HashedPassword $hashedPassword): bool;
}
