<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Domain\Service\Auth;

use Tranquillity\Domain\Model\Auth\Password;
use Tranquillity\Domain\Model\Auth\HashedPassword;
use Tranquillity\Domain\Service\Auth\HashingService;

class NativePhpHashingService implements HashingService
{
    private $algorithm;
    private array $options;

    public function __construct($algorithm, array $options = [])
    {
        $this->algorithm = $algorithm;
        $this->options = $options;
    }

    public function hash(Password $password): HashedPassword
    {
        $hash = password_hash($password->toString(), $this->algorithm, $this->options);
        return new HashedPassword($hash);
    }

    public function verify(Password $password, HashedPassword $hashedPassword): bool
    {
        return password_verify($password->toString(), $hashedPassword->toString());
    }
}
