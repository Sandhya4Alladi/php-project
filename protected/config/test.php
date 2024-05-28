<?php 
 
return CMap::mergeArray( 
    require (dirname(__FILE__) . '/main.php'), 
    array( 
        'components' => array( 
            'fixture' => array( 
                'class' => 'system.test.CDbFixtureManager', 
            ), 
            "import" => array( 
                'application.tests.unit.*', 
                'application.tests.components.*', 
            ), 
        ), 
    ) 
); 