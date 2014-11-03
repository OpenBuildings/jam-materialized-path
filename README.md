Jam Materialized Path
=====================

[![Build Status](https://travis-ci.org/OpenBuildings/jam-materialized-path.png?branch=master)](https://travis-ci.org/OpenBuildings/jam-materialized-path)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/OpenBuildings/jam-materialized-path/badges/quality-score.png)](https://scrutinizer-ci.com/g/OpenBuildings/jam-materialized-path/)
[![Code Coverage](https://scrutinizer-ci.com/g/OpenBuildings/jam-materialized-path/badges/coverage.png)](https://scrutinizer-ci.com/g/OpenBuildings/jam-materialized-path/)
[![Latest Stable Version](https://poser.pugx.org/OpenBuildings/jam-materialized-path/v/stable.png)](https://packagist.org/packages/OpenBuildings/jam-materialized-path)

Materialized path nesting for Jam ORM models

Usage
-----

Add this behaviors your Model

```php
class Model_Category extends Jam_Model {

    public static function initialize(Jam_Meta $meta)
    {
        $meta
            ->behaviors(array(
                'materializedpath' => Jam::behavior('materializedpath')
            ));
    }
}
```

__Database Table:__

```
┌─────────────────────────┐
│ Table: Category         │
├─────────────┬───────────┤
│ id          │ ingeter   │
│ name        │ string    │
│ parent_id*  │ integer   │
│ path*       │ string    │
└─────────────┴───────────┘
* Required fields
```

Methods
-------

It will add "parent" and "children" associations to the repo. The model will get the convenience methods:

Method                                    | Description
------------------------------------------|--------------------------------------------------
__decendents__()                          | Get a query builder collection for all the decendents
__ansestors__()                           | Get a query builder collection for all the ansestors
__is_root__()                             | Boolean check if it is root (has parent) or not
__is\_descendent\_of__(Jam_Model $parent) | Chech if a model is descendant
__is\_ansestor\_of__(Jam_Model $child)    | Chech if model is ansestor
__depth__()                               | The depth of the item in the hierarchy

License
-------

Copyright (c) 2014, Clippings Ltd. Developed by Ivan Kerin

Under BSD-3-Clause license, read LICENSE file.
