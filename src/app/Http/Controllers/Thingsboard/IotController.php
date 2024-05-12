<?php

namespace App\Http\Controllers\Thingsboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;
use \Carbon\Carbon;
use App\Exports\TelemetryExport;
use Maatwebsite\Excel\Facades\Excel;



class IotController extends Controller
{
    //
    private $token;

    private $things_host = "https://liwad.bizibug.com:443";

    private $customer_id = "e74e6dc0-336e-11ee-9b37-17bc1eaae52e";

    public function __construct(){
          if(!Session::has('things_token_expiration') || Carbon::parse(Session::get('things_token_expiration'))->lt(Carbon::now()) ){
                $this->authToken();
        }
    }


    public function authToken(){

            $client = new Client();
            $response = $client->post( $this->things_host .'/api/auth/login', [
                'json' => [
                    'username' => 'tenant@thingsboard.org',
                    'password' => 'tenant',
                ],
            ]);

            $token = json_decode($response->getBody()->getContents(), true)['token'];
            $this->setToken($token);
            //return $this->

    }


    public function getToken(){
        return $this->token;
    }

    public function removeToken(){
        Session::forget('things_token');
        Session::forget('things_token_expiration');
    }

    public function setToken($token){
        Session::put('things_token',$token);
        Session::put('things_token_expiration',Carbon::now()->addHours(1));

        $this->token = $token;
    }


    public function getDevices(){
       // api/tenant/devices?pageSize=10&page=0"
        $endpoint =  $this->things_host . '/api/customer/'.$this->customer_id.'/deviceInfos?pageSize=100&page=0';
        $client = new Client();

        $response = $client->request('GET', $endpoint, [
                                    'headers' => [
                                        'Accept' => 'application/json',
                                        'X-Authorization' => 'Bearer ' . $this->token,
                                    ],
                                    
                                ]);

        $body = $response->getBody();
        $content = $body->getContents();
        $data = json_decode($content);
        $devices = collect($data->data);

        $devices = $devices->map(function($d){
                return [
                        "value" => [ "id" => $d->id->id, "name" => $d->name],
                        "text" =>  $d->name
                      ] ;
                });

        return $devices;
    }

    public function getCustomerId(){

    }

    public function getAttrib(Request $r){

        $endpoint =  $this->things_host . '/api/plugins/telemetry/'.$r->device_type.'/'.$r->device_id.'/keys/timeseries';
        $client = new Client();
        $response = $client->request('GET', $endpoint, [
                                    'headers' => [
                                        'Accept' => 'application/json',
                                        'X-Authorization' => 'Bearer ' . $this->token,
                                    ],
                                    
                                ]);

        $body = $response->getBody();
        return   $content = $body->getContents();
    }

    public function getTelemetry(Request $r){

        $start =  Carbon::parse($r->date_from)->startOfDay()->setTimezone('UTC')->timestamp * 1000;
        $end =  Carbon::parse($r->date_to)->endOfDay()->setTimezone('UTC')->timestamp * 1000;
        $endpoint =  $this->things_host . '/api/plugins/telemetry/'.$r->device_type.'/'. $r->device_id.'/values/timeseries';

        $client = new Client();
        $response = $client->request('GET', $endpoint, [
                                    'headers' => [
                                        'Accept' => 'application/json',
                                        'X-Authorization' => 'Bearer ' . $this->token,
                                    ],
                                    
                                    "query" => [
                                        "keys" => implode(",",$r->keys),
                                        "startTs" => $start,
                                        "endTs" => $end,
                                        "useStrictDataTypes" => true,
                                        "limit" => 99999

                                    ],
                                                                  
                                    
                                ]);

        $body = $response->getBody();
        $content =  json_decode($body->getContents(), true);
        
        $to_excel = [];
        $header = [[
            "text" => "Timestamp","value" => 'Timestamp'

        ]];

      
        $k_count = 0;

        foreach($content as $k => $s ){

            array_push($header,[
                "text" => ucfirst($k) ,"value" => $k
            ]);
           
            for($i =0;$i < count($s) ; $i++){

                $time = Carbon::parse($s[$i]['ts'] / 1000)->setTimezone('Asia/Manila')->format('Y-m-d H:i:s');
                $value = $s[$i]['value'];

                if (is_array($value)) {
                        $value =  "x: ".$value['x'].', y: '.$value['y'].', z:'.$value['z'];
                } 

                if($k_count == 0){
                        $to_excel[] = [
                            "Timestamp" => $time,
                            $k =>  $value
                        ];
                }else{
                        $to_excel[$i][$k] = $value;
                }
    
            }

            $k_count++;
        }
       // array_unshift($to_excel, $header);

        return [
            "table_header" => $header,
            "table_data" => $to_excel
        ];
       
      
        $export = new TelemetryExport(collect($to_excel));

         return Excel::download($export, str_replace("/","",$r->device_name).'.xlsx');
        


    }

}
