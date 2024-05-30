<?php

namespace App\Models;


use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];



    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Create a new user instance.
     *
     * @param array $data
     * @return User
     */
    public static function createUser($data)
    {
        return self::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Authorize user and return the user instance or null.
     *
     * @param array $data
     * @return User|null
     */
    public static function authorizeUser($data)
    {
        $user = self::where('email', $data['email'])->firstOrFail();
        // dd($data);

        if ($user && Hash::check($data['password'], $user->password)) {
            return $user; // Return the user instance directly, not an array

        }

        return null; // Return null if authorization fails
    }

    public function activeTokens()
    {
        return $this->hasMany(ActiveToken::class);
    }


    /**
     * Blacklist the given token.
     *
     * @param  string  $token
     * @return void
     */
    public function blacklistToken($token)
    {
        // dd($token);
        BlacklistedToken::create(['token' => $token]);
        $this->activeTokens()->where('token', $token)->delete();
    }




    public function createToken()
    {
        $token = JWTAuth::fromUser($this);
        $this->activeTokens()->create(['token' => $token]);
        return $token;
    }
}
