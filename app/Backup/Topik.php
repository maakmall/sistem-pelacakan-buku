<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topik extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mst_topic';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'topic_id';

    const CREATED_AT = 'input_date';
    const UPDATED_AT = 'last_update';

    protected $fillable = ['topic', 'topic_type', 'classification'];
}
