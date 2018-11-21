<?php 

App::uses('Component', 'Controller');

//load twilio library
require_once(ROOT . DS . 'vendors' . DS . 'twilio'.DS. 'sdk' . DS . 'Twilio' . DS . 'autoload.php');
use Twilio\Rest\Client;

class SmsComponent extends Component {
    private $strings = array('Hey %1$s, time to play the %3$s against %2$s', '%1$s it is your turn to play the %3$s against %2$s','%1$s, you\'re playing the %3$s against %2$s now','Mario Kart Time! %3$s, %1$s vs %2$s');
    
    function notifyNext($phone,$player1,$player2,$cup){
        $aString = $this->strings[rand(0,count($this->strings) - 1)];
        $this->sendSMS($phone, sprintf($aString,$player1,$player2,$cup));
    }
    
    function sendSMS($phone,$message){
        
        //check that we have a real phone number
        if($this->validatePhone($phone))
        {
            $client = new Client("AC867dfd10155a3580b6df8eb314b6ff85","a33ea4c205f004a39f2f4deecc2a1bbb");
        
            $client->messages->create('+1' . $phone,array('from'=>'+17153188779 ','body'=>$message));
        }
    }
    
    function validatePhone($phone){
        $result = false;
        
        if($phone != null && strlen($phone) >= 10)
        {
            $result = true;
        }
        
        return $result;
    }
}

?>