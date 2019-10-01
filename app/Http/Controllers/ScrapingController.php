<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte;

class ScrapingController extends Controller
{
    public function scraping(Request $request)
    {
        $crawler = Goutte::request('GET', 'http://funspot.jugem.jp/');

        // dump("start");
        // dump($crawler->filter('.entry')->children()->text());
        // dump("end");

        dump("start");
        $crawler->filter('.entry')->each(function ($node) {
            // dump($node->children()->text());
            $title = $node->filter('.entry-title a')->text();
            $text = "";
            // dump($node->filter('.jgm_entry_desc_mark p')->text());
            $node->filter('.jgm_entry_desc_mark p')->each(function ($node) {
                dump($node->text());
                // var_dump($node->text());
            // $text = $node->text();
            // // $text .= $node->text();
            });

            dump($title);
            dump($text);
        });
        dump("end");

        $crawler->filter('.entry-title a')->each(function ($node) {
            dump($node->text());
        });

        $crawler->filter('.jgm_entry_desc_mark a')->each(function ($node) {
            dump($node->text());
        });

        return view('welcome');
    }
}
