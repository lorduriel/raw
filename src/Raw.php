<?php

namespace LoRDFM\Raw;

use App;
use Config;

use LoRDFM\Raw\Templates\ContractStub;
use LoRDFM\Raw\Templates\RepositoryStub;
use LoRDFM\Raw\Templates\ControllerStub;
use LoRDFM\Raw\Templates\RouteGroupStub;
use LoRDFM\Raw\Templates\RouteStub;

use LoRDFM\Raw\Annotations\Rawable;
use LoRDFM\Raw\Annotations\HasMany;
use LoRDFM\Raw\Annotations\BelongsTo;
use LoRDFM\Raw\Annotations\RawableController;
use LoRDFM\Raw\Annotations\RawableRepository;
use LoRDFM\Raw\Annotations\RawableContract;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\ClassLoader\ClassLoader;

class Raw
{   
    /**
     * Doctrine annotations reader
     *
     * @var object
     */
    private $reader;

    /**
     * Array of models to generate repositories
     *
     * @var array
     */
    private $models = [];

    /**
     * Array of options send to Rawable command
     *
     * @var array
     */
    private $options = [];


    /**
    * @param array $models
    * @param array $options
    */

    public function __construct($specificModels, $options)
    {   
        $this->reader = new AnnotationReader();
        $this->options = $options;

        $modelsPath = config('raw')['models_default_path'];
        $modelsPath = str_replace("\\", DIRECTORY_SEPARATOR , $modelsPath);

        $modelsFromPath = scandir($modelsPath);

        foreach ($modelsFromPath as $model) {
            if($model != '.' && $model != '..'){
                $model = str_replace( ".php", "", $model);
                if(sizeof($specificModels) > 0 && in_array($model, $specificModels)){
                    $model = config('raw')['models_default_namespace'].'\\'.$model;
                    array_push($this->models, get_class(new $model()));
                }
                if(sizeof($specificModels) == 0) {
                    $model = config('raw')['models_default_namespace'].'\\'.$model;
                    array_push($this->models, get_class(new $model()));
                }
            }
        }
    }

    /**
     * Load the annotations definition and generate the repository files
     *
     * @return void 
     */
    public function run()
    {
        AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR.'annotations'.DIRECTORY_SEPARATOR.'Rawable.php');
        AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR.'annotations'.DIRECTORY_SEPARATOR.'HasMany.php');
        AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR.'annotations'.DIRECTORY_SEPARATOR.'BelongsTo.php');

        AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR.'annotations'.DIRECTORY_SEPARATOR.'RawableController.php');
        AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR.'annotations'.DIRECTORY_SEPARATOR.'RawableRepository.php');
        AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR.'annotations'.DIRECTORY_SEPARATOR.'RawableContract.php');
        
        $this->createDirs();
        $this->getModels();
    }

    /**
     * Generate the repository directories
     *
     * @return void 
     */
    public function createDirs()
    {   
        $routesPath = config('raw')['routes_default_path'];
        $routesPath = str_replace("\\", DIRECTORY_SEPARATOR , $routesPath);

        $repositoriesPath = config('raw')['repositories_default_path'];
        $repositoriesPath = str_replace("\\", DIRECTORY_SEPARATOR , $repositoriesPath);

        if (!file_exists($routesPath)) {
            mkdir($routesPath, 0777, true);
        }

        if (!file_exists($repositoriesPath)) {
            mkdir($repositoriesPath, 0777, true);
            if (!file_exists($repositoriesPath.DIRECTORY_SEPARATOR.'Contracts')) {
                mkdir($repositoriesPath.DIRECTORY_SEPARATOR.'Contracts', 0777, true);
            }
        }

    }

     /**
     * Iterate over the models and check if have the @Rawable annotation
     *
     * @return void 
     */
    public function getModels()
    {
        foreach ($this->models as $model ) {
            # echo PHP_EOL.$model.PHP_EOL;
            $this->isRawable($model);
        }
       
    }

    /**
     * Check the annotations in each model
     * and implements each case
     * @param Class $model 
     * @return void 
     */
    public function isRawable($model)
    {
        $reflClass = new \ReflectionClass($model);
        $classAnnotations = $this->reader->getClassAnnotations($reflClass);

        foreach ($classAnnotations as $annot) {
            if ($annot instanceof Rawable) {
                echo "Making repository for ".$model.PHP_EOL;
                
                $hasMany = [];
                $belongsTo = [];

                $controllerPath = null;
                $controllerNamespace = null;

                $contractPath = null;
                $contractNamespace = null;

                $repositoryPath = null;
                $repositoryNamespace = null;

                foreach ($classAnnotations as $annot) {
                    if ($annot instanceof HasMany) {
                        $hasMany = $annot->getModels();
                    }

                    if ($annot instanceof BelongsTo) {
                        $belongsTo = $annot->getModels();
                    }

                    if ($annot instanceof RawableContract) {
                        $contractPath = $annot->getPath();
                        $contractNamespace = $annot->getNamespace();
                    }

                    if ($annot instanceof RawableRepository) {
                        $repositoryPath = $annot->getPath();
                        $repositoryNamespace = $annot->getNamespace();
                    }

                    if ($annot instanceof RawableController) {
                        $controllerPath = $annot->getPath();
                        $controllerNamespace = $annot->getNamespace();
                    }

                }

                $this->createContract($model, $hasMany, $belongsTo, $contractPath, $contractNamespace);
                $this->createRepository($model, $hasMany, $belongsTo, $repositoryPath, $repositoryNamespace, $contractNamespace);
                $this->createController($model, $hasMany, $belongsTo, $controllerPath, $controllerNamespace, $contractNamespace);

                $routeGroup = $this->createRouteGroups($model, $hasMany, $belongsTo, $controllerNamespace);

            }
        }

    }

    /**
     * Creates the contract for the Rawable Model
     *
     * @param Class $model 
     * @param Array<Class> $hasMany
     * @param Array<Class> $belongsTo 
     * @param String $path 
     * @param String $namespace
     * @return void 
     */
    public function createContract($model, $hasMany, $belongsTo, $path = null, $namespace = null)
    {   

        $modelInstance = new $model();

        $classWithoutNamespace = substr(strrchr(get_class($modelInstance), "\\"), 1);

        $destinationPath = config('raw')['contracts_default_path'];
        if(isset($path)){
            $destinationPath = $path;
        }

        $destinationPath = str_replace("\\", DIRECTORY_SEPARATOR , $destinationPath);

        if (!file_exists($destinationPath.DIRECTORY_SEPARATOR)) {
            mkdir($destinationPath.DIRECTORY_SEPARATOR, 0777, true);
        }
       
        $productionFileName = $destinationPath.DIRECTORY_SEPARATOR.$classWithoutNamespace."Contract.php";

        $contractTemplate = new ContractStub($classWithoutNamespace, $hasMany, $belongsTo, $namespace);
        $output =  $contractTemplate->getTemplate();
        
        if(file_exists($productionFileName)){
            echo "No existe".$productionFileName.PHP_EOL;
            if($this->options['force']=="true"){
                $productionFileHandler = fopen($productionFileName, 'w');
                fwrite($productionFileHandler, $output);
                fclose($productionFileHandler);
            } else {
                echo "This Contract already exists. If you want to overwrite it use '--force'".PHP_EOL;
            }
        } else {
            echo "existe".$productionFileName.PHP_EOL;
            $productionFileHandler = fopen($productionFileName, 'w');
            fwrite($productionFileHandler, $output);
            fclose($productionFileHandler);
        }
    }

    /**
     * Creates the repository for the Rawable Model
     *
     * @param Class $model 
     * @param Array<Class> $hasMany
     * @param Array<Class> $belongsTo 
     * @param String $path 
     * @param String $controllerNamespace
     * @param String $contractNamespace
     * @return void 
     */
    public function createRepository($model, $hasMany, $belongsTo, $path = null, $controllerNamespace = null, $contractNamespace = null)
    {    

        $modelInstance = new $model();

        $classWithoutNamespace = substr(strrchr(get_class($modelInstance), "\\" ), 1);

        $destinationPath = config('raw')['repositories_default_path'];
        if(isset($path)){
            $destinationPath = $path;
        }

        $destinationPath = str_replace("\\", DIRECTORY_SEPARATOR , $destinationPath);

        if (!file_exists($destinationPath.DIRECTORY_SEPARATOR)) {
            mkdir($destinationPath.DIRECTORY_SEPARATOR, 0777, true);
        }

        $productionFileName = $productionFileName = $destinationPath.DIRECTORY_SEPARATOR.$classWithoutNamespace."Repository.php";

        $repositoryTemplate = new RepositoryStub($classWithoutNamespace, $hasMany, $belongsTo, $controllerNamespace, $contractNamespace);
        $output =  $repositoryTemplate->getTemplate();

        if(file_exists($productionFileName)){
            if($this->options['force']=="true"){
                $productionFileHandler = fopen($productionFileName, 'w');
                fwrite($productionFileHandler, $output);
                fclose($productionFileHandler);
            } else {
                echo "This Repository already exists. If you want to overwrite it use '--force=true'".PHP_EOL;
            }
        } else {
            $productionFileHandler = fopen($productionFileName, 'w');
            fwrite($productionFileHandler, $output);
            fclose($productionFileHandler);
        }
    }

    /**
     * Creates the controller for the Rawable Model
     *
     * @param Class $model 
     * @param Array<Class> $hasMany
     * @param Array<Class> $belongsTo 
     * @param String $path 
     * @param String $controllerNamespace
     * @param String $contractNamespace
     * @return void 
     */
    public function createController($model, $hasMany, $belongsTo, $path = null, $controllerNamespace = null, $contractNamespace = null)
    {   
        $modelInstance = new $model();

        $classWithoutNamespace = substr(strrchr(get_class($modelInstance), "\\"), 1);

        $destinationPath = config('raw')['controllers_default_path'];
        if(isset($path)){
            $destinationPath = $path;
        }

        $destinationPath = str_replace("\\", DIRECTORY_SEPARATOR , $destinationPath);

        if (!file_exists($destinationPath.DIRECTORY_SEPARATOR)) {
            mkdir($destinationPath.DIRECTORY_SEPARATOR, 0777, true);
        }

        $productionFileName = $destinationPath.DIRECTORY_SEPARATOR.$classWithoutNamespace."Controller.php";

        $uses = class_uses($modelInstance);
        $hasValidations = false;
        
        if(in_array("LoRDFM\Raw\RawValidation", $uses)){
            $hasValidations = true;
        }

        $repositoryTemplate = new ControllerStub($classWithoutNamespace, $hasMany, $belongsTo, $controllerNamespace, $contractNamespace, $hasValidations);
        $output =  $repositoryTemplate->getTemplate();

        if(file_exists($productionFileName)){
            if($this->options['force']=="true"){
                $productionFileHandler = fopen($productionFileName, 'w');
                fwrite($productionFileHandler, $output);
                fclose($productionFileHandler);
            } else {
                echo "This Controller already exists. If you want to overwrite it use '--force'".PHP_EOL;
            }
        } else {
            $productionFileHandler = fopen($productionFileName, 'w');
            fwrite($productionFileHandler, $output);
            fclose($productionFileHandler);
        }
    }

    /**
     * Creates the route group for the Rawable Model
     *
     * @param Class $model 
     * @param Array<Class> $hasMany
     * @param Array<Class> $belongsTo 
     * @param String $controllerNamespace 
     * @return void 
     */
    public function createRouteGroups($model, $hasMany, $belongsTo, $controllerNamespace = null)
    {

        $modelInstance = new $model();

        $classWithoutNamespace = substr(strrchr(get_class($modelInstance), "\\"), 1);

        $controller = $classWithoutNamespace."Controller";

        $routeGroupTemplate = new RouteGroupStub($classWithoutNamespace, $controller, $hasMany, $belongsTo, $controllerNamespace);
        $output =  $routeGroupTemplate->getTemplate();

        $groupOutput = "<?php\n".$output;

        $destinationPath = config('raw')['routes_default_path'];
        $destinationPath = str_replace("\\", DIRECTORY_SEPARATOR , $destinationPath);
        
        $productionFileName = $destinationPath.DIRECTORY_SEPARATOR.lcfirst($classWithoutNamespace)."-routes.php";

        if(file_exists($productionFileName)){
            if($this->options['force']=="true"){
                $productionFileHandler = fopen($productionFileName, 'w');
                fwrite($productionFileHandler, $groupOutput);
                fclose($productionFileHandler);
            } else {
                echo "This RouteGroup already exists. If you want to overwrite it use '--force'".PHP_EOL;
            }
        } else {
            $productionFileHandler = fopen($productionFileName, 'w');
            fwrite($productionFileHandler, $groupOutput);
            fclose($productionFileHandler);
        }

    }

}