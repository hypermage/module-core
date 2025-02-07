<?php

declare(strict_types=1);

namespace Hypermage\Core\ViewModel;

use Hypermage\Core\Model\BlockSpecification;
use Hypermage\Core\Model\BlockSpecificationFactory;
use Hypermage\Core\Model\Signature;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\BlockInterface;

readonly class Htmx implements ArgumentInterface
{
    public function __construct(
        protected RequestInterface $request,
        protected BlockSpecificationFactory $blockSpecificationFactory,
        protected Signature $signature,
    )
    {
    }

    public function isHtmxRequest(): bool
    {
        return isset($_SERVER['HTTP_HX_REQUEST']);
    }

    public function getBlockSpecification(BlockInterface $block): BlockSpecification
    {
        return $this->blockSpecificationFactory->fromBlock($block);
    }

    public function getUrl(string $url, array $params = []): string
    {
        $signature = $this->signature->sign($params);
        $params['signature'] = $signature;

        return $url . '?' . http_build_query($params);
    }
}
