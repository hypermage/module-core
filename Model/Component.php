<?php

declare(strict_types=1);

namespace Hypermage\Core\Model;

use Magento\Framework\View\Element\BlockInterface;

readonly class Component implements BlockInterface
{
    public function __construct(
        protected BlockInterface $block,
    ) {
    }

    public function toHtml(): string
    {
        return $this->block->toHtml();
    }
}
