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

    private function notifyLine(string $txet){
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(env('LINE_ACCESS_TOKEN'));
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => env('LINE_CHANNEL_SECRET')]);


        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($txet);
        $response = $bot->broadcast($textMessageBuilder);

        \Log::info($response->getHTTPStatus() . ' ' . $response->getRawBody());
    }

    public function webhook(Request $request){
        \Log::info("post webhook");

        // 仮実装
        \Log::info($request->input('destination'));
        \Log::info($request->input('events'));
        return view('welcome');
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
                [ 'title' => $title ,'text' => $this->text ]
            );

            if (!$this->isScraped($item)) {
                $item->save();

                // TODO:仮実装
                // $this->notifyLine("ブログ更新あり");
                // $this->notifyLine($item->title);
                // $this->notifyLine($item->text);
            } else {

                // TODO:仮実装
                $this->notifyLine("ブログ更新なし");
                // $this->notifyLine($item->title);
                // $this->notifyLine($item->text);
            }

            dump($title);
            dump($this->text);
        });
        dump("end");

        return view('welcome');
    }
}
