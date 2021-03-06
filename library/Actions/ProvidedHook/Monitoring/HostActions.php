<?php

namespace Icinga\Module\Actions\ProvidedHook\Monitoring;

use Icinga\Module\Monitoring\Hook\HostActionsHook;
use Icinga\Module\Monitoring\Object\Host;
use Icinga\Application\Config;	

class HostActions extends HostActionsHook
{
    public function getActionsForHost(Host $host)
    {
        $config = Config::app('modules/actions/actions');
        $this->config = $config;
	
	$returnarray=array();
	foreach($this->config as $action => $c){
		if(preg_match("/".$c->filter."/",$host->getName()) && $c->type === "host"){
			$returnarray[$action] = "actions/command?action=$action&host=".$host->getName();
		}
	}
	return $returnarray;
    }
}
