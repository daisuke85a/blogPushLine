<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte;
use App\Item;
use App\Channel;

class ScrapingController extends Controller
{
    private $text;

    private function isScraped(Item $item): bool
    {
        //タイトルが一致しているかを確認する
        $count = Item::where('title', $item->title)->count();
        return $count ? true:false;
    }

    private function hasKeyword(Channel $channel, Item $item): bool
    {
        if ($channel->keyword == '*') {
            return true;
        }

        //タイトルか本文にキーワードが含まれているかを確認する
        if (strpos($item->title . $item->text, $channel->keyword) !== false) {
            return true;
        }

        return false;
    }

    private function notifyLine(Channel $channel, string $txet)
    {
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($channel->access_token);
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $channel->channel_secret]);

        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($txet);
        $response = $bot->broadcast($textMessageBuilder);

        \Log::info($response->getHTTPStatus() . ' ' . $response->getRawBody());
    }

    public function webhook(Request $request)
    {
        \Log::info("post webhook");

        // 仮実装
        \Log::info($request->input('destination'));
        \Log::info($request->input('events'));
        return view('welcome');
    }

    private function scraping_funspot()
    {
        $crawler = Goutte::request('GET', 'http://funspot.jugem.jp/');

        $crawler->filter('.entry')->each(function ($node) {
            $title = $node->filter('.entry-title a')->text();
            $this->text = "";
            $node->filter('.jgm_entry_desc_mark p')->each(function ($node) {
                $this->text =  $this->text . $node->text();
            });

            // \Log::info($this->text);

            $item = Item::make(
                [ 'title' => $title ,'text' => $this->text ]
            );

            if (!$this->isScraped($item)) {
                $item->save();

                \Log::info("item save item->title={$item->title}");
                $channels = Channel::all();
                foreach ($channels as $channel) {
                    if ($this->hasKeyword($channel, $item)) {
                        $this->notifyLine($channel, $item->title . "\n" . $item->text);
                        \Log::info("Has Keyword keyword={$channel->keyword} item->title={$item->title}");
                    }
                }
            } else {
                \Log::info("item was Scraped item->title={$item->title}");
            }
        });
    }

    public function scraping()
    {
        $crawler = Goutte::request('GET', 'https://jko.hateblo.jp/');

        $crawler->filter('.entry')->each(function ($node) {
            $title = $node->filter('.entry-title a')->text();
            $this->text = "";
            $node->filter('.entry-content *')->each(function ($node) {
                // \Log::debug($node->text());
                if (!empty($node->text())) {
                    \Log::debug('not empty');
                    \Log::debug($node->text());
                    $this->text =  $this->text . "\n\n" . $node->text();
                } else {
                    // \Log::debug('empty');
                }
            });

            $item = Item::make(
                [ 'title' => $title ,'text' => $this->text ]
            );

            \Log::debug('text');
            \Log::debug($this->text);

            // \Log::debug($this->text);
            // \Log::debug(print_r($node, true));

            if (!$this->isScraped($item)) {
                $item->save();

                \Log::info("item save item->title={$item->title}");
                $channels = Channel::all();
                foreach ($channels as $channel) {
                    if ($this->hasKeyword($channel, $item)) {
                        $this->notifyLine($channel, $item->title . "\n" . $item->text);
                        \Log::info("Has Keyword keyword={$channel->keyword} item->title={$item->title}");
                    }
                }
            } else {
                \Log::info("item was Scraped item->title={$item->title}");
            }
        });
    }

    public function scraping_shake()
    {
        $crawler = Goutte::request('GET', 'http://blog.studio-shake.com/');

        $crawler->filter('div.entry')->each(function ($node) {
            $title = $node->filter('h2')->text();
            $this->text = "";
            $text = "";
            $node->filter('.jgm_entry_desc_mark')->each(function ($node) {
                $node->filter('div.service_button')->clear();
                $node->each(function ($node) {
                    // var_dump($pos = mb_strrpos($node->text(), "Tweet"));
                    // var_dump(mb_substr($node->text(),0,$pos));
                    if (false !== $pos = mb_strrpos($node->text(), "Tweet")) {
                        $this->text =  $this->text .  mb_substr($node->text(), 0, $pos);
                    } else {
                        $this->text =  $this->text .  $node->text();
                    }
                });
                var_dump($this->text);
                // $this->text =  $this->text . $text;
            });

            $item = Item::make(
                [ 'title' => $title ,'text' => $this->text ]
            );

            if (!$this->isScraped($item)) {
                $item->save();

                \Log::info("item save item->title={$item->title}");
                $channels = Channel::all();
                foreach ($channels as $channel) {
                    if ($this->hasKeyword($channel, $item)) {
                        $this->notifyLine($channel, $item->title . "\n" . $item->text);
                        \Log::info("Has Keyword keyword={$channel->keyword} item->title={$item->title}");
                    }
                }
            } else {
                \Log::info("item was Scraped item->title={$item->title}");
            }
        });

        return view('welcome');
    }
}
