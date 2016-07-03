<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Ruleset\Contract\Rule;

interface ReferenceNormalizerInterface
{
    public function normalize(string $reference) : array;

    public function isRulesetReference(string $reference) : bool;

    public function isStandardReference(string $reference) : bool;
}
