<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\CrlController;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;

class Request implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    public function __construct(public $qs)
    {
        $this->qs = $qs;
    }
    
    /**
     * Execute the job.
     */
    public function handle()
    {
        $crl = new CrlController;
        
        $data0 = Http::retry(100,10)->pool(function (Pool $pool) {
            foreach ($this->qs->qs as $name => $url) {
                    $pool->as($name)->get($url[0]);
                }
            });
        $data1 = Http::retry(100,10)->pool(function (Pool $pool) {
            foreach ($this->qs->qs as $name => $url) {
                    $pool->as($name)->get($url[1]);
                }
            });

        $data = $crl->generate(
            array_merge_recursive(
                $crl->ext($data0,0),
                $crl->ext($data1,1))
            );

        
        $crl->dbsave($data);
    }
}
