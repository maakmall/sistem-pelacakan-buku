<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempatTerbit extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mst_place';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'place_id';

    const CREATED_AT = 'input_date';
    const UPDATED_AT = 'last_update';

    protected $fillable = ['place_name'];
}
