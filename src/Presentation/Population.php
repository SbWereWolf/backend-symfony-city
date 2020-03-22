<?php


namespace App\Presentation;

class Population
{
    /**
     * @var string
     */
    private string $city;
    /**
     * @var int
     */
    private int $amount;

    private function __construct(string $city, int $amount)
    {
        $this->city = $city;
        $this->amount = $amount;
    }

    /**
     * @param string $city
     * @param int $amount
     * @return static
     */
    public static function make($city, $amount): self
    {
        $dwellers = new static((string)$city, (int)$amount);

        return $dwellers;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }
}