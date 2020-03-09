<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;

    $dev = MODX_BASE_PATH . 'Extras/msManagerOrderAddressMap/';
    /** @var xPDOCacheManager $cache */
    $cache = $modx->getCacheManager();
    if (file_exists($dev) && $cache) {
        if (!is_link($dev . 'assets/components/msmanagerorderaddressmap')) {
            $cache->deleteTree(
                $dev . 'assets/components/msmanagerorderaddressmap/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_ASSETS_PATH . 'components/msmanagerorderaddressmap/', $dev . 'assets/components/msorderaddressmap');
        }
        if (!is_link($dev . 'core/components/msmanagerorderaddressmap')) {
            $cache->deleteTree(
                $dev . 'core/components/msmanagerorderaddressmap/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_CORE_PATH . 'components/msmanagerorderaddressmap/', $dev . 'core/components/msmanagerorderaddressmap');
        }
    }
}

return true;