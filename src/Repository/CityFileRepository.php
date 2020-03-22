<?php declare(strict_types=1);

namespace App\Repository;

use App\Interfaces\FileProviderInterface;
use App\Repository;

class CityFileRepository extends Repository
{
    public function __construct(FileProviderInterface $provider, string $entity)
    {
        parent::__construct($provider, $entity);
    }
}
