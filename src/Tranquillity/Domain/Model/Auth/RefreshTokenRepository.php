<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Model\Auth;

interface RefreshTokenRepository
{
    public function nextIdentity(): RefreshTokenId;

    public function add(RefreshToken $refreshToken): void;

    public function remove(RefreshToken $refreshToken): void;

    public function update(RefreshToken $refreshToken): void;

    public function findById(RefreshTokenId $id): ?RefreshToken;

    public function findByToken(string $token): ?RefreshToken;
}
