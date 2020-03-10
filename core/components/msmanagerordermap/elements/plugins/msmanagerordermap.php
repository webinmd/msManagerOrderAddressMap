<?php

/** @var modX $modx */
switch ($modx->event->name) {
    case 'msOnManagerCustomCssJs':
        
        if ($page != 'orders') return;

            $modx->controller->addLexiconTopic('msmanagerordermap:default');
        
            // get order delivery 
            if(!$deliveryString = $modx->getOption('msmanagerordermap_deliveries')) {
                return;
            }
            
            $locale = $modx->getOption('msmanagerordermap_locale') ?: 'ru_RU';
            $key = $modx->getOption('msmanagerordermap_key');
           
            if(!$key && strlen($key) != 36) {
                $modx->log(xPDO::LOG_LEVEL_ERROR, $modx->lexicon('error_msmanagerordermap__key_empty'));
                return;
            }
        
            if(!$addressFields = $modx->getOption('msmanagerordermap_address_fields')) {
                $modx->log(xPDO::LOG_LEVEL_ERROR,$modx->lexicon('error_msmanagerordermap__address_fields_empty'));
                return;
            }
 
            $scrollZoom = $modx->getOption('msmanagerordermap_scrollZoom_disable') ?: 'enabled';
        
        	$modx->regClientStartupHTMLBlock("
        	
        	    <script src='https://api-maps.yandex.ru/2.1/?lang={$locale}&amp;apikey={$key}'></script>
        	    
        	    <script>
        	    
        	    Ext.onReady(function() {
        	    
        	        Ext.ComponentMgr.onAvailable('minishop2-window-order-update', function(){
        	        
            	        ///////////////////////
                        this.fields.items[2].items.push({
                            xtype: 'panel',
                            html: '<div id=\'ms-order-address-map\' style=\'height: 350px; margin-top: 20px;\'></div>'
                        });
                        
                        ///////////////////////
                        setTimeout(function() {
                        
                            let delivery = parseInt(document.getElementsByName('delivery')[0].value);
                            let deliveryArray = [{$deliveryString}]; 
                            let addressArray = '{$addressFields}'.split(','); 
                            
                            if(deliveryArray.includes(delivery)) {                                  
                                for (i = 0, len = addressArray.length, address = ''; i < len; i++) {   
                                    if(document.getElementsByName(addressArray[i])[0] !== undefined) {
                                        address += document.getElementsByName(addressArray[i])[0].value + ' '; 
                                    }  
                                } 
        
                                if(address) {            
                                    ymaps.ready(init);
                                }
                            } 
                            
                        } , 100); 
                        
                        function init(){ 
                            var map,
                                placemark;
                    
                            map = new ymaps.Map('ms-order-address-map', { 
                                zoom: 16,
                                center: [55.753994, 37.622093],
                                controls: ['smallMapDefaultSet']
                            }); 
                            
                            if('{$scrollZoom}' != 'enabled') {
                                map.behaviors.disable('scrollZoom');
                            }
                    
                            ymaps.geocode(address, {
                                results: 1
                            }).then(function (res) {
                            
                                // Выбираем первый результат геокодирования.
                                var firstGeoObject = res.geoObjects.get(0),
                                
                                    // Координаты геообъекта.
                                    coords = firstGeoObject.geometry.getCoordinates(),
                                    
                                    // Область видимости геообъекта.
                                    bounds = firstGeoObject.properties.get('boundedBy');
                    
                                firstGeoObject.options.set('preset', 'islands#darkBlueDotIconWithCaption');
                                
                                // Получаем строку с адресом и выводим в иконке геообъекта.
                                firstGeoObject.properties.set('iconCaption', firstGeoObject.getAddressLine());
                    
                                // Добавляем первый найденный геообъект на карту.
                                map.geoObjects.add(firstGeoObject);
                                
                                // Масштабируем карту на область видимости геообъекта.
                                map.setBounds(bounds, { 
                                    checkZoomRange: true
                                });             
                            });
                        }                        
    
        	        });
                     
                });
                        	    
        	    </script>
        	    
        	"); 
    break; 

}
