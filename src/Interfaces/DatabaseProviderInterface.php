<?php declare(strict_types=1);

namespace App\Interfaces;

interface DatabaseProviderInterface extends ProviderInterface
{
    public function report(
        string $source, string $titleKey,
        string $related, string $relationKey): array;
}
