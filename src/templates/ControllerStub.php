<?php 

namespace LoRDFM\Raw\Templates;

use ICanBoogie\Inflector;
/**
 * 
 */
class ControllerStub
{
	/**
     * The stub file where the content will be replaced
     *
     * @var mixed
     */
	protected $template;

	/**
     * Name of the model
     *
     * @var string
     */
	protected $classWithoutNamespace;

	/**
     * Array of current model's 'toMany' relationships
     *
     * @var array
     */
	protected $hasMany;

	/**
     * Array of current model's 'belongsTo' relationships
     *
     * @var array
     */
	protected $belongsTo;

	/**
     * Customizable namespace to match your desired structure
     *
     * @var string
     */
	protected $controllerNamespace;

	/**
     * Customizable contract namespace to match your desired structure
     *
     * @var string
     */
	protected $contractNamespace;

	/**
     * Check if the model has static validations por POST and PUT
     *
     * @var boolean
     */
	protected $hasValidations;

	/**
     * Language inflector to pluralize the model's name
     *
     * @var ICanBoogie\Inflector
     */
	protected $inflector;

	function __construct($classWithoutNamespace, $hasMany, $belongsTo, $controllerNamespace =  null, $contractNamespace = null, $hasValidations = false)
	{	
		$this->inflector = Inflector::get('en');

		$this->classWithoutNamespace = $classWithoutNamespace;
		$this->hasMany = $hasMany;
		$this->belongsTo = $belongsTo;
		$this->placeHolder = "RawableModelClass";
		$this->controllerNamespace = $controllerNamespace;
		$this->contractNamespace = $contractNamespace;
		$this->hasValidations = $hasValidations;
		$this->template = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."stubs".DIRECTORY_SEPARATOR."controller.plain.stub");
	}


	public function hasManyRelationships()
	{	
		if(!sizeof($this->hasMany)>0){
			
			$relationships = str_replace("\${hasManyRelationships}", "", $this->template);

			$this->template = $relationships;

			return $this->template;
		}

		$relationships = "";

		foreach ($this->hasMany as $relationShipModel) {
			
			$pluralizedModel = $this->inflector->pluralize($relationShipModel);

			$hasManyTemplate = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."stubs".DIRECTORY_SEPARATOR."controller_hasmany_belongsto.plain.stub");

			$manyModelsSubstitution = str_replace("\${ManyModels}", $pluralizedModel, $hasManyTemplate);
			$byModelSubstitution = str_replace("\${ByModel}", $this->placeHolder, $manyModelsSubstitution);

			$relationshipsSubstitution = $byModelSubstitution;

			$relationships = $relationships.$relationshipsSubstitution."\n";
			
		}

		$this->template = str_replace("\${hasManyRelationships}", $relationships, $this->template);

		return $this->template;
			
	}


	public function belongsToRelationships()
	{		

		if(!sizeof($this->belongsTo) > 0){
			
			$relationships = str_replace("\${belongsToRelationships}", "", $this->template);
			
			$this->template = $relationships;

			return $this->template;
		}

		$relationships = "";

		foreach ($this->belongsTo as $relationShipModel) {
			
			$pluralizedModel = $this->inflector->pluralize($this->classWithoutNamespace);

			$belongsToTemplate = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."stubs".DIRECTORY_SEPARATOR."controller_hasmany_belongsto.plain.stub");

			$manyModelsSubstitution = str_replace("\${ManyModels}", $pluralizedModel, $belongsToTemplate);
			$byModelSubstitution = str_replace("\${ByModel}", $relationShipModel, $manyModelsSubstitution);

			$relationshipsSubstitution = $byModelSubstitution;

			$relationships = $relationships.$relationshipsSubstitution."\n";
			
		}

		$this->template = str_replace("\${belongsToRelationships}", $relationships, $this->template);

		return $this->template;
			
	}

	public function getTemplate()
	{

		$controllerNamespace = config('raw')['controllers_default_namespace'];
		if(isset($this->controllerNamespace)){
			$controllerNamespace = $this->controllerNamespace;
		}

		$contractNamespace = config('raw')['contracts_default_namespace'];
		if(isset($this->contractNamespace)){
			$contractNamespace = $this->contractNamespace;
		}

		$validations = "/**\n\t\t\t\t* @todo Validation implementation\n\t\t\t\t*/";

		if($this->hasValidations){
			$this->template =  str_replace("\${storeValidations}", "RawableModelClass::storeValidations()", $this->template);
			$this->template =  str_replace("\${updateValidations}", "RawableModelClass::updateValidations()", $this->template);
		} else {
			$this->template =  str_replace("\${storeValidations}", $validations , $this->template);
			$this->template =  str_replace("\${updateValidations}", $validations , $this->template);
		}

		$this->template = $this->hasManyRelationships();
		$this->template = $this->belongsToRelationships();

		$this->template =  str_replace($this->placeHolder, $this->classWithoutNamespace, $this->template);

		$this->template =  str_replace("\${ControllersNamespace}", $controllerNamespace, $this->template);
		
		$this->template =  str_replace("\${ContractNamespace}", $contractNamespace, $this->template);

		$this->template =  str_replace("\${ModelsNamespace}", config('raw')['models_default_namespace'], $this->template);

		return $this->template;
	}
}