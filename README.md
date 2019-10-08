# Laravel Latest Relation
Eloquent macros for querying the latest HasMany relationship in Laravel

## Installation
Install via composer:
`composer require nullthoughts/laravel-latest-relation`

## Usage / Examples
Use the Builder methods inside a whereHas closure:

### Latest:

#### whereLatest($column, $value)
**Query**
```php
$users = User::whereHas('logins', function ($query) {
    $query->whereLatest('device_type', 'desktop');
});
```

**Dynamic Scope**
```php
public function scopeByCondition($query, $condition)
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
public function scopeByCondition($query, $condition)
{
    return $query->whereHas('logins', function ($query) {
        $query->latestRelation()->whereNotNull('device_type');
    });
}
```

### Earliest:

```php
$users = User::whereHas('logins', function ($query) {
    $query->whereEarliest('device_type', 'desktop');
});

$users = User::whereHas('logins', function ($query) {
    $query->earliestRelation()->whereNotNull('device_type');
});
```