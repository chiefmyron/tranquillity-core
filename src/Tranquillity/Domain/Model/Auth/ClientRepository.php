<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Model\Auth;

interface ClientRepository
{
    public function nextIdentity(): ClientId;

    public function add(Client $client): void;

    public function remove(Client $client): void;

    public function update(Client $client): void;

    public function findById(ClientId $id): ?Client;

    public function findByName(string $name): ?Client;
}
