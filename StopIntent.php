<?php
/**
 * Created by Aleksandr Berdnikov.
 * Copyright 2016 Onix-Systems.
*/

namespace climo;

use AlexaPHPSDK\Intent;
use AlexaPHPSDK\Response;

class StopIntent extends Intent {
    
    public function ask($params = array()) {
        
        return $this->endSessionResponse();
    }
    
    public function run($params = array()) {

        return $this->endSessionResponse();
    }
    
}

