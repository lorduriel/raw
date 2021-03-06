<?php

/**
 * @package LoRDFM\Raw
 * @category configuration
 */

return [

	/*
    |--------------------------------------------------------------------------
    | Models Namespace
    |--------------------------------------------------------------------------
    | For import use only
    */
    'models_default_namespace' => 'App\Models',

    /*
    |--------------------------------------------------------------------------
    | Controlles Namespace
    |--------------------------------------------------------------------------
    | Namespace of the Controllers.
    | Will be used to complete the namespace of the controllers
    | in the generated routes
    */
    'controllers_default_namespace' => 'App\Http\Controllers',

    
    /*
    |--------------------------------------------------------------------------
    | Contracts Namespace
    |--------------------------------------------------------------------------
    | Namespace of the Contracts.
    | Will be used to indicate the namespace
    | for the dependency injection of the repositories
    */
    'contracts_default_namespace' => 'App\Repositories\Contracts',

    /*
    |--------------------------------------------------------------------------
    | Repositories Namespace
    |--------------------------------------------------------------------------
    | Namespace of the Repositiroes.
    | Will be used to indicate the namespace
    | for the dependency injection of the repositories
    */
    'repositories_default_namespace' => 'App\Repositories',

    /*
    |--------------------------------------------------------------------------
    | Models Path
    |--------------------------------------------------------------------------
    | Where to find the models.
    | These are just intended to not force exactly match between namespaces and paths
    */
    'models_default_path' => 'app\Models',

    /*
    |--------------------------------------------------------------------------
    | Controllers Path
    |--------------------------------------------------------------------------
    | Where to publish the controllers.
    | These are just intended to not force exactly match between namespaces and paths
    */
    'controllers_default_path' => 'app\Http\Controllers',

    /*
    |--------------------------------------------------------------------------
    | Contracts Path
    |--------------------------------------------------------------------------
    | Where to publish the contracts for repositories.
    | These are just intended to not force exactly match between namespaces and paths
    */
    'contracts_default_path' => 'app\Repositories\Contracts',

    /*
    |--------------------------------------------------------------------------
    | Repositories Path
    |--------------------------------------------------------------------------
    | Where to publish the repositories.
    | These are just intended to not force exactly match between namespaces and paths
    */
    'repositories_default_path' => 'app\Repositories',

    /*
    |--------------------------------------------------------------------------
    | Development Path
    |--------------------------------------------------------------------------
    | A path to publish files generated by raw:repository.
    | It is intended to keep a place to store an editable version of the reopsitory files
    | and keep them there before copying them into their definitive path and namespace
    */
    'development_default_path' => 'app\Raw',

    /*
    |--------------------------------------------------------------------------
    | Development Path
    |--------------------------------------------------------------------------
    | A path to publish files generated by raw:repository.
    | It is intended to keep a place to store an editable version of the reopsitory files
    | and keep them there before copying them into their definitive path and namespace
    */
    'routes_default_path' => 'app\Raw\Routes'

];
