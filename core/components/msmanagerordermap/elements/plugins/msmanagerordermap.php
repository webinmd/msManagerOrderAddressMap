<?php

/** @var modX $modx */
switch ($modx->event->name) {
    case 'msOnManagerCustomCssJs':
        
        if ($page != 'orders') return;

            $modx->controller->addLexiconTopic('msmanagerordermap:default'); 
             
            if(!$deliveryString = $modx->getOption('msmanagerordermap_deliveries')) {
                return;
            }
            
            $locale = $modx->getOption('msmanagerordermap_locale') ?: 'ru_RU';
            $prefix = $modx->getOption('msmanagerordermap_address_prefix') ?: 'empty';
            $suffix = $modx->getOption('msmanagerordermap_address_suffix') ?: 'empty';
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
                                html: '<div id=\'ms-order-address-map\'></div>'
                            }); 
                            ///////////////////////
                            
                            setTimeout(function() {
                            
                                var delivery = parseInt(document.getElementsByName('delivery')[0].value);
                                var deliveryArray = [{$deliveryString}]; 
                                var addressArray = '{$addressFields}'.split(','); 
                                 
                                if(deliveryArray.includes(delivery)) {  
                                    document.getElementById('ms-order-address-map').setAttribute('style', 'height:350px; margin-top: 20px;');
                                    getAddress(addressArray);
                                } 
                                
    
                                for (i = 0, len = addressArray.length, address = ''; i < len; i++) {   
                                    if(document.getElementsByName(addressArray[i])[0] !== undefined) { 
                                        document.getElementsByName(addressArray[i])[0].addEventListener('change', (event) => {
                                            getAddress(addressArray); 
                                        });
                                    }  
                                } 
                                
                            } , 100); 
                            
                            function getAddress(addressArray) {
                                var address;
                                for (i = 0, len = addressArray.length, address = ''; i < len; i++) {   
                                    if(document.getElementsByName(addressArray[i])[0] !== undefined) {
                                        address += document.getElementsByName(addressArray[i])[0].value + ' '; 
                                    }  
                                } 
        
                                if(address) {                                  
                                    if('{$prefix}' != 'empty') {
                                        address = '{$prefix}'+' '+address;
                                    }                                    
                                    if('{$suffix}' != 'empty') {
                                        address =  address+' '+'{$suffix}';
                                    }    
                                    document.getElementById('ms-order-address-map').innerHTML = '';
                                    ymaps.ready(init(address));
                                }
                            }
    
                            
                            function init(address) { 
                                
                                return function () {
                                
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
                            }
        
            	        });
                         
                    });
                        	    
        	    </script>
        	    
        	"); 
    break; 

}