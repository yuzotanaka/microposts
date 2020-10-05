<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }

    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    public function follow($userId)
    {
        $exist = $this->is_following($userId);

        $its_me = $this->id == $userId;

        if ($exist || $its_me) {
            return false;
        } else {
            $this->followings()->attach($userId);
            return true;
        }
    }

    public function unfollow($userId)
    {
        $exist = $this->is_following($userId);

        $its_me = $this->id == $userId;

        if ($exist && !$its_me) {
            $this->followings()->detach($userId);
            return true;
        } else {
            return false;
        }
    }

    public function is_following($userId)
    {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    public function feed_microposts()
    {
        $userIs = $this->followings()->pluck('users.id')->toArray();
        
        $userIds[] = $this->id;
        
        return Micropost::whereIn('user_id', $userIds);
    }

    public function loadRelationshipCounts()
    {
        $this->loadCount('microposts', 'followings', 'followers', 'favorites');
    }
    
    public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    
    public function favorite($userId)
    {
        $exist = $this->is_favorites($userId);

        $its_me = $this->micropost == $userId;

        if ($exist || $its_me) {
            return false;
        } else {
            $this->favorites()->attach($userId);
            return true;
        }
    }

    public function unfavorite($userId)
    {
        $exist = $this->is_favorites($userId);

        $its_me = $this->micropost == $userId;

        if ($exist && !$its_me) {
            $this->favorites()->detach($userId);
            return true;
        } else {
            return false;
        }
    }
    
    public function is_favorites($userId)
    {
        return $this->favorites()->where('micropost_id', $userId)->exists();
    }
    
}
