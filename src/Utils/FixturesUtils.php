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
        $paragraphs = $this->faker->paragraphs($nbr);
        return '{"blocks":[{"key":"401uo","text":"'.implode($paragraphs).'","type":"unstyled","depth":0,"inlineStyleRanges":[],"entityRanges":[],"data":{}}],"entityMap":{}}';
    }
}