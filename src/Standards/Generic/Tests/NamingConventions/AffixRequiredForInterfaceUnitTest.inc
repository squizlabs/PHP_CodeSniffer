<?php

interface SomeNameInterface
{

}

interface ISomeName // error
{

}

interface ISomeNameInterface
{

}


interface ISomeOtherNameinterface
{

}

interface mInterface
{

}

// phpcs:set Generic.NamingConventions.AffixRequiredForInterface affixType prefix
// phpcs:set Generic.NamingConventions.AffixRequiredForInterface affixValue I

interface SomeOtherNameInterface // error
{

}

interface IInterface
{

}

interface I
{

}

// phpcs:set Generic.NamingConventions.AffixRequiredForInterface affixType prefix
// phpcs:set Generic.NamingConventions.AffixRequiredForInterface affixValue PREF
// phpcs:set Generic.NamingConventions.AffixRequiredForInterface isCaseSensitive true

interface Badpref // error
{

}

interface PREFName
{

}


interface PrefInvalid // error
{

}

// phpcs:set Generic.NamingConventions.AffixRequiredForInterface affixType prefix
// phpcs:set Generic.NamingConventions.AffixRequiredForInterface affixValue PrefixLongerThanInterfaceName

interface ShortName // error
{

}

// phpcs:set Generic.NamingConventions.AffixRequiredForInterface affixType prefix
// phpcs:set Generic.NamingConventions.AffixRequiredForInterface affixValue PrefixSameAsInterfaceName
// phpcs:set Generic.NamingConventions.AffixRequiredForInterface isCaseSensitive true

interface PrefixSameAsInterfaceName
{

}
