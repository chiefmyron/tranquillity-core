<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Model\Auth;

interface AuthorizationCodeRepository
{
    public function nextIdentity(): AuthorizationCodeId;

    public function add(AuthorizationCode $authorizationCode): void;

    public function remove(AuthorizationCode $authorizationCode): void;

    public function update(AuthorizationCode $authorizationCode): void;

    public function findById(AuthorizationCodeId $id): ?AuthorizationCode;

    public function findByCode(string $code): ?AuthorizationCode;
}
