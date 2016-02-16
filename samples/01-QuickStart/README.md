# Laravel Haml Sample

## 1. Use a route as usual

```php
Route::get('/', function () {
    return view('welcome');
});
```

## 2. Code your blade in HAML code

```haml
.container
	.row
		.col-md-10.col-md-offset-1
			.panel.panel-default
				.panel-heading Welcome!
				.panel-body Your Application's Landing Page.
```