<?php

declare(strict_types=1);

namespace Hypermage\Core\ViewModel;

use Hypermage\Core\Model\HxParams;
use Hypermage\Core\Model\HxParamsFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\BlockInterface;

readonly class Htmx implements ArgumentInterface
{
    public function __construct(
        protected RequestInterface     $request,
        protected HxParamsFactory      $hxParamsFactory,
    )
    {
    }

    /**
     * Any modifications on the block data before calling this method will persist.
     */
    public function getHxParams(BlockInterface $block): HxParams
    {
        return $this->hxParamsFactory->create($block);
    }

    public function isHtmxRequest(): bool
    {
        return isset($_SERVER['HTTP_HX_REQUEST']);
    }
}
