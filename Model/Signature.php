<?php

declare(strict_types=1);

namespace Hypermage\Core\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use RuntimeException;

/**
 * We must add a signature to the request to prevent client side from manipulating the request
 * Using hash_hmac with sha256 algorithm we can ensure integrity of the request
 */
class Signature
{
    public final const string XML_PATH_SECRET_KEY = 'hypermage/general/secret_key';
    public final const string ALGORITHM = 'sha256';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
    )
    {
    }

    /**
     * @throws RuntimeException
     */
    public function sign(array $data): string
    {
        unset($data['signature']);

        return hash_hmac(self::ALGORITHM, http_build_query($data), $this->getHash());
    }

    /**
     * @throws RuntimeException
     */
    public function validate(array $data): bool
    {
        if (!isset($data['signature'])) {
            return false;
        }

        $signature = $data['signature'];

        $expectedSignature = $this->sign($data);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * @throws RuntimeException
     */
    private function getHash(): string
    {
        $hash = $this->scopeConfig->getValue(self::XML_PATH_SECRET_KEY);

        if (!$hash) {
            throw new RuntimeException('No Hypermage signature found');
        }

        return $hash;
    }
}
