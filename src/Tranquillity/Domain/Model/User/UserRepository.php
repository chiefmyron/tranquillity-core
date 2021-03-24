<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Model\User;

interface UserRepository
{
    public function nextIdentity(): UserId;

    public function add(User $user): void;

    public function remove(User $user): void;

    public function update(User $user): void;

    public function findById(UserId $id): ?User;

    public function findByUsername(string $username): ?User;

    public function list(array $filterConditions, array $sortConditions, int $pageNumber, int $pageSize): UserCollection;
}
