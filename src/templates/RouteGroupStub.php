<?php 

namespace LoRDFM\Raw\Templates;

use ICanBoogie\Inflector;
/**
 * 
 */
class RouteGroupStub
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
     * Array of current model's 'toOne' relationships
     *
     * @var array
     */
	protected $hasOne;

	/**
     * Array of current model's 'toMany' relationships
     *
     * @var array
     */
	protected $hasMany;

	/**
     * Array of current model's 'belongsTo' relationships
     *
     * @var string
     */
	protected $belongsTo;

	/**
     * Customizable Controller namespace to match your desired structure
     *
     * @var string
     */
	protected $controllerNamespace;

	/**
     * Language inflector to pluralize the model's name
     *
     * @var string
     */
	protected $inflector;

	function __construct($classWithoutNamespace, $controller, $hasOne, $hasMany, $belongsTo, $controllerNamespace = null)
	{	
		$this->inflector = Inflector::get('en');

		$this->classWithoutNamespace = $classWithoutNamespace;
		$this->controller = $controller;
		$this->hasOne = $hasOne;
		$this->hasMany = $hasMany;
		$this->belongsTo = $belongsTo;
		$this->controllerNamespace = $controllerNamespace;
		$this->template = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."stubs".DIRECTORY_SEPARATOR."route_group.plain.stub");
	}


	public function hasOneRelationships()
	{	
		if(!sizeof($this->hasOne)>0){
			
			$relationships = str_replace("\${hasOneRelationships}", "", $this->template);

			$this->template = $relationships;

			return $this->template;
		}

		$relationships = "";

		foreach ($this->hasOne as $relationShipModel) {

			$hasOneTemplate = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."stubs".DIRECTORY_SEPARATOR."route_group_hasone.plain.stub");

			$OneModelsSubstitution = str_replace("\${OneModels}", $relationShipModel, $hasOneTemplate);
			$byModelSubstitution = str_replace("\${ByModel}", $this->classWithoutNamespace, $OneModelsSubstitution);
			$lcFirstManuyModelsSubstitution = str_replace("\${lcFirstOneModelsPlural}", $this->inflector->pluralize(lcfirst($relationShipModel)), $byModelSubstitution);

			$relationshipsSubstitution = $lcFirstManuyModelsSubstitution;

			$relationships = $relationships.$relationshipsSubstitution."\n";
			
		}

		$this->template = str_replace("\${hasOneRelationships}", $relationships, $this->template);

		return $this->template;
			
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

			$hasManyTemplate = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."stubs".DIRECTORY_SEPARATOR."route_group_hasmany.plain.stub");

			$manyModelsSubstitution = str_replace("\${ManyModels}", $pluralizedModel, $hasManyTemplate);
			$byModelSubstitution = str_replace("\${ByModel}", $this->classWithoutNamespace, $manyModelsSubstitution);
			$lcFirstManuyModelsSubstitution = str_replace("\${lcFirstManyModelsPlural}", $this->inflector->pluralize(lcfirst($relationShipModel)), $byModelSubstitution);

			$relationshipsSubstitution = $lcFirstManuyModelsSubstitution;

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

			$belongsToTemplate = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."stubs".DIRECTORY_SEPARATOR."route_group_belongsto.plain.stub");

			$manyModelsSubstitution = str_replace("\${ManyModels}", $pluralizedModel, $belongsToTemplate);
			$byModelSubstitution = str_replace("\${ByModel}", $relationShipModel, $manyModelsSubstitution);
			$lcFirstByModelSubstitution = str_replace("\${lcFirstByModel}", lcfirst($relationShipModel),$byModelSubstitution);
			$lcFirstManuyModelsSubstitution = str_replace("\${lcFirstManyModelsPlural}", $this->inflector->pluralize(lcfirst($relationShipModel)), $lcFirstByModelSubstitution);

			$relationshipsSubstitution = $lcFirstManuyModelsSubstitution;

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

		$this->template = $this->hasOneRelationships();
		$this->template = $this->hasManyRelationships();
		$this->template = $this->belongsToRelationships();

		$this->template =  str_replace("\${Controller}", $controllerNamespace."\\".$this->controller, $this->template);
		$this->template =  str_replace("\${RawableModelClassPlural}", $this->inflector->pluralize($this->classWithoutNamespace), $this->template);
		$this->template =  str_replace("\${lcFirstRawableModelClass}", $this->inflector->pluralize(lcfirst($this->classWithoutNamespace)), $this->template);

		return $this->template;
	}
}