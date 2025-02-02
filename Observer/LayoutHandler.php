<?php

declare(strict_types=1);

namespace Hypermage\Core\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

readonly class LayoutHandler implements ObserverInterface
{
    protected final const string ENABLED = 'hypermage_general/general/enabled';

    public function __construct(
        private LayoutInterface       $layout,
        private RequestInterface      $request,
        private ScopeConfigInterface  $scopeConfig,
        private StoreManagerInterface $storeManager,
    )
    {
    }

    public function execute(Observer $observer)
    {
        if (!$this->isHypermageEnabled()) {
            return;
        }

        $actionName = $this->request->getFullActionName();

        $update = $this->layout->getUpdate();
        $update->addHandle('hypermage_default');
        $update->addHandle('hypermage_' . $actionName);
    }

    private function isHypermageEnabled(): bool
    {
        $website = $this->getCurrentWebsite();

        $value = $this->scopeConfig->getValue(self::ENABLED, ScopeInterface::SCOPE_WEBSITE, $website->getId());

        return $value === '1';
    }

    private function getCurrentWebsite(): WebsiteInterface
    {
        return $this->storeManager->getWebsite();
    }
}
