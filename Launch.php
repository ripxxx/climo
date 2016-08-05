<?php
/**
 * Created by Aleksandr Berdnikov.
 * Copyright 2016 Onix-Systems.
*/

namespace climo;

use AlexaPHPSDK\LaunchRequest;
use AlexaPHPSDK\Response;

class Launch extends LaunchRequest {
    
    public function run($params = array()) {
        $statusIntent = new StatusIntent($this->user);
        return $statusIntent->run($params);
    }
    
}

