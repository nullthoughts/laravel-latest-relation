# Laravel Latest Relation
Eloquent macros for querying the latest HasMany relationship in Laravel. 

More information on the problem and solutions: [Dynamic scope on latest record in Laravel's HasMany relationships, Part 1: solving with Subqueries - nullthoughts.com](https://nullthoughts.com/development/2019/10/08/dynamic-scope-on-latest-relationship-in-laravel/)

## Installation
Install via composer:
`composer require nullthoughts/laravel-latest-relation`

## Usage / Examples
Use the Builder methods inside a whereHas closure:

### Latest:

#### whereLatestRelation($relation, $column, $value)
**Query**
```php
$users = User::whereLatestRelation('logins', 'device_type', 'desktop');
```

**Dynamic Scope**
```php
public function scopeUsingDevice($query, $device)
{
    return $query->whereLatestRelation('logins', 'device_type', $device);
}
```

#### whereLatest($column, $value)
**Query**
```php
$users = User::whereHas('logins', function ($query) {
    $query->whereLatest('device_type', 'desktop');
});
```

**Dynamic Scope**
```php
public function scopeUsingDevice($query, $device)
{
    return $query->whereHas('logins', function ($query) use ($device) {
        $query->whereLatest('device_type', $device);
    });
}
```

#### latestRelation()
**Query**
```php
$users = User::whereHas('logins', function ($query) {
    $query->latestRelation()->whereBetween(
        'created_at', [
            Carbon::now()->startOfDay(),
            Carbon::now()->endOfDay()
        ]);
});
```

**Dynamic Scope**
```php
public function scopeHavingDeviceType($query)
{
    return $query->whereHas('logins', function ($query) {
        $query->latestRelation()->whereNotNull('device_type');
    });
}
```

### Earliest:

```php
$users = User::whereLatestRelation('logins', 'device_type', 'desktop');

$users = User::whereHas('logins', function ($query) {
    $query->whereEarliest('device_type', 'desktop');
});

$users = User::whereHas('logins', function ($query) {
    $query->earliestRelation()->whereNotNull('device_type');
});
```