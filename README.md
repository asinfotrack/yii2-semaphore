# Yii2-semaphore
A lightweight semaphore component for the yii2-framework

## Installation

### Add dependency

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
$ composer require asinfotrack/yii2-semaphore
```

or add

```
"asinfotrack/yii2-semaphore": "~1.0.0"
```

to the `require` section of your `composer.json` file.

### Configuration

All you need to do is add the component config to your corresponding config file.

```php
    'components' => [
        
        //your other components...
        
        'semaphore' => [
            //use the file based implementation
            'class' => \asinfotrack\yii2\semaphore\components\FileSemaphore::class,
            'lockFolderAlias' => '@runtime/semaphores',
        ],

        //your other components...

    ],
```

## Usage

The following example code shows how to work with the component within a console command. The two methods differ in the
way the handle the case when a semaphore is taken already: the first method waits for the lock to become available, while
the second doesn't. Without the second param of `acquire()` set to false, the lock is awaited.

For each semaphore you work with a string constant which can be freely defined. In this manner multiple semaphores
can be used in parallel. Internally the component works with the php `flock()`-function. Therefore you have to keep in 
mind the [differences](https://www.php.net/manual/en/function.flock.php#refsect1-function.flock-notes) between the 
underlying operating systems.

```php
class SemaphoreDemoController extends \yii\console\Controller
{
    
    public function actionWaitForLock()
    {
        Yii::$app->semaphore->acquire('my-lock', true);

        //do the actual work

        Yii::$app->semaphore->release('my-lock');
    }
    
    public function actionSkipIfNotAvailable()
    {
        if (!Yii::$app->semaphore->acquire('my-lock', false)) {
            $this->stderr('Lock already taken');
            return ExitCode::UNAVAILABLE;
        }

        //do the actual work
    
        Yii::$app->semaphore->release('my-lock');
    }

}
```

## Chagelog

###### [v1.0.0](https://github.com/asinfotrack/yii2-semaphore/releases/tag/1.0.0)
- initial release
- main class in a stable condition
