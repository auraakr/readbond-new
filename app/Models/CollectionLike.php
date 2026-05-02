<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionLike extends Model
{
    protected $fillable = ['collection_id', 'user_id'];

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}