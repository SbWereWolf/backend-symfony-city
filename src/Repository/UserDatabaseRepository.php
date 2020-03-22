<?php declare(strict_types=1);

namespace App\Repository;

use App\Interfaces\DatabaseProviderInterface;
use App\Repository;

class UserDatabaseRepository extends Repository
{
    public function __construct(
        DatabaseProviderInterface $provider, string $entity)
    {
        parent::__construct($provider, $entity);
    }

    public function getSource(): string
    {
        return $this->entity::getSource();
    }

    public function getParentKey(): string
    {
        return $result = $this->entity::getParentKey();
    }
}
