<?php declare(strict_types=1);

namespace App\Services;

use App\Presentation\Population;
use App\Repository\CityDatabaseRepository;
use App\Repository\UserDatabaseRepository;

class Reporter
{
    /**
     * @var CityDatabaseRepository
     */
    private CityDatabaseRepository $dbForCity;
    /**
     * @var UserDatabaseRepository
     */
    private UserDatabaseRepository $dbForUser;

    public function __construct(
        CityDatabaseRepository $dbForCity,
        UserDatabaseRepository $dbForUser)
    {
        $this->dbForCity = $dbForCity;
        $this->dbForUser = $dbForUser;
    }

    /**
     * @return Population[]
     */
    public function __invoke(): array
    {
        $data = $this->dbForCity->report(
            $this->dbForUser->getSource(),
            $this->dbForUser->getParentKey());

        $populations = [];
        foreach ($data as $row) {
            $populations[] =
                Population::make($row['city'], $row['amount']);
        }

        return $populations;
    }
}
