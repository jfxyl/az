<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Article;
use App\Events\ArticlePush;
use Schema;
use DB;

class ArticleController extends Controller
{
    public function store(Request $request)
    {
        // 保存文章
        // $article = Article::create($request->all());
        // $article->save();

        $article = Article::find(1);
        // dump($article);

        $users = User::all(['id']);
        // dump($users);
        foreach($users as $user){
            $table = 'keywords_'.$user->id;
            if(Schema::hasTable($table)){
                // dump($user);
                $keywords = DB::table($table)->select('keywords')->get();
                dump($keywords);
            }
        }

        // event(new ArticlePush($article));
    }

    
}
