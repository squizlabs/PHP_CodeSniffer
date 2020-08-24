<?php

trait BadName {} // error

trait GoodTrait {}

trait GoodTraitTrait {}

trait BadTraitName {} // error

trait BadTraitNametrait {} // error

trait NormalTraitNameTrait {}