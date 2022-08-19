<?php

namespace App\Models;

// use Illuminate\Auth\Authenticatable;

use Illuminate\Auth\Authenticatable as Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/*
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable as Notifiable;
use Illuminate\Auth\Authenticatable as Authenticatable;
*/

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
// class User extends Authenticatable implements JWTSubject
{
    use Authenticatable, Authorizable, HasFactory;
    // use Authenticatable;
    // use Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'phone', 'address', 'email'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'created_at' => 'datetime:d M Y h:i A',
        'updated_at' => 'datetime:d M Y h:i A'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function role() {
        return $this->belongsTo(UserRole::class, 'user_role_id', 'id');
    }

    public function messages() {
        return $this->hasMany(Message::class, 'user_id', 'id');
    }

    public function pages() {
        // return $this->belongsToMany(FbPage::class, 'user_pages', 'user_id', 'page_id');
        return $this->hasManyThrough(FbPage::class, UserPage::class);
    }

    public function user_pages() {
        return $this->hasMany(UserPage::class, 'user_id', 'id');
    }
}
