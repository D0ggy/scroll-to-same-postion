<?php


namespace App\Http\Controllers;

use Illuminate\Support\Collection;


class ArticleController extends Controller
{
    public function list()
    {
        return view('list');
    }

    public function ajaxList()
    {
        $page = request('page', 1);
        $size = 10;
        $start = $size * ($page - 1);
        $total = $start + $size;
        $articles = new Collection();
        for ($i = $start; $i < $total; $i++) {
            $post = new \stdClass();
            $post->id = $i + 1;
            $post->title = '测试文章呀' . ($i + 1);
            $post->content = '内容内容' . ($i + 1) . '内容内容' . ($i + 1);
            $articles->add($post);
        }
        return response()->json($articles);
    }

    public function detail()
    {
        return view('detail');
    }
}
