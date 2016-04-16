# Smoothness
A Laravel package to manage WHERE clause.  
(This is for Laravel 5+. [For Laravel 4.2](https://github.com/SUKOHI/Smoothness/tree/1.0))

[Demo](http://demo-laravel52.capilano-fw.com/smoothness)

# Installation

Execute composer command.

    composer require sukohi/smoothness:2.*

# Preparation

At first, set `SmoothnessTrait` in your Model.

    use Sukohi\smoothness\SmoothnessTrait;
    
    class Item extends Eloquent {
    
        use SmoothnessTrait;

    }

Secondary, add configuration values also in your Model.

**columns:** Columns and Labels you want to use. (Required)  
**condition:** Condition type which are and &amp; or (Optional, Default: and)  

	protected $smoothness = [
		'columns' => [
			'id' => 'ID',
			'title' => 'Title',
			'created_at' => 'Date'
		],
		'condition' => 'and'
	];

**Query Scope:** You also can utilize `Query Scopes` instead of column name.  

    'columns' => [
        'scope::filterTitle' => 'LABEL'
    ],

in this case, you need to prepare a scope method in your model. ([About Query Scopes](https://laravel.com/docs/4.2/eloquent#query-scopes))
    
    public function scopeFilterTitle($query, $value) {

        return $query->where('title', $value);

    }

# Usage

Now you can use a method called `smoothness`.

(in Controller)

    $items = Item::smoothness()->get();

After call `smoothness()`, you can access to sort data through `$smoothness`.
    
(in View)

**condition:** Current condition. `and` &amp; `or`.

    Condition: {{ $smoothness->condition }}
    
**values:** Submitted data.
    
    @foreach($smoothness->values as $column => $value)
        {{ $column }} => {{ $value }}
    @endforeach

**symbols:** Submitted data that has value. 

    @foreach($smoothness->has_values as $column => $value)
        {{ $column }} => {{ $value }}
    @endforeach

**has_columns:** Array values of columns that has value.

    @foreach($smoothness->columns as $column => $label)
        {{ $label }}:<input type="text" name="{{ $column }}" value="{{ $smoothness->values->$column }}"><br>
    @endforeach

**appends:** Array values for pagination
  
    {{ $items->appends($smoothness->appends)->links() }}

# Change condition
The 1st argument is for setting condition type.

    Item::smoothness('or')->get();

# Relationship

You can use this package with relationship using join().

(in Controller)

    $items = Item::join('item_details', 'item_details.item_id', '=', 'items.id')
                ->smoothness()
                ->paginate(5);

(in Model)

	protected $smoothness = [
		'columns' => [
			'items.id' => 'ID',
			'items.title' => 'Title',
			'items.created_at' => 'Date',
			'item_details.address' => 'Address'
		]
	];

# License

This package is licensed under the MIT License.

Copyright 2016 Sukohi Kuhoh