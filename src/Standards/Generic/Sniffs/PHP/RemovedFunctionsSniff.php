<?php
/**
 * Alerts developers to functions that have been removed from PHP.
 *
 * @author    Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\PHP;

class RemovedFunctionsSniff extends ForbiddenFunctionsSniff
{


    /**
     * A list of forbidden functions with their alternatives.
     *
     * The value is NULL if no alternative exists. IE, the
     * function should just not be used.
     *
     * @var array(string => string|null)
     */
    public $forbiddenFunctions = array();


    /**
     * A list of functions that have been removed from PHP.
     *
     * The list is keyed by the minor version in which the function
     * was first removed from PHP core. A list of functions can be
     * found in the PHP manual.
     *
     * @link http://php.net/manual/en/migration70.removed-exts-sapis.php
     *
     * @var array(string => array)
     */
    protected $removedFunctions = array(
        70000 => array(
            'ereg' => 'preg_match',
            'mssql_bind' => null,
            'mssql_close' => null,
            'mssql_connect' => null,
            'mssql_data_seek' => null,
            'mssql_execute' => null,
            'mssql_fetch_array' => null,
            'mssql_fetch_assoc' => null,
            'mssql_fetch_batch' => null,
            'mssql_fetch_field' => null,
            'mssql_fetch_object' => null,
            'mssql_fetch_row' => null,
            'mssql_field_length' => null,
            'mssql_field_name' => null,
            'mssql_field_seek' => null,
            'mssql_field_type' => null,
            'mssql_free_result' => null,
            'mssql_free_statement' => null,
            'mssql_get_last_message' => null,
            'mssql_guid_string' => null,
            'mssql_init' => null,
            'mssql_min_error_severity' => null,
            'mssql_min_message_severity' => null,
            'mssql_next_result' => null,
            'mssql_num_fields' => null,
            'mssql_num_rows' => null,
            'mssql_pconnect' => null,
            'mssql_query' => null,
            'mssql_result' => null,
            'mssql_rows_affected' => null,
            'mssql_select_db' => null,
            'mysql_affected_rows' => null,
            'mysql_client_encoding' => null,
            'mysql_close' => null,
            'mysql_connect' => null,
            'mysql_create_db' => null,
            'mysql_data_seek' => null,
            'mysql_db_name' => null,
            'mysql_db_query' => null,
            'mysql_drop_db' => null,
            'mysql_errno' => null,
            'mysql_error' => null,
            'mysql_escape_string' => null,
            'mysql_fetch_array' => null,
            'mysql_fetch_assoc' => null,
            'mysql_fetch_field' => null,
            'mysql_fetch_lengths' => null,
            'mysql_fetch_object' => null,
            'mysql_fetch_row' => null,
            'mysql_field_flags' => null,
            'mysql_field_len' => null,
            'mysql_field_name' => null,
            'mysql_field_seek' => null,
            'mysql_field_table' => null,
            'mysql_field_type' => null,
            'mysql_free_result' => null,
            'mysql_get_client_info' => null,
            'mysql_get_host_info' => null,
            'mysql_get_proto_info' => null,
            'mysql_get_server_info' => null,
            'mysql_info' => null,
            'mysql_insert_id' => null,
            'mysql_list_dbs' => null,
            'mysql_list_fields' => null,
            'mysql_list_processes' => null,
            'mysql_list_tables' => null,
            'mysql_num_fields' => null,
            'mysql_num_rows' => null,
            'mysql_pconnect' => null,
            'mysql_ping' => null,
            'mysql_query' => null,
            'mysql_real_escape_string' => null,
            'mysql_result' => null,
            'mysql_select_db' => null,
            'mysql_set_charset' => null,
            'mysql_stat' => null,
            'mysql_tablename' => null,
            'mysql_thread_id' => null,
            'mysql_unbuffered_query' => null,
            'sybase_affected_rows' => null,
            'sybase_close' => null,
            'sybase_connect' => null,
            'sybase_data_seek' => null,
            'sybase_deadlock_retry_count' => null,
            'sybase_fetch_array' => null,
            'sybase_fetch_assoc' => null,
            'sybase_fetch_field' => null,
            'sybase_fetch_object' => null,
            'sybase_fetch_row' => null,
            'sybase_field_seek' => null,
            'sybase_free_result' => null,
            'sybase_get_last_message' => null,
            'sybase_min_client_severity' => null,
            'sybase_min_error_severity' => null,
            'sybase_min_message_severity' => null,
            'sybase_min_server_severity' => null,
            'sybase_num_fields' => null,
            'sybase_num_rows' => null,
            'sybase_pconnect' => null,
            'sybase_query' => null,
            'sybase_result' => null,
            'sybase_select_db' => null,
            'sybase_set_message_handler' => null,
            'sybase_unbuffered_query' => null,
        ),
    );


    /**
     * Constructor.
     *
     * Loop through the removedFunctions array and create a list of
     * everything that's been removed up to the version of PHP currently
     * being used.
     */
    public function __construct()
    {
        foreach ($this->removedFunctions as $versionId => $functions) {
            if (PHP_VERSION_ID >= $versionId) {
                $this->forbiddenFunctions = array_merge($this->forbiddenFunctions, $functions);
            }
        }

    }//end __construct()


    /**
     * Generates the error or warning for this sniff.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the forbidden function
     *                                               in the token array.
     * @param string                      $function  The name of the forbidden function.
     * @param string                      $pattern   The pattern used for the match.
     *
     * @return void
     */
    protected function addError($phpcsFile, $stackPtr, $function, $pattern=null)
    {
        $data  = array($function);
        $error = 'Function %s() has been removed';
        $type  = 'Removed';

        if ($this->forbiddenFunctions[$function] !== null) {
            $type  .= 'WithAlternative';
            $data[] = $this->forbiddenFunctions[$function];
            $error .= '; use %s() instead';
        }

        $phpcsFile->addError($error, $stackPtr, $type, $data);

    }//end addError()


}//end class
