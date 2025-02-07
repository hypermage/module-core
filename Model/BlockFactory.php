<?php

declare(strict_types=1);

namespace Hypermage\Core\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

readonly class BlockFactory
{
    public function __construct(
        private PageFactory $pageFactory,
        private RequestInterface $request,
    )
    {
    }

    public function create(BlockSpecification $specification): ?BlockInterface
    {
        $page = $this->getPage($specification->getLayoutHandles());
        $block = $page->getLayout()->getBlock($specification->getNameInLayout()) ?: null;

        if (!$block) {
            return null;
        }

        $data = $specification->getData();
        foreach ($data as $key => $value) {
            $block->setData($key, $value);
        }

        return $block;
    }

    private function getPage(array $handles): Page
    {
        $currentLayoutHandle = $this->request->getFullActionName();

        $cmsPage = $this->pageFactory->create(false, ['isIsolated' => true]);
        $cmsPage->getLayout()->getUpdate()->removeHandle($currentLayoutHandle);
        $cmsPage->getLayout()->getUpdate()->addPageHandles($handles);

        return $cmsPage;
    }
}
