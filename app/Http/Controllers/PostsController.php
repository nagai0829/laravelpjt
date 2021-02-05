<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post, App\Category;	// ←★忘れず追記
use App\Http\Requests\PostRequest; // class宣言の外に追記

class PostsController extends Controller
{
    // public function index()
    // {
    //     $posts = Post::orderBy('created_at', 'desc')->paginate(10);        
    //     return view('bbs.index', ['posts' => $posts]);
    // }

    /**
    * 一覧
     */
    public function index(Request $request)
    {
        // カテゴリ取得
        $category = new Category;
        $categories = $category->getLists();
        $searchword = $request->searchword;
 
        $category_id = $request->category_id;
        $posts = Post::with('comments')        // ←★これ
            ->orderBy('created_at', 'desc')
            ->categoryAt($category_id)
            ->fuzzyNameMessage($searchword) // ←★変更
            ->paginate(10);
 
        return view('bbs.index', [
            'posts' => $posts, 
            'categories' => $categories, 
            'category_id'=>$category_id,
            'searchword' => $searchword // ←★追加
        ]);
}
    
    /**
     * 詳細
     */
    public function show(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        
        return view('bbs.show', [
            'post' => $post,
            ]);
    }
    
    /**
     * 投稿フォーム
     */
    public function create()
    {
        $category = new Category;
        $categories = $category->getLists()->prepend('選択', '');
 
        return view('bbs.create', ['categories' => $categories]);
    }
    
    
    /**
     * バリデーション、登録データの整形など
     */
    public function store(PostRequest $request)
    {
        $savedata = [
            'name' => $request->name,
            'subject' => $request->subject,
            'message' => $request->message,
            'category_id' => $request->category_id,
        ];

        $post = new Post;
        $post->fill($savedata)->save();
        
        return redirect('/bbs')->with('poststatus', '新規投稿しました');
    }
    
    /**
     * 編集画面
     */
    // public function edit($post_id)
    // {
    //     $post = Post::findOrFail($post_id);
    //     return view('bbs.edit', ['post' => $post]);
    // }

    /**
     * 編集フォーム
    */
    public function edit($id)
    {
        $category = new Category;
        $categories = $category->getLists();
 
        $post = Post::findOrFail($id);
        return view('bbs.edit', ['post' => $post, 'categories' => $categories]);
    }
    
    
    /**
     * 編集実行
     */
    public function update(PostRequest $request)
    {
        $savedata = [
            'name' => $request->name,
            'subject' => $request->subject,
            'message' => $request->message,
            'category_id' => $request->category_id,
        ];
        
        $post = new Post;
        $post->fill($savedata)->save();

        return redirect('/bbs')->with('poststatus', '投稿を編集しました');
    }

    /**
     * 物理削除
     */
    public function destroy($id)
    {
     $post = Post::findOrFail($id);
    
    $post->comments()->delete(); // ←★コメント削除実行
     $post->delete();  // ←★投稿削除実行
 
     return redirect('/bbs')->with('poststatus', '投稿を削除しました');
    }
}