<?php
/**
 * Created by Aleksandr Berdnikov.
 * Copyright 2016 Onix-Systems.
*/

namespace climo;

use AlexaPHPSDK\Intent as BaseIntent;
use AlexaPHPSDK\Response;
use AlexaPHPSDK\Skill;
use AlexaPHPSDK\User;

class StatusIntent extends BaseIntent {
    
    protected function getStationData($token) {
        $url = 'https://api.netatmo.com/api/getstationsdata?access_token='.$token;
        
        @$data = file_get_contents($url);
        if(empty($data)) {
            return false;
        }
        
        $data = json_decode($data, true);
        if(!is_array($data) || !isset($data['body']) || !is_array($data['body']) || !isset($data['body']['devices']) || !is_array($data['body']['devices'])) {
            return false;
        }
        return array_shift($data['body']['devices']);
    }
    
    protected function generateResponse($shouldEndSession) {
        $response = new Response($shouldEndSession);
        
        $token = $this->user->token;
        if(empty($token)) {
            $response->forceAcccountLinking(((isset($skill['authorizationRequestMessage']))? $skill['authorizationRequestMessage']: ''));
        }
        else {
            $data = $this->getStationData($token);
            if(is_array($data) && isset($data['station_name'])) {
                //var_dump($data);
                $response->addText('<p>Here is the summary for '.$data['station_name'].'</p>');
                $response->addText('<p>Temperature '.$data['dashboard_data']['Temperature'].'</p>');//temperature
                $response->addText('<p>Humidity '.$data['dashboard_data']['Humidity'].'</p>');//humidity
                $response->addText('<p>Absolute pressure '.$data['dashboard_data']['AbsolutePressure'].'</p>');//presure
                $response->addText('<p>Noise level '.$data['dashboard_data']['Noise'].'</p>');//noise
                $response->addText('<p>Carbon dioxide level '.$data['dashboard_data']['CO2'].'</p>');//co2
                
                $response->setTitle($data['station_name'].' weather station');
                
                $description = "Temperature: ".$data['dashboard_data']['Temperature']."\\nHumidity: ".$data['dashboard_data']['Humidity']."\\nAbsolute pressure: ".$data['dashboard_data']['AbsolutePressure']."\\nNoise level: ".$data['dashboard_data']['Noise']."\\nCarbon dioxide level: ".$data['dashboard_data']['CO2'];
                $response->setDescription($description);
            }
            else {
                $response->forceAcccountLinking(((isset($skill['authorizationRequestMessage']))? $skill['authorizationRequestMessage']: ''));
            }
        }
        
        return $response;
    }
    
    public function ask($params = array()) {
        return $this->generateResponse(true);
    }
    
    public function run($params = array()) {
        return $this->generateResponse(true);
    }
}

