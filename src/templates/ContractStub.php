<?php 

namespace LoRDFM\Raw\Templates;

use ICanBoogie\Inflector;

class ContractStub
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
     * @var string
     */
	protected $belongsTo;

	/**
     * Customizable namespace to match your desired structure
     *
     * @var string
     */
	protected $namespace;

	/**
     * Language inflector to pluralize the model's name
     *
     * @var string
     */
	protected $inflector;

	function __construct($classWithoutNamespace, $hasMany, $belongsTo, $namespace = null)
	{	
		$this->inflector = Inflector::get('en');

		$this->classWithoutNamespace = $classWithoutNamespace;
		$this->hasMany = $hasMany;
		$this->belongsTo = $belongsTo;
		$this->placeHolder = "RawableModelClass";
		$this->namespace = $namespace;
		$this->template = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."stubs".DIRECTORY_SEPARATOR."contract.plain.stub");
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

			$hasManyTemplate = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."stubs".DIRECTORY_SEPARATOR."contract_hasmany_belongsto.plain.stub");

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
		if(!sizeof($this->belongsTo) > null){
			
			$relationships = str_replace("\${belongsToRelationships}", "", $this->template);
			
			$this->template = $relationships;

			return $this->template;
		} 

		$relationships = "";

		foreach ($this->belongsTo as $relationShipModel) {
			
			$pluralizedModel = $this->inflector->pluralize($this->classWithoutNamespace);

			$belongsToTemplate = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."stubs".DIRECTORY_SEPARATOR."contract_hasmany_belongsto.plain.stub");

			$manyModelsSubstitution = str_replace("\${ManyModels}", $pluralizedModel, $belongsToTemplate);
			$byModelSubstitution = str_replace("\${ByModel}", $relationShipModel,  $manyModelsSubstitution);

			$relationshipsSubstitution = $byModelSubstitution;

			$relationships = $relationships.$relationshipsSubstitution."\n";
			
		}

		$this->template = str_replace("\${belongsToRelationships}", $relationships, $this->template);

		return $this->template;
			
	}

	public function getTemplate()
	{
		$namespace = config('raw')['contracts_default_namespace'];
		if(isset($this->namespace)){
			$namespace = $this->namespace;
		}

		$this->template = $this->hasManyRelationships();
		$this->template = $this->belongsToRelationships();

		$this->template =  str_replace("\${ContractNamespace}", $namespace, $this->template);
		$this->template =  str_replace($this->placeHolder, $this->classWithoutNamespace, $this->template);
		return $this->template;
	}
}