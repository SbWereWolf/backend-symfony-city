<?php declare(strict_types=1);

namespace App\Services;

use App\Entity\City;
use App\Entity\User;
use App\Repository\CityDatabaseRepository;
use App\Repository\CityFileRepository;
use App\Repository\UserDatabaseRepository;
use App\Repository\UserFileRepository;
use DomainException;
use stdClass;

class Parser
{
    private $fileForCity;
    private $fileForUser;
    private $counter = 0;
    /**
     * @var CityDatabaseRepository
     */
    private CityDatabaseRepository $dbForCity;
    /**
     * @var UserDatabaseRepository
     */
    private UserDatabaseRepository $dbForUser;

    public function __construct(
        CityFileRepository $fileForCity,
        UserFileRepository $fileForUser,
        CityDatabaseRepository $dbForCity,
        UserDatabaseRepository $dbForUser)
    {
        $this->fileForCity = $fileForCity;
        $this->fileForUser = $fileForUser;
        $this->dbForCity = $dbForCity;
        $this->dbForUser = $dbForUser;
    }

    public function __invoke(string $file): ?int
    {
        $oFile = fopen(VAR_DIR . $file, 'r');
        if ($oFile === false) {
            return null;
        }

        while (($line = fgets($oFile)) !== false) {
            $this->processLine($line);
        }

        if (!feof($oFile)) {
            throw new DomainException('Конец файла не достигнут');
        }
        fclose($oFile);

        return $this->counter;
    }

    private function processLine(string $line): bool
    {
        $data = json_decode(trim($line), false, 13, JSON_THROW_ON_ERROR);
        $city = $this->saveData($data);
        if (null !== $city) {
            $this->counter++;
            return true;
        }

        return false;
    }

    private function saveData(stdClass $data): ?City
    {
        $city = new City($data->name, $data->country);
        if ($this->fileForCity->insert($city) === false
            || $this->dbForCity->insert($city) === false) {
            return null;
        }

        foreach ($data->users as $user) {
            $user = new User($user->name, $user->phone);
            if ($this->fileForUser->insert($user, [$user::getParentKey() => $city->getId()]) !== false
                && $this->dbForUser->insert($user, [$user::getParentKey() => $city->getId()]) !== false) {
                $city->attachUser($user);
            }
        }

        return $city;
    }
}
