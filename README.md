# Smoothness
A Laravel package to manage WHERE clause.  
(This is for Laravel 4.2. [For Laravel 5](https://github.com/SUKOHI/Smoothness))

[Demo](http://demo-laravel52.capilano-fw.com/smoothness)

# Installation

Execute composer command.

    composer require sukohi/smoothness:3.*

# Preparation

At first, set `SmoothnessTrait` in your Model.

    use Sukohi\smoothness\SmoothnessTrait;
    
    class Item extends Eloquent {
    
        use SmoothnessTrait;

    }

Secondary, add configuration values also in your Model.

**columns:** keys and column names you want to use. (Required)  
**labels:** Labels and keys you want to use. (Optional)  
**condition:** Condition type which are and &amp; or (Optional, Default: auto)  

	protected $smoothness = [
		'columns' => [
			'search_id' => 'id',
			'search_title' => 'title',
			'search_date' => 'created_at'
		],
		'labels' => [
			'id' => 'ID',
			'title' => 'Item Title',
			'search_date' => 'Date'
		],
		'condition' => 'and'
	];

**Note:**  If you set `auto` in `condition`, you can change condition value through URL params like this.  

    http://example.com/test?condition=or

**Query Scope:** You also can utilize `Query Scopes` instead of column name.  

    'columns' => [
        'scope_title' => 'scope::filterTitle'
    ],

in this case, you need to prepare a scope method in your model. ([About Query Scopes](https://laravel.com/docs/4.2/eloquent#query-scopes))
    
    public function scopeFilterTitle($query, $value) {

        return $query->where('title', $value);

    }
    
    or you also can get condition value at the same time.
    
    public function scopeFilterTitle($query, $value, $condition) {

        if($condition == 'or') {
        
            return $query->orWhere('title', $value);
        
        }

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
    
    @foreach($smoothness->values as $key => $value)
        <input type="text" name="{{ $key }}" value="{{ $value }}">
    @endforeach
    
    or
        
    $smoothness->values->get('KEY_NAME');

**has_values:** Submitted data that has value. 

    @foreach($smoothness->has_values as $column => $value)
        {{ $column }} => {{ $value }}
    @endforeach

    or
        
    $smoothness->has_values->get('KEY_NAME');

**labels:** Array values of columns that has value.

    @foreach($smoothness->labels as $key => $label)
        {{ $label }}
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