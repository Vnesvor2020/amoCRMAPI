<?php
class token{
    public function connect(){
        $bd = mysqli_connect("Login", "name_user", "pass", "table");
            if($bd == false){
                echo "Ошибка";
        }else{
                echo "К базе подключено успешно!";
        };
        mysqli_set_charset($bd, "utf8");
        return $bd;
    }
    public  function get_token(){
        $sql = "SELECT * FROM access_tokens WHERE 1";
        $result = mysqli_query($this->connect(), $sql);
             while($row = mysqli_fetch_assoc($result)) {
                 $refresh_token = $row["refresh_token"];
                 $access_token = $row["access_token"];
                 $expires_in= $row["expires_in"];
             }
             $date_now = date("d.m.y");
             if(empty($expires_in) or strtotime(date("d.m.y"))>strtotime($expires_in)){
                 //При просрочке сроков
                 $this->new_token($refresh_token);
             }else{
                 //При нормальных сроках
                 return $access_token;
             }
    }
    public function update_token($refresh_token, $access_token , $expires_in){
        $sql = "UPDATE access_tokens SET refresh_token = '".$refresh_token."', access_token = '".$access_token."', expires_in = '".$expires_in."' WHERE id = 1";
        mysqli_query($this->connect(), $sql);
        $this->get_token();
    }
    public function new_token($refresh_token){
        
        
        $subdomain = 'apitest67'; //Поддомен нужного аккаунта
        $link = 'https://' . $subdomain . '.amocrm.ru/oauth2/access_token'; //Формируем URL для запроса
    
        if(empty($refresh_token)){
        $data = [
            'client_id' => 'get_to_amo',
            'client_secret' => 'get_to_amo',
            'grant_type' => 'authorization_code',
            'code' => 'get_to_amo',
            'redirect_uri' => 'url yor server',
            ];
            echo "Я тут";
        }else{
        $data = [
            'client_id' => 'get_to_amo',
            'client_secret' => 'get_to_amo',
            'grant_type' => 'refresh_token',
            "refresh_token"=> $refresh_token,
            'redirect_uri' => 'url yor server',
            ];
            }

//==================================================================================================
            $curl = curl_init(); 
            curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
            curl_setopt($curl,CURLOPT_URL, $link);
            curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
            curl_setopt($curl,CURLOPT_HEADER, false);
            curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
            $out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
            $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            $code = (int)$code;
                $errors = [
                    400 => 'Bad request',
                    401 => 'Unauthorized',
                    403 => 'Forbidden',
                    404 => 'Not found',
                    500 => 'Internal server error',
                    502 => 'Bad gateway',
                    503 => 'Service unavailable',
                ];

                        try
                        {
                            /** Если код ответа не успешный - возвращаем сообщение об ошибке  */
                            if ($code < 200 || $code > 204) {
                                throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
                            }
                        }
                        catch(Exception $e)
                        {
                            die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
                        }


                        $response = json_decode($out, true);
//===========================================================================================
    $date_now = date("d.m.y");
    $dateend = strtotime($date_now) + $response['expires_in'];
    date("d.m.y",$dateend);
$this->update_token($response['refresh_token'], $response['access_token'], date("d.m.y",$dateend));

    }
}
?>