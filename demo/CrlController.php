<?php

namespace App\Http\Controllers;

use App\Jobs\Request;
use App\Models\Crl;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;
use Symfony\Component\DomCrawler\Crawler;

class CrlController extends Controller



{

    public $list = array();

    public function index() {
        $names_url = 'http://old.tsetmc.com/Loader.aspx?Partree=151317&Type=MostVisited&Flow=1';
        $names = $this->names($names_url);
        $this->chop($names);
    }



    public function chop($names) {
        $x = 0;
        foreach ($names as $name => $values) {
            $list[$name] = $values;
            unset($names[$name]);
            if ($x == 50) {
                Request::dispatch(new Request($list));
                $x = -1;
                $list = [];
            }
            $x = $x+1;
        }
        Request::dispatch(new Request($list));

    }

    public static function ext(array $res,int $index) {
        switch ($index) {
            case 0:
                foreach ($res as $i => $r) {
                    $res[$i] = [
                        'EPS' => (int) $r->json('instrumentInfo')['eps']['estimatedEPS'], 
                        'P/EG' => $r->json('instrumentInfo')['eps']['sectorPE'],
                        'PSR' => (int) $r->json('instrumentInfo')['eps']['psr']];
                    }
                return $res;

            case 1:
                foreach ($res as $i => $r) {
                    $res[$i] = [
                        'PC' => $r->json('closingPriceInfo')['pClosing']];
                }
                return $res;
            
        }
    }

    public static function generate($merged) {
        foreach ($merged as $name => $values) {
            if ($values['EPS'] == 0) {
                $merged[$name] += [
                    'P/E' => null 
                ];
            } else {
                $merged[$name] += [
                    'P/E' => round($values['PC'] / $values['EPS'], 2)  
                ];
            }
            if ($values['PSR'] == 0) {
                $merged[$name] += [
                    'P/S' => 0
                ];
            } else {
                $merged[$name] += [
                    'P/S' => round($values['PC'] / $values['PSR'], 2)
                ];
            }
        }
        return $merged;
    }

    public function names(string $url) {
        $doc = Http::get($url);
        $crawler = new Crawler($doc);

        $names = $crawler
        ->filter("a[target='_blank']")
        ->reduce(function ($node, int $i): bool {
            if ($i % 2 == 0) {return true;}return false;
        });

        $list = array();
        foreach ($names as $link) {
            parse_str($link->getAttribute('href'),$url);
            $api_link = [
                'https://cdn.tsetmc.com/api/Instrument/GetInstrumentInfo/' . $url['i'],
                'https://cdn.tsetmc.com/api/ClosingPrice/GetClosingPriceInfo/' . $url['i']];
            $list[$link->textContent] = $api_link;
        }

        return $list;
    }


    public function quest(array $qs, int $index) {
        return Http::retry(100,10)->pool(function (Pool $pool) use ($qs, $index) {
            foreach ($qs as $name => $url) {
                $pool->as($name)->get($url[$index]);
            }
        });
    }
    

    public static function dbsave($list) {
        foreach ($list as $name => $values) {
            Crl::create([
                'name' => $name,
                'EPS' => $values['EPS'],
                'P/E' => $values['P/E'],
                'P/EG' => $values['P/EG'],
                'P/S' => $values['P/S'],
            ]);
        }
    }
}
