<?php

use Icinga\Authentication\Auth;
$auth = Auth::getInstance();

$this->providePermission('action/conf', $this->translate('Allow to configure actions'));

$this->provideConfigTab('config', array(
    'title' => 'Configuration',
    'label' => 'Configuration',
    'url' => 'config'
));

