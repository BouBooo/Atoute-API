<?php

namespace App\Utils;

use Faker\Factory;
use Faker\Generator;

class FixturesUtils
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    /**
     * @return mixed
     */
    public function getRandomItem(array $tab, int $nbr = 1)
    {
        return $tab[array_rand($tab, $nbr)];
    }

    public function generateParagraph(int $nbr = 5): string
    {
        return '<p>' . implode('</p><p>', $this->faker->paragraphs($nbr)) . '</p>';
    }
}