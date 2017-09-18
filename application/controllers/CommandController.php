<?php

namespace Icinga\Module\Actions\Controllers;


use Icinga\Web\Controller;
use Icinga\Web\Navigation\Navigation;
use Icinga\Application\Config;	

class CommandController  extends Controller
{
    public function indexAction()
    {        
	    $auth = Auth::getInstance();
            if ($auth->hasPermission('actions/command')){

		    $host = $this->params->getRequired('host');

		    $action = $this->params->getRequired('action');
		    $config = Config::app('modules/actions/actions');
		    $command = str_replace('$host_name$',$host,$config->get($action,"command"));
		    $this->view->command = $command;
		    $this->view->action = $action;
		    $this->view->output .= shell_exec($command." 2>&1");
		    $this->getTabs();
	    }
    }
    public function getTabs()
	{
		$tabs = parent::getTabs();
		$tabs->add(
			'action',
			array(
				'title' => 'Action',
				'url' => 'actions/config/update?action='.$this->view->action,
				'baseTarget' => '_main'
			)
		);
		return $tabs;
	} 
}

