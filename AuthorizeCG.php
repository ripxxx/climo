<?php
/**
 * Created by Aleksandr Berdnikov.
 * Copyright 2016 Onix-Systems.
*/

namespace climo;

use AlexaPHPSDK\Authorize as BaseAuthorize;
use AlexaPHPSDK\Skill;

class Authorize extends BaseAuthorize {
    
    protected function getAccessToken($code, $redirectUrl) {
        $url = $this->config['token_url'];

        $postData = http_build_query(array(
            'grant_type' => "authorization_code",
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'code' => $code,
            'redirect_uri' => $redirectUrl,
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
        $response = file_get_contents($url, false, $context);
        $params = json_decode($response, true);//"https://api.netatmo.com/api/getuser?access_token="

        return $params;
    }
    
    public function run(array $params, array $skillParams) {
        $skill = Skill::getInstance();
        $redirectUrl = $skill['skillHttpsUrl'];
        if((count($skillParams) > 0) && ($skillParams[0] == 'back')) {
            if(isset($params['code']) && isset($skill['authorizationRedirectUrl'])) {
                $authorizationRedirectUrl = $skill['authorizationRedirectUrl'];
                $_params = $this->getAccessToken($params['code'], rtrim($redirectUrl, '/').'/authorize/back');
                $requestParams = array(
                    "access_token" => $_params['access_token'],
                    "token_type" => "Bearer",
                    "expires_in" => $_params['expires_in'],
                    "refresh_token" => $_params['refresh_token'],
                    'state' => ((isset($params['state']))? $params['state']: ''),
                );
                $authorizationRedirectUrl.= ((strpos($authorizationRedirectUrl, '?') === false)? '?': '&').http_build_query($requestParams);

                if(!$this->redirect($authorizationRedirectUrl)) {
                    $this->router->notFound();
                    return false;
                }
                return true;
            }
            $this->router->notFound();
            return false;
        }
        else if((count($skillParams) > 0) && ($skillParams[0] == 'refresh')) {
            
        }
        else {
            $url = $this->config['url'];
            $requestParams = array('scope' => $this->config['scope']);
            $requestParams['client_id'] = $this->config['client_id'];
            $requestParams['client_secret'] = $this->config['client_secret'];
            $requestParams['response_type'] = 'code';
            $requestParams['redirect_uri'] = rtrim($redirectUrl, '/').'/authorize/back';
            $requestParams['state'] = ((isset($params['state']))? $params['state']: '');

            $url.= '?'.http_build_query($requestParams);

            $this->redirect($url);
            return true;
        }
    }
  
}
