<?php
class BlogHookBehavior extends ModelBehavior {
	var $registerHooks = array('beforeFind');
	function  beforeFind(&$model, $query) {
		if($model->alias == 'BlogPost') {
			$query['conditions']['BlogPost.name LIKE'] = '%新商品%';
		}
		return $query;
	}
}