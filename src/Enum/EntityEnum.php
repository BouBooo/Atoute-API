<?php

namespace App\Enum;

abstract class EntityEnum
{
    // Activities
    public const IT = 'IT';
    public const COMPUTER_SCIENCE = 'Informatique';
    public const LOGISTIC = 'Logistique';
    public const FOOD_INDUSTRY = 'Industrie alimentaire';

    public static array $activities = [EntityEnum::IT, EntityEnum::COMPUTER_SCIENCE, EntityEnum::LOGISTIC, EntityEnum::FOOD_INDUSTRY];

    // Type
    public const CDI = 'CDI';
    public const CDD = 'CDD';
    public const ALTERNATION = 'Alternance';
    public const INTERNSHIP = 'Stage';

    public static array $types = [EntityEnum::CDI, EntityEnum::CDD, EntityEnum::ALTERNATION, EntityEnum::INTERNSHIP];
}