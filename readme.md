# RAW


Raw is a quick repository pattern scaffolding package for laravel.
It mixes [Doctrine Annotations](https://www.doctrine-project.org/projects/doctrine-annotations/en/latest/index.html) with [Eloquent ORM](https://laravel.com/docs/5.4/eloquent) to handle the structure of your project from the models.

Do you have something in mind? You should be able to build it from the very bottom without suffer.


# Install
    composer require lordfm/raw

Add the provider to your `app.php`

    'providers' = [
		/*
		* Lots of providers
		*
		*/
		LoRDFM\Raw\RawServiceProvider::class
	]
		


Publish the `raw.php` configuiration file

    php artisan vendor:publish --provider="LoRDFM\Raw\ServiceProvider"

## Annotate your model
Declare the use of `LoRDFM\Raw\Annotations\Rawable` and add the annotation on top of your model.
Be sure to not leave blank lines between the annotation and the class.

MyModel.php

    <?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    use LoRDFM\Raw\Annotations\Rawable;

    /**
    *
    * @Rawable
    */
    class MyModel extends Model
    {

    }


Now, just run

    php artisan raw:repository

What happened?. We just built our typical `--resource Controller` along with a `Contract` interface to declare all crud related methods and a `Repository` class to implement them.

By default they will be stored in  `app\Http\Controllers `, `app\Repositories ` and  `app\Repositories\Contracts `.

As you may be expecting, y can create complex models with relationships. We support
the nex relationships

| Realation              |  Annotation        |
| :------------------: | :-----------------: |
|  one to many       |  @HasMany        |
|  many to one       |  @BelongsTo      |
|  has one               |  @HasOne          |




## Annotations
The annotations are pretty straight forward, ech one handles the relation of the model to another model and the declaration of the files holding the repository pattern.

### @Rawable
Each model you want to be handled by the package should have this annotation.

### @HasMany
An array of models with related as `One to Many` from the current model
```
@HasMany(models = {"Articles", "Books", ...})
```
### @HasOne
An array of models with related as `One to One` from the current model
```
@HasOne(models = {"Profile", ...})
```

### @BelongsTo
An array of models with related as `Many to One` from the current model
```
@BelongsTo(models = {"Author", "Publisher", ...})
```

### @RawableController
Handles the creation of the controllers.
Support `path` and a `namespace` to store and declare the `--resource` style controller for your model.
```
* @RawableController(path = "app\Http\Controllers\Api", namespace = "App\Http\Controllers\Api")
```

### @RawableContract
Handles the cration of the `interface` class (aka 'contract') with the methods your repository should implement
Support `path` and a `namespace` to store and declare the interface
```
@RawableContract(path = "app\Repository\Contracts", namespace = "App\Repository\Contracts")
```

### @RawableRepository
Handles the repository class creation.
Support `path` and a `namespace` to store and delcare the class
```
@RawableRepository(path = "app\Repository", namespace = "App\Repository")
```

## Now What?
Long story short, we are going to use a very well known example of related entities to demonstrate the use of the pluging. 
Classic **Author** -> **Articles** example:

#### Author model

    <?php
    namespace  App\Models;
    
    use Illuminate\Database\Eloquent\Model;
    
    use LoRDFM\Raw\Annotations\Rawable;
    use LoRDFM\Raw\Annotations\RawableController;
    use LoRDFM\Raw\Annotations\RawableContract;
    use LoRDFM\Raw\Annotations\RawableRepository;
    use LoRDFM\Raw\Annotations\HasMany;
    use LoRDFM\Raw\Annotations\HasOne;
    
    /**
    *
    * @Rawable
    * @HasMany(models = {"Article"})
    * @HasOne(models = {"Profile"})
    * @RawableController(path = "app\Http\Controllers\Api", namespace = "App\Http\Controllers\Api")
    * @RawableContract(path = "app\Repository\Contracts", namespace = "App\Repository\Contracts")
    * @RawableRepository(path = "app\Repository", namespace = "App\Repository")
    */
    class  Author  extends  Model
    {
    
	    public  function  articles()
	    {
		    return  $this->hasMany('App\Models\Article');
	    }
    
	    public  function  profile()
	    {
		    return  $this->hasOne('App\Models\Profile');
	    }
	    
    }
The Author holds a `One to One` relation to a Profilemodel nad `One to Many`
to the  Article model

#### Article model
    <?php

    namespace  App\Models;
    
    use Illuminate\Database\Eloquent\Model;
   
    use LoRDFM\Raw\Annotations\Rawable;
    use LoRDFM\Raw\Annotations\RawableController;
    use LoRDFM\Raw\Annotations\RawableContract;
    use LoRDFM\Raw\Annotations\RawableRepository;
    use LoRDFM\Raw\Annotations\BelongsTo;
    
    /**
    *
    * @Rawable
    * @BelongsTo(models = {"Author"})
    * @RawableController(path = "app\Http\Controllers\Api", namespace = "App\Http\Controllers\Api")
    * @RawableContract(path = "app\Repository\Contracts", namespace = "App\Repository\Contracts")
    * @RawableRepository(path = "app\Repository", namespace = "App\Repository")
    */
    class  Article  extends  Model
    {
	    public  function  author()
	    {
	    return  $this->belongsTo('App\Models\Author');
	    }
    }

The Article holds `Many to One` to their author
### Profile model

    <?php

    namespace  App\Models;
    
    use Illuminate\Database\Eloquent\Model;

    class  Profile  extends  Model
    {
	    public  function  author()
	    {
	    return  $this->belongsTo('App\Models\Author');
	    }
    }

The  Profile model has no @Rawable implementation at all, not required for the moment.

## Let's go!

Now, just run

     php artisan raw:repository
and it will tell you on wich model is working and creating its repository

    ...
    Making repository for App\Models\Article
    Making repository for App\Models\Author
    ...
If the repository for the model already exists you will be warned

    Making repository for App\Models\Author
    This Contract already exists. If you want to overwrite it use '--force=true'
    This Repository already exists. If you want to overwrite it use '--force=true'
    This Controller already exists. If you want to overwrite it use '--force=true'
    This RouteGroup already exists. If you want to overwrite it use '--force=true'

You can overwrite them using `--force=true`

    php artisan raw:repository --force=true
Be careful, the corresponding files will be completely overwritten.
You may choose to run the command for just some models

    php artisan raw:repository Author
    php artisan raw:repository Author Article
    php artisan raw:repository Author Article Publisher ...
