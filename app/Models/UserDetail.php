<?php

namespace App\Models;

/**
 * @deprecated This class is deprecated and will be removed in a future update.
 * Use the User model instead as the tables have been merged.
 */
class UserDetail extends User
{
    // This class is now a proxy to the User model
    protected $table = 'users';
    protected $primaryKey = 'user_id';
} 