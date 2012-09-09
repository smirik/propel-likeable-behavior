LikeableBehavior
====================

Installation
------------

Download LikeableBehavior.php and put it somewhere.

``` ini
propel.behavior.likeable.class = path.to.likeable.behavior
```

If you are using composer then just add:
```js
{
    "require": {
        "smirik/propel-likeable-behavior": "*"
    }
}
```

Usage
-----

Add to schema.xml:

``` xml
<behavior name="likeable" />
```

Behavior will create table *likes* and add several methods to the Model:

``` php

public function addLike($user_id, $mark = 1, PropelPDO $con = null);
public function removeLike($user_id, PropelPDO $con = null)
public function hasLike($user_id, PropelPDO $con = null)
public function countLikes(PropelPDO $con = null)

```

*user_id* could be any integer.

Requirements
------------

* Model should have getId() method.
* Type will be placed automatically based on class name (with namespace)

Credits
-------
* William Durand <william.durand1@gmail.com> for documentation.
* Glorpen https://bitbucket.org/glorpen/taggablebehaviorbundle for samples.

