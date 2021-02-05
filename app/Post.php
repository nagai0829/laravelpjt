<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
     // 割り当て許可
     protected $fillable = [
        'name',
        'subject',
        'message', 
        'category_id'
    ];


    /**
    * 「名前・本文」検索スコープ
    */
    public function scopeFuzzyNameMessage($query, $searchword)
    {
        if (empty($searchword)) {
            return;
       }
 
        return $query->where(function ($query) use($searchword) {
            $query->orWhere('name', 'like', "%{$searchword}%")
                  ->orWhere('message', 'like', "%{$searchword}%");
        });
}



    /**
     * 
     */
    public function comments()
    {
        // 投稿は複数のコメントを持つ
        return $this->hasMany('App\Comment');
    }

    /**
     * 
     */
    public function category()
    {
        // 投稿は1つのカテゴリーに属する
        return $this->belongsTo('App\Category');
    }

    /**
     * 任意のカテゴリを含むものとする（ローカル）スコープ
     * 
     */
    public function scopeCategoryAt($query, $category_id)
    {
        if (empty($category_id)) {
            return;
        }
 
        return $query->where('category_id', $category_id);
    }

    
}