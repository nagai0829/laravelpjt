<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * 
     */
    public function posts()
    {
        // カテゴリは複数のポストを持つ
        return $this->hasMany('App\Post');
    }

    /**
     * カテゴリーの一覧を取得
    */
    public function getLists()
    {
        $categories = Category::orderBy('id','asc')->pluck('name', 'id');
    
        return $categories;
    }
}