<?php

namespace App\Models;

use Canvas\Models\Post;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinnedPost extends Model
{
    use HasFactory;

    protected $table = 'pinned_posts';

    protected $fillable = [
        'post_id',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }
}
