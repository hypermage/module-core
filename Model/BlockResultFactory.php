<?php

declare(strict_types=1);

namespace Hypermage\Core\Model;

use Hypermage\Core\Model\BlockResult as BlockResult;
use Magento\Framework\View\Element\BlockInterface;

readonly class BlockResultFactory
{
    public function create(BlockInterface $block): BlockResult
    {
        return new BlockResult($block);
    }
}
