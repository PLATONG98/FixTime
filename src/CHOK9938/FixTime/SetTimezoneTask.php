<?php
namespace CHOK9938\FixTime;

use pocketmine\Server;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Internet;

class SetTimezoneTask extends AsyncTask{
    public function __construct(Plugin $plugin){
        $this->plugin = $plugin;
    }
    
    public function onRun(){
        $data = Internet::getURL($this->getApiURL(), 15);
        $this->setResult(@json_decode($data, true));
    }
    
    public function onCompletion(Server $server){
        $data = $this->getResult();
        if($data === null){
            $server->getLogger()->info('Couldn\'t get and set timezone');
        }else{
            $tzone = $data['timezone'];
            if(TimezoneFixer::isAvailableTimezome($tzone)){
                date_default_timezone_set($tzone);
                $server->getLogger()->info('Timezone received. Set timezone to '.$tzone);
            }else{
                $this->getServer()->getLogger()->info('Received Timezone '.$tzone.' is not available.');
            }
        }
        $this->instance->disable();
    }
    
    protected function getApiURL() : string{
        return 'http://ip-api.com/json';
    }
}