<?php

namespace Icinga\Module\Actions\Controllers;


use Icinga\Exception\NotFoundError;
use Icinga\Web\Controller;
use Icinga\Web\Notification;
use Icinga\Web\Navigation\Navigation;

class CommandController  extends Controller
{
    public function indexAction()
    {
		
	    $this->view->action = $this->params->getRequired('action');
	    $this->view->command = $this->params->getRequired('command');
	    $this->view->pwd = shell_exec("pwd");
	    $this->view->output .= shell_exec($this->view->command." 2>&1");
	    $this->getTabs();
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

