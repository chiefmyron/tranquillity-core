<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Service\User;

use Tranquillity\Domain\Model\User\Password;
use Tranquillity\Domain\Model\User\HashedPassword;

interface HashingService
{
    public function hash(Password $password): HashedPassword;

    public function verify(Password $password, HashedPassword $hashedPassword): bool;
}
