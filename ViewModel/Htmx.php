<?php

declare(strict_types=1);

namespace Hypermage\Core\ViewModel;

use Hypermage\Core\Model\ComponentDataFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\BlockInterface;

readonly class Htmx implements ArgumentInterface
{
    public function __construct(
        protected RequestInterface     $request,
        protected ComponentDataFactory $componentDataFactory,
    )
    {
    }

    public function getRequestUrl(BlockInterface $block): string
    {
        $componentData = $this->componentDataFactory->fromBlock($block);

        $query = $componentData . '&signature=' . $componentData->getSignature();

        return "/hypermage/block/block?{$query}";
    }

    public function isHtmxRequest(): bool
    {
        return isset($_SERVER['HTTP_HX_REQUEST']);
    }
}
