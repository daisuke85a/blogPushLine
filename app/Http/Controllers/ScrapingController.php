<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte;
use App\Item;

class ScrapingController extends Controller
{
    private $text;

    private function isScraped(Item $item): bool
    {
        //タイトルと本文が両方とも一致しているかを確認する
        $count = Item::where('title', $item->title)->orWhere('text', $item->title)->count();
        return $count ? true:false;
    }

    public function scraping(Request $request)
    {
        $crawler = Goutte::request('GET', 'http://funspot.jugem.jp/');

        $crawler->filter('.entry')->each(function ($node) {
            $title = $node->filter('.entry-title a')->text();
            $this->text = "";
            $node->filter('.jgm_entry_desc_mark p')->each(function ($node) {
                $this->text =  $this->text . $node->text();
            });

            $item = Item::make(
                [ 'title' => $title ,'text' => $this->text ],
            );

            if (!$this->isScraped($item)) {
                $item->save();
                dump("true");
            } else {
                dump("false");
            }

            dump($title);
            dump($this->text);
        });
        dump("end");

        return view('welcome');
    }
}
