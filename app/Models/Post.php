<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'thumbnail', 'short_description', 
        'content', 'user_id', 'views', 'status'
    ];

    public function author() {
        return $this->belongsTo(User::class, 'user_id');
    }
}