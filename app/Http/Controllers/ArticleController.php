<?php

namespace App\Http\Controllers;

use App\Hail;

class ArticleController extends Controller
{
    public function __invoke(Hail $hail)
    {
        if ($hail->isAuthorised()) {
            $articles = $hail->getArticles(config('services.hail.organisation'));
        } else {
            $articles = [];
        }

        return view('index', ['articles' => collect($articles)]);
    }
}
