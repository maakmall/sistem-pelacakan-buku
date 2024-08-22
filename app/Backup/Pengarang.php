<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengarang extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mst_author';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'author_id';

    const CREATED_AT = 'input_date';
    const UPDATED_AT = 'last_update';

    protected $fillable = ['author_name', 'authority_type'];
}
