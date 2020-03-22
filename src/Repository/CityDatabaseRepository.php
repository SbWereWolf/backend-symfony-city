<?php declare(strict_types=1);

namespace App\Repository;

use App\Interfaces\DatabaseProviderInterface;
use App\Repository;

class CityDatabaseRepository extends Repository
{
    public function __construct(
        DatabaseProviderInterface $provider, string $entity)
    {
        parent::__construct($provider, $entity);
    }

    public function report(string $related, string $relationKey): array
    {
        /* @var DatabaseProviderInterface $provider */
        $provider = $this->provider;
        $titleKey = $this->entity::getNameKey();
        $source = $this->entity::getSource();

        return $provider->report(
            $source, $titleKey, $related, $relationKey);
    }
}
