@echo off
REM +------------------------------------------------------------------------+
REM | BSD Licence                                                            |
REM +------------------------------------------------------------------------+
REM | This software is available to you under the BSD license,               |
REM | available in the LICENSE file accompanying this software.              |
REM | You may obtain a copy of the License at                                |
REM |                                                                        |
REM | http://matrix.squiz.net/developer/tools/php_cs/licence                 |
REM |                                                                        |
REM | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS    |
REM | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT      |
REM | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR  |
REM | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT   |
REM | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,  |
REM | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT       |
REM | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,  |
REM | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY  |
REM | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT    |
REM | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE  |
REM | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.   |
REM +------------------------------------------------------------------------+
REM | Copyright (c), 2006 Squiz Pty Ltd (ABN 77 084 670 600).                |
REM | All rights reserved.                                                   |
REM +------------------------------------------------------------------------+
REM
REM @package PHP_CodeSniffer
REM @author  Squiz Pty Ltd

"@php_bin@" -d include_path="@php_dir@" "@bin_dir@\phpcs" %*
