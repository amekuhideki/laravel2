<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;

class InsertPrefectures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:insertPrefectures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command insert prefectures';

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
        $prefectures_search_info = $this->prefecturesSearch();
        $regions = $prefectures_search_info['Area']['Region'];

        foreach($regions as $region)
        {
	    $prefecture_info = $region['Prefecture'];
            if(isset($region['Prefecture']['@attributes']))
	    {
		$prefecture_code = $region['Prefecture']['@attributes']['cd'];
		$prefecture_name = $region['Prefecture']['@attributes']['name'];
		$this->insertPrefecture($prefecture_code, $prefecture_name);
            } else {
		foreach($prefecture_info as $large_area)
   		{
		    $prefecture_code = $large_area['@attributes']['cd'];
		    $prefecture_name = $large_area['@attributes']['name'];
		    $this->insertPrefecture($prefecture_code, $prefecture_name);
		}
	    }
        }
    }

    public function prefecturesSearch()
    {
        $prefectures_info = '';
        try {
            $client = new Client();
            $res = $client->request('POST', 'http://jws.jalan.net/APICommon/AreaSearch/V1/', [
                'form_params' => [
                    'key' => env('JALAN_API'),
                ],
                'allow_redirects' => true
            ]);

            $body = $res->getBody();
            $xml_res = @simplexml_load_string($body);
            $json_res = json_encode($xml_res);
            $prefectures_info = json_decode($json_res, true);
        } catch (\Exception $e) {
            echo '終了:2';
        }
        return $prefectures_info;
    }

    public function insertPrefecture($prefecture_id, $prefecture_name)
    {
        $prefecture = new \App\Prefecture;
	$prefecture->prefecture_id = $prefecture_id;
        $prefecture->prefecture_name = $prefecture_name;
        $prefecture->save();
    }
}
