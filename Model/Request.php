<?php

declare(strict_types=1);

namespace Hypermage\Core\Model;

use Exception;
use Magento\Framework\App\RequestInterface;

readonly class Request
{
    public function __construct(
        private RequestInterface     $request,
        private ComponentDataFactory $componentDataFactory,
        private Signature            $signature,
    )
    {
    }

    public function validate(): bool
    {
        $data = $this->request->getParams();

        if (!isset($data['signature'])) {
            return false;
        }

        $receivedSignature = $data['signature'];
        unset($data['signature']);

        $signature = $this->signature->sign($data);

        return $receivedSignature === $signature;
    }

    public function getData(): ?ComponentData
    {
        try {
            return $this->componentDataFactory->fromRequest($this->request);
        } catch (Exception $e) {
            return null;
        }
    }
}
