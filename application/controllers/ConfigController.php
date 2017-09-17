<?php

namespace Icinga\Module\Actions\Controllers;


use Icinga\Exception\NotFoundError;
use Icinga\Forms\ConfirmRemovalForm;
use Icinga\Web\Controller;
use Icinga\Web\Notification;
use Icinga\Module\Actions\Forms\Config\GeneralConfigForm;

class ConfigController  extends Controller
{
    public function init()
    {
	$this->assertPermission('actions/config');
    }

    /**
     * List Actions
     */
    public function indexAction()
    {
        $this->getTabs()->add('actions', array(
            'active'    => true,
            'label'     => $this->translate('Actions'),
            'url'       => $this->getRequest()->getUrl()
        ));
        $this->view->actions = $this->Config('actions');
	$this->view->user = exec("whoami");
    }
    /**
     * Add a new Action
     */
    public function newAction()
    {
        $this->getTabs()->add('new-action', array(
            'active'    => true,
            'label'     => $this->translate('New Action'),
            'url'       => $this->getRequest()->getUrl()
        ));
        $actions = new GeneralConfigForm();
        $actions
            ->setIniConfig($this->Config('actions'))
            ->setRedirectUrl('actions/config')
            ->handleRequest();
        $this->view->form = $actions;
    }
    /**
     * Remove a action
     */
    public function removeAction()
    {
        $action = $this->params->getRequired('action');
        $this->getTabs()->add('remove-action', array(
            'active'    => true,
            'label'     => $this->translate('Remove Action'),
            'url'       => $this->getRequest()->getUrl()
        ));
        $actions = new GeneralConfigForm();
        try {
            $actions
                ->setIniConfig($this->Config('actions'))
                ->bind($action);
        } catch (NotFoundError $e) {
            $this->httpNotFound($e->getMessage());
        }
        $confirmation = new ConfirmRemovalForm(array(
            'onSuccess' => function (ConfirmRemovalForm $confirmation) use ($action, $actions) {
                $actions->remove($action);
                if ($actions->save()) {
                    Notification::success(mt('actions', 'Action removed'));
                    return true;
                }
                return false;
            }
        ));
        $confirmation
            ->setRedirectUrl('actions/config')
            ->setSubmitLabel($this->translate('Remove Action'))
            ->handleRequest();
        $this->view->form = $confirmation;
    }
    /**
     * Update a action
     */
    public function updateAction()
    {
        $action = $this->params->getRequired('action');
        $this->getTabs()->add('update-action', array(
            'active'    => true,
            'label'     => $this->translate('Update Action'),
            'url'       => $this->getRequest()->getUrl()
        ));
        $actions = new GeneralConfigForm();
        try {
            $actions
                ->setIniConfig($this->Config('actions'))
                ->bind($action);
        } catch (NotFoundError $e) {
            $this->httpNotFound($e->getMessage());
        }
        $actions
            ->setRedirectUrl('actions/config')
            ->handleRequest();
        $this->view->form = $actions;
    }
}

