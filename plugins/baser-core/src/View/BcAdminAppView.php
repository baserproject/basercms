<?php
namespace BaserCore\View;

class BcAdminAppView extends AppView {
    public function initialize():void
    {
        parent::initialize();
        $this->loadHelper('BaserCore.BcAdminForm', ['templates' => 'BaserCore.bc_form']);
        $this->loadHelper('BaserCore.BcBaser');
        $this->loadHelper('BaserCore.BcAuth');
        $this->loadHelper('BaserCore.BcAdmin');
        if(!$this->get('title')) {
            $this->set('title', 'Undefined');
        }
    }
}
