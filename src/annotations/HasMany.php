<?php

namespace LoRDFM\Raw\Annotations;
/**
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 *   @Attribute("models", type = "array")
 * })
 */
final class HasMany
{
    private $models;

    public function __construct(array $values)
    {
        $this->models = $values['models'];
    }

    public function getModels()
    {
    	return $this->models;
    }
}