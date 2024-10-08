<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use App\Models\HelpCenterArticle;
use App\Models\HelpCenterFAQ;
use App\Models\HelpCenterVideo;
use Auth;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {

        $faq = HelpCenterFAQ::orderBy('id', 'desc')->take(5)->get();
        $articles = HelpCenterArticle::orderBy('id', 'desc')->take(6)->get();
        $lang = session('appLocale', Auth::user()->interface_language ?? config('app.locale'));

        return inertia('Front/SupportCenter/Index', compact('faq', 'articles', 'lang'));
    }

    public function faq()
    {

        $faq = HelpCenterFAQ::all();
        $lang = session('appLocale', Auth::user()->interface_language ?? config('app.locale'));
        $articles = HelpCenterArticle::orderBy('id', 'desc')->take(6)->get();

        return inertia('Front/SupportCenter/Faq', compact('faq', 'lang'));
    }

    public function faq_show($id)
    {
        $articles = HelpCenterArticle::orderBy('id', 'desc')->take(6)->get();

        $faq = HelpCenterFAQ::where('id', $id)->first();
        if ($faq) {

            $faq->load('media');
        }

        return inertia('Front/SupportCenter/ShowFaq', compact('faq'));
    }

    public function blog()
    {
        $articles = HelpCenterArticle::orderBy('id', 'desc')->take(6)->get();

        $articles = HelpCenterArticle::all();
        return inertia('Front/SupportCenter/Blog', compact('articles'));
    }

    public function blog_show($id)
    {
        $articles = HelpCenterArticle::orderBy('id', 'desc')->take(6)->get();

        $article = HelpCenterArticle::where('id', $id)->first();
        if ($article) {

            $article->load('media');
        }
        return inertia('Front/SupportCenter/ShowBlog', compact('article', 'articles'));
    }

    public function video(Request $request)
    {
        $articles = HelpCenterArticle::orderBy('id', 'desc')->take(6)->get();

        $videos = HelpCenterVideo::all()->load('media');

        return inertia('Front/SupportCenter/VideoEducation', compact('videos'));
    }
}
