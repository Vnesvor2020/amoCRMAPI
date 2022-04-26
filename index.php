<?php
include 'database.php';
class amocrm{
    public $subdomain = 'your amocrm domain'; 
    public $access_token;
    function __construct(){
        $token = new token;
        $this->access_token =  $token->get_token();
      
    }
    public function seacrh_contact($query){
        $link = 'https://' . $this->subdomain . '.amocrm.ru/api/v4/contacts'; 
        $headers = [
            'Authorization: Bearer ' . $this->access_token
        ];
            $curl = curl_init(); 
            curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
            curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl,CURLOPT_URL, $link.'?with=leads&query='.$query);
            curl_setopt($curl,CURLOPT_HEADER, false);
            curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
            $out = curl_exec($curl);
            curl_close($curl);
            $leads = json_decode($out, true);
            $this->seacrh_leads($leads["_embedded"]["contacts"][0]["_embedded"]["leads"]);
    }
    public function seacrh_leads($id){
        file_put_contents(__DIR__ . '/message.txt', print_r($id, true));
    }
}


$req = new amocrm;
$req->seacrh_contact("9190464271");
?>