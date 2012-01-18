<?php
Channels::includeSystem('Widget');
Widget::includeWidget('PasswordField');
$blah = new PasswordFieldWidgetType();
Widget::getWidget('PasswordField');
?>
