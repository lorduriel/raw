<?php

namespace LoRDFM\Raw\Annotations;
/**
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 *   @Attribute("namespace", type = "string"),
 *   @Attribute("path", type = "string"),
 *   @Attribute("contract", type = "string")
 * })
 */
final class RawableRepository
{
    /** @Required */
    private $namespace;

    /** @Required */
    private $path;

    /** @Required */
    private $contract;

    public function __construct(array $values)
    {
        if(array_key_exists("namespace", $values)){
            $this->namespace = $values['namespace'];
        }

        if(array_key_exists("path", $values)){
            $this->path = $values['path'];
        }

        if(array_key_exists("contract", $values)){
            $this->contract = $values['contract'];
        }
    }

    public function getNamespace()
    {
    	return $this->namespace;
    }

    public function getPath()
    {
    	return $this->path;
    }

    public function getContract()
    {
    	return $this->contract;
    }
}