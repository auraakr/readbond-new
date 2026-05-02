<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionCommentLike extends Model
{
    protected $fillable = ['collection_comment_id', 'user_id'];

    public function comment()
    {
        return $this->belongsTo(CollectionComment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}