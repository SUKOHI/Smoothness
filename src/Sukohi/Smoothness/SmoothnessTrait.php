<?php namespace Sukohi\Smoothness;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;

trait SmoothnessTrait {

	public function scopeSmoothness($query, $condition = '') {

		$condition = $this->getSmoothnessCondition($condition);
		$current_values = $current_has_values = $has_keys = [];
		$default_flag = true;

		foreach ($this->smoothness['columns'] as $key => $column) {

			$value = Request::get($key);
			$current_values[$key] = $value;

			if(Request::exists($key)) {

				$default_flag = false;

				if(!Request::has($key)) {

					continue;

				}

				$current_has_values[$key] = $value;
				$has_keys[] = $key;

				if(strpos($column, 'scope::') === 0) {

					$method = camel_case(str_replace('::', '_', $column));

					if(method_exists($this, $method)) {

						$this->$method($query, $value, $condition);

					} else {

						throw new \Exception('Method '. $method .'() Not Found.');

					}

				} else {

					$where = ($condition == 'or') ? 'orWhere' : 'where';
					$query->$where($column, 'LIKE', '%'. $value .'%');

				}

			}

		}

		if(!$default_flag && empty(implode('', $current_values))) {

			$query->whereRaw('1 <> 1');

		}

		$results = new \stdClass();
		$results->values = Collection::make($current_values);
		$results->has_values = Collection::make($current_has_values);
		$results->labels = $this->getSmoothnessLabels();
		$results->has_keys = $has_keys;
		$results->appends = $current_values;
		$results->condition = $condition;
		$results->conditions = $this->getSmoothnessConditions($condition);
		View::Share('smoothness', $results);

	}

	private function getSmoothnessCondition($condition) {

		if(!isset($this->smoothness['condition']) || $this->smoothness['condition'] == 'auto') {

			$condition = Request::get('condition');

		}

		if(!in_array($condition, ['and', 'or'])) {

			$condition = 'and';

		}

		return $condition;

	}

	private function getSmoothnessConditions($condition) {

		return Collection::make([
			'and' => ($condition == 'and'),
			'or' => ($condition == 'or')
		]);

	}

	private function getSmoothnessLabels() {

		$labels = array_get($this->smoothness, 'labels', []);
		return Collection::make($labels);

	}

}