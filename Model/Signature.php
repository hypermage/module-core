<?php

declare(strict_types=1);

namespace Hypermage\Core\Model;

use Magento\Framework\App\Cache\Backend\Config;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * We must add a signature to the request to prevent client side from manipulating the request
 * Using hash_hmac with sha256 algorithm we can ensure integrity of the request
 */
class Signature
{
    public final const string XML_PATH_SECRET_KEY = 'hypermage/general/secret_key';
    public final const string ALGORITHM = 'sha256';

    public function __construct(
        private readonly WriterInterface $configWriter,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly Pool $cacheFrontendPool,
    )
    {
    }

    public function sign(array $data): string
    {
        return hash_hmac(self::ALGORITHM, http_build_query($data), $this->getHash());
    }

    public function validate(array $data, string $signature): bool
    {
        $expectedSignature = $this->sign($data);

        return hash_equals($expectedSignature, $signature);
    }

    private function getHash(): string
    {
        $hash = $this->scopeConfig->getValue(self::XML_PATH_SECRET_KEY, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);

        if (!$hash) {
            $hash = $this->generateHash();
        }

        return $hash;
    }

    private function generateHash(): string
    {
        $hash = bin2hex(random_bytes(32));
        $this->configWriter->save(self::XML_PATH_SECRET_KEY, $hash);

        foreach ($this->cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->clean();
        }

        return $hash;
    }
}
