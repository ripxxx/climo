<?php
/**
 * Created by Aleksandr Berdnikov.
 * Copyright 2016 Onix-Systems.
*/

namespace climo;

use AlexaPHPSDK\Authorize as BaseAuthorize;
use AlexaPHPSDK\Skill;

class Authorize extends BaseAuthorize {
    
    protected function getAccessToken($userName, $userPassword) {
        $url = $this->config['token_url'];

        $postData = http_build_query(array(
            'grant_type' => "password",
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'username' => $userName,
            'password' => $userPassword,
            'scope' => $this->config['scope']
        ));

        $options = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postData
            )
        );

        $context = stream_context_create($options);
        @$response = file_get_contents($url, false, $context);
        if(!is_string($response)) {
            return NULL;
        }
        $params = json_decode($response, true);//"https://api.netatmo.com/api/getuser?access_token="

        return $params;
    }
    
    protected function printLoginFrom($url, $state, $userName = '', $message = '') {
        require('LoginForm.php');
    }
    
    public function run(array $params, array $skillParams) {
        $skill = Skill::getInstance();
        $redirectUrl = $skill['skillHttpsUrl'];
        if((count($skillParams) > 0) && ($skillParams[0] == 'login')) {
            $userName = filter_input(INPUT_POST, 'login');
            $userPassword = filter_input(INPUT_POST, 'password');
            $state = filter_input(INPUT_POST, 'state');
            
            if(empty($userName) || empty($userPassword)) {
                $this->printLoginFrom(rtrim($redirectUrl, '/').'/authorize/login', $state, $userName, 'Please, fill in login and password fields.');
                return false;
            }
            else {
                $params = $this->getAccessToken($userName, $userPassword);
                if(is_null($params)) {
                    $this->printLoginFrom(rtrim($redirectUrl, '/').'/authorize/login', $state, $userName, 'Your login or password is incorrect.');
                    return false;
                }
                $authorizationRedirectUrl = $skill['authorizationRedirectUrl'];
                $requestParams = array(
                    "access_token" => $params['access_token'],
                    "token_type" => "Bearer",
                    //"expires_in" => $params['expires_in'],
                    //"refresh_token" => $params['refresh_token'],
                    'state' => $state,
                );
                $authorizationRedirectUrl.= '#'.http_build_query($requestParams);
                if(!$this->redirect($authorizationRedirectUrl)) {
                    $this->router->notFound();
                    return false;
                }
                return true;
            }
        }
        else {
            $this->printLoginFrom(rtrim($redirectUrl, '/').'/authorize/login', ((isset($params['state']))? $params['state']: ''));
            
            return true;
        }
    }
    
}