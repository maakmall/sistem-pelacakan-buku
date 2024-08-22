<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penerbit extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mst_publisher';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'publisher_id';

    const CREATED_AT = 'input_date';
    const UPDATED_AT = 'last_update';

    protected $fillable = ['publisher_name'];
}
