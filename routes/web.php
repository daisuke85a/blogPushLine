<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
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
});
