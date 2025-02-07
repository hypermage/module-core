<?php

declare(strict_types=1);

namespace Hypermage\Core\Model;

use Magento\Framework\View\Element\BlockInterface;

readonly class BlockSerializer
{
    public function __construct(
        private BlockFactory              $componentFactory,
        private BlockSpecificationFactory $blockSpecificationFactory,
        private Signature                 $signature,
    )
    {
    }

    public function serialize(BlockInterface $block): string
    {
        $componentData = $this->blockSpecificationFactory->fromBlock($block);
        $signature = $this->signature->sign($componentData->toArray());

        return http_build_query([
            'signature' => $signature,
            'data' => $componentData->toArray(),
        ]);
    }

    public function deserialize(array $serializedBlock): ?BlockInterface
    {
        $data = $serializedBlock['data'];
        $data['signature'] = $serializedBlock['signature'];

        if (!$this->signature->validate($data)) {
            throw new \Exception('Invalid signature');
        }

        $componentData = $this->blockSpecificationFactory->fromArray($data);

        return $this->componentFactory->fetch($componentData);
    }
}
