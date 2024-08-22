<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GMD extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mst_gmd';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'gmd_id';

    const CREATED_AT = 'input_date';
    const UPDATED_AT = 'last_update';
}
