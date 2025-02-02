<?php

declare(strict_types=1);

namespace Hypermage\Core\Model;

use Hypermage\Core\Controller\Block\Block;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Result\Page;

readonly class PageProvider
{
    public function __construct(
        private PageFactory $pageFactory
    )
    {
    }

    /**
     * @description Get the page layout as determined by the provided layout handles
     * @param array $handles The layout handles to be used
     * @return Page The requested page layout
     */
    public function getRequestedPage(array $handles): Page
    {
        $cmsPage = $this->pageFactory->create(false, ['isIsolated' => true]);

        $cmsPage->getLayout()->getUpdate()->removeHandle(Block::LAYOUT_HANDLE);
        $cmsPage->getLayout()->getUpdate()->addPageHandles($handles);

        return $cmsPage;
    }

    /**
     * @description Get the requested block from the page layout
     * @param Page $page The page layout
     * @param string $name The name of the block
     * @return BlockInterface|null The requested block or null if not found
     */
    public function getRequestedBlock(Page $page, string $name): ?BlockInterface
    {
        return $page->getLayout()->getBlock($name) ?: null;
    }
}
