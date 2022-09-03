<?php

namespace App\Overrides;

use MessagePack\BufferUnpacker;
use Rootsoft\Algorand\Utils\Transformers\MessagePackTransformer;
use Rootsoft\Algorand\Utils\Transformers\TransformerFactory;

class CustomTransformerFactory extends TransformerFactory {
    /**
     * Messagepack decoder.
     * @var BufferUnpacker
     */
    public BufferUnpacker $unpacker;

    /**
     * A list of possible transformers.
     * @var MessagePackTransformer[]
     */
    private array $transformers;

    public function __construct(?BufferUnpacker $unpacker = null)
    {
        $this->unpacker = $unpacker ?? new BufferUnpacker();
        $this->transformers = [];
    }

    /**
     * Register a new transformer.
     * @param MessagePackTransformer $transformer
     */
    public function registerTransformer(MessagePackTransformer $transformer)
    {
        $this->transformers[$transformer->type()] = $transformer;
    }

    /**
     * Find the transformer for the class name.
     *
     * @param string $className
     * @return MessagePackTransformer
     */
    public function findTransformer(string $className): MessagePackTransformer
    {
        return $this->transformers[$className];
    }

    /**
     * Transform and unpack the binary data.
     * @param string $data
     * @param string $className
     * @return mixed
     */
    public function transform(string $data, string $className, ?array $extraData = null)
    {
        // Find the transformer for the class name
        $transformer = $this->transformers[$className];

        // Unpack the messagepack
        $this->unpacker->reset($data);
        $value = $this->unpacker->unpack();

        // Unpack the messagepack
        return $transformer->transform($className, $value, $extraData);
    }
}
