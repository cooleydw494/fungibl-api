<?php

namespace App\Overrides;

use MessagePack\BufferUnpacker;
use MessagePack\Packer;
use MessagePack\TypeTransformer\BinTransformer;
use Rootsoft\Algorand\Utils\Transformers\ApplicationCreateTransformer;
use Rootsoft\Algorand\Utils\Transformers\ApplicationTransformer;
use Rootsoft\Algorand\Utils\Transformers\ApplicationUpdateTransformer;
use Rootsoft\Algorand\Utils\Transformers\AssetConfigTransformer;
use Rootsoft\Algorand\Utils\Transformers\AssetFreezeTransformer;
use Rootsoft\Algorand\Utils\Transformers\AssetTransferTransformer;
use Rootsoft\Algorand\Utils\Transformers\BaseTransactionTransformer;
use Rootsoft\Algorand\Utils\Transformers\KeyRegistrationTransformer;
use Rootsoft\Algorand\Utils\Transformers\LogicSignatureTransformer;
use Rootsoft\Algorand\Utils\Transformers\PaymentTransactionTransformer;
use Rootsoft\Algorand\Utils\Transformers\SignedTransactionTransformer;
//use Rootsoft\Algorand\Utils\Transformers\TransformerFactory;

class CustomEncoder extends \Rootsoft\Algorand\Utils\Encoder {

    /**
     * A singleton instance, handling the encoding.
     * @var
     */
    private static $instance;

    private function __construct()
    {
        $this->packer = new Packer(null, [new BinTransformer(), new SignedTransactionTransformer()]);
        $this->unpacker = new BufferUnpacker();
        $this->transformerFactory = new CustomTransformerFactory($this->unpacker);
        $this->transformerFactory->registerTransformer(new BaseTransactionTransformer());
        $this->transformerFactory->registerTransformer(new CustomSignedTransactionTransformer($this->transformerFactory));
        $this->transformerFactory->registerTransformer(new PaymentTransactionTransformer());
        $this->transformerFactory->registerTransformer(new AssetConfigTransformer());
        $this->transformerFactory->registerTransformer(new AssetTransferTransformer());
        $this->transformerFactory->registerTransformer(new AssetFreezeTransformer());
        $this->transformerFactory->registerTransformer(new KeyRegistrationTransformer());

        $this->transformerFactory->registerTransformer(new ApplicationTransformer());
        $this->transformerFactory->registerTransformer(new ApplicationUpdateTransformer());
        $this->transformerFactory->registerTransformer(new ApplicationCreateTransformer());

        $this->transformerFactory->registerTransformer(new LogicSignatureTransformer());
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Decode a binary MessagePack string into the class object.
     * @param string $data
     * @param string $class
     * @return mixed
     */
    public function decodeMessagePack(string $data, string $className, ?array $extraData = null)
    {
        $data = $this->transformerFactory->transform($data, $className, $extraData);

        return $data;
    }
}
