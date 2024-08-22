<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Pustakawan extends Authenticatable
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * The column name of the password field using during authentication.
     *
     * @var string
     */
    protected $authPasswordName = 'passwd';

    const CREATED_AT = 'input_date';
    const UPDATED_AT = 'last_update';
}
