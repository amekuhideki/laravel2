<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use GuzzleHttp\Client;

class HotelSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:hotelSearch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command hotel search';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $hotel_info = $this->hotelSearch();
        $hotel_number = $hotel_info['NumberOfResults'];
        
        $start = 1;
        while($start<=$hotel_number)
        {
	    $hotel_search = $this->hotelSearch($start);
            $hotel_info = $hotel_search['Hotel'];
            foreach($hotel_info as $info)
            {
                var_dump($info);
                exit;
            }
exit;
            $this->insertHotelsInfo($hotel_serch);
            $start += 100;
        }
        
    }

    public function hotelSearch($start=1, $count=100)
    {
        $hotel_info = '';
        try {
            $client = new Client();
            $res = $client->request('POST', 'http://jws.jalan.net/APIAdvance/HotelSearch/V1/', [
                'form_params' => [
                    'key' => env('JALAN_API'),
                    'pref' => '010000',
                    'start' => $start,
                    'count' => $count,
                ],
                'allow_redirects' => true
            ]);

            $body = $res->getBody();
            $xml_res = @simplexml_load_string($body);
            $json_res = json_encode($xml_res);
            $hotel_info = json_decode($json_res, true);
        } catch (\Exception $e) {
            echo '終了:2';
        }
        return $hotel_info;
    }

    public function insertHotelsInfo($hotel_info)
    {
        $users = DB::table('users')->get();
        var_dump($users);
exit;
    }
}
