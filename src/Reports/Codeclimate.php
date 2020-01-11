<?php

namespace PHP_CodeSniffer\Reports;

use PHP_CodeSniffer\Files\File;

/**
 * Codeclimate report for PHP_CodeSniffer.
 */
class Codeclimate implements Report
{
    /**
     * {@inheritdoc}
     */
    public function generateFileReport(
        $report,
        File $phpcsFile,
        $showSources = false,
        $width = 80) 
    {
        foreach ($report['messages'] as $line => $lineErrors) {
            foreach ($lineErrors as $colErrors) {
                foreach ($colErrors as $error) {
                    if (!Sniffs::isValidIssue($error)) {
                        continue;
                    }

                    $issue = new CodeclimateIssue();
                    $issue->check_name  = $error['source'];
                    $issue->description = str_replace(array("\n", "\r", "\t"), array('\n', '\r', '\t'), $error['message']);
                    $issue->location->path = $phpcsFile->getFilename();
                    $issue->location->lines->begin = $line;
                    $issue->location->lines->end   = $line;
                    $issue->remediation_points = Sniffs::pointsFor($error);

                    echo json_encode($issue, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), ',';
                }
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(
        $cachedData,
        $totalFiles,
        $totalErrors,
        $totalWarnings,
        $totalFixable,
        $showSources = false,
        $width = 80,
        $interactive = false,
        $toScreen = true)
    {
        echo '[', rtrim($cachedData, ','), ']', PHP_EOL;
    }
}

class CodeclimateIssue
{
    /**
     * @var string
     */
    public $type = 'issue';

    /**
     * @var string
     */
    public $check_name = '';

    /**
     * @var string
     */
    public $description = '';

    /**
     * @var array
     */
    public $categories = array('Styles');

    /**
     * @var CodeclimateIssueLocation
     */
    public $location;

    /**
     * @var integer
     */
    public $remediation_points = 0;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->location = new CodeclimateIssueLocation();
    }
}

class CodeclimateIssueLocation
{
    /**
     * @var string
     */
    public $path = '';

    /**
     * @var CodeclimateIssueLocationLines
     */
    public $lines;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->lines = new CodeclimateIssueLocationLines();
    }
}

class CodeclimateIssueLocationLines
{
    /**
     * @var integer
     */
    public $begin = 0;

