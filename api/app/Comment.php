<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['creator_id', 'parent_id', 'commentable_type', 'body'];
}
