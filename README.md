# Laravel Model Soft Cascade

A laravel package to handle cascade delete and restore on model relations.

This package not only handle the cascade delete of chile models on parement model soft delete but also the cascade restore of child model records on parent model restore form soft deleted state . At the same time it can handle the cascade force delete that is by force deleting the parent , all the child will also force delete . See the config file to get more details . 

## Installation

Require the package using composer:

```bash
composer require touhidurabir/laravel-model-soft-cascade
```

To publish the config file:
```bash
php artisan vendor:publish --provider="Touhidurabir\ModelSoftCascade\ModelSoftCascadeServiceProvider" --tag=config
```

## Usage

The config file **soft-cascade** file contains most of the basic configurations details like for which delete or restore event, it will invoke the cascading functionality, should the cascading run as database transactional operation, how it will behave when models get force deleted etc . For full details check the config file .

To use this, simply use the trait **HasSoftCascade** in model and implement the abstract method **cascadable** which will return an **array** . 

```php
class User extends Model {

    use SoftDeletes;

    use HasSoftCascade;

    public function cascadable() : array {

        return [
            'profile', 'posts'
        ];
    }

    public function profile() {

        return $this->hasOne('Touhidurabir\ModelSoftCascade\Tests\App\Profile');
    }

    public function posts() {

        return $this->hasMany('Touhidurabir\ModelSoftCascade\Tests\App\Post');
    }
}
```

> Note : Make sure that model do use the **SoftDeletes** trait otherwise it will throw exception . 

By default the **cascadable** should return an array which contains the relations methods name for which we want to apply cascadable behaviour . The rest of the configurations will be pull from the published config file . But it is possible to override this on model specific way . Such as : 

```php
/**
 * The cascade custom configurations
 *
 * @return array
 */
public function cascadable() : array  {

    return [
        'delete' => [
            'enable'    => true,
            'event'     => 'deleted',
            'relations' => [...],
            'force'     => true,
        ],
        'restore' => [
            'enable'    => true,
            'event'     => 'restored',
            'relations' => ['comments'],
        ]
    ];
}
```

The above example display all the possible options to set for specific model to determine how the cascade functionality should work for that specific model . 

One notebale case of the above example is that by following this model specific configuration, it is possible to only use the **delete** or **restore** cascade behaviour for a given model . This is done by not setting the **delete** or **restore** key of the return array of **cascadable** . For example, only to have delete cascade behavioud, do as following : 

```php
/**
 * The cascade custom configurations
 *
 * @return array
 */
public function cascadable() : array  {

    return [
        'delete' => [
            'enable'    => true,
            'event'     => 'deleted',
            'relations' => [...],
            'force'     => true,
        ]
    ];
}
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](./LICENSE.md)