    /**
     * @var integer
     */
    public $end = 0;
}

/**
 * @license
 * Copyright (c) 2015 Code Climate, Inc.
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
class Sniffs
{
    const DEFAULT_POINTS = 70000;
    const INVALID_SNIFFS = array(
        "Internal.NoCodeFound",
        "Internal.Tokenizer.Exception"
    );
    public static $sniffs = array(
        "Drupal.Array.Array" => 60000,
        "Drupal.CSS.ClassDefinitionNameSpacing" => 50000,
        "Drupal.CSS.ColourDefinition" => 50000,
        "Drupal.Classes.ClassCreateInstance" => 60000,
        "Drupal.Classes.ClassDeclaration" => 60000,
        "Drupal.Classes.FullyQualifiedNamespace" => 50000,
        "Drupal.Classes.InterfaceName" => 50000,
        "Drupal.Classes.UnusedUseStatement" => 50000,
        "Drupal.Commenting.ClassComment" => 50000,
        "Drupal.Commenting.DocComment" => 60000,
        "Drupal.Commenting.DocCommentStar" => 50000,
        "Drupal.Commenting.FileComment" => 60000,
        "Drupal.Commenting.FunctionComment" => 60000,
        "Drupal.Commenting.HookComment" => 50000,
        "Drupal.Commenting.InlineComment" => 60000,
        "Drupal.Commenting.PostStatementComment" => 50000,
        "Drupal.ControlStructures.ControlSignature" => 50000,
        "Drupal.ControlStructures.ElseIf" => 50000,
        "Drupal.ControlStructures.InlineControlStructure" => 50000,
        "Drupal.Files.EndFileNewline" => 50000,
        "Drupal.Files.FileEncoding" => 50000,
        "Drupal.Files.LineLength" => 50000,
        "Drupal.Files.TxtFileLineLength" => 50000,
        "Drupal.Formatting.MultiLineAssignment" => 60000,
        "Drupal.Formatting.SpaceInlineIf" => 60000,
        "Drupal.Formatting.SpaceUnaryOperator" => 50000,
        "Drupal.Functions.DiscouragedFunctions" => 50000,
        "Drupal.Functions.FunctionDeclaration" => 60000,
        "Drupal.InfoFiles.AutoAddedKeys" => 50000,
        "Drupal.InfoFiles.ClassFiles" => 60000,
        "Drupal.InfoFiles.DuplicateEntry" => 60000,
        "Drupal.InfoFiles.Required" => 60000,
        "Drupal.NamingConventions.ValidClassName" => 60000,
        "Drupal.NamingConventions.ValidFunctionName" => 60000,
        "Drupal.NamingConventions.ValidGlobal" => 60000,
        "Drupal.NamingConventions.ValidVariableName" => 60000,
        "Drupal.Semantics.ConstantName" => 50000,
        "Drupal.Semantics.EmptyInstall" => 60000,
        "Drupal.Semantics.FunctionAlias" => 60000,
        "Drupal.Semantics.FunctionT" => 50000,
        "Drupal.Semantics.FunctionWatchdog" => 60000,
        "Drupal.Semantics.InstallHooks" => 60000,
        "Drupal.Semantics.LStringTranslatable" => 60000,
        "Drupal.Semantics.PregSecurity" => 60000,
        "Drupal.Semantics.RemoteAddress" => 60000,
        "Drupal.Semantics.TInHookMenu" => 60000,
        "Drupal.Semantics.TInHookSchema" => 60000,
        "Drupal.Strings.UnnecessaryStringConcat" => 60000,
        "Drupal.WhiteSpace.CloseBracketSpacing" => 50000,
        "Drupal.WhiteSpace.Comma" => 50000,
        "Drupal.WhiteSpace.EmptyLines" => 60000,
        "Drupal.WhiteSpace.Namespace" => 50000,
        "Drupal.WhiteSpace.ObjectOperatorIndent" => 60000,
        "Drupal.WhiteSpace.ObjectOperatorSpacing" => 50000,
        "Drupal.WhiteSpace.OpenBracketSpacing" => 50000,
        "Drupal.WhiteSpace.OperatorSpacing" => 50000,
        "Drupal.WhiteSpace.ScopeClosingBrace" => 50000,
        "Drupal.WhiteSpace.ScopeIndent" => 50000,
        "DrupalPractice.CodeAnalysis.VariableAnalysis" => 50000,
        "DrupalPractice.Commenting.AuthorTag" => 50000,
        "DrupalPractice.Commenting.CommentEmptyLine" => 50000,
        "DrupalPractice.FunctionCalls.CheckPlain" => 50000,
        "DrupalPractice.FunctionCalls.CurlSslVerifier" => 50000,
        "DrupalPractice.FunctionCalls.DbQuery" => 50000,
        "DrupalPractice.FunctionCalls.DbSelectBraces" => 50000,
        "DrupalPractice.FunctionCalls.DefaultValueSanitize" => 50000,
        "DrupalPractice.FunctionCalls.FormErrorT" => 50000,
        "DrupalPractice.FunctionCalls.LCheckPlain" => 50000,
        "DrupalPractice.FunctionCalls.MessageT" => 50000,
        "DrupalPractice.FunctionCalls.TCheckPlain" => 50000,
        "DrupalPractice.FunctionCalls.Theme" => 50000,
        "DrupalPractice.FunctionCalls.VariableSetSanitize" => 50000,
        "DrupalPractice.FunctionDefinitions.AccessHookMenu" => 50000,
        "DrupalPractice.FunctionDefinitions.FormAlterDoc" => 50000,
        "DrupalPractice.FunctionDefinitions.HookInitCss" => 50000,
        "DrupalPractice.FunctionDefinitions.InstallT" => 50000,
        "DrupalPractice.General.AccessAdminPages" => 50000,
        "DrupalPractice.General.ClassName" => 50000,
        "DrupalPractice.General.DescriptionT" => 50000,
        "DrupalPractice.General.FormStateInput" => 50000,
        "DrupalPractice.General.LanguageNone" => 50000,
        "DrupalPractice.General.OptionsT" => 50000,
        "DrupalPractice.General.VariableName" => 50000,
        "Generic.ControlStructures.InlineControlStructure.NotAllowed" => 60000,
        "Generic.Files.LineEndings.InvalidEOLChar" => 60000,
        "Generic.Files.LineLength.TooLong" => 60000,
        "Generic.Functions.FunctionCallArgumentSpacing.NoSpaceAfterComma" => 50000,
        "Generic.WhiteSpace.DisallowTabIndent.TabsUsed" => 60000,
        "Generic.WhiteSpace.ScopeIndent.Incorrect" => 50000,
        "Generic.WhiteSpace.ScopeIndent.IncorrectExact" => 50000,
        "PEAR.Functions.ValidDefaultValue.NotAtEnd" => 60000,
        "PSR1.Classes.ClassDeclaration.MissingNamespace" => 600000,
        "PSR1.Classes.ClassDeclaration.MultipleClasses" => 400000,
        "PSR1.Files.SideEffects.FoundWithSymbols" => 500000,
        "PSR2.ControlStructures.ControlStructureSpacing.SpacingAfterOpenBrace" => 50000,
        "PSR2.ControlStructures.ElseIfDeclaration.NotAllowed" => 50000,
        "PSR2.ControlStructures.SwitchDeclaration.TerminatingComment" => 50000,
        "PSR2.Files.ClosingTag.NotAllowed" => 50000,
        "PSR2.Files.EndFileNewline.NoneFound" => 50000,
        "PSR2.Methods.FunctionCallSignature.CloseBracketLine" => 50000,
        "PSR2.Methods.FunctionCallSignature.ContentAfterOpenBracket" => 50000,
        "PSR2.Methods.FunctionCallSignature.Indent" => 50000,
        "PSR2.Methods.FunctionCallSignature.MultipleArguments" => 70000,
        "PSR2.Methods.FunctionCallSignature.SpaceAfterOpenBracket" => 50000,
        "PSR2.Methods.FunctionCallSignature.SpaceBeforeCloseBracket" => 50000,
        "PSR2.Methods.FunctionCallSignature.SpaceBeforeOpenBracket" => 50000,
        "Squiz.Classes.ValidClassName.NotCamelCaps" => 50000,
        "Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace" => 50000,
        "Squiz.ControlStructures.ControlSignature.SpaceAfterCloseBrace" => 50000,
        "Squiz.ControlStructures.ControlSignature.SpaceAfterCloseParenthesis" => 50000,
        "Squiz.ControlStructures.ControlSignature.SpaceAfterKeyword" => 50000,
        "Squiz.ControlStructures.ControlSignature.SpaceBeforeSemicolon" => 50000,
        "Squiz.ControlStructures.ForLoopDeclaration.NoSpaceAfterFirst" => 50000,
        "Squiz.ControlStructures.ForLoopDeclaration.NoSpaceAfterSecond" => 50000,
        "Squiz.Functions.FunctionDeclarationArgumentSpacing.NoSpaceBeforeArg" => 50000,
        "Squiz.Functions.MultiLineFunctionDeclaration.BraceOnSameLine" => 50000,
        "Squiz.Functions.MultiLineFunctionDeclaration.ContentAfterBrace" => 50000,
        "Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore" => 50000,
        "Squiz.WhiteSpace.ScopeClosingBrace.Indent" => 50000,
        "Squiz.WhiteSpace.SuperfluousWhitespace.EndLine" => 50000,
        "WordPress.Arrays.ArrayAssignmentRestrictions" => 50000,
        "WordPress.Arrays.ArrayDeclaration" => 50000,
        "WordPress.Arrays.ArrayKeySpacingRestrictions" => 50000,
        "WordPress.CSRF.NonceVerification" => 50000,
        "WordPress.Classes.ValidClassName" => 50000,
        "WordPress.Files.FileName" => 50000,
        "WordPress.Functions.FunctionRestrictions" => 50000,
        "WordPress.NamingConventions.ValidFunctionName" => 50000,
        "WordPress.NamingConventions.ValidVariableName" => 50000,
        "WordPress.PHP.DiscouragedFunctions" => 50000,
        "WordPress.PHP.StrictComparisons" => 50000,
        "WordPress.PHP.StrictInArray" => 50000,
        "WordPress.PHP.YodaConditions" => 50000,
        "WordPress.VIP.AdminBarRemoval" => 50000,
        "WordPress.VIP.CronInterval" => 50000,
        "WordPress.VIP.DirectDatabaseQuery" => 50000,
        "WordPress.VIP.FileSystemWritesDisallow" => 50000,
        "WordPress.VIP.OrderByRand" => 50000,
        "WordPress.VIP.PluginMenuSlug" => 50000,
        "WordPress.VIP.PostsPerPage" => 50000,
        "WordPress.VIP.RestrictedFunctions" => 50000,
        "WordPress.VIP.RestrictedVariables" => 50000,
        "WordPress.VIP.SessionFunctionsUsage" => 50000,
        "WordPress.VIP.SessionVariableUsage" => 50000,
        "WordPress.VIP.SlowDBQuery" => 50000,
        "WordPress.VIP.SuperGlobalInputUsage" => 50000,
        "WordPress.VIP.TimezoneChange" => 50000,
        "WordPress.VIP.ValidatedSanitizedInput" => 50000,
        "WordPress.Variables.GlobalVariables" => 50000,
        "WordPress.Variables.VariableRestrictions" => 50000,
        "WordPress.WP.EnqueuedResources" => 50000,
        "WordPress.WP.PreparedSQL" => 50000,
        "WordPress.WhiteSpace.CastStructureSpacing" => 50000,
        "WordPress.WhiteSpace.ControlStructureSpacing" => 50000,
        "WordPress.WhiteSpace.OperatorSpacing" => 50000,
        "WordPress.XSS.EscapeOutput" => 50000,
    );
    public static function pointsFor($issue)
    {
        $sniffName = $issue["source"];
        if (array_key_exists($sniffName, self::$sniffs)) {
            return self::$sniffs[$sniffName];
        } else {
            return $issue["severity"] * self::DEFAULT_POINTS;
        }
    }
    public static function isValidIssue($issue) {
        $sniffName = $issue["source"];
        if (in_array($sniffName, self::INVALID_SNIFFS)) {
            return false;
        } else {
            return true;
        }
    }
}
