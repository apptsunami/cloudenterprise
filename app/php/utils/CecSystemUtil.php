<?PHP
/* Copyright (C) 2008 App Tsunami, Inc. */
/* 
 *  This program is free software: you can redistribute it and/or modify 
 *  it under the terms of the GNU General Public License as published by 
 *  the Free Software Foundation, either version 3 of the License, or 
 *  (at your option) any later version. 
 * 
 *  This program is distributed in the hope that it will be useful, 
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of 
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 *  GNU General Public License for more details. 
 * 
 *  You should have received a copy of the GNU General Public License 
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>. 
 */
/* CecSystemUtil.php */
$rDir = '';

class CecSystemUtil {

  const OPENSSL_BIN = '/usr/bin/openssl';
  const PHP_BIN = '/usr/bin/php';

  /* fileName */
  const CMD_CAT_SPRINTF = 'LC_ALL=et_EE.ISO-8859-1 cat %s 2>/dev/null';
  /* procId */
  const CMD_CHECK_PROC_SPRINTF = 'if [ -d /proc/%d ];then echo 0;else echo -1;fi';
  /* localFile hostName remoteDir */
  const CMD_COPY_FILE_TO_REMOTE_SPRINTF = 'scp -p %s %s:%s 2>/dev/null';
  /* password outputFile inputFile*/
  /* note: -o must appear before -d */
  const CMD_GPG_DECRYPT_SPRINTF = 'echo "%s"|/usr/bin/gpg --password-fd 0 -o %s -d %s';
  /* fileName */
  const CMD_GUNZIP_CAT_SPRINTF = 'LC_ALL=et_EE.ISO-8859-1 gunzip -c %s |cat 2>/dev/null';
  /* fileName */
  const CMD_GUNZIP_TAIL_SPRINTF = 'LC_ALL=et_EE.ISO-8859-1 gunzip -c %s | tail -n %d 2>/dev/null';
  /* dir */
  const CMD_GUNZIP_DIR_SPRINTF = 'gunzip -N -r %s';
  /* dir outputFile */
  const CMD_GUNZIP_DIR_TO_FILE_SPRINTF = 'gunzip -c -r %s >%s 2>/dev/null';
  /* inputFile */
  const CMD_GUNZIP_FILE_SPRINTF = 'gunzip -N %s 2>/dev/null';
  /* inputFile outputFile */
  const CMD_GUNZIP_FILE_TO_FILE_SPRINTF = 'gunzip -c %s > %s 2>/dev/null';
  /* gzFile */
  const CMD_GUNZIP_LIST_SPRINT = 'gunzip -l %s';
  /* dir */
  const CMD_GZIP_DIR_SPRINTF = 'gzip -r %s';
  /* dir output */
  const CMD_GZIP_DIR_TO_FILE_SPRINTF = 'gzip -c -r %s > %s';
  /* inputFile */
  const CMD_GZIP_FILE_SPRINTF = 'gzip %s';
  /* inputFile outputFile */
  const CMD_GZIP_FILE_TO_FILE_SPRINTF = 'gzip -c %s > %s';
  /* lineCount fileName */
  const CMD_TAIL_SPRINTF = 'LC_ALL=et_EE.ISO-8859-1 tail -n %d %s 2>/dev/null';
  /* command */
  const CMD_SH_C_SPRINTF = 'sh -c "%s"';
  /* identityFile userName hostName command */
  const CMD_SSH_HOST_COMMAND_SPRINTF = 'ssh -i %s %s@%s "%s"';
  /* zipfile sourcefiles */
  const CMD_ZIP_FILE_TO_FILE_SPRINTF = 'zip %s %s';
  /* password zipfile sourcefiles */
  const CMD_ZIP_FILE_PASSWORD_TO_FILE_SPRINTF = 'zip -P %s %s %s';
  /* zipfile sourcefiles */
  const CMD_ZIP_DIR_TO_FILE_SPRINTF = 'zip -r %s %s';
  /* password zipfile sourcefiles */
  const CMD_ZIP_DIR_PASSWORD_TO_FILE_SPRINTF = 'zip -r -P %s %s %s';
  /* gzFile */
  const CMD_UNZIP_LIST_SPRINT = 'unzip -l %s';
  /* zipfile */
  const CMD_UNZIP_FILE_SPRINTF = 'unzip %s';
  /* zipfile outputFile */
  const CMD_UNZIP_FILE_TO_FILE_SPRINTF = 'unzip -p %s > %s';
  /* outputDir zipfile */
  const CMD_UNZIP_FILE_TO_DIR_SPRINTF = 'unzip -d %s %s ';
  /* password zipfile */
  const CMD_UNZIP_FILE_PASSWORD_SPRINTF = 'unzip -P %s %s';
  /* password zipfile outputFile */
  const CMD_UNZIP_FILE_PASSWORD_TO_FILE_SPRINTF = 'unzip -P %s %s > %s';
  /* password outputDir zipfile */
  const CMD_UNZIP_FILE_PASSWORD_TO_DIR_SPRINTF = 'unzip -P %s -d %s %s';

  const LOCALHOST = 'localhost';

  static public function isLocalHost($hostName) {
    if (empty($hostName)) return(true);
    if ($hostName == self::LOCALHOST) return(true);
    return(false);
  } // isLocalHost


} // CecSystemUtil

?>
