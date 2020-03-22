<?php declare(strict_types=1);

namespace App\Interfaces;

interface RepositoryInterface
{
    public function bindEntity(string $entity) : void;
    public function clear() : bool;
    public function insert(EntityInterface &$entity) : bool;
}
