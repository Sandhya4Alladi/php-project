<?php
 
return array(
    'aws' => array(
        'region' => $_ENV['AWS_REGION'],
        'credentials' => array(
            'key'    => $_ENV['ACCESS_KEY'],
            'secret' => $_ENV['SECRET_ACCESS_KEY'],
        ),
    ),
);
 
?>