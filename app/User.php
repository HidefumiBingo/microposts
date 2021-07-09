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
    
    //このユーザが所有する投稿
    public function microposts() {
        return $this->hasMany(Micropost::class);
    }
    
    //このユーザがフォロー中のユーザ
    public function followings() {
        return $this->belongsToMany(User::class, 'user_follow','user_id','follow_id')->withTimestamps();
    }
    
    //このユーザをフォロー中のユーザ
    public function followers() {
        return $this->belongsToMany(User::class,'user_follow','follow_id','user_id')->withTimestamps();
    }
    
    public function loadRelationshipCounts() {
        $this->loadCount(['microposts','followings','followers','favorites']);
    }
    
    
    //$userIdで指定されたユーザをフォローする
    public function follow($userId) {
        //すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        //対象が自分自身かどうかの確認
        $its_me = $this->id == $userId;
        
        if($exist || $its_me) {
            //フォローしていれば何もしない
            return false;
        } else {
            //未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    //$userIdで指定されたユーザをアンフォローする
    public function unfollow($userId) {
        //すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        //対象が自分自身かどうかの確認
        $its_me = $this->id == $userId;
        
        if($exist && !$its_me) {
            //すでにフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }
    
    // 指定された$userIdのユーザをこのユーザがフォロー中であるか調べる。フォロー中ならtureを返す
    public function is_following($userId) {
        //フォロー中のユーザの中に$userIdのものが存在するか
        return $this->followings()->where('follow_id',$userId)->exists();
    }
    
    
    //このユーザとフォロー中のユーザに絞り込む
    public function feed_microposts() {
        //このユーザがフォロー中のユーザのidを取得して配列にする
        $userIds = $this->followings()->pluck('users.id')->toArray();
        //このユーザのidも配列に追加
        $userIds[] = $this->id;
        //それらのユーザが所有する投稿に絞り込む
        return Micropost::whereIn('user_id',$userIds);
    }
    
    
    // お気に入り機能
    
    public function favorites() {
        return $this->belongsToMany(Micropost::class,'favorites','user_id','micropost_id')->withTimestamps();
    }
    
    
    // $micropostIdで指定された投稿をお気に入りにする
    public function favorite($micropostId) {
    // すでにお気に入りに入れているか確認
        $exist = $this->is_favorites($micropostId);
        
        if($exist) {
            // すでにお気に入りに入れていればなにもしない
            return false;
        } else {
            // お気に入りに入れる
            $this->favorites()->attach($micropostId);
            return true;
        }
    }
    
    
    //$micropostIdで指定された投稿をお気に入りから外す
    public function unfavorite($micropostId) {
        // すでにお気に入りに入れているか確認
        $exist = $this->is_favorites($micropostId);
        
        if($exist) {
            // すでにお気に入りに入れていれば外す
            $this->favorites()->detach($micropostId);
            return true;
        } else {
            // お気に入りに入れていなければ何もしない
            return false;
        }
    }
    
    
    //指定された$micropostIdがお気に入りに入っているか調べる。入れていればtrueを返す。
    public function is_favorites($micropostId) {
        return $this->favorites()->where('micropost_id',$micropostId)->exists();
    }
    
    
    
}
