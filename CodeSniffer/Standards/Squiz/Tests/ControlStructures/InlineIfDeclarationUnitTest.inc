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
?>