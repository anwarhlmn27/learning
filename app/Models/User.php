<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'avatar',
        'sidebar_color',
        'sidebar_font_color',
        'navbar_color',
        'navbar_font_color',
        'content_color',
        'content_font_color',
        'font_family',
        'lms_sidebar_color',
        'lms_sidebar_font_color',
        'lms_navbar_color',
        'lms_navbar_font_color',
        'lms_content_color',
        'lms_content_font_color',
        'lms_font_family',
        'language',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    public function hasRole($roleName)
    {
        if (is_array($roleName)) {
            return $this->roles()->whereIn('name', $roleName)->exists();
        }
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function hasPermission($permissionName)
    {
        return $this->roles()->whereHas('permissions', function($query) use ($permissionName) {
            $query->where('name', $permissionName);
        })->exists();
    }

    public function dosen()
    {
        return $this->hasOne(Dosen::class, 'user_id');
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function classRooms()
    {
        return $this->belongsToMany(ClassRoom::class, 'class_users', 'user_id', 'class_room_id')
                    ->withTimestamps();
    }
}
