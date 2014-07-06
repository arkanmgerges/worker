## Description

Worker is a simple library that will allow the user to provide a function or class method to be executed into a separate php process, beside this, it accepts on success function or class method, and on error function or class method.

## License

Licensed under [MIT](http://www.opensource.org/licenses/mit-license.php). Totally free for private or commercial projects.

## Requirement

The library needs PHP 5.4+.

## Installation

In your composer.json add the following in the **require** section: 


```
{
    "require": {
        "arkanmgerges/worker": "dev-master"
    }
}
```

And then:
```sh
php composer.phar update
```
or if you have installed composer in your system to be called directly without php then:
```sh
composer update
```

## Tutorial
### 1. Use Worker
```php
Use Worker\Worker
```
### 2. Using Anonymous Function
```php
$worker = new Worker(
    // Here you can provide your main callback
    function($arg1 = '', $arg2 = '') {
        file_put_contents('result.txt', $arg1 . $arg2);
    },
    // The second one is used when main callback has completed successfully
    function() {
        file_put_contents('success.txt', 'success');
    },
    // If an exception has happened in the main callback then this callback will be called with an error message
    function($e) {
        file_put_contents('error.txt', 'error');
    }
);

// Start the worker, and pass 2 arguments to the main callback. It is also possible to pass more arguments
$worker->start('first arg', 'second arg');
```

### 3. Using Class Object Method
```php
class SomeClass
{
    public function method($arg1, $arg2, $arg3)
    {
        file_put_contents('result.txt', $arg1 . $arg2 . $arg3);
    }
};
```
And then somewhere:
```php
$object = new SomeClass();
// Pass array, first item is the object and second item is the name of the class method
$worker = new Worker([$object, 'method']);
// Start worker and send 3 arguments
$worker->start('from', ' object method', ', this is nice');
```
### 4. Using Class Static Method
```php
class SomeClass
{
    public static function method($arg1, $arg2, $arg3)
    {
        file_put_contents('result.txt', $arg1 . $arg2 . $arg3);
    }
};
```
And somewhere:
```php
$worker = new Worker(__NAMESPACE__ . '\SomeClass::method');
$worker->start('from', ' class method', ', nice');
```
