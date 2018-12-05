<?php

namespace LoRDFM\Raw\Annotations;
/**
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 *   @Attribute("namespace", type = "string"),
 *   @Attribute("path", type = "string")
 * })
 */
final class RawableRepository
{
    private $namespace;
    private $path;

    public function __construct(array $values)
    {
        $this->namespace = $values['namespace'];
        $this->path = $values['path'];
    }

    public function getNamespace()
    {
    	return $this->namespace;
    }

    public function getPath()
    {
    	return $this->path;
    }
}