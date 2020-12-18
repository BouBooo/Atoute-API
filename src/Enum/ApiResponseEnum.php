<?php

namespace App\Enum;

abstract class ApiResponseEnum
{
    // JWT 
    public const INVALID_CREDENTIALS = 'Bad credentials.';

    // API
    public const USER_REGISTERED = 'registered_successfully';
    public const USER_NOT_VERIFIED = 'user_not_verified';
    public const USER_ALREADY_APPLIED = 'has_already_applied';
    public const USER_CANNOT_APPLY = 'company_cant_applied';

    public const OFFER_DELETE = 'offer_removed';
    public const OFFER_BAD_OWNER = 'not_offer_owner';

    public const RESUME_BAD_OWNER = 'not_your_resume';

    public const APPLICATION_CREATED = 'application_created';
    public const APPLICATION_REMOVED = 'application_removed';
    public const APPLICATION_NOT_FOUND = 'application_not_found';
}