<?php

declare(strict_types=1);

namespace Hypermage\Core\Model;

use Magento\Framework\View\Element\BlockInterface;

readonly class HxParamsFactory
{
    public function __construct(
        private ComponentDataFactory $componentDataFactory,
        private Signature            $signature,
    )
    {
    }

    public function create(BlockInterface $block): HxParams {
        return new HxParams(
            $block,
            $this->componentDataFactory,
            $this->signature,
        );
    }
}
