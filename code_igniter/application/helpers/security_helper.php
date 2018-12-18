<?php
if (!defined('BASEPATH')) {
     exit('No direct script access allowed');
}
#
#  Copyright 2003-2015 Opmantek Limited (www.opmantek.com)
#
#  ALL CODE MODIFICATIONS MUST BE SENT TO CODE@OPMANTEK.COM
#
#  This file is part of Open-AudIT.
#
#  Open-AudIT is free software: you can redistribute it and/or modify
#  it under the terms of the GNU Affero General Public License as published
#  by the Free Software Foundation, either version 3 of the License, or
#  (at your option) any later version.
#
#  Open-AudIT is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU Affero General Public License for more details.
#
#  You should have received a copy of the GNU Affero General Public License
#  along with Open-AudIT (most likely in a file named LICENSE).
#  If not, see <http://www.gnu.org/licenses/>
#
#  For further information on Open-AudIT or for a license other than AGPL please see
#  www.opmantek.com or email contact@opmantek.com
#
# *****************************************************************************

/*
* @category  Helper
* @package   Open-AudIT
* @author    Mark Unwin <marku@opmantek.com>
* @copyright 2014 Opmantek
* @license   http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @version   2.3.2
* @link      http://www.open-audit.org
 */

# Take a string and return the decrypted variant
if (! function_exists('oa_decrypt')) {
    /**
     * Wrap crypto_aead_*_decrypt() in a drop-dead-simple decryption interface
     *
     * @link https://paragonie.com/b/kIqqEWlp3VUOpRD7
     * @param string $message - Encrypted message
     * @param string $key     - Encryption key
     * @return string
     * @throws Exception
     */
    function simpleDecrypt($message, $key = '')
    {
        if (php_uname('s') != 'Windows NT') {
            set_include_path('/usr/local/open-audit/code_igniter/application/third_party/sodium_compat');
        } else {
            set_include_path('c:\\xampplite\\open-audit\\code_igniter\\application\\third_party\\sodium_compat');
        }
        require_once "autoload.php";

        if (empty($key)) {
            $CI = & get_instance();
            $key = mb_substr("00000000000000000000000000000000".$CI->config->config['encryption_key'], -32);
        }

        $message = hex2bin($message);
        $nonce = mb_substr($message, 0, 24, '8bit');
        $ciphertext = mb_substr($message, 24, null, '8bit');
        $plaintext = ParagonIE_Sodium_Compat::crypto_aead_xchacha20poly1305_ietf_decrypt(
            $ciphertext,
            $nonce,
            $nonce,
            $key
        );
        if (!is_string($plaintext)) {
            throw new Exception('Invalid message');
        }
        return $plaintext;
    }

}

# Take a string and return the encrypted variant
if (! function_exists('simpleEncrypt')) {
    /**
     * Wrap crypto_aead_*_encrypt() in a drop-dead-simple encryption interface
     *
     * @link https://paragonie.com/b/kIqqEWlp3VUOpRD7
     * @param string $message
     * @param string $key
     * @return string
     */
    function simpleEncrypt($message, $key = '')
    {
        if (php_uname('s') != 'Windows NT') {
            set_include_path('/usr/local/open-audit/code_igniter/application/third_party/sodium_compat');
        } else {
            set_include_path('c:\\xampplite\\open-audit\\code_igniter\\application\\third_party\\sodium_compat');
        }
        require_once "autoload.php";

        if (empty($key)) {
            $CI = & get_instance();
            $key = mb_substr("00000000000000000000000000000000".$CI->config->config['encryption_key'], -32);
        }

        $nonce = random_bytes(24); // NONCE = Number to be used ONCE, for each message
        $encrypted = ParagonIE_Sodium_Compat::crypto_aead_xchacha20poly1305_ietf_encrypt(
            $message,
            $nonce,
            $nonce,
            $key
        );
        return bin2hex($nonce . $encrypted);
    }
}


/* End of file security_helper.php */
/* Location: ./system/application/helpers/security_helper.php */