<?php

trait BadName {} // error

trait GoodTrait {}

trait GoodTraitTrait {}

trait BadTraitName {} // error

trait BadTraitNametrait {}

trait NormalTraitNameTrait {}