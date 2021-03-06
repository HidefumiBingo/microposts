<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

class UsersController extends Controller
{
    public function index() {
        //ユーザー一覧をidの降順で取得
        $users = User::orderBy('id','desc')->paginate(10);
        
        //ユーザー一覧ビューでそれを表示
        return view('users.index', [
            'users' => $users,    
        ]);
    }
    
    
    public function show($id) {
        //idの値でユーザを検索して取得
        $user = User::findOrFail($id);
        
        $user->loadRelationshipCounts();
        $microposts = $user->microposts()->orderBy('created_at','desc')->paginate(10);
        
        //ユーザ詳細ビューでそれを表示
        return view('users.show',[
            'user' => $user, 
            'microposts' => $microposts,
        ]);
    }
    
    
    //ユーザのフォロー一覧ページを表示するアクション
    public function followings($id) {
        //idの値でユーザを検索して取得
        $user = User::findOrFail($id);
        
        //関係するモデルの件数をロード
        $user->loadRelationshipCounts();
        
        //ユーザのフォロー一覧を取得
        $followings = $user->followings()->paginate(10);
        
        //フォロー一覧ビューでそれらを表示
        return view('users.followings', [
            'user' => $user,
            'users' => $followings,
        ]);
    }
    
    // ユーザのフォロワー一覧を表示するアクション
    public function followers($id) {
        //idの値でユーザを検索して取得
        $user = User::findOrFail($id);
        
        //関係するモデルの件数をロード
        $user->loadRelationshipCounts();
        
        //ユーザのフォロワー一覧を取得
        $followers = $user->followers()->paginate(10);
        
        //フォロワー一覧ビューでそれらを表示
        return view('users.followers', [
            'user' => $user,
            'users' => $followers,
        ]);
    }
    
    
    //お気に入り機能
    public function favorites($id) {
        //  ユーザのidを取得
        $user = User::findOrFail($id);
        
        //  お気に入りの件数取得
        $user->loadRelationshipCounts();
        
        //　ユーザのお気に入りの一覧取得
        $favorites = $user->favorites()->paginate(10);
        
        //  お気に入り一覧ビューで表示
        return view('users.favorites', [
            'user' => $user,
            'microposts' => $favorites,
        ]);
    }
    
}
