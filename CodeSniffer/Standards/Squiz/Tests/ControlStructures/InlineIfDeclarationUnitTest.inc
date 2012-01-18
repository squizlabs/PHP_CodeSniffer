<?php

echo 'var is '.(($var < 0) ? 'negative' : 'positive');
echo 'var is '.($var < 0 ? 'negative' : 'positive');
echo 'var is '.(($var < 0) ? 'negative' :'positive');
echo 'var is '.(($var < 0) ? 'negative': 'positive');
echo 'var is '.(($var < 0) ? 'negative' :   'positive');
echo 'var is '.(($var < 0) ? 'negative'   : 'positive');
echo 'var is '.(($var < 0) ?   'negative' : 'positive');
echo 'var is '.(($var < 0)  ? 'negative' : 'positive');

echo 'var is '.(($var < 0)
                    ? 'negative'
                    : 'positive');

$args = array(
         '"'.$this->id.'"',
         '"'.$this->stepInfo['title'].'"',
         '"'.((isset($this->stepInfo['description']) === TRUE) ? $this->stepInfo['description'] : '').'"',
         '"'.(isset($this->stepInfo['description']) === TRUE ? $this->stepInfo['description'] : '').'"',
         '"'.$this->stepInfo['title'].'"',
        );

echo (TRUE)?'Hello':'Bye';

?>