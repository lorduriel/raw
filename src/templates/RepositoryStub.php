<?php 

namespace LoRDFM\Raw\Templates;

use ICanBoogie\Inflector;
/**
 * 
 */
class RepositoryStub
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
	protected $repositoryNamespace;

	/**
     * Customizable contarct namespace to match your desired structure
     *
     * @var string
     */
	protected $contractNamespace;

	/**
     * Language inflector to pluralize the model's name
     *
     * @var string
     */
	protected $inflector;

	function __construct($classWithoutNamespace, $hasMany, $belongsTo, $repositoryNamespace = null, $contractNamespace = null)
	{	
		$this->inflector = Inflector::get('en');

		$this->classWithoutNamespace = $classWithoutNamespace;
		$this->hasMany = $hasMany;
		$this->belongsTo = $belongsTo;
		$this->placeHolder = "RawableModelClass";
		$this->repositoryNamespace = $repositoryNamespace;
		$this->contractNamespace = $contractNamespace;
		$this->template = file_get_contents(__DIR__."\\..\stubs\\repository.plain.stub");;
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

			$hasManyTemplate = file_get_contents(__DIR__."\\..\stubs\\repository_hasmany.plain.stub");

			$manyModelsSubstitution = str_replace("\${ManyModels}", $pluralizedModel, $hasManyTemplate);
			$byModelSubstitution = str_replace("\${ByModel}", $this->placeHolder, $manyModelsSubstitution);
			$lcFirstModelPluralSubstitution = str_replace("\${lcFirstManyModelsPlural}", $this->inflector->pluralize(lcfirst($relationShipModel)), $byModelSubstitution);

			$relationshipsSubstitution = $lcFirstModelPluralSubstitution;

			$relationships = $relationships.$relationshipsSubstitution."\n";
			
		}

		$this->template = str_replace("\${hasManyRelationships}", $relationships, $this->template);

		return $this->template;
			
	}


	public function belongsToRelationships()
	{	

		if(!sizeof($this->belongsTo) > 0){
			
			$this->template = str_replace("\${belongsToRelationships}", "", $this->template);
			$this->template = str_replace("\${belongsToModels}", "", $this->template);
			
			return $this->template;
		}

		$relationships = "";
		$belongsToModels = [];

		foreach ($this->belongsTo as $relationShipModel) {
			
			$pluralizedModel = $this->inflector->pluralize($this->classWithoutNamespace);

			$belongsToTemplate = file_get_contents(__DIR__."\\..\stubs\\repository_belongsto.plain.stub");

			$manyModelsSubstitution = str_replace("\${ManyModels}", $pluralizedModel, $belongsToTemplate);
			$byModelSubstitution = str_replace("\${ByModel}", $relationShipModel, $manyModelsSubstitution);

			$relationshipsSubstitution = $byModelSubstitution;

			$relationships = $relationships.$relationshipsSubstitution."\n";

			array_push($belongsToModels, "use ".config('raw')['models_default_namespace']."\\".$relationShipModel.";");
			
		}
	
		$this->template = str_replace("\${belongsToRelationships}", $relationships, $this->template);
		$this->template = str_replace("\${belongsToModels}", implode("\n", $belongsToModels), $this->template);

		$this->template = str_replace("\${lcFirstManyModelsPlural}", $this->inflector->pluralize(lcfirst($this->classWithoutNamespace)), $this->template);

		return $this->template;
			
	}


	public function getTemplate()
	{	
		$repositoryNamespace = config('raw')['repositories_default_namespace'];
		if(isset($this->repositoryNamespace)){
			$repositoryNamespace = $this->repositoryNamespace;
		}

		$contractNamespace = config('raw')['contracts_default_namespace'];
		if(isset($this->contractNamespace)){
			$contractNamespace = $this->contractNamespace;
		}

		$this->template = $this->hasManyRelationships();

		$this->template = $this->belongsToRelationships();

		$this->template =  str_replace("\${RepositoryNamespace}", $repositoryNamespace, $this->template);

		$this->template =  str_replace("\${ContractNamespace}", $contractNamespace, $this->template);

		$this->template =  str_replace($this->placeHolder, $this->classWithoutNamespace, $this->template);

		return $this->template;
	}

}