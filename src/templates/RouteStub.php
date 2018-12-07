<?php 

namespace LoRDFM\Raw\Templates;

/**
 * 
 */
class RouteStub
{
	/**
     * The stub file where the content will be replaced
     *
     * @var mixed
     */
	protected $template;

	/**
     * Array of route groups
     *
     * @var array
     */
	protected $routes;

	function __construct($routes)
	{	
		$this->routes = $routes;
		$this->template = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."stubs".DIRECTORY_SEPARATOR."routes.plain.stub");
	}

	public function getTemplate()
	{
		$implodedRoutes = implode("\n", $this->routes);

		$this->template =  str_replace("\${Routes}", $implodedRoutes, $this->template);

		return $this->template;
	}
}