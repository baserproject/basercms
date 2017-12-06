<?php

App::uses('DataSource', 'Model/Datasource');

class TestSource extends DataSource {

	public function describe($model) {
		return compact('model');
	}

	public function listSources($data = null) {
		return ['test_source'];
	}

	public function create(Model $model, $fields = [], $values = []) {
		return compact('model', 'fields', 'values');
	}

	public function read(Model $model, $queryData = [], $recursive = null) {
		return compact('model', 'queryData');
	}

	public function update(Model $model, $fields = [], $values = [], $conditions = null) {
		return compact('model', 'fields', 'values');
	}

	public function delete(Model $model, $id = null) {
		return compact('model', 'id');
	}
}
