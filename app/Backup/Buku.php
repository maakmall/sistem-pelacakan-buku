<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'biblio';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'biblio_id';

    const CREATED_AT = 'input_date';
    const UPDATED_AT = 'last_update';

    protected $fillable = [
        'title', 'gmd_id', 'sor', 'edition', 'isbn_issn', 'publisher_id', 'publish_year', 'collation', 'series_title', 'call_number', 'language_id', 'source', 'publish_place_id', 'classification', 'notes', 'image', 'file_att', 'opac_hide', 'promoted', 'labels', 'frequency_id', 'spec_detail_info', 'content_type_id', 'media_type_id', 'carrier_type_id'
    ];

    public function penerbit()
    {
        return $this->belongsTo(Penerbit::class, 'publisher_id');
    }

    public function pengarang()
    {
        return $this->belongsToMany(Pengarang::class, 'biblio_author', 'biblio_id', 'author_id')->withPivot('level') // Jika ingin mendapatkan data tambahan dari pivot, misalnya 'level'
        ->orderBy('pivot_level'); // Mengurutkan berdasarkan level di tabel pivot;
    }
}
