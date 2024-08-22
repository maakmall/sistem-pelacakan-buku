<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'file_id';

    const CREATED_AT = 'input_date';
    const UPDATED_AT = 'last_update';

    protected $fillable = ['file_title', 'file_name', 'file_url', 'file_dir', 'mime_type', 'file_desc', 'uploader_id'];
}
