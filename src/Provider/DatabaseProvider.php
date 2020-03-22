<?php declare(strict_types=1);

namespace App\Provider;

use App\Interfaces\DatabaseProviderInterface;
use App\Interfaces\EntityInterface;
use App\IsEntityClass;
use PDO;

class DatabaseProvider implements DatabaseProviderInterface
{
    use IsEntityClass;

    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function insert(
        EntityInterface $entity, array $fields = []): ?int
    {
        $data = array_merge(
            $entity->toArray(false), $fields);

        $flag = $this->pdo->prepare(
            $this->makeQuery($entity, array_keys($data)))
            ->execute($data);

        if ($flag) {
            return (int)$this->pdo->lastInsertId();
        }

        return null;
    }

    private function makeQuery(
        EntityInterface $entity, array $keys = []): string
    {
        return sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $entity::getSource(),
            implode(
                ',', array_map(fn($key) => '`' . $key . '`', $keys)),
            implode(
                ',', array_map(fn($key) => ':' . $key, $keys))
        );
    }

    public function clear(string $entity): bool
    {
        $this->isEntityClass($entity);

        /* @var EntityInterface $entity */
        return (bool)$this->pdo->query(
            'DELETE FROM ' . $entity::getSource());
    }


    public function report(
        string $source, string $titleKey,
        string $related, string $relationKey): array
    {
        $data = $this->pdo
            ->query("
SELECT city.$titleKey as city, count(*) as amount 
FROM $source as city 
JOIN $related as dweller on dweller.$relationKey = city.id
GROUP BY city.id
")->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }
}
