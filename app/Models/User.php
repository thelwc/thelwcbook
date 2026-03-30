<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordRequest;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',   // <--- Mới
        'address', // <--- Mới
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    // Lấy danh sách sách yêu thích của User
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordRequest($token));
    }
    public function booksOwned()
    {
        return $this->belongsToMany(Book::class, 'book_user', 'user_id', 'book_id');
    }
    // Thêm hàm này để lấy danh sách đơn hàng của User
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
