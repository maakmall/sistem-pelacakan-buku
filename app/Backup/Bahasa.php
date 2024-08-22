<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bahasa extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mst_language';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'language_id';

    /**
     * The data type of the primary key.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    const CREATED_AT = 'input_date';
    const UPDATED_AT = 'last_update';

    protected $fillable = ['language_id', 'language_name'];
}
