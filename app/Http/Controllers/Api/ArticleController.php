<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Article;
use App\Events\ArticlePush;
use Schema;
use DB;
use Auth;

class ArticleController extends Controller
{
    public function store(Request $request)
    {
        // 保存文章
        $article = Article::create($request->all());
        $article->save();

        // $article = Article::find(10);

        $created_at = now();
        $users = User::all(['id']);
        foreach($users as $user){
            $keywordTable = 'keywords_'.$user->id;
            // if(Schema::hasTable($keywordTable)){
                $keywords = DB::table($keywordTable)->select('keyword')->where('mark',0)->get();
                // 执行写入，并推送
                if($this->writeToPrivate($user,$article,$keywords,$created_at)){
                    event(new ArticlePush($article,$user));
                }
            // }
        }
        // 公共频道推送
        event(new ArticlePush($article));
        echo 'success';
    }

    public function articles(Request $request)
    {
        // 判断上拉下拉
        $type = $request->type;
        $id = $request->id;
        $fields = ['id','platform','title','media','content','origin_link','pub_time'];
        $articles = [];
        // 用户已登录
        if(true){
            $user = User::find(1);
            $privateNewsTable = 'private_news_'.$user->id;
            if($type == 'up'){
                $articles = DB::table($privateNewsTable)
                ->leftJoin('news_all',$privateNewsTable.'.news_id','=','news_all.id')
                ->where($privateNewsTable.'.news_id','>',$id)
                ->orderBy($privateNewsTable.'.news_id','desc')
                ->get($fields);
            }elseif($type == 'down'){
                $articles = DB::table($privateNewsTable)
                ->leftJoin('news_all',$privateNewsTable.'.news_id','=','news_all.id')
                ->where($privateNewsTable.'.news_id','<',$id)
                ->orderBy($privateNewsTable.'.news_id','desc')
                ->limit(20)
                ->get($fields);
                $articles = $this->addArticle($user,$articles,$fields);
            }else{
                $articles = DB::table($privateNewsTable)
                ->leftJoin('news_all',$privateNewsTable.'.news_id','=','news_all.id')
                ->orderBy($privateNewsTable.'.news_id','desc')
                ->get($fields);
                $articles = $this->addArticle($user,$articles,$fields);
            }
        }else{
            if($type == 'up'){
                $articles = Article::where('id','>',$id)->orderBy('id','desc')->get($fields);
            }elseif($type == 'down'){
                $articles = Article::where('id','<',$id)->orderBy('id','desc')->limit(20)->get($fields);
            }else{
                $articles = Article::orderBy('id','desc')->limit(20)->get($fields);
            }
        }       
        return $articles;
    }

    /** 
     * $user    当前用户
     * $min     获取到的文章的最小id
     * $amount  需要补充的文章数
     * $fields  查询字段
     */
    protected function addArticle($user,$articles,$fields)
    {
        if($articles->count() >= 20) return $articles;
        $min = $articles->min('id');
        $keywordTable = 'keywords_'.$user->id;
        $keywords = DB::table($keywordTable)->get(['keyword']);
        $datas = Article::where('id','<',$min)->orderBy('id','desc')->limit(10)->get($fields);
        //  咨询库没有足够的数据可被查询
        if($datas->count() <= 0) return $articles;
        $created_at = now();
        foreach($datas as $data){
            if($this->writeToPrivate($user,$data,$keywords,$created_at)){
                $articles->push($data);
            }
            if($articles->count() >= 20) return $articles;
        }
        //  获取到的咨询数达不到20条，继续执行
        if($articles->count() < 20){
            $this->addArticle($user,$articles,$fields);
        }
    }

    public function writeToPrivate($user,$article,$keywords,$created_at){
        // 根据关键词判断是否需要写入用户咨询表
        $write = true;
        foreach($keywords as $keyword){
            if(strpos($article->title,$keyword->keyword) !== false){
                $write = false;
            }
        }
        // 写入用户咨询表
        if($write){
            $privateNewsTable = 'private_news_'.$user->id;
            DB::table($privateNewsTable)->insert(['news_id'=>$article->id,'created_at'=>$created_at]);
        }
        return $write;
    }
}
