<?php
namespace CHOK9938\FixTime;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class FixTime extends PluginBase{
    public function onEnable(){
        @mkdir($this->getDataFolder(), 0744, true);
        $this->saveResource('config.yml', false);
        $config = new Config($this->getDataFolder().'config.yml', Config::YAML);
        if($config->get('timezone-mode') === 'manual'){
            $tZ = $config->get('manual-timezone');
            $this->getServer()->getLogger()->info('Setting timezone to '.$tZ.'...');
            $this->setTimezoneManual($tZ);
        }else{
            $this->getServer()->getLogger()->info('Getting timezone from your ip address in the background...');
            $this->setTimezoneAuto();
        }
    }
    
    public static function isAvailableTimezome(string $timezone) : bool{
        $list = \timezone_identifiers_list();
        return in_array($timezone, $list);
    }
    
    private function setTimezoneManual(string $timezone) : void{
        if(self::isAvailableTimezome($timezone)){
            date_default_timezone_set($timezone);
            $this->getServer()->getLogger()->info('Set timezone to '.$timezone);
        }else{
            $this->getServer()->getLogger()->info("Timezone {$timezone} is not available");
        }
        $this->disable();
    }
    
    private function setTimezoneAuto() : void{
        $this->getServer()->getAsyncPool()->submitTask(new SetTimezoneTask($this));
    }
    
    public function disable(){
        $this->getServer()->getPluginManager()->disablePlugin($this);
    }
}