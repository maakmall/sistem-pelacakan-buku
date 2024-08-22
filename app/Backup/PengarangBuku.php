<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengarangBuku extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'biblio_author';

    /**
     * The primary key associated with the table.
     *
     * @var array
     */
    protected $primaryKey = ['biblio_id', 'author_id'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key.
     *
     * @var string
     */
    protected $keyType = 'int'; // or 'int' depending on your key type

    /**
     * Disable timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Override the method to set keys for save query.
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if (!is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $key) {
            $query->where($key, '=', $this->getKeyForSaveQuery($key));
        }

        return $query;
    }

    /**
     * Get the primary key value for a save query.
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName)) {
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }
}
