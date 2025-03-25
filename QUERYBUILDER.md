# Laravel Query Builder Documentation

## Overview

The Laravel Query Builder provides a convenient, fluent interface for working with your database. It allows you to build SQL queries in a programmatic way and helps manage complex queries with ease. In this documentation, we'll cover the basic query builder methods and then focus on the methods used in the `DashboardController` for the specific case of getting dashboard statistics.

---

## Basic Query Builder Methods

Here are some of the most commonly used methods provided by the Query Builder in Laravel:

### 1. `table()`
The `table` method is used to specify which table you want to query.

```php
DB::table('users');
```

### 2. `select()`
The `select` method is used to define the columns that should be retrieved.

```php
DB::table('users')->select('name', 'email');
```

### 3. `where()`
The `where` method adds basic `WHERE` clauses to your query.

```php
DB::table('users')->where('status', 'active');
```

### 4. `join()`
The `join` method is used to perform an SQL `JOIN` between two tables.

```php
DB::table('orders')
    ->join('users', 'orders.user_id', '=', 'users.id');
```

### 5. `groupBy()`
The `groupBy` method is used to group results by one or more columns.

```php
DB::table('plants')
    ->groupBy('category_id');
```

### 6. `orderBy()`
The `orderBy` method is used to order the results by one or more columns.

```php
DB::table('users')->orderBy('name', 'asc');
```

### 7. `limit()`
The `limit` method is used to limit the number of results returned.

```php
DB::table('users')->limit(10);
```

### 8. `count()`
The `count` method is used to count the number of rows that match the query.

```php
DB::table('orders')->count();
```

### 9. `raw()`
The `raw` method is used to execute raw SQL expressions.

```php
DB::table('order_plant')
    ->select(DB::raw('count(plant_id) as orders_count'));
```

### 10. `get()`
The `get` method retrieves the results of the query.

```php
DB::table('users')->get();
```

---

## Full Example of the Code in `DashboardController`

The following code uses the Query Builder methods described above to retrieve the dashboard statistics:

```php
public function index()
{
    try {
        // Total number of orders
        $totalOrders = Order::count();

        // Most popular plants (based on the number of times they have been ordered)
        $mostPopularPlants = DB::table('order_plant')
            ->select('plants.name', DB::raw('count(order_plant.plant_id) as orders_count'))
            ->join('plants', 'plants.id', '=', 'order_plant.plant_id')
            ->groupBy('plants.name')
            ->orderByDesc('orders_count')
            ->limit(5)
            ->get();

        // Plant distribution by category
        $categoryDistribution = DB::table('plants')
            ->select('categories.name as category_name', DB::raw('count(plants.id) as plant_count'))
            ->join('categories', 'categories.id', '=', 'plants.category_id')
            ->groupBy('categories.name')
            ->get();

        return response()->json(compact('totalOrders', 'mostPopularPlants', 'categoryDistribution'), 200);
    } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => $th->getMessage(),
        ], 500);
    }
}
```

### Summary

In this controller, we used the following methods from the Query Builder:

- `table()` — Specifies the table to query.
- `select()` — Defines the columns to retrieve.
- `join()` — Joins multiple tables.
- `groupBy()` — Groups the results.
- `orderByDesc()` — Orders the results in descending order.
- `limit()` — Limits the number of records returned.
- `get()` — Executes the query and retrieves the results.

---

## Conclusion

Laravel's Query Builder provides a clean and expressive syntax for interacting with your database. It abstracts much of the complexity of SQL while still allowing you to write powerful, flexible queries. By using the methods discussed in this document, you can easily perform tasks like selecting, filtering, grouping, and ordering data, making it a great tool for building robust applications.

