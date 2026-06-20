<?php

namespace BcBurgerEditor\View\Cell;

use BcBurgerEditor\Lib\BurgerEditorUtil;
use Cake\View\Cell;

class BurgerEditorCell extends Cell
{

	// エディタ出力
	public function display($inputId, $draftId)
	{
		$this->set([
			'inputId' => $inputId,
			'draftId' => $draftId,
			'addonDir' => BurgerEditorUtil::getAddonPath()
		]);
	}

}
