<?php

declare(strict_types=1);

namespace Hypermage\Core\Setup\Patch\Data;

use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Hypermage\Core\Model\Signature;

readonly class GenerateSignature implements DataPatchInterface
{
    public function __construct(
        private WriterInterface $configWriter,
        private Pool $cacheFrontendPool,
    )
    {
    }

    public function apply()
    {
        $hash = bin2hex(random_bytes(32));
        $this->configWriter->save(Signature::XML_PATH_SECRET_KEY, $hash);

        foreach ($this->cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->clean();
        }
    }

    public function getAliases()
    {
        return [];
    }

    public static function getDependencies()
    {
        return [];
    }
}
