<?php

use Icinga\Authentication\Auth;
$auth = Auth::getInstance();

$this->providePermission('actions/conf', $this->translate('Allow to configure actions'));
$this->providePermission('actions/command', $this->translate('Allow to execute actions'));

$this->provideConfigTab('config', array(
    'title' => 'Configuration',
    'label' => 'Configuration',
    'url' => 'config'
));

