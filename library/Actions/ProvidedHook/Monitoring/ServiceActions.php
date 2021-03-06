<?php

namespace Icinga\Module\Actions\ProvidedHook\Monitoring;

use Exception;
use Icinga\Application\Config;
use Icinga\Module\Monitoring\Hook\ServiceActionsHook;
use Icinga\Module\Monitoring\Object\Service;
use Icinga\Web\Url;

class ServiceActions extends ServiceActionsHook
{
    public function getActionsForService(Service $service)
    {
        $config = Config::app('modules/actions/actions');
        $this->config = $config;
	
	$returnarray=array();
	foreach($this->config as $action => $c){
		if(preg_match("/".$c->filter."/",$service->getName()) && $c->type==="service"){
			$returnarray[$action] = "actions/command?action=$action&host=".$service->getHost()->getName();
		}
	}
	return $returnarray;
    }
}
