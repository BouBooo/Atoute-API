<?php

namespace App\Enum;

abstract class EntityEnum
{
    // Activities
    public const IT = 'IT';
    public const PHARMACEUTICAL = 'Pharmaceutical industry';
    public const LOGISTIC = 'Logictic';
    public const FOOD = 'Food industry';

    public static array $activities = [EntityEnum::IT, EntityEnum::PHARMACEUTICAL, EntityEnum::LOGISTIC, EntityEnum::FOOD];

    // Type
    public const CDI = 'CDI';
    public const CDD = 'CDD';
    public const ALTERNATION = 'Alternance';
    public const INTERNSHIP = 'Stage';

    public static array $types = [EntityEnum::CDI, EntityEnum::CDD, EntityEnum::ALTERNATION, EntityEnum::INTERNSHIP];
}