<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Model\Auth;

interface AccessTokenRepository
{
    public function nextIdentity(): AccessTokenId;

    public function add(AccessToken $accessToken): void;

    public function remove(AccessToken $accessToken): void;

    public function update(AccessToken $accessToken): void;

    public function findById(AccessTokenId $id): ?AccessToken;

    public function findByToken(string $token): ?AccessToken;
}
