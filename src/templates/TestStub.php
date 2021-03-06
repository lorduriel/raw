<?php 

namespace LoRDFM\Raw\Templates;

use ICanBoogie\Inflector;
/**
 * 
 */
class TestStub
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
     * Customizable namespace to match your desired structure
     *
     * @var string
     */
	protected $testNamespace;


	/**
     * Language inflector to pluralize the model's name
     *
     * @var string
     */
	protected $inflector;

	function __construct($classWithoutNamespace, $hasOne, $hasMany, $belongsTo, $testNamespace = null, $repositoryNamespace = null)
	{	
		$this->inflector = Inflector::get('en');

		$this->classWithoutNamespace = $classWithoutNamespace;
		$this->hasOne = $hasOne;
		$this->hasMany = $hasMany;
		$this->belongsTo = $belongsTo;
		$this->placeHolder = "RawableModelClass";
		$this->placeHolderPlural = "RawableModelClassPlural";
		$this->testNamespace = $testNamespace;
		$this->repositoryNamespace = $repositoryNamespace;
		$this->template = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."stubs".DIRECTORY_SEPARATOR."unit-test.plain.stub");;
	}


	public function hasOneRelationships()
	{	
		if(!sizeof($this->hasOne)>0){
			
			$relationships = str_replace("\${hasOneRelationshipsTest}", "", $this->template);

			$this->template = $relationships;

			return $this->template;
		}

		$relationships = "";

		foreach ($this->hasOne as $relationShipModel) {

			$hasOneTemplate = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."stubs".DIRECTORY_SEPARATOR."unit-test_hasone.plain.stub");

			$OneModelsSubstitution = str_replace("\${OneModels}", $relationShipModel, $hasOneTemplate);
			$byModelSubstitution = str_replace("\${ByModel}", $this->placeHolder, $OneModelsSubstitution);
			$lcFirstModelSubstitution = str_replace("\${lcFirstOneModel}", lcfirst($relationShipModel), $byModelSubstitution);

			$relationshipsSubstitution = $lcFirstModelSubstitution;

			$relationships = $relationships.$relationshipsSubstitution."\n";
			
		}

		$this->template = str_replace("\${hasOneRelationshipsTest}", $relationships, $this->template);

		return $this->template;
			
	}

	public function hasManyRelationships()
	{	
		if(!sizeof($this->hasMany)>0){
			
			$relationships = str_replace("\${hasManyRelationshipsTest}", "", $this->template);

			$this->template = $relationships;

			return $this->template;
		}

		$relationships = "";

		foreach ($this->hasMany as $relationShipModel) {
			
			$pluralizedModel = $this->inflector->pluralize($relationShipModel);

			$hasManyTemplate = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."stubs".DIRECTORY_SEPARATOR."unit-test_hasmany.plain.stub");

			$manyModelsSubstitution = str_replace("\${ManyModels}", $pluralizedModel, $hasManyTemplate);
			$byModelSubstitution = str_replace("\${ByModel}", $this->placeHolder, $manyModelsSubstitution);
			$lcFirstModelPluralSubstitution = str_replace("\${lcFirstManyModelsPlural}", $this->inflector->pluralize(lcfirst($relationShipModel)), $byModelSubstitution);

			$relationshipsSubstitution = $lcFirstModelPluralSubstitution;

			$relationships = $relationships.$relationshipsSubstitution."\n";
			
		}

		$this->template = str_replace("\${hasManyRelationshipsTest}", $relationships, $this->template);

		return $this->template;
			
	}


	public function belongsToRelationships()
	{	

		if(!sizeof($this->belongsTo) > 0){
			
			$this->template = str_replace("\${belongsToRelationshipsTest}", "", $this->template);
			$this->template = str_replace("\${belongsToModels}", "", $this->template);
			
			return $this->template;
		}

		$relationships = "";
		$belongsToModels = [];

		foreach ($this->belongsTo as $relationShipModel) {
			
			$pluralizedModel = $this->inflector->pluralize($this->classWithoutNamespace);

			$belongsToTemplate = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."stubs".DIRECTORY_SEPARATOR."unit-test_belongsto.plain.stub");

			$manyModelsSubstitution = str_replace("\${ManyModels}", $pluralizedModel, $belongsToTemplate);
			$byModelSubstitution = str_replace("\${ByModel}", $relationShipModel, $manyModelsSubstitution);

			$relationshipsSubstitution = $byModelSubstitution;

			$relationships = $relationships.$relationshipsSubstitution."\n";

			array_push($belongsToModels, "use ".config('raw')['models_default_namespace']."\\".$relationShipModel.";");
			
		}
	
		$this->template = str_replace("\${belongsToRelationshipsTest}", $relationships, $this->template);
		$this->template = str_replace("\${belongsToModels}", implode("\n", $belongsToModels), $this->template);

		$this->template = str_replace("\${lcFirstManyModelsPlural}", $this->inflector->pluralize(lcfirst($this->classWithoutNamespace)), $this->template);

		return $this->template;
			
	}


	public function getTemplate()
	{	
		$testNamespace = config('raw')['repositories_default_namespace'];
		if(isset($this->testNamespace)){
			$testNamespace = $this->testNamespace;
		}

		$this->template = $this->hasOneRelationships();

		$this->template = $this->hasManyRelationships();

		$this->template = $this->belongsToRelationships();

		$this->template =  str_replace("ModelNamespace", config('raw')['models_default_namespace'], $this->template);

		$this->template =  str_replace("\${TestNamespace}", $testNamespace, $this->template);

		$this->template =  str_replace("\${RepositoryNamespace}", $this->repositoryNamespace, $this->template);

		$this->template =  str_replace($this->placeHolderPlural, $this->inflector->pluralize($this->classWithoutNamespace), $this->template);

		$this->template =  str_replace($this->placeHolder, $this->classWithoutNamespace, $this->template);

		return $this->template;
	}

}