<?php namespace Sukohi\Smoothness;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;

trait SmoothnessTrait {

	public function scopeSmoothness($query, $condition = '') {

		if(empty($condition)) {

			$condition = $this->getSmoothnessCondition();

		}

		$current_values = new \stdClass();
		$current_has_values = new \stdClass();
		$has_columns = [];
		$default_flag = true;

		foreach ($this->smoothness['columns'] as $column => $label) {

			$under_bar_column = str_replace('.', '_', $column);
			$value = Request::get($under_bar_column);
			$current_values->$column = $value;

			if(Request::exists($under_bar_column)) {

				$default_flag = false;

				if(!Request::has($under_bar_column)) {

					continue;

				}

				$current_has_values->$column = $value;
				$has_columns[] = $column;

				if(strpos($column, 'scope::') === 0) {

					$method = camel_case(str_replace('::', '_', $column));

					if(method_exists($this, $method)) {

						$this->$method($query, $value);

					} else {

						throw new \Exception('Method '. $method .'() Not Found.');

					}

				} else {

					$where = ($condition == 'or') ? 'orWhere' : 'where';
					$query->$where($column, 'LIKE', '%'. $value .'%');

				}

			}

		}

		if(!$default_flag && empty(implode('', (array)$current_values))) {

			$query->whereRaw('1 <> 1');

		}

		$results = new \stdClass();
		$results->values = $current_values;
		$results->has_values = $current_has_values;
		$results->has_columns = $has_columns;
		$results->columns = $this->getSmoothnessColumns();
		$results->appends = (array)$current_values;
		$results->condition = $condition;
		View::Share('smoothness', $results);

	}

	private function getSmoothnessColumns() {

		$columns = new \stdClass();

		foreach ($this->smoothness['columns'] as $column => $label) {

			$columns->$column = $label;

		}

		return $columns;

	}

	private function getSmoothnessCondition() {

		return $this->smoothness['condition'];

	}

}