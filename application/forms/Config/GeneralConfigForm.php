<?php

namespace Icinga\Module\Actions\Forms\Config;

use Icinga\Exception\AlreadyExistsException;
use Icinga\Exception\IcingaException;
use Icinga\Exception\NotFoundError;
use Icinga\Forms\ConfigForm;
use Icinga\Web\Notification;

class GeneralConfigForm extends ConfigForm
{
    protected $boundAction;
    
    public function init()
    {
        $this->setName('form_config_actions_commmand');
    }

    public function createElements(array $formData)
    {
        $this->addElement(
            'text',
            'name',
            array(
                'description'   => $this->translate('The name of the action'),
                'label'         => $this->translate('Name'),
                'required'      => true
            )
        );

        $this->addElement(
            'text',
            'command',
            array(
                'label'         => $this->translate('Command'),
                'description'   => $this->translate('Command wich will be executed in when you click on the action. You can use the $host_name$ variable here.'),
                'required'      => true
            )
        );
        $this->addElement(
            'text',
            'filter',
            array(
                'label'         => $this->translate('Filter'),
                'description'   => $this->translate('Action will only displayed on Services or Hosts matching this regex'),
                'required'      => true
            )
        );
        $this->addElement(
            'select',
            'type',
            array(
		'label'         => $this->translate('Service or Host Action'),
		'multiOptions'  => array(
			'host'  	=> $this->translate('Host'),
			'service'  	=> $this->translate('Service'),
		),
                'description'   => $this->translate('Choose if it is a Host or Service Action'),
                'required'      => true
            )
        );
    }

    public function getSubmitLabel()
    {
        if (($submitLabel = parent::getSubmitLabel()) === null) {
            if ($this->boundAction === null) {
                $submitLabel = $this->translate('Add action');
            } else {
                $submitLabel = $this->translate('Update action');
            }
        }
        return $submitLabel;
    }

    public function onRequest()
    {
        return;
    }

    public function onSuccess()
    {
        $name = $this->getElement('name')->getValue();
        $values = array(
            'command'   => $this->getElement('command')->getValue(),
            'filter'   => $this->getElement('filter')->getValue(),
            'type'   => $this->getElement('type')->getValue(),
        );

        if ($this->boundAction === null) {
            $successNotification = $this->translate('Action saved');
            try {
                $this->add($name, $values);
            } catch (AlreadyExistsException $e) {
                $this->addError($e->getMessage());
                return false;
            }
        } else {
            $successNotification = $this->translate('Action updated');
            try {
                $this->update($name, $values, $this->boundAction);
            } catch (IcingaException $e) {
                // Exception may be AlreadyExistsException or NotFoundError
                $this->addError($e->getMessage());
                return false;
            }
        }
        if ($this->save()) {
            Notification::success($successNotification);
            return true;
        }
        return false;
    }

    public function add($name, array $values)
    {
        if ($this->config->hasSection($name)) {
            throw new AlreadyExistsException(
                $this->translate('Can\'t add action \'%s\'. Action already exists'),
                $name
            );
        }
        $this->config->setSection($name, $values);
        return $this;
    }

    public function bind($name)
    {
        if (! $this->config->hasSection($name)) {
            throw new NotFoundError(
                $this->translate('Can\'t load action \'%s\'. Action does not exist'),
                $name
            );
        }
        $this->boundAction = $name;
        $actions = $this->config->getSection($name)->toArray();
        $actions['name'] = $name;
        $this->populate($actions);
        return $this;
    }

    public function remove($name)
    {
        if (! $this->config->hasSection($name)) {
            throw new NotFoundError(
                $this->translate('Can\'t remove action \'%s\'. Action does not exist'),
                $name
            );
        }
        $this->config->removeSection($name);
        return $this;
    }

    public function update($name, array $values, $oldName)
    {
        if ($name !== $oldName) {
            $this->remove($oldName);
            $this->add($name, $values);
        } else {
            if (! $this->config->hasSection($name)) {
                throw new NotFoundError(
                    $this->translate('Can\'t update action \'%s\'. Action does not exist'),
                    $name
                );
            }
            $this->config->setSection($name, $values);
        }
        return $this;
    }
}
