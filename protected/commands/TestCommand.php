<?php 

class TestCommand extends CConsoleCommand
{

    public function run($args) {
        print_r($args);
        echo "test\n";
    }
}