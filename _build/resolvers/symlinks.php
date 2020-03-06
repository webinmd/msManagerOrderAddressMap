<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;

    $dev = MODX_BASE_PATH . 'Extras/msOrderAddressMap/';
    /** @var xPDOCacheManager $cache */
    $cache = $modx->getCacheManager();
    if (file_exists($dev) && $cache) {
        if (!is_link($dev . 'assets/components/msorderaddressmap')) {
            $cache->deleteTree(
                $dev . 'assets/components/msorderaddressmap/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_ASSETS_PATH . 'components/msorderaddressmap/', $dev . 'assets/components/msorderaddressmap');
        }
        if (!is_link($dev . 'core/components/msorderaddressmap')) {
            $cache->deleteTree(
                $dev . 'core/components/msorderaddressmap/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_CORE_PATH . 'components/msorderaddressmap/', $dev . 'core/components/msorderaddressmap');
        }
    }
}

return true;