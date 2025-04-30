<?php

/** Adminer - Compact database management
 * @link https://www.adminer.org/
 * @author Jakub Vrana, https://www.vrana.cz/
 * @copyright 2007 Jakub Vrana
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
 * @version 5.2.1
 */

namespace
Adminer;

const
  VERSION = "5.2.1";
error_reporting(24575);
set_error_handler(function ($Cc, $Ec) {
  return !!preg_match('~^Undefined (array key|offset|index)~', $Ec);
}, E_WARNING | E_NOTICE);
$Zc = !preg_match('~^(unsafe_raw)?$~', ini_get("filter.default"));
if ($Zc || ini_get("filter.default_flags")) {
  foreach (array('_GET', '_POST', '_COOKIE', '_SERVER') as $X) {
    $cj = filter_input_array(constant("INPUT$X"), FILTER_UNSAFE_RAW);
    if ($cj) $$X = $cj;
  }
}
if (function_exists("mb_internal_encoding")) mb_internal_encoding("8bit");
function
connection($h = null)
{
  return ($h ?: Db::$he);
}
function
adminer()
{
  return
    Adminer::$he;
}
function
driver()
{
  return
    Driver::$he;
}
function
connect()
{
  $Fb = adminer()->credentials();
  $J = Driver::connect($Fb[0], $Fb[1], $Fb[2]);
  return (is_object($J) ? $J : null);
}
function
idf_unescape($v)
{
  if (!preg_match('~^[`\'"[]~', $v)) return $v;
  $Ae = substr($v, -1);
  return
    str_replace($Ae . $Ae, $Ae, substr($v, 1, -1));
}
function
q($Q)
{
  return
    connection()->quote($Q);
}
function
escape_string($X)
{
  return
    substr(q($X), 1, -1);
}
function
idx($xa, $y, $l = null)
{
  return ($xa && array_key_exists($y, $xa) ? $xa[$y] : $l);
}
function
number($X)
{
  return
    preg_replace('~[^0-9]+~', '', $X);
}
function
number_type()
{
  return '((?<!o)int(?!er)|numeric|real|float|double|decimal|money)';
}
function
remove_slashes(array $Kg, $Zc = false)
{
  if (function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()) {
    while (list($y, $X) = each($Kg)) {
      foreach (
        $X
        as $se => $W
      ) {
        unset($Kg[$y][$se]);
        if (is_array($W)) {
          $Kg[$y][stripslashes($se)] = $W;
          $Kg[] = &$Kg[$y][stripslashes($se)];
        } else $Kg[$y][stripslashes($se)] = ($Zc ? $W : stripslashes($W));
      }
    }
  }
}
function
bracket_escape($v, $Ea = false)
{
  static $Li = array(':' => ':1', ']' => ':2', '[' => ':3', '"' => ':4');
  return
    strtr($v, ($Ea ? array_flip($Li) : $Li));
}
function
min_version($tj, $Oe = "", $h = null)
{
  $h = connection($h);
  $Eh = $h->server_info;
  if ($Oe && preg_match('~([\d.]+)-MariaDB~', $Eh, $B)) {
    $Eh = $B[1];
    $tj = $Oe;
  }
  return $tj && version_compare($Eh, $tj) >= 0;
}
function
charset(Db $g)
{
  return (min_version("5.5.3", 0, $g) ? "utf8mb4" : "utf8");
}
function
ini_bool($ce)
{
  $X = ini_get($ce);
  return (preg_match('~^(on|true|yes)$~i', $X) || (int)$X);
}
function
sid()
{
  static $J;
  if ($J === null) $J = (SID && !($_COOKIE && ini_bool("session.use_cookies")));
  return $J;
}
function
set_password($sj, $N, $V, $F)
{
  $_SESSION["pwds"][$sj][$N][$V] = ($_COOKIE["adminer_key"] && is_string($F) ? array(encrypt_string($F, $_COOKIE["adminer_key"])) : $F);
}
function
get_password()
{
  $J = get_session("pwds");
  if (is_array($J)) $J = ($_COOKIE["adminer_key"] ? decrypt_string($J[0], $_COOKIE["adminer_key"]) : false);
  return $J;
}
function
get_val($H, $n = 0, $tb = null)
{
  $tb = connection($tb);
  $I = $tb->query($H);
  if (!is_object($I)) return
    false;
  $K = $I->fetch_row();
  return ($K ? $K[$n] : false);
}
function
get_vals($H, $d = 0)
{
  $J = array();
  $I = connection()->query($H);
  if (is_object($I)) {
    while ($K = $I->fetch_row()) $J[] = $K[$d];
  }
  return $J;
}
function
get_key_vals($H, $h = null, $Hh = true)
{
  $h = connection($h);
  $J = array();
  $I = $h->query($H);
  if (is_object($I)) {
    while ($K = $I->fetch_row()) {
      if ($Hh) $J[$K[0]] = $K[1];
      else $J[] = $K[0];
    }
  }
  return $J;
}
function
get_rows($H, $h = null, $m = "<p class='error'>")
{
  $tb = connection($h);
  $J = array();
  $I = $tb->query($H);
  if (is_object($I)) {
    while ($K = $I->fetch_assoc()) $J[] = $K;
  } elseif (!$I && !$h && $m && (defined('Adminer\PAGE_HEADER') || $m == "-- ")) echo $m . error() . "\n";
  return $J;
}
function
unique_array($K, array $x)
{
  foreach (
    $x
    as $w
  ) {
    if (preg_match("~PRIMARY|UNIQUE~", $w["type"])) {
      $J = array();
      foreach ($w["columns"] as $y) {
        if (!isset($K[$y])) continue
          2;
        $J[$y] = $K[$y];
      }
      return $J;
    }
  }
}
function
escape_key($y)
{
  if (preg_match('(^([\w(]+)(' . str_replace("_", ".*", preg_quote(idf_escape("_"))) . ')([ \w)]+)$)', $y, $B)) return $B[1] . idf_escape(idf_unescape($B[2])) . $B[3];
  return
    idf_escape($y);
}
function
where(array $Z, array $o = array())
{
  $J = array();
  foreach ((array)$Z["where"] as $y => $X) {
    $y = bracket_escape($y, true);
    $d = escape_key($y);
    $n = idx($o, $y, array());
    $Xc = $n["type"];
    $J[] = $d . (JUSH == "sql" && $Xc == "json" ? " = CAST(" . q($X) . " AS JSON)" : (JUSH == "sql" && is_numeric($X) && preg_match('~\.~', $X) ? " LIKE " . q($X) : (JUSH == "mssql" && strpos($Xc, "datetime") === false ? " LIKE " . q(preg_replace('~[_%[]~', '[\0]', $X)) : " = " . unconvert_field($n, q($X)))));
    if (JUSH == "sql" && preg_match('~char|text~', $Xc) && preg_match("~[^ -@]~", $X)) $J[] = "$d = " . q($X) . " COLLATE " . charset(connection()) . "_bin";
  }
  foreach ((array)$Z["null"] as $y) $J[] = escape_key($y) . " IS NULL";
  return
    implode(" AND ", $J);
}
function
where_check($X, array $o = array())
{
  parse_str($X, $Xa);
  remove_slashes(array(&$Xa));
  return
    where($Xa, $o);
}
function
where_link($t, $d, $Y, $Lf = "=")
{
  return "&where%5B$t%5D%5Bcol%5D=" . urlencode($d) . "&where%5B$t%5D%5Bop%5D=" . urlencode(($Y !== null ? $Lf : "IS NULL")) . "&where%5B$t%5D%5Bval%5D=" . urlencode($Y);
}
function
convert_fields(array $e, array $o, array $M = array())
{
  $J = "";
  foreach (
    $e
    as $y => $X
  ) {
    if ($M && !in_array(idf_escape($y), $M)) continue;
    $ya = convert_field($o[$y]);
    if ($ya) $J
      .= ", $ya AS " . idf_escape($y);
  }
  return $J;
}
function
cookie($C, $Y, $He = 2592000)
{
  header("Set-Cookie: $C=" . urlencode($Y) . ($He ? "; expires=" . gmdate("D, d M Y H:i:s", time() + $He) . " GMT" : "") . "; path=" . preg_replace('~\?.*~', '', $_SERVER["REQUEST_URI"]) . (HTTPS ? "; secure" : "") . "; HttpOnly; SameSite=lax", false);
}
function
get_settings($Bb)
{
  parse_str($_COOKIE[$Bb], $Ih);
  return $Ih;
}
function
get_setting($y, $Bb = "adminer_settings")
{
  $Ih = get_settings($Bb);
  return $Ih[$y];
}
function
save_settings(array $Ih, $Bb = "adminer_settings")
{
  $Y = http_build_query($Ih + get_settings($Bb));
  cookie($Bb, $Y);
  $_COOKIE[$Bb] = $Y;
}
function
restart_session()
{
  if (!ini_bool("session.use_cookies") && (!function_exists('session_status') || session_status() == 1)) session_start();
}
function
stop_session($hd = false)
{
  $kj = ini_bool("session.use_cookies");
  if (!$kj || $hd) {
    session_write_close();
    if ($kj && @ini_set("session.use_cookies", '0') === false) session_start();
  }
}
function &get_session($y)
{
  return $_SESSION[$y][DRIVER][SERVER][$_GET["username"]];
}
function
set_session($y, $X)
{
  $_SESSION[$y][DRIVER][SERVER][$_GET["username"]] = $X;
}
function
auth_url($sj, $N, $V, $k = null)
{
  $gj = remove_from_uri(implode("|", array_keys(SqlDriver::$gc)) . "|username|ext|" . ($k !== null ? "db|" : "") . ($sj == 'mssql' || $sj == 'pgsql' ? "" : "ns|") . session_name());
  preg_match('~([^?]*)\??(.*)~', $gj, $B);
  return "$B[1]?" . (sid() ? SID . "&" : "") . ($sj != "server" || $N != "" ? urlencode($sj) . "=" . urlencode($N) . "&" : "") . ($_GET["ext"] ? "ext=" . urlencode($_GET["ext"]) . "&" : "") . "username=" . urlencode($V) . ($k != "" ? "&db=" . urlencode($k) : "") . ($B[2] ? "&$B[2]" : "");
}
function
is_ajax()
{
  return ($_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest");
}
function
redirect($Ke, $bf = null)
{
  if ($bf !== null) {
    restart_session();
    $_SESSION["messages"][preg_replace('~^[^?]*~', '', ($Ke !== null ? $Ke : $_SERVER["REQUEST_URI"]))][] = $bf;
  }
  if ($Ke !== null) {
    if ($Ke == "") $Ke = ".";
    header("Location: $Ke");
    exit;
  }
}
function
query_redirect($H, $Ke, $bf, $Tg = true, $Jc = true, $Sc = false, $zi = "")
{
  if ($Jc) {
    $Xh = microtime(true);
    $Sc = !connection()->query($H);
    $zi = format_time($Xh);
  }
  $Rh = ($H ? adminer()->messageQuery($H, $zi, $Sc) : "");
  if ($Sc) {
    adminer()->error
      .= error() . $Rh . script("messagesPrint();") . "<br>";
    return
      false;
  }
  if ($Tg) redirect($Ke, $bf . $Rh);
  return
    true;
}
class
Queries
{
  static $Og = array();
  static $Xh = 0;
}
function
queries($H)
{
  if (!Queries::$Xh) Queries::$Xh = microtime(true);
  Queries::$Og[] = (preg_match('~;$~', $H) ? "DELIMITER ;;\n$H;\nDELIMITER " : $H) . ";";
  return
    connection()->query($H);
}
function
apply_queries($H, array $T, $Fc = 'Adminer\table')
{
  foreach (
    $T
    as $R
  ) {
    if (!queries("$H " . $Fc($R))) return
      false;
  }
  return
    true;
}
function
queries_redirect($Ke, $bf, $Tg)
{
  $Og = implode("\n", Queries::$Og);
  $zi = format_time(Queries::$Xh);
  return
    query_redirect($Og, $Ke, $bf, $Tg, false, !$Tg, $zi);
}
function
format_time($Xh)
{
  return
    lang(0, max(0, microtime(true) - $Xh));
}
function
relative_uri()
{
  return
    str_replace(":", "%3a", preg_replace('~^[^?]*/([^?]*)~', '\1', $_SERVER["REQUEST_URI"]));
}
function
remove_from_uri($ig = "")
{
  return
    substr(preg_replace("~(?<=[?&])($ig" . (SID ? "" : "|" . session_name()) . ")=[^&]*&~", '', relative_uri() . "&"), 0, -1);
}
function
get_file($y, $Rb = false, $Wb = "")
{
  $Yc = $_FILES[$y];
  if (!$Yc) return
    null;
  foreach (
    $Yc
    as $y => $X
  ) $Yc[$y] = (array)$X;
  $J = '';
  foreach ($Yc["error"] as $y => $m) {
    if ($m) return $m;
    $C = $Yc["name"][$y];
    $Gi = $Yc["tmp_name"][$y];
    $yb = file_get_contents($Rb && preg_match('~\.gz$~', $C) ? "compress.zlib://$Gi" : $Gi);
    if ($Rb) {
      $Xh = substr($yb, 0, 3);
      if (function_exists("iconv") && preg_match("~^\xFE\xFF|^\xFF\xFE~", $Xh)) $yb = iconv("utf-16", "utf-8", $yb);
      elseif ($Xh == "\xEF\xBB\xBF") $yb = substr($yb, 3);
    }
    $J
      .= $yb;
    if ($Wb) $J
      .= (preg_match("($Wb\\s*\$)", $yb) ? "" : $Wb) . "\n\n";
  }
  return $J;
}
function
upload_error($m)
{
  $We = ($m == UPLOAD_ERR_INI_SIZE ? ini_get("upload_max_filesize") : 0);
  return ($m ? lang(1) . ($We ? " " . lang(2, $We) : "") : lang(3));
}
function
repeat_pattern($sg, $z)
{
  return
    str_repeat("$sg{0,65535}", $z / 65535) . "$sg{0," . ($z % 65535) . "}";
}
function
is_utf8($X)
{
  return (preg_match('~~u', $X) && !preg_match('~[\0-\x8\xB\xC\xE-\x1F]~', $X));
}
function
format_number($X)
{
  return
    strtr(number_format($X, 0, ".", lang(4)), preg_split('~~u', lang(5), -1, PREG_SPLIT_NO_EMPTY));
}
function
friendly_url($X)
{
  return
    preg_replace('~\W~i', '-', $X);
}
function
table_status1($R, $Tc = false)
{
  $J = table_status($R, $Tc);
  return ($J ? reset($J) : array("Name" => $R));
}
function
column_foreign_keys($R)
{
  $J = array();
  foreach (adminer()->foreignKeys($R) as $q) {
    foreach ($q["source"] as $X) $J[$X][] = $q;
  }
  return $J;
}
function
fields_from_edit()
{
  $J = array();
  foreach ((array)$_POST["field_keys"] as $y => $X) {
    if ($X != "") {
      $X = bracket_escape($X);
      $_POST["function"][$X] = $_POST["field_funs"][$y];
      $_POST["fields"][$X] = $_POST["field_vals"][$y];
    }
  }
  foreach ((array)$_POST["fields"] as $y => $X) {
    $C = bracket_escape($y, true);
    $J[$C] = array("field" => $C, "privileges" => array("insert" => 1, "update" => 1, "where" => 1, "order" => 1), "null" => 1, "auto_increment" => ($y == driver()->primary),);
  }
  return $J;
}
function
dump_headers($Pd, $lf = false)
{
  $J = adminer()->dumpHeaders($Pd, $lf);
  $eg = $_POST["output"];
  if ($eg != "text") header("Content-Disposition: attachment; filename=" . adminer()->dumpFilename($Pd) . ".$J" . ($eg != "file" && preg_match('~^[0-9a-z]+$~', $eg) ? ".$eg" : ""));
  session_write_close();
  if (!ob_get_level()) ob_start(null, 4096);
  ob_flush();
  flush();
  return $J;
}
function
dump_csv(array $K)
{
  foreach (
    $K
    as $y => $X
  ) {
    if (preg_match('~["\n,;\t]|^0|\.\d*0$~', $X) || $X === "") $K[$y] = '"' . str_replace('"', '""', $X) . '"';
  }
  echo
  implode(($_POST["format"] == "csv" ? "," : ($_POST["format"] == "tsv" ? "\t" : ";")), $K) . "\r\n";
}
function
apply_sql_function($s, $d)
{
  return ($s ? ($s == "unixepoch" ? "DATETIME($d, '$s')" : ($s == "count distinct" ? "COUNT(DISTINCT " : strtoupper("$s(")) . "$d)") : $d);
}
function
get_temp_dir()
{
  $J = ini_get("upload_tmp_dir");
  if (!$J) {
    if (function_exists('sys_get_temp_dir')) $J = sys_get_temp_dir();
    else {
      $p = @tempnam("", "");
      if (!$p) return '';
      $J = dirname($p);
      unlink($p);
    }
  }
  return $J;
}
function
file_open_lock($p)
{
  if (is_link($p)) return;
  $r = @fopen($p, "c+");
  if (!$r) return;
  chmod($p, 0660);
  if (!flock($r, LOCK_EX)) {
    fclose($r);
    return;
  }
  return $r;
}
function
file_write_unlock($r, $Lb)
{
  rewind($r);
  fwrite($r, $Lb);
  ftruncate($r, strlen($Lb));
  file_unlock($r);
}
function
file_unlock($r)
{
  flock($r, LOCK_UN);
  fclose($r);
}
function
first(array $xa)
{
  return
    reset($xa);
}
function
password_file($i)
{
  $p = get_temp_dir() . "/adminer.key";
  if (!$i && !file_exists($p)) return '';
  $r = file_open_lock($p);
  if (!$r) return '';
  $J = stream_get_contents($r);
  if (!$J) {
    $J = rand_string();
    file_write_unlock($r, $J);
  } else
    file_unlock($r);
  return $J;
}
function
rand_string()
{
  return
    md5(uniqid(strval(mt_rand()), true));
}
function
select_value($X, $A, array $n, $yi)
{
  if (is_array($X)) {
    $J = "";
    foreach (
      $X
      as $se => $W
    ) $J
      .= "<tr>" . ($X != array_values($X) ? "<th>" . h($se) : "") . "<td>" . select_value($W, $A, $n, $yi);
    return "<table>$J</table>";
  }
  if (!$A) $A = adminer()->selectLink($X, $n);
  if ($A === null) {
    if (is_mail($X)) $A = "mailto:$X";
    if (is_url($X)) $A = $X;
  }
  $J = adminer()->editVal($X, $n);
  if ($J !== null) {
    if (!is_utf8($J)) $J = "\0";
    elseif ($yi != "" && is_shortable($n)) $J = shorten_utf8($J, max(0, +$yi));
    else $J = h($J);
  }
  return
    adminer()->selectVal($J, $A, $n, $X);
}
function
is_mail($tc)
{
  $za = '[-a-z0-9!#$%&\'*+/=?^_`{|}~]';
  $fc = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';
  $sg = "$za+(\\.$za+)*@($fc?\\.)+$fc";
  return
    is_string($tc) && preg_match("(^$sg(,\\s*$sg)*\$)i", $tc);
}
function
is_url($Q)
{
  $fc = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';
  return
    preg_match("~^(https?)://($fc?\\.)+$fc(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i", $Q);
}
function
is_shortable(array $n)
{
  return
    preg_match('~char|text|json|lob|geometry|point|linestring|polygon|string|bytea~', $n["type"]);
}
function
count_rows($R, array $Z, $me, array $vd)
{
  $H = " FROM " . table($R) . ($Z ? " WHERE " . implode(" AND ", $Z) : "");
  return ($me && (JUSH == "sql" || count($vd) == 1) ? "SELECT COUNT(DISTINCT " . implode(", ", $vd) . ")$H" : "SELECT COUNT(*)" . ($me ? " FROM (SELECT 1$H GROUP BY " . implode(", ", $vd) . ") x" : $H));
}
function
slow_query($H)
{
  $k = adminer()->database();
  $_i = adminer()->queryTimeout();
  $Mh = driver()->slowQuery($H, $_i);
  $h = null;
  if (!$Mh && support("kill")) {
    $h = connect();
    if ($h && ($k == "" || $h->select_db($k))) {
      $ve = get_val(connection_id(), 0, $h);
      echo
      script("const timeout = setTimeout(() => { ajax('" . js_escape(ME) . "script=kill', function () {}, 'kill=$ve&token=" . get_token() . "'); }, 1000 * $_i);");
    }
  }
  ob_flush();
  flush();
  $J = @get_key_vals(($Mh ?: $H), $h, false);
  if ($h) {
    echo
    script("clearTimeout(timeout);");
    ob_flush();
    flush();
  }
  return $J;
}
function
get_token()
{
  $Rg = rand(1, 1e6);
  return ($Rg ^ $_SESSION["token"]) . ":$Rg";
}
function
verify_token()
{
  list($Hi, $Rg) = explode(":", $_POST["token"]);
  return ($Rg ^ $_SESSION["token"]) == $Hi;
}
function
lzw_decompress($Ka)
{
  $bc = 256;
  $La = 8;
  $gb = array();
  $eh = 0;
  $fh = 0;
  for ($t = 0; $t < strlen($Ka); $t++) {
    $eh = ($eh << 8) + ord($Ka[$t]);
    $fh += 8;
    if ($fh >= $La) {
      $fh -= $La;
      $gb[] = $eh >> $fh;
      $eh &= (1 << $fh) - 1;
      $bc++;
      if ($bc >> $La) $La++;
    }
  }
  $ac = range("\0", "\xFF");
  $J = "";
  $Bj = "";
  foreach (
    $gb
    as $t => $fb
  ) {
    $sc = $ac[$fb];
    if (!isset($sc)) $sc = $Bj . $Bj[0];
    $J
      .= $sc;
    if ($t) $ac[] = $Bj . $sc[0];
    $Bj = $sc;
  }
  return $J;
}
function
script($Oh, $Ki = "\n")
{
  return "<script" . nonce() . ">$Oh</script>$Ki";
}
function
script_src($hj, $Tb = false)
{
  return "<script src='" . h($hj) . "'" . nonce() . ($Tb ? " defer" : "") . "></script>\n";
}
function
nonce()
{
  return ' nonce="' . get_nonce() . '"';
}
function
input_hidden($C, $Y = "")
{
  return "<input type='hidden' name='" . h($C) . "' value='" . h($Y) . "'>\n";
}
function
input_token()
{
  return
    input_hidden("token", get_token());
}
function
target_blank()
{
  return ' target="_blank" rel="noreferrer noopener"';
}
function
h($Q)
{
  return
    str_replace("\0", "&#0;", htmlspecialchars($Q, ENT_QUOTES, 'utf-8'));
}
function
nl_br($Q)
{
  return
    str_replace("\n", "<br>", $Q);
}
function
checkbox($C, $Y, $ab, $xe = "", $Kf = "", $eb = "", $ze = "")
{
  $J = "<input type='checkbox' name='$C' value='" . h($Y) . "'" . ($ab ? " checked" : "") . ($ze ? " aria-labelledby='$ze'" : "") . ">" . ($Kf ? script("qsl('input').onclick = function () { $Kf };", "") : "");
  return ($xe != "" || $eb ? "<label" . ($eb ? " class='$eb'" : "") . ">$J" . h($xe) . "</label>" : $J);
}
function
optionlist($Pf, $xh = null, $lj = false)
{
  $J = "";
  foreach (
    $Pf
    as $se => $W
  ) {
    $Qf = array($se => $W);
    if (is_array($W)) {
      $J
        .= '<optgroup label="' . h($se) . '">';
      $Qf = $W;
    }
    foreach (
      $Qf
      as $y => $X
    ) $J
      .= '<option' . ($lj || is_string($y) ? ' value="' . h($y) . '"' : '') . ($xh !== null && ($lj || is_string($y) ? (string)$y : $X) === $xh ? ' selected' : '') . '>' . h($X);
    if (is_array($W)) $J
      .= '</optgroup>';
  }
  return $J;
}
function
html_select($C, array $Pf, $Y = "", $Jf = "", $ze = "")
{
  static $xe = 0;
  $ye = "";
  if (!$ze && substr($Pf[""], 0, 1) == "(") {
    $xe++;
    $ze = "label-$xe";
    $ye = "<option value='' id='$ze'>" . h($Pf[""]);
    unset($Pf[""]);
  }
  return "<select name='" . h($C) . "'" . ($ze ? " aria-labelledby='$ze'" : "") . ">" . $ye . optionlist($Pf, $Y) . "</select>" . ($Jf ? script("qsl('select').onchange = function () { $Jf };", "") : "");
}
function
html_radios($C, array $Pf, $Y = "", $Ah = "")
{
  $J = "";
  foreach (
    $Pf
    as $y => $X
  ) $J
    .= "<label><input type='radio' name='" . h($C) . "' value='" . h($y) . "'" . ($y == $Y ? " checked" : "") . ">" . h($X) . "</label>$Ah";
  return $J;
}
function
confirm($bf = "", $yh = "qsl('input')")
{
  return
    script("$yh.onclick = () => confirm('" . ($bf ? js_escape($bf) : lang(6)) . "');", "");
}
function
print_fieldset($u, $Fe, $wj = false)
{
  echo "<fieldset><legend>", "<a href='#fieldset-$u'>$Fe</a>", script("qsl('a').onclick = partial(toggle, 'fieldset-$u');", ""), "</legend>", "<div id='fieldset-$u'" . ($wj ? "" : " class='hidden'") . ">\n";
}
function
bold($Na, $eb = "")
{
  return ($Na ? " class='active $eb'" : ($eb ? " class='$eb'" : ""));
}
function
js_escape($Q)
{
  return
    addcslashes($Q, "\r\n'\\/");
}
function
pagination($E, $Ib)
{
  return " " . ($E == $Ib ? $E + 1 : '<a href="' . h(remove_from_uri("page") . ($E ? "&page=$E" . ($_GET["next"] ? "&next=" . urlencode($_GET["next"]) : "") : "")) . '">' . ($E + 1) . "</a>");
}
function
hidden_fields(array $Kg, array $Sd = array(), $Dg = '')
{
  $J = false;
  foreach (
    $Kg
    as $y => $X
  ) {
    if (!in_array($y, $Sd)) {
      if (is_array($X)) hidden_fields($X, array(), $y);
      else {
        $J = true;
        echo
        input_hidden(($Dg ? $Dg . "[$y]" : $y), $X);
      }
    }
  }
  return $J;
}
function
hidden_fields_get()
{
  echo (sid() ? input_hidden(session_name(), session_id()) : ''), (SERVER !== null ? input_hidden(DRIVER, SERVER) : ""), input_hidden("username", $_GET["username"]);
}
function
enum_input($U, $_a, array $n, $Y, $wc = null)
{
  preg_match_all("~'((?:[^']|'')*)'~", $n["length"], $Re);
  $J = ($wc !== null ? "<label><input type='$U'$_a value='$wc'" . ((is_array($Y) ? in_array($wc, $Y) : $Y === $wc) ? " checked" : "") . "><i>" . lang(7) . "</i></label>" : "");
  foreach ($Re[1] as $t => $X) {
    $X = stripcslashes(str_replace("''", "'", $X));
    $ab = (is_array($Y) ? in_array($X, $Y) : $Y === $X);
    $J
      .= " <label><input type='$U'$_a value='" . h($X) . "'" . ($ab ? ' checked' : '') . '>' . h(adminer()->editVal($X, $n)) . '</label>';
  }
  return $J;
}
function
input(array $n, $Y, $s, $Da = false)
{
  $C = h(bracket_escape($n["field"]));
  echo "<td class='function'>";
  if (is_array($Y) && !$s) {
    $Y = json_encode($Y, 128 | 64 | 256);
    $s = "json";
  }
  $dh = (JUSH == "mssql" && $n["auto_increment"]);
  if ($dh && !$_POST["save"]) $s = null;
  $qd = (isset($_GET["select"]) || $dh ? array("orig" => lang(8)) : array()) + adminer()->editFunctions($n);
  $cc = stripos($n["default"], "GENERATED ALWAYS AS ") === 0 ? " disabled=''" : "";
  $_a = " name='fields[$C]'$cc" . ($Da ? " autofocus" : "");
  $Bc = driver()->enumLength($n);
  if ($Bc) {
    $n["type"] = "enum";
    $n["length"] = $Bc;
  }
  echo
  driver()->unconvertFunction($n) . " ";
  $R = $_GET["edit"] ?: $_GET["select"];
  if ($n["type"] == "enum") echo
  h($qd[""]) . "<td>" . adminer()->editInput($R, $n, $_a, $Y);
  else {
    $Cd = (in_array($s, $qd) || isset($qd[$s]));
    echo (count($qd) > 1 ? "<select name='function[$C]'$cc>" . optionlist($qd, $s === null || $Cd ? $s : "") . "</select>" . on_help("event.target.value.replace(/^SQL\$/, '')", 1) . script("qsl('select').onchange = functionChange;", "") : h(reset($qd))) . '<td>';
    $ee = adminer()->editInput($R, $n, $_a, $Y);
    if ($ee != "") echo $ee;
    elseif (preg_match('~bool~', $n["type"])) echo "<input type='hidden'$_a value='0'>" . "<input type='checkbox'" . (preg_match('~^(1|t|true|y|yes|on)$~i', $Y) ? " checked='checked'" : "") . "$_a value='1'>";
    elseif ($n["type"] == "set") {
      preg_match_all("~'((?:[^']|'')*)'~", $n["length"], $Re);
      foreach ($Re[1] as $t => $X) {
        $X = stripcslashes(str_replace("''", "'", $X));
        $ab = in_array($X, explode(",", $Y), true);
        echo " <label><input type='checkbox' name='fields[$C][$t]' value='" . h($X) . "'" . ($ab ? ' checked' : '') . ">" . h(adminer()->editVal($X, $n)) . '</label>';
      }
    } elseif (preg_match('~blob|bytea|raw|file~', $n["type"]) && ini_bool("file_uploads")) echo "<input type='file' name='fields-$C'>";
    elseif ($s == "json" || preg_match('~^jsonb?$~', $n["type"])) echo "<textarea$_a cols='50' rows='12' class='jush-js'>" . h($Y) . '</textarea>';
    elseif (($wi = preg_match('~text|lob|memo~i', $n["type"])) || preg_match("~\n~", $Y)) {
      if ($wi && JUSH != "sqlite") $_a
        .= " cols='50' rows='12'";
      else {
        $L = min(12, substr_count($Y, "\n") + 1);
        $_a
          .= " cols='30' rows='$L'";
      }
      echo "<textarea$_a>" . h($Y) . '</textarea>';
    } else {
      $Wi = driver()->types();
      $Ye = (!preg_match('~int~', $n["type"]) && preg_match('~^(\d+)(,(\d+))?$~', $n["length"], $B) ? ((preg_match("~binary~", $n["type"]) ? 2 : 1) * $B[1] + ($B[3] ? 1 : 0) + ($B[2] && !$n["unsigned"] ? 1 : 0)) : ($Wi[$n["type"]] ? $Wi[$n["type"]] + ($n["unsigned"] ? 0 : 1) : 0));
      if (JUSH == 'sql' && min_version(5.6) && preg_match('~time~', $n["type"])) $Ye += 7;
      echo "<input" . ((!$Cd || $s === "") && preg_match('~(?<!o)int(?!er)~', $n["type"]) && !preg_match('~\[\]~', $n["full_type"]) ? " type='number'" : "") . " value='" . h($Y) . "'" . ($Ye ? " data-maxlength='$Ye'" : "") . (preg_match('~char|binary~', $n["type"]) && $Ye > 20 ? " size='" . ($Ye > 99 ? 60 : 40) . "'" : "") . "$_a>";
    }
    echo
    adminer()->editHint($R, $n, $Y);
    $ad = 0;
    foreach (
      $qd
      as $y => $X
    ) {
      if ($y === "" || !$X) break;
      $ad++;
    }
    if ($ad && count($qd) > 1) echo
    script("qsl('td').oninput = partial(skipOriginal, $ad);");
  }
}
function
process_input(array $n)
{
  if (stripos($n["default"], "GENERATED ALWAYS AS ") === 0) return;
  $v = bracket_escape($n["field"]);
  $s = idx($_POST["function"], $v);
  $Y = $_POST["fields"][$v];
  if ($n["type"] == "enum" || driver()->enumLength($n)) {
    if ($Y == -1) return
      false;
    if ($Y == "") return "NULL";
  }
  if ($n["auto_increment"] && $Y == "") return
    null;
  if ($s == "orig") return (preg_match('~^CURRENT_TIMESTAMP~i', $n["on_update"]) ? idf_escape($n["field"]) : false);
  if ($s == "NULL") return "NULL";
  if ($n["type"] == "set") $Y = implode(",", (array)$Y);
  if ($s == "json") {
    $s = "";
    $Y = json_decode($Y, true);
    if (!is_array($Y)) return
      false;
    return $Y;
  }
  if (preg_match('~blob|bytea|raw|file~', $n["type"]) && ini_bool("file_uploads")) {
    $Yc = get_file("fields-$v");
    if (!is_string($Yc)) return
      false;
    return
      driver()->quoteBinary($Yc);
  }
  return
    adminer()->processInput($n, $Y, $s);
}
function
search_tables()
{
  $_GET["where"][0]["val"] = $_POST["query"];
  $_h = "<ul>\n";
  foreach (table_status('', true) as $R => $S) {
    $C = adminer()->tableName($S);
    if (isset($S["Engine"]) && $C != "" && (!$_POST["tables"] || in_array($R, $_POST["tables"]))) {
      $I = connection()->query("SELECT" . limit("1 FROM " . table($R), " WHERE " . implode(" AND ", adminer()->selectSearchProcess(fields($R), array())), 1));
      if (!$I || $I->fetch_row()) {
        $Gg = "<a href='" . h(ME . "select=" . urlencode($R) . "&where[0][op]=" . urlencode($_GET["where"][0]["op"]) . "&where[0][val]=" . urlencode($_GET["where"][0]["val"])) . "'>$C</a>";
        echo "$_h<li>" . ($I ? $Gg : "<p class='error'>$Gg: " . error()) . "\n";
        $_h = "";
      }
    }
  }
  echo ($_h ? "<p class='message'>" . lang(9) : "</ul>") . "\n";
}
function
on_help($mb, $Kh = 0)
{
  return
    script("mixin(qsl('select, input'), {onmouseover: function (event) { helpMouseover.call(this, event, $mb, $Kh) }, onmouseout: helpMouseout});", "");
}
function
edit_form($R, array $o, $K, $fj, $m = '')
{
  $ji = adminer()->tableName(table_status1($R, true));
  page_header(($fj ? lang(10) : lang(11)), $m, array("select" => array($R, $ji)), $ji);
  adminer()->editRowPrint($R, $o, $K, $fj);
  if ($K === false) {
    echo "<p class='error'>" . lang(12) . "\n";
    return;
  }
  echo "<form action='' method='post' enctype='multipart/form-data' id='form'>\n";
  if (!$o) echo "<p class='error'>" . lang(13) . "\n";
  else {
    echo "<table class='layout'>" . script("qsl('table').onkeydown = editingKeydown;");
    $Da = !$_POST;
    foreach (
      $o
      as $C => $n
    ) {
      echo "<tr><th>" . adminer()->fieldName($n);
      $l = idx($_GET["set"], bracket_escape($C));
      if ($l === null) {
        $l = $n["default"];
        if ($n["type"] == "bit" && preg_match("~^b'([01]*)'\$~", $l, $ah)) $l = $ah[1];
        if (JUSH == "sql" && preg_match('~binary~', $n["type"])) $l = bin2hex($l);
      }
      $Y = ($K !== null ? ($K[$C] != "" && JUSH == "sql" && preg_match("~enum|set~", $n["type"]) && is_array($K[$C]) ? implode(",", $K[$C]) : (is_bool($K[$C]) ? +$K[$C] : $K[$C])) : (!$fj && $n["auto_increment"] ? "" : (isset($_GET["select"]) ? false : $l)));
      if (!$_POST["save"] && is_string($Y)) $Y = adminer()->editVal($Y, $n);
      $s = ($_POST["save"] ? idx($_POST["function"], $C, "") : ($fj && preg_match('~^CURRENT_TIMESTAMP~i', $n["on_update"]) ? "now" : ($Y === false ? null : ($Y !== null ? '' : 'NULL'))));
      if (!$_POST && !$fj && $Y == $n["default"] && preg_match('~^[\w.]+\(~', $Y)) $s = "SQL";
      if (preg_match("~time~", $n["type"]) && preg_match('~^CURRENT_TIMESTAMP~i', $Y)) {
        $Y = "";
        $s = "now";
      }
      if ($n["type"] == "uuid" && $Y == "uuid()") {
        $Y = "";
        $s = "uuid";
      }
      if ($Da !== false) $Da = ($n["auto_increment"] || $s == "now" || $s == "uuid" ? null : true);
      input($n, $Y, $s, $Da);
      if ($Da) $Da = false;
      echo "\n";
    }
    if (!support("table") && !fields($R)) echo "<tr>" . "<th><input name='field_keys[]'>" . script("qsl('input').oninput = fieldChange;") . "<td class='function'>" . html_select("field_funs[]", adminer()->editFunctions(array("null" => isset($_GET["select"])))) . "<td><input name='field_vals[]'>" . "\n";
    echo "</table>\n";
  }
  echo "<p>\n";
  if ($o) {
    echo "<input type='submit' value='" . lang(14) . "'>\n";
    if (!isset($_GET["select"])) echo "<input type='submit' name='insert' value='" . ($fj ? lang(15) : lang(16)) . "' title='Ctrl+Shift+Enter'>\n", ($fj ? script("qsl('input').onclick = function () { return !ajaxForm(this.form, '" . lang(17) . "â¦', this); };") : "");
  }
  echo ($fj ? "<input type='submit' name='delete' value='" . lang(18) . "'>" . confirm() . "\n" : "");
  if (isset($_GET["select"])) hidden_fields(array("check" => (array)$_POST["check"], "clone" => $_POST["clone"], "all" => $_POST["all"]));
  echo
  input_hidden("referer", (isset($_POST["referer"]) ? $_POST["referer"] : $_SERVER["HTTP_REFERER"])), input_hidden("save", 1), input_token(), "</form>\n";
}
function
shorten_utf8($Q, $z = 80, $di = "")
{
  if (!preg_match("(^(" . repeat_pattern("[\t\r\n -\x{10FFFF}]", $z) . ")($)?)u", $Q, $B)) preg_match("(^(" . repeat_pattern("[\t\r\n -~]", $z) . ")($)?)", $Q, $B);
  return
    h($B[1]) . $di . (isset($B[2]) ? "" : "<i>â¦</i>");
}
function
icon($Od, $C, $Nd, $Bi)
{
  return "<button type='submit' name='$C' title='" . h($Bi) . "' class='icon icon-$Od'><span>$Nd</span></button>";
}
if (isset($_GET["file"])) {
  if (substr(VERSION, -4) != '-dev') {
    if ($_SERVER["HTTP_IF_MODIFIED_SINCE"]) {
      header("HTTP/1.1 304 Not Modified");
      exit;
    }
    header("Expires: " . gmdate("D, d M Y H:i:s", time() + 365 * 24 * 60 * 60) . " GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: immutable");
  }
  @ini_set("zlib.output_compression", '1');
  if ($_GET["file"] == "default.css") {
    header("Content-Type: text/css; charset=utf-8");
    echo
    lzw_decompress("h:M±h´ÄgÌÐ±ÜÍ\"PÑiÒmcC³éÞd<Ìfóa¼ä:;NBqR;1Lf³9ÈÞu7%©d\\;3ÍÇAÐä`%EÃ!¨¬e9&ã°r4MÂAØv2\r&:©Î¦sê0ìÛ*3MÃ¡ºä-;LC@èÌi:dt3-8aI\$Ã£°êe§	Ë#9lT!Ñº>e\0ÊdÄÉdõC±ç:6\\c£A¾ÀrhÚM4ëk·ÔâÎZ|O+f9ÉÆXå±7h\"ìSi¶¨ú¼|Ç+9èáÅÆ£©ÌÐ-4W~T:¹zkHá b{ Ðí&Ñtª:Ü¸.K v8#\",7!pp2¡\0\\Á¼ \$Îr7 Þ#ði\"ôaÌT (Lî2#:\0Î¤ËxØ½ÌXFÇd&Îjv ¨°Ú£@düE¯ÛúÿÀ!,9.+`JáahbDP<Á°|\"Âèò¢¨Cpì>ÿË+b2	L¡é{óFÏQ´|¦©ºrÁKlÉÔ_Æt=ÉÏðbÀK|©Í®ºª\r=ÓR¬> è0±£(ö¼¯kèbJU,PUumI.tèA-KüXý4²z)MPÐçkºÙ3e`¾N>D#Â9\\(YTÛã@÷hLÅ1]È´ÍºNKÕ¶2\\73i/V¤¯lÛÅYÁÒBA/[JÖÝÄÐ\r;'²2^íªÔb¹Û£3éT=0Hö8\rí+6¹kf×CÏ]qÕW)ëÆÂ²Cÿ2`A°¾82Á!À¾hmÇÐ²GD£ú¼2-C öYc`Â<¾s È6ù2µ¶9uæøyÌÒMgyí=,CZO~^3¥Òî0Ó2<¡Ðk0£wM®{d#`ZÛúãÞº±ëùæ6¯C%»¼ª=RqØð¼_+ìµ-ÛK>ôÒ\n'GÀòA¡\$íú¡¡^j><ögf¾hømb*/\$\$lµÀØ´µg)Aj×ó wò#á£ÁÔÕõTNÕ]Tÿ¾ã%ZÿùjJ¦ªCf4ÆòázFà'* xª¹ª¬Öho&k\räÅ,­árÔ:>s(úLAsë®¸5Ct¥©n6¤ãË ll\\9DåÜ\\!mv\0ÊA{9`.¸â ×¡Slg6¡àÛ!.2Á0PØÔ Ñi\r\$7w ¿â;G°Æ\$0âCIÃ^J\nçL·Pc¼'*Eh°äb;µpÞBÃÛ(x:DÈLË.j±9AC@ I3jf5sI`X}ÆÜÒ#ºà7T`d¤jhH49S\nqHJQ ØH`,FPÊ0\\{¨m\rÌ~@20u!	\$PoQ¥4ÇÎ\nZLæM°BêÂk)@¶d³éSLÈpv Ïy²ÐBß^oàä*¾R\"ÒýÓâ#ºrÍ¥S;\r4ï&GùI©æT	°ràê9=6¡ÈQT\0\0Äïf#¤ù=\$Õ¢ÒH6PæY:®G\$ Ý0è9:a3Hz;GÃ\r!hJ±nÐ7	ýoYÿú¨¨¥ôWLvÌÛi|ùÄ%-â¤d\$pDäR2T\rÕpaU§øn5rªjÁ\$r%D©Ç)\$GÚBuÎà:²ø`¥(l àSD)I	ÀÖÁØ9ç*ê\rtÐ2¡ ÆzI§g[Xûc,u\rvJ5?§ªÒÃ\":^.uàJùPo\$t\$18Ò\nnKÄT%EZ,6íDHVôóª¹i«&z¶«xpdrx*}ÊR25+Ñfì2w»qþ0X1Ï2dXß¢èÌW©æÃV8f\"ëq(uðE GqM#Ð°ñ#K3WAÞvYôÌçÃeK]t¾]EÂëj=SXî@ÏÓ\rÓ\$åÖ9ÂäÜ¬0ØP7\"D.å<;ªNjæ=õºüÀå^èmmÚG¤68 ÆC%v'ªyßÆk/^5ì3ä@Í.Úka*DºßÑ³:Ñ7ÿC}ýÄ`ø`í`)Ý7Îõç­|	3Ð iÕé¨½Âï4\0.:®QßLçäØÍ¨»Üfâ'%äÝ©M	¥¡ÙY3\0Ç##tP6(BÏd§ ©Èoy¯6­|à5þ¸IH7 âöæz?ù(ÑÚÅ\$«RWT¼è°¦:û(ºá`rÏ¶i s=D\\,kr1ôÆÙ2Ø`êñAä9¢&nÁ~ÍÌÒ¬£6;«vp ÓM#È]«¿ïàÉ´ÃÙØAÅiJ.þÜá»tî»çÄîYsíOPwÄ¸¦mâæZÝÿÀAUãÊ·JðN?z»3\$PôqsÛU9,©ÖÊóó#©µ5Pnb¦ãuNÑ{N`êé¥Û°iw\rbº&Eµ\\tg÷ðb¾a1+mÈpw#ÂvlÇýUÔö¡Ò\0ïe.(wb@ïéþ´\\°w()´èE¼¢;äZé]/&ËÃ|>Q¶\"c	<F\rÑ7ÜÉåÏµ\\'ÊSfe÷\rRðVlo/.\nÖàFì±oè ·ehýÔeñÃj× ÈTÙsa4³2à2 Ö`oÎ\\¿A?ö]ðIoB[±{7' ÝQ%¨6ýs7\$Ãò~%»u)Õ5i ÷ÿ0¡#¥£\rÆfäËMXN\\É\0­¤ï,TüíôETo\0{oÄÂRr^§äîC @Z.C,þÂcû'°J-êBL \rP«CD\"êb ^.\"ÚóàÊÀh¤\0ØàØ\r\0à\n`	   n o	à\r ´\r ¢0À` 0£¢	Á\rp 	0À\n F@`à V\0 \n¢\r\0¤\njÀÌ\n@ \0Ü\r \nÀ	 Þ\n@Ê@à\r\0ð& ë\n@Ì @Æ àz­Æ*©wÐq0g£5ðaPxGÂÕ		\n¥\n­µ½\rpÅÍ\rpÓ\rÁ	Ðß0æ\rëðó`¢\r@ð@º ìà°é^\r ð\0î\r d@îð3 ­Ñ1Q9ÑAB¨<®tÈ1N?ÑSÃá°v-aðpð	pÛP§\n°¯°¿0Ç°Ï°Õ\0@ÑÐíõ©Ñ°\r ^Àä\"i@\nÀ 6 \0ê	 p\nò\n` qÞQDí¦BäM°d9ñTUq÷1û Ã2	ò\n2²rR#°Ð2+\r/Õ#¡@ñ\" ÖQ\rÀÜ\ràÀÈ@\n h\nãªíÀ\0Ì`¨	ÀÆ@±!ñ;ñCoæUÒ2õòñe Qk ±p à!P±3Ñ!àr%ÒÁÛpÀ	 Í,Ðð`ìðî\n `\n@êàff ° ª`Æ \nà¦@´	âF#È`pí# äÿoÂÿ \\%Bl»Ï?çÞM-jPñór¤3/Ó3*QlpÀ	p\r`=Î\n\0_>±1'ñ#\0ß>\0¡\ràÀäà ¢\n@â fÊ0Á'±@ÄÞÄÀÊÅ\0è\rdFhµI\$ó`Øè,Üò¤¸éçCÍÈPÝT>Ê7\0]EÌ£ÊDG¼©ÍAC´\\BMDÔò¥fmdè(\rôOG FçÆiDNïÉn2é4tÎwFtíFí®ÞÙHCÔu+°èÏ\$K¬6èEä¤.AKÔL*1JK>º©èÈñMÔÁH¨ø\"GNPjÄE´>ì ëH&5HÔ÷LM#EÍPc¶c8æl £¢,ÿ¢µCâ¦NPtÇ@V tü\nþÔÖÝ´õI	kGÎH	¤)D(JPl1jnðlÔ¬ÜJí~Ø*&ðn\\ÙÕHUfLk¯KôrFºìÇ<|HNx\\ NlêNäòôI¢Ö\0rzMtU|ZêôÄ¸¦õºåÔè\rHCþÎB\"æ@ób¦cnA ÆJ9Ort´A4¸\rªÙ@hòÀA^`Û^¥V0Õ^!LòjUþðÛ.µ^\r\"±¬ì©kaõìlpd Öþ©}\0î¬ÖNïàÔþIGPÕöËUûYtyMPräYÔÒEÚÔ¥xÄÔÄé6`×`jg´Þµ1SB²Ü°èòíÕXÖ8V?Id[IµßQ.åÖíÝ`ñi¬îÌ²vÔÉÂU)ÔÎàÓ\n2PV~ãÌ ¢¦\"\"&§µr]-¢ áp*¨\0f\"Kj`Ïq\nJã\"q¥F¬.öú\"@r«Â(³`òä3q>\"ôfØ\r\$Ø­£ ¢R1Ìªh&Hú`Z¬V	u+MoÊ¬¬\n3Jê\r Ä×2Iü ©D'×!S0W0J?yûpjZ.·\n\r ÷pw\"-+ãzr!`Å|v2\nl¢f(¤m<Ì=âF\r©Q}Ï~7æÌ\r·à#å½oÎ3÷ï}·ìØx<ø~×ýW¬øiEÃ£à[8\n bjjë\r: ïØ¶)vÖê'{·ÕVçq\no{·±)CàßiÀê\rø%·éààÊC(Økkôøô4Ød¾ ¿øjXLN÷(A}xeø|ø±wø´ÁGxhäíXx¦\rÔ%K¾öÞ¼oqx¸»8s4e¸xÏ¤ínÓ*4Føc8~hIp]â{åÎ%ù( ·Óøð<åV÷à£øC¹BØ{wØý¹OÆ£øù}¹Q8[×ñ[{cT%ù&´Êo·Á¹:*béE`émIYWk8Yo§ùu¸)¹Y5o9ãÞ¦÷Ù¹<¦8(ù?\0[s×@*8··}¹ß9g\rÓ¹¹â\0\n'wÂ±x)ÝÙ©µº92·Z1ùï@[Iº+¢÷_57=D§qz!}ºK¤ùNdå£Ú3\0æãqº+C¡Øú¿Y_g8Øúy¸½Ú¨Kâ4Ú{ÙS¨82ZzX\0Ï¨z©úS§ØªÚ±¹e« ¾\rª>¾:£§ù­ÚÇ¬´_¬ZÃ£e»¬úµ¨®:ç¨ø¿u÷­{ÈUMÚa°ØíB«zÉûãb2YSWJ(wOwÓwm ØªZN÷l¶åË§CÌæ9å§í´Ðæ8BDÊ¤6©£Zy±x{èæ;!©[m¯¬Û{}»¸)¯¸#Î4¶[®´Å(½b½ É¸«úÕu¨û­««¹Ê,O¥\"Fª7y?»9£¼ÙndÑ}»±¹{Ýs½{¹ e´Ê¦>\"Öcc§¬d¤ÞÒcs{þÌvdCN½[Àû¹GM¿Cç­ÉDE@");
  } elseif ($_GET["file"] == "dark.css") {
    header("Content-Type: text/css; charset=utf-8");
    echo
    lzw_decompress("h:M±h´ÄgÆÈh0ÁLÐàd91¢S!¤Û	F!°äv}0Ìfóa¼å	G2ÎNa'3IÐÊdK%Ó	Òm(\r&ãXèÐo;NBÉÄêy>2Sª*¶^#ÆQÐ1=¥ÆJ¶W^L£¡Ëo¡ÌÆc!Äf³­6»mâ¾a¯³l4&1Lf³9ÈÞu7VDc3Øn82IÎ°Ê,:5ÊØr·P«ö1 Äm¡>5W/Fc©Dh2L\rN¯ËÝWo&ÄhkÞe2ÙÐÀb12Æ¼~0Ð ãD}N¶\0úf4MCé­×ìn=´pã©ZØ´²NÓ~Í;îÑ-C òæ%êz¢99PéÁã¤\"­Â²ß;\0fñª8à9¡pÔ:mÜ8ãâ@\nXà:rØ3#¨Øû­CÀ[¹Cxî#ðI2\\\"¡ÀpÆÑÀ]#¨Ó5R¬r6#ÃL7ððß!H\$\$IRdÛ'Ë8]	§xÜé+ò¦>ÅC@-ã¨Û;ÃîÜbï<ä©2ÃðN4,«ãã-Mr¥6IcX4¹aÊÃ5KETh@1@íÍR®K9\r£(æ9#8ËG¡CpwID5Ì2Øl\"_'ÓÊUBÌU¡9c@ÃG=C\nèÛSÈ0§Õàj®×7PUàÈ£Û9J]®<×\nÆ²Ïzû?B÷Ô2ÍÒÜ4\r/P\rÁM[X¡F_ìÿj¬HÓbnC&Â¡f%@cC^.2ã8¨×CÑ}^sw½LðÂ/ø5OÙMä¸Ú³	*Xî?bÍ.IgÊÔ&óaqÝ>çFNå½-`æy¬ä4¥s»áÓj\\&:SaåP;ô¼²Hëû®XÎÞ¯éd¡kt?.´õ±,ZOÁ·@@8Z3©c\"ÑèÃ\nØ=AH1\\ZÏ^/kêÿÅÎLíuC\\ñc)0OüÃMÍïlpr7ä\rqÁ¶ÙWRaÆ¡¥Øöc@Áwmk/Û8£*?ÇÌè4ª5æ\\m§¡kàù>d1nðëUQ#£§Üø¾wçæ«Lo&hÄªPrnR,5ôz\"\$3»dYH(p\rÂALAC)pTõPl²!\"L´8ÀÂRà´&\0µîZà±0P8×ÆûÜãÉJ	`Â¨e0	®Ú1û	®DÄJs°H³)kÆ ¡[ÅóÔCÈypjx,\rAm!Ù<h1ä");
  } elseif ($_GET["file"] == "functions.js") {
    header("Content-Type: text/javascript; charset=utf-8");
    echo
    lzw_decompress("':Ì¢Ðäi1ã³1ÔÝ	4ÍÀ£ÌQ6a&ó°Ç:OAIìäe:NFáD|Ý!Cyêm2ËÅ\"ãÔÊr<Ì±ÙÊ/C#Ùö:DbqSeJË¦CÜº\n\n¡Ç±S\rZH\$RAÜS+XKvtdÜg:£í6EvXÅ³jÉmÒ©ej×2M§©äúB«Ç&Ê®L§C°3åQ0ÕLÆé-xè\nÓìDÈÂyNaäPn:ç¼äèsÍ( cLÅÜ/õ£(Æ5{ÞôQy4øg-ý¢êi4ÚfÐÎ(ÕëbUýÏk·îo7Ü&ãºÃ¤ô*ACb¾¢Ø`.­Û\rÎÐÜü»ÏÄú¼Í\n ©ChÒ<\r)`èØ¥`æ7¥CÊÈâZùµãXÊ<QÅ1X÷¼@·0dp9EQüf¾°ÓFØ\rä!æ(hô£)Ã\np'#Ä¤£HÌ(i*r¸æ&<#¢æ7KÈÈ~# ÈA:N6ã°Ê©lÕ,§\rôJPÎ3£!@Ò2>Cr¾¡¬h°Ná]¦(a0M3Í2×6ÔUæãE2'!<·Â#3R<ðÛãXÒæÔCHÎ7#nä+±a\$!èÜ2àP0¤.°wd¡r:Yö¨éE²æ!]<¹jâ¥ó@ß\\×pl§_\rÁZ¸Ò¬TÍ©ZÉsò3\"²~9À©³jãPØ)QYbÝDëYc¿`zácµÑ¨ÌÛ'ë#tBOh¢*2ÿ<ÅOêfg-Z£Õ# è8aÐ^ú+r2bø\\á~0©áþ¥ùàW©¸ÁÞnÙp!#`åëZö¸6¶12×Ã@é²kyÈÆ9\rìäB3çpÞî6°è<£!pïG¯9àno6s¿ð#FØ3íÙàbA¨Ê6ñ9¦ýÀZ£#ÂÞ6ûÊ%?s¨È\"ÏÉ|Ø§)þbJc\r»½NÞsÉÛih8Ï¹æÝè:;èúHåÞõuI5û@è1îªAèPaH^\$H×vãÖ@ÃL~¨ùb9'§ø¿±S?PÐ-¯ò0Cð\nRòmÌ4ÞÓÈ:ÀõÜÔ¸ï2òÌ4µh(k\njIÈ6\"EY#¹Wrª\rG8£@tÐáXÔâÌBS\nc0ÉkC I\rÊ°<u`A!ó)ÐÔ2ÖC¢\0=¾ æáäP1Ó¢K!¹!åpÄIsÑ,6âdÃéÉi1+°ÈâÔkê<¸^	á\nÉ20´FÔ_\$ë)f\0 ¤C8E^¬Ä/3W!×)u*äÔè&\$ê2Y\n©]EkñDV¨\$ïJ²xTse!RY» R`=Lò¸ãàÞ«\nl_.!²V!Â\r\nHÐk²\$×`{1	|± °i<jRrPTG|w©4b´\r¡Ç4d¤,§E¡È6©äÏ<Ãh[Nq@Oi×>'Ñ©\r¥ó;¦]#æ}Ð0»ASIJdÑA/QÁ´â¸µÂ@t\r¥UGÄ_G<éÍ<y-IÉzò¤Ð\" PÂàB\0ýíÀÈÁq`ïvAaÌ¡Jå RäÊ®)JB.¦TÜñL¡îy¢÷ Cpp\0(7cYYa¨Mé1em4Óc¢¸r£«S)oñÍàpæC!I¼¾SÂb0mìñ(dEHø¸ß³Xª£/¬P©èøyÆXé85ÈÒ\$+Ö»²gdèöÎÎyÝÜÏ³J×Øë ¢lE¢urÌ,dCX}e¬ìÅ¥õ«m]Ð2 Ì½È(-z¦Zåú;Iöî¼\\) ,\n¤>ò)·¤æ\rVS\njx*w`â´·SFiÌÓd¯¼,»áÐZÂJFM}Ð À\\Z¾PìÝ`¹zØZûE]íd¤ÉOëcmÔ]À ¬Á%þ\"w4¥\n\$øÉzV¢SQDÛ:Ý6«äGwMÔîS0B-sÆê)ã¾Zí¤cÇ2Î´A;æ¥n©Wz/AÃZh G~cc%Ë[ÉD£&lFRæ77|ªI¢3¹íg0ÖLa½äcÃ0RJ2ÏÑ%³ÃFáº SÃ ©L½^ trÚîÙtñÃ¡Ê©;Ç.åÅ>ùÃá[®aN»¤Ï^Ã(!g@1ððó¢üN·zÔ<béÝäÛÑõO,ÛóCîuº¸D×tjÞ¹I;)®Ýé\nnäcºáÈíW<sµ	Å\0÷hN¼PÓ9ÎØ{ue¤utëµöè°ºó§½ 3òî=g¥ëº¸ÎÓJìÍºòWQ0øØw9p-Àº	ý§øËðÙ'5»´\nOÛ÷e)MÈ)_kàz\0V´ÖÚúÞ;jîlîÎ\nÀ¦êçxÕPf-ä`CË.@&]#\0Ú¶pðyÍÆtËdú¶ Ãó¼b}	G1·mßrußÀ*ñ_ÀxD²3Çq¼BÓsQæ÷uús%ê\nª5s§ut½Â{sòy¥øN¯4¥,J{4@®þ\0»PÄÊÃ^º=¯l²`èe~FÙ¡h3oé\"¤q·R<iUT°[QàôUÇM6üT. ºê0'pe\\¼½ôÞ5ßÖÌpCe	ÙÔ\"* M	¨¦Dþ±?ûhüØ2¡ÐãzU@7°CÓ4ýaµ²iE!fË\$üB¤<9o*\$¯ælH\$ Å@ààÊæP\rNÀYn<\$²	ÀQ=F&¥ *@]\0ÊÏË W'dÖ z\$æÐjÐP[¢ö\$òä¯Ð0#& _Ì`+B)wv%	âÔLcJRSÀÂi`ÌÅ®	FW	êË\nBP\nç\r\0}	ï¦®0²Zð¸ò/`j\$«: §8ieüÀØÏxâ¹Â±îa ¬GnøsgO¢äU%VU°@NÀ¤Ïúd+®(oJï@XÆèàzM'FÙ£àWhV®I^Ù¢1>Ý@Ð\"î¨¤ ÈQñR!\\¢`[¥¤«¨.Ø0fbF;ëÂçFpÏp/t`Â ô®(§ÀVé¸ø bÈ²(HlÁÎÔ¯1v­ÞðHÐï1Tï3ñqàÉ1¦ÑªfË\nT\$°éàNq+Ëí`ÞvÖÇï\rüVmûÇr°¨Ø'Ï¸±ñg%«\"Lm¼((CLz\"hâXØm= \\H\n0U f&M\$¤g\$ñU`a\rPþ>`Ë#gªhôî`R4HÑ'ç©­³²GK;\"M¶Û¨ThµBEn\"b> Ú\rÀ©#\0æN:í#_	QQ1{	f:BËÂáRª&àÜã)JµÄBr¹+ÂK.\$ÐPqõ-r®S%TIT&Qö·Ò{#2o(*P¯â5ï`1H®¢'	<Tðd±÷ª¾sÀì,NÚÊ ÒÉÔì^\r%3îÐ\r&à4Bì/\0ÐkLH\$³4dÓ>àÒ/³à¶µHö·* ºù3JÇÐ¥<Hh©pú'çO/&ï2I.îx3V.¢s5Óe3íªÛZÛ(õ9Eg§;R;±J½QÃ@ªÓvgz@¶Þó'dZ&Â,Uã²ßò¦F æb*²DòH! ä\r;%x'G#°Í wÁ#°Ö È2;#òBvÀXÉâaí\nb{4KG¦ß%°ÒGuE`\\\rB\r\0¨-mW\rM\"¶#EôcFbFÕnzÓóÿ@4JÈÒ[\$Êë%2V%ô&TÔVdÕ4hemN¯-;EÄ¾%E¥E´r <\"@»FÔPÂ·L Üß­Ü4EÉð°ÒÄz`Ðu7éN4¯Ë\0°F:hÎKh/:\"MÊZÔö\r+P4\r?¤SøO;B©0\$FCEpÇM\"%H4D´|LNFtEÑþgþ°5å=J\r\"Þ¼5³õ4à¾KñP\rbZà¨\r\"pEQ'DwKõW0îg'l\"hQFïC,ùCc®òIHÒP hF]5µ& fTæÌiSTUS¨ÿîÉ[4[uºNe\$oüKìÜO àÿb\" 5ï\0DÅ)EÒ%\"±]Âî/­âÈÐJ­6UÂdÿ`õña)V-0DÓbMÍ)­ïÔ¯ØýÄ`æ%ñELt+ìÛ6C7jëdµ¤:´V4Æ¡3î -ßR\rGòIT®#¥<4-CgCP{V\$'ëÓ÷gàûR@ä'Ð²S=%À½óFñk: ¢kØ9®²¤óe]aO¼ÒG9;îù-6Ûâ8WÀ¨*øx\"U®YlBïîöò¯ðÖ´°·	§ý\nîp®ðÉlÉìÒZm\0ñ5¢òä®ðOqÌ¨ÌÍbÊW1s@ÐùKéº-pîûÆE¦Spw\nGWoQÓqG}vpw}qñqÓ\\Æ7ÆRZ÷@Ìì¡týtÆ;pG}w×/%\"LE\0tÀhâ)§\ràJÚ\\W@à	ç|D#S³¸ÆVÏâR±z2Ïõövµú©	ã}¨¢¯(¸\0y<¤X\r×Ýx±°q·<µIsk1Sñ-Q4Yq8î#ÞîvîÐd.Ö¹S;qË!,'(òä<.è±J7Hç\".³·¨ñu°ü#ÊQ\rerÀXv[¬h\$â{-éY °ûJBgéiM8¸'Â\nÆtDZ~/bÖÕ8¸\$¸¸DbROÂOÆû`O5S>¸öÎ[ DÇê¸¥ä_3Xø)©À'éÄJd\rX»©¸UDìU X8òx¯-æàPÌN` 	à¦\nZà@Ra48§Ì:ø©\0éx°ÖN§\\ê0%ã·f\\ ð>\"@^\0ZxàZ\0ZaBr#åXÇð\r¨{àË¹flFb\0[Þ\0[6	¢° ©=â\n ¦WBøÆ\$'©kG´(\$yÌe9Ò(8Ù& h®îRÜÙæoØÈ¼ ÇøY£4Øô7_­dùã9'ý¢ú Üúï²ûz\rÙÖ  åðþvGèO8èØìMOh'æèXöS0³\0\0Ê	¸ý9s?öI¹MY¢8Ø 9ðüä£HO,4	xsP¤*G¢çc8·ªQÉ øwB|Àz	@¦	à£9cÉK¤¤QGÄbFjÀXúoSª\$dFHÄPÃ@Ñ§<å¶´Å,}ï®m£rÿ\"Å'k`¡cà¡x¦e»C¨ÑCìì:¼ÞØ:XÌ ¹TÞÂÂ^´dÆÃqh¤ÎsÃ¹×LvÊÒ®0\r,4µ\r_vÔLòj¥jMáb[  ðlsÀÞZ°@øºäÁ¶;fí`2Ycëeº'MerÊÛF\$È!êê\n ¤	*0\rºAN»LP¥äjÙ»»¿¼;Æ£VÓQ|(ð3ÄÊ[p8óú¼|Ô^\räBf/DÆØÕÒ Bð_¶N5Mô© \$¼\naZÐ¦¶È~ÀUlï¥eõrÅ§rÒZ®aZ³¹ãøÕ£s8RÀGZ w®¢ªN_Æ±«YÏ£òm­âªÀ]¦;ÆLÚÿº¶cøû°Å°ÆÚIÀQ3¹OãÇ|y*`  ê5ÉÚ4ð;&v8#¯Rô8+`XÍbVð6¸Æ«i3Fõ×EÐôØoc82ÛM­\"¶¹©G¦Wb\rOÐC¿VdèÓ­¤w\\äÍ¯*cSiÀQÒ¯ã³R`úd7}	º)¢Ï´·,+bd§Û¹½FN£3¾¹L\\ãþeRn\$&\\rôê+dæÕ]O5kq,&\"DCU6j§pçÇÉ\\'@oµ~è5N=¨|&è´!ÏÕBØwHÚyyz7Ï·(Çøâ½b5(3Ö_\0`zÐb®Ð£r½8	ð¢ZàvÈ8LË·)²SÝM<²*7\$º\rRb·âB%ýàÆ´DszÏR>[Q½Ð&Q«¨À¯¡Ì'\rppÌz·/<}L¢#°ÎÂÐâZ¹ã²\"tÆï\n.4Þgæ«Pºp®Dìnà¥Ê¹NÈâFàd\0`^åä\rnÈ×³#_âÄ w(ü2÷<7-ªXÞ¹\0··s¬ø,^¹hC,å!:×\rKÓ.äÝÓ¢¯Å¢ï¹ÔØ\\ò+vZàê\0§Q9eÊËEöw?>°\$}£·D#ªðã cÓ0MV3½%Y»ÛÀ\rûÄtj5ÔÅ7¼ü{ÅLz=­<ë8IøMõ°õâGØÑÎÞLÅ\$á2{(ÿpe?uÝ,Rïd*Xº4é®ý¿Í\0\"@}<.@õ	ÞN²²\$î«XUjsÝ/üî<>\"* è#\$Ôþ÷Õ&CPI	ÿèt¿áùü¦î?è ´	ðOËÇ\\ Ì_èÎQ5YH@ÙbâÑcÑhî·ùæë±O0T©' 8¡wü»­öj+Hv_#ºíïì06ÈwÖXà»d+£Ü\\Àå\n\0	\\ð>sîÓA	PFöd8m'@\nH´\0¬cèOwSßØYá`²¨¢R×ýDna\" ì~Â?Ámð|@6ä½+ìGxVä\0°WÓ°nw.¡Øb«9Ã¸EÈ|E·ÃÂ\rÐr¬\"Ðøx¸-¸êâ\rN6n·\$Ò¬ý-BíHæ^Ó)â¥y&ãã×WÇ§àbvRì	¸¥³N\0°Ànâ	T`8X¬ðA\r:{Oþ@\" !Á¤\$KÂäqoÐËjYÖªJ´þÂíÜh}d<1IÇxdÊÎTT4NeeC0ä¥¿:DFÚ5LÞ*::HjZå­FõRªMÖnS\n>POó[\$V8;#K\\'ùBÖè»R®Ø¯°RÑ_8Ájé*Ej \\~vÆÂÐvÄÛp@TX\0002dE	HíVðñ×D\"Q'EDJB~A´A¤Il*'\n¶Yå.è+©9¾ñpgÒ/\"¸18Ä0IAÊFCÈ¨V*aèPÀdÖÐ£5H\" AØå6Ýs¬YÝØ;è¨È/¨¸0ãv}y\rÍâÎ×¥1u\"Ëmãñ_º0ç`ß¯¿\\B1^\nk\r]lhø}]HBW`±0½ê¨¹rFf)W,ÕÒ§]sm9'O¢xÔ½Í,ê9J8§£? 4ÉÉï¡\"ÒèÛ½Ì<Ñ-S¨ÉÃþMÃ;ÐvÌñ6y|ZòÁ¨%àa#8¢TC!pºË\nØïCZ(ï½wéØa ·Á?9|ó0<BL\r\n]ÀPB0¤&+tÄHñÖàDx^÷î³,Lð}[¦ÄBñx}½ÐruÐË\0¾\0005åS@\"UØ@Ü°\0\$äÁÞ\"Ò Ä]l/	ùíIâB4¯.Â6 Âd7\r@=ªß¬¢ÕÛ*G j¬üf`»:HnìÔbÄ71Çê)C<@AÍY#°¦¡ëÑeoâÖY!ÅÊIDM¼\nlt¨/)\\43)®Ù2ï­É¸Ó)Á²f[ ppp1µ©#Ã¶p\0Ä§ÅlÀ^{A¤THå6ÖÊ«è\n\0PâH.\r|ÀTFD0SyðÀÏ'1Ö´¤K² dØµ±¯ÄBþCç&Å)þWs Hee+@4 r·áÛ*Lp1<üfNY'­-	XKVa¦L­¥ö\"\"ìl£qÉ.YJHàm HV/lCÞ&àÀH)oÁ&\\2Ä­%âáéz\n^Q(6ìD ÈûJq°á«\00a#Ë6\0vr,»MÌú&AÔòì»9%YdBêhÀÖ!W\0êb\r{Æ@Ç1¹I¬22AÚÚ)H¾a@r0GÉÜ7Dd.LM<ã2ÐÈË,k/Meª¹}Ò3ä=\0Ð&ÉBø\nPd.\"ÈñF3XÈSd(*¨J6 äF:¬×)1Â1á?lQ&Ïùµ¬h<JÍ¤fdEÕº*ñx\n\0¼À.\"B -#£ÀÎt¿IÎ«õÐ	I8 ²8dh	«èx§~°	L!K(úBXµ£-ÈìhÎåc/Öræ×PÕIõ«NÊ2È|Éç×¶Ò|\"µM'¡K,\\H°Ée5*o]4ÒFP	2Í<)T¾oÀ\n¢¸ØI¶Ú¢Ä!¨(ø_8Xrç;uúàØNJù¡é[rûDC:¸@ÁÍ³Àl\0©e\\*x@AÈ¡&í(5Ã×,ª#1xÀ º!T Dª­(QáDJ|D D:\0ÉAÙÐ¹Ô ÁbaEÓ?rn°²WkxøX=i,\$3[r9BÆ±§dã¡þ\0ºÔH4­«É<(zÊºô?àsIbJ©g UÂ\n(}¨J\"à¦ABÐ19~ÅIé#Ú\$¹%d  e\"µ`Àìátª¨'O= À @\$µO\nmT×o+Zäñø-­¢êßPF?Ò_I¤JËX Ä£2Â¢ê-V¶;ª?2¥Áá0¡*P3Éªõë_T<E¥JÅ\\(Ý2ô Ø)êIQé¬©·óÉRL&¥Í!È¯KÁiÑt»¤°ÎKúHRl¢È¬Es¶¿¤DøxÇ´¬i¾ºÖ!faBÉñó¼FÔËe>Vç©É-QjÂIÅ7§þ\"%RhÈ g£áM³ø«Õ-b£58RÂ¨¯Ä*ã§9ÔÆê°«·Ô9¤2Q0ý¬IR[üZ£ÝN\0÷ÇÂ20£¡ÂÐ\\[@áQ\0¤ÔJxùµäEC{©â\$lp1=\0·RÐ¾É>E~ßÆê×Ñ:0À%R+)\0°	ÆQá@(\"¡_jTX\0ì\r1\0P9#\0ÍôòH;Bª|À²LöZ¼Æ6ù/Bà\nB{ñðà|HÄ,á	*;(õ`Ê2@6ª>¡	å?P\0/¹ó\0|\\ÅeBÜ`jq©U/\rc©üêÔÒ¤6(N\0º/\$à\n8µj*U\$ñºy*³=¬;ð\$f¬â8XØBCEþr\"/àªkÚ%\\9k§ùèBð0§F­À(¬ð'ôUôªµÆ®m¤@kT\0Õ¹EáÍsEhyòe\nä) )b7ªã(W%,ÈJ¤r¨ó2D¶rhEù\n0Qê3 U9TPOÀÕô°8j|¤}ÃR<0ÈâZl ÐØTáö°ÈÙÚ*¯\$ÎÀU\rÛ\"¤.ª Ts~Ë~(ð3aº¨@Õ+là`:Î`­:OiùBXÁ?Ê¦é7¾Lj|Í:nK:Ø²}²\0ÝÉUMc`P%nn\n,ì4áQ'%+H.è\"#GÐ3`¥¡ÝèÝ\n1fg\0Ð'¼k¦²qxD<\",a|{~þó¸ÜC<S»iBï\nkNþ ÖG³}Óàk:Îî­ÀÝgÛ)JD°hÃf¢\"kV~³ámM`HOkD¬^0/tj«l³\r!Ïf<ÀGôÛTºÕvµ#@­ek@2«wéý´0ÜÜ­tÄÙÄ¯1ÄuÌyvË%8±?1¼ÛÊlæ×xtÇmp­fK3ZÜJ£=\0@^p·ÂÛ¹¶æ³ø]Ò²'ëtÙ¡@C·bëå\r[ÈãVôµ-½ÀËo-¦Ý e·}ÀéYªÜ	-é-m³I\0+ÍVßDÛ[B+ç(-Ù4ä«>®qèi>=½î/0-¦cLpJ b\ndáò)â«#áGËs­·ä\"ÒQÐNø`.úÈÔyÈEtPqÔI]ó¤ëJ8¼»rWTÅÁIµèf÷aG.ë7yçËlÙÕA³7'¥1	âS-ÙxI§m·ËÂL:eÎAÆWøÝÎ¶EIÝâWzÔ3Wòý°)*/)CÊÇÿx*c]ì%÷}½âÅ»_ÏÌIvÍ²½'\$U÷ÝS4k5WÊJC® 7*b%<WC@ÂÆ	À¼©»c{Þ´«ò¬3)Xò&&¢eLìIå¢,Nì 2k#p5 ´f4«öÇºëz¯#â½ä\\®ºà¡ûNøbÔUðoyðÈSÕ4¾`qÓ~1=ì8å¸*áOOJêC¡ñ®âÚè'Dd,@kLñ¹à¤÷\\âj2Í©Äê±<³@_q÷2\0Õ±Á)`Àêýs°±óF\0¡ÓâÀÖ\n­F×<*Àx*Àë`ÔàÁ-\rø|@ÑñÔ7ðH@wóÿH]µå\0¶àü_w¾µh0!Ës¢1Ï¾¦Ç¬hW°.Ãê=WªR*÷A_ÆåEDÔ· ?1,UbÌ9=tÈ4Ã¨¤äW¢^åäÙ;ßè±Ì@ò(1<DâEÌHx©T()0z`Ñ_Ð;¨ALé±)\nÌK[fHWo@bBKÀiM±Ãd+ï>èvI¶(z:äÝ.ÝÀ 9uiÑ¤DYÖâ¾ûÉO`ö®á]I\0°RÄ,K,÷¨ã6L¸Ä\"\"£1gª(­|T.,ñ9vb+\rk]u¶&è©|©åb£SÍÅd[¼,gêèaJº(CÄök¤\rFØÂ+	ñ9âÂL©¹)Â)UAßBUhÂgàc3xñ-n9±úü»äxÈ®2¯´q¬ibÖrY7ékÌyìf, §¼àÎ)¬Ùª¤J:«NÂ8ÜRcly\nõ¼2ÅWô;¬.>Åv6Q#A0­ê{Î­iùï7~@VXÀ¢^¿å11-É+Ïv|£Ü]Vf¸¢û.{	áÒÀ\r·§;ê1lp°/ÙõuFÇd\$PÐ®0=@kSÆ0hÁÉÂ@Ñ/*(OæV.´G>(rËÎ!6àª÷®òY=XZ@Â:²'&06kE|Ï'|H;¼æNògÒ%ËW+Âæ¯4ù;Í¯¯'x|f©9­ÌÚ(O¨ðd¦§é·w%9]¦×f}ÌÃGÖÔÄs¦µçÂ¾óÓ÷XM0ÍégQ·ª¶8Ìù+O}¶Í0}9ÖÐÞ»ßNhÎ/mgDés°ü¦Äà\nÍ74å³P~}O)©UgÜ9ùÉÖj§8PÝ¸Á(Ð%ÄóöÛjÞ7oAB×Ði)üKò½Ùu¤ë´ }s±1è=odÝV[Ä´\
n¬ç²zlMÐ·r:F#{Öð*#°xÜÜ°¯<Ds½k/mw :^æë¦âÉ1¿ÄÏD¨2ºz*Ñònª%ôÞåÓÚiâÃ *Ê!8-·á¦tH'íã\rÍÐºÐ4äÝ8`¿\"¡»»i]ZZ>Z\0Þ¦9ûìÚ+ä~á\$Þ­LÄP\\ìXA©¬ èÀóÍiççzÒhÂ\$÷ÂSMÚT'1×èÏDÍâ	Ë5E©\0Ä\$ãttÔ®¥ì:\rMÆ·S¦ÓlsªAfÖKàk,NlÛD^zz²dS®/rt²Nù>ýo%i¥½\0J¯B©po¢ÜRÃê/ÖÙ«x\ny+«ì,e4Îq5Q'JD]¿B@mÓ´ÈÃR§Ski~úÜÎ¶t0ç[ 1z	&×û^\nOÕ¶²ÉV÷ëÀ³GV@T*ÞH9ÑÏG0\0'Ö`Ñ°\råûbQKsLd*;\ná×æÁ.ÄUNpà,Lâ@TRàe øbFÀøyn> IKÀ¶rGû	@Ù?cIÝu%GöOô1 ÖCöh¦5TüyüI­Ù:\\0¼àX¥Ë>öÊ0ËÞ¾ûQB¶©EI/-LBTÚ!bï÷6ìÿk`jp\0KÂ>kdâÄ/äISk.+*Íû¡R|gR¡ýøW\\wùÂÓtà.)¤^Zc8ÕZ~FÀ°SÇµÔSmÌ;b>\0jz=î¢T'Á>Ìåqy}:»u§µ&åÀWºDQ¢Ïc-ªËþÇ6<[e÷xØ èÐî[ú¹L©\0wmùl°tzëç<S&ðådbÜxÍúoiâgK©\r`ÖÂµÔ?D5u@bN¸àOð¤·¤íøYÔ[õè£Àñ{ÃNïréût±¾ó\0ïÅtMscBW?°*D.põ¤'2Ge\rp*#­e¹ÐêÚÅCýÓø\"³QI\nhiøQÁ@á\rl	ß´à_.¤Êt*á^øsÁ9ðïWhqÕê¸~,¤áYÎ¸ÄdQsÂ¦\rBjºõDÿÇ¡ ñ<<T)C´\n¶ø°Í&¹D{\rÐlÖðÑ-RãÊ\r@rk§éÏ¢ø +ZíûïP¾ÛÖÎèéu8È¨ôÇÚsãÙøóoç#äÊgÈuï¹\$F&\n-v\"PÜÎæ¶Ûjnntë1ßV®§»¥öêAwbxßÄDÑ5áÍ-Ô0³a\0\r§/!ÈI¢Ñúí|/ÞhánGf-Mdna^(eïa´¤Â¨·YÞÏZ,SEöN\\§Õó¸=Ò4~MÍ´¸\rÆëýÒFtÅ¦ñu\"|`ÑÒEá²ÀRózÂDÌ`â{Äè@k/KæY¹®3sJ¡ä¿5XGÍª%®9)Qà £QÜäá¦1th¶ô!TRæ²ñÑHÂâÚQÝ\rCåEÔ0#wçG2ÂÞ/¾Ö/çé=^ /ÔºñÎÎÄÙËE¬\0{+òüt+¨äqßÐ±ªæIÍt·|ú÷ÈÕvêðqª¹ÔÆ&Ï\r\\ëVß =°EbÚënOÎrnêX({É¹uzK­¯`=:ø\núÄß÷\0ªêÇÐ[é%:pq+¦ÔRldYë\"ÅÇ[VÏu{H-­ÁH×_ýâ¢8jëVÕ5à\"\0\"N?E;+°O~»wNÃ];L'íSOFÔêä»±Dæ-×!#sNÉ<Õêô Â¯Ñþmu³¤ÈóG¯8ûÎTn]¶¼Îá:úzIMn O°8ÀèÄz5o\\57<ÅÍÅ²#8â¨ñé?sNîºÛLõ¸	}úxîÖ&4î?ç[àz½ôó³·¶Éý¡<*W¸èÝóÀe}{HZ§±,(<oÔoÀxW¨t¶2íÐÝ# A*·¡»o\\ç¼R²}xH>NP¸|QÉ|x°'È-° ÛÅ2\0 Ý?Æ¾2*\r|]töpá\"¢Ú²JuuXybÞD\nÊZ|H7 _òW®þGuXyH>T\r¨G»äþQl¼ñ¨ÉÂçn!Îu'Ä*ºC5¸Ý>Uª2!b	£9PwÂÝ4åüõá¢}yèWÞ|ñâa\$¾géêÁ óTÇUË¡&~9(\\*½!b_Ïùûw±7\\£Çð]=ß\\*ä­@ð#N7Íªè¯Á5QN`@<\0°6!9ÆÑl¥\$wI\$4 õ¾2ë\$¥&Ðì.RZòà³Yuyá¤³ìpå&SI®Ý@¨EJiLcõºV®1Fñ1äZ\r\r¦àh¡kÚ»öHHñË¿®ªªöKý§ ?xµâ-0\nÛêdÍN3KóCÓ59)Ä¾:B#¨ÌdN5A1ÆÆøÌOd[3Ú áh[s~)±9 DNâyøáñþ>âÀX±'È½ÎÏHèòç,î)Ú½\"Âeó0;\0Ëqeo>¦Û=®|«2¦G+B¨@z·Ïäøò@]}rQîÁÒ k/|íGñ:Ñ¯äW\0ça4>ò^|õïìgÝoûXEä9püÅLrgAÄ6¼p¿eúïÛÇ1ï´*Åëã½7ÚÀ[ö>]ý#ë?jB¨~Ö/¿}Å3ÿ:ûU\$ð?¼<¿GüäaÿïÁ\n>0#!i>.{A}'hQÿLwë~W_¨îªTh#dÀÅÃ»ªdFQ¸µóâ*{æø\"\"¤P{õà}Þ4 N×ÕÓió­Õ\r_ÅÊØÄe?l4À2¡?\nåFú	åôqÎUï×Ä½°_Ýÿ`_üõÇàjý¬{_k_Ûo÷~ÿ¿c*#ÿ(´/ü!Dn¤Fÿ`ïü?@sôBÚ!®?;ÜEâ²úÿþ¾ÿ\0kþ	ÿ*NýìD;¼õ°+d\nZZdB»À÷ `B5æP\n8¬ÖéàðÌc#ou½¤kßËMÝ¯w.ìªFÀJ¦È!|®Ä2FcY).¬§ºôXHyò[ëê~ù#/&¢£öã[À ÿñÂY@ý¨À(|\r\0,O¼ñ0YbÔÎ²Å¬ï\$0×ÓÛaËÀÉ A\$Çú0,Ë@ªÓ°>>9úÁ\\tiø<\0ãq\0Ä}@`ñ\0fVj°­dß '(	!_²nõ 0+c´µiig8a]'=-¬B!(§Ø8_ÝëÆx²j©µ)\rH5HïYn	,f«rí}-d\$òÖH ¬2né´ÜÈ=à-­d©FE-dáé¨aN_z4@À[ènã\$x!!i0TªÊuÀ8ÌÉ¸¼¯þ\0PZ8ZÁ¹êcçàÁ®+ÐAAF(äÁØÛ`mg*¸vS, ÇÜðKcAþÛ¬ &Ä¨9êÀÁücÝ0w+nÎ=°)\$ëÐQð~AÛaæ\0004\0uñ{Ä(´¤\$°­y	!°BÛ A<µaAz ¨ÁZA4\$ZY9.aX\rdÚALÂv|oOz|ßÂZÜ(îeíZ£ÄÀ");
  } elseif ($_GET["file"] == "jush.js") {
    header("Content-Type: text/javascript; charset=utf-8");
    echo
    lzw_decompress("v0F£©ÌÐ==ÎFS	ÐÊ_6MÆ³èèr:ECI´Êo:CXc\ræØJ(:=E¦a28¡xð¸?Ä'i°SANNùðxsNBáÌVl0çS	ËUl(D|ÒçÊP¦À>Eã©¶yHchäÂ-3Ebå ¸b½ßpEÁpÿ9.Ì~\n?Kb±iw|È`Ç÷d.¼x8EN¦ã!Í23©á\rÑYÌèy6GFmY8o7\n\r³0²<d4E'¸\n#\ròñ¸è.C!Ä^tè(õÍbqHïÔ.¢sÿ2NqÙ¤Ì9î¦÷À#{cëÞåµÁì3nÓ¸2»Ár¼:<+Ì9CÈ¨®Ã\n<ô\r`Èö/bè\\ È!HØ2SÚF#8ÐÇI78ÃK«*Úº!ÃÀèéæ+¨¾:+¯ù&2|¢:ã¢9ÊÁÚ:­ÐN§¶ãpA/#À 0Dá\\±'Ç1ØÓïª2a@¶¬+Jâ¼.£c,ø£°1¡@^.BàÜÑá`OK=`BÎPè6 Î>(eK%! ^!Ï¬BÈáHSs8^9Í3¤O1àÑ.Xj+â¸îM	#+ÖF£:7SÚ\$0¾V(ÙFQÃ\r!Iä*¡X¶/Ì¸ë67=ÛªX3ÝØ³Ð^±ígf#WÕùgð¢8ßíhÆ7µ¡E©k\rÖÅ¹GÒ)íÏtWe4öVØ½ó&7\0RôÈN!0Ü1WÝãy¢CPÊã!íåi|Àgn´Û.\rã0Ì9¿AîÝ¸¶Û¶ø^×8vÁl\"bì|yHYÈ2ê90Òß.ý:yê¬áÚ6:²Ø¿·nû\0Qµ7áøbkü<\0òéæ¹¸è-îBè{³Á;Öù¤òã W³Ê Ï&Á/nå¥wíî2A×µö¥AÁ0yu)¦­¬kLÆ¹tkÛ\0ø;Éd=%m.ö×Åc5¨fìï¸*×@4Ý Ò¼cÿÆ¸Ü|\"ë§³òh¸\\Úf¸PNÁðqûÈÁsfÎ~PÊpHp\n~«>T_³ÒQOQÏ\$ÐVßÞSpn1¤Ê }=©LëüJeuc¤©ØaA|;ÈNó-ºôZÑ@R¦§Í³ Î	Áú.¬¤2Ðêèª`REéí^iP1&¸Þ(²\$ÐCÍY­5á¸Øø·axh@ÑÃ=Æ²â +>`þ×¢Ð¯\r!b´ðrö2pø(=¡Ýø!es¯X4GòHhc íMS.Ð|YjHðzBàSVÀ 0æjä\nf\ràåÍÁDoð%ø\\1ÿÒMI`(Ò:! -3=0äÔÍ è¬Sø¼ÓgWe5¥ðz(h©ÖdårÓ«KiÊ@Y.¥áÈ\$@sÑ±EI&çÃDfSR}±ÅrÚ½?x\"¢@ng¬÷ÀPI\\U<ô5X\"E0t8Yé=`=£>Qñ4Bk ¬¸+p`þ(8/N´qSKõr¯ëÿiîO*[JùRJY±&uÄÊ¢7¡¤³úØ#Ô>ÂÓXÃ»ë?APòCDÁDò\$ÙÁõY¬´<éÕãµX[½d«då:¥ìa\$¸Î üWç¨/Éè¶!+eYIw=9ÂÍiÙ;q\r\nÿØ1è³xÚ0]Q©<÷zI9~Wåý9RDKI6ÛLíÞCz\"0NWWzH4½ xgû×ª¯x&ÚF¿aÓè\\éxà=Ó^Ô´þKHx¨Ù0èEÃÒÉã§Xµk,ñ¼R ~	àñÌóNyºSzú¨6\0D	æ¡ìðØhs|.õò=Ix}/ÂuNçü'Råìn'|so8rå£tèæéÈa¨\0°5PòÖ dwÌÇÆÌq³¹5(XµHp|K¬2`µ]FU~!ÊØ=å Ê|ò,upê\\ ¾C¨oçT¶eâC}*¨¥f¢#shpáÏ5æ³®mZxàçfn~v)DH4çevÉVªõbyò¶TÊÇÌ¥,´ô<Íy,ÖÌ«2¹ôÍz^÷¥ K2¢xo	 ·2Ñ Iùa½hõ~ ­cêejõ6­×)ÿ]¦Ô¢5×ÍdG×EÎtË'Ná=VÐÝÉ@Ðþàb^åÌÚöp:kË1StTÔFF´`´¾`øò{{Ôï­é4÷7ÄpcPòØ·öõVÀì9ÂÙLt	M¶µ­Ò{öC©l±±n47sÉPL¬!ñ9{l a°Á½!pG%Ü)<Á·2*ä<9rVÉø\\©³]îWËtn\r<ÄÞ0£vJæ ±Iãi 1½Ys{uHÕ°?ëÛ®ÇÎUoäAßr`SÿCcïôvË³Jéc§µõÔû=Ïã-H/À®Øq'E° ïw|ÂNß{\r};ø>þxèrÛãÁu5B¸*\0¹àìÈM³©ïÚaîí\0à{HU·öçCâ¹Wå»³ÉyB'Í<Ç6ó[´s¾Ùíyÿî¾ë»ç@Ùï{ïQàü>?/<µK@  À¨B|aH\"¾ R	á@>~@BhEL\$ð®[°Sa \"Ð0ìFe`b\0üÀ@\n`=ÒínÚù.*ÌîOèÏ´nï ò¯<jO¦lM\"mRÊÎð/±¦*î&TèÄTû _E4èÌúÏ8Üðç|R0*ñoÖÊBo>S%\$ª ÈN¸<î|ÎÅÎ¾ðy¯7\n§Ì÷íÞ´,é¯¢óðú°¶ì¬íPtíÐ\"l&TîoíE05nùüão©ÐrøðväîéÐèùÆÖ£BpðòpËÏ\nÔçPÙÝÐ.-,æÔq÷ÀÖø3\r/p°Pß b¾éÆÁ%mÐèÎP2?P°ñ@ó°÷0(ö/gpz÷0è`ÜÑgÏð×Ï\\å¬³qòñ>øpú@\\©ªuë@Â Ø\$Ne°Q¦úçÌè0(A(¦mcL'`Bh\r-!Íb`ñÛk`Ê ¶Ùäþ`NË0§	§Ð¯nN×`ú»D\0Ú@~¦ÄÆÀ`KâÃÂ] ×\r¨|®©ÀÊ¾àA#ËöiÔYåxfã¢\r4 ,vÌ\0ÞQØÉ NÀñXoÏìí´© q©'ª tr\$°änpì6%ê%lyMbØÊ(âS)L')¶Þ¯L²MIs {&ñ KH@d×l¶wf0Éíx§Ö6§ö~3¯X½h0\"ä»DÎ+òA¬\$Â`b\$àÇ%2VÅL¾Ì Q\"¢%¦ÖR©FVÀNy+F\n ¤	 %fz½+1Z¿ÄØMÉ¾ÀR%@Ú6\"ìbN5.²ä\0æWàÄ¤d¾4Å'l|9.#`äõeæ¶Ø£j6ëÎ¤Ãv ¶ÄÍvÚ¥¤\rh\rÈs7©\"@¾\\DÅ°i8cq8Ä	Â\0Ö¶bL. ¶\rdTb@E è \\2`P( B'ã¶º0 ¶/àô|Â3ú³þì&R.Ss+-¢áàcAi4K}é:¬¾àºÁ\0O9,©Bä@ÀCCÂA'B@N=Ï;7S¿<3ÇDIÚMW7³ÒEDö\rÅ¨°v¹@½DÈº9 ñl~\rïdö5z^r!ñ}I¥íÅsBè¦\0eTK!ÁKUH´ô/¨2i%<=ÐÆØ^ úÃgÙ8r7sÒÆÇ%N³»E@vÃsl5\rpàÇ\$­@¤ Î£PÀÔ\râ\$=Ð%4änX\\XdúÔzÙ¬~Oàæxë:m\" &g5Qn½(àµ5&rs N\r9ìÔÂ.IY63g6µ]QsvÃb/O ò|È¨@Êy§^ur\"UvIõ{V-MVuOD h`5túÉ\0ÔÓTõ,	(ßê®qRG.l6[S0@Ñ%´¶C}T7æ85mYëú)õ8ÛCú¹râ;ôØ¦)´M+ÿ4	À ÉÇ4õÌ|©Îª1ÔZJ`×5X,¬L\07T\rx­çHdR*Þ¦ÛJÐ¦\rØõ52Àð-Cm1SRéªT`N¢e@'Æ¦*ª*`ø>£\0|¢ðI!®E,¨ag.ËcupÆÃ9`B¸ªaa¶¨Þpê`¤mî6ÒàR~\0öàÖg-cmO´ñ1\reINQNíqo\rþnqÜ¶ôR6ùn´Snít¤wÆÃ¦\r ]a¶¤-Ïa*¬¯\\5Wpv^ OV`AFÀè3#82põH'J'nM('M=jÂ9k´ZbBn<î@<ç \0¾fe¤:\0ìK(úN´õ¼vðõïí-!é1¶ÞH(QgôÂÂµÉy< íd¢\\¥c\\òs,uÖËq0­~¢i~ëÎÌe°Ñ¶¢Ò*Ñ~öÈ ù~ ÆMØmÙÒÓ}WØ\rîÄ æ@Ô\"i¤\$Bãòácägä5b?Ê6!wÖÓ+xl1`¾`ÞÁ	sØ ê÷î÷¨Ë.ÇvCnhEd QÓid\"6µ´`¨\"&fìxµ(\"â2êQzç\$Ä[0%±0lw u×Ú>wë%Ø±µ%»w²ZÌ\"-ÿuí%ì÷ó¤Yg±þ>x\\Ë-ï×¤¼àà-v\\ýx^'M	PùÌÝYPëìÝù)8û%C§@ØDF ÌÒ\r@Ä\0¼\\à¾0Nâñ.S\$¹YIÕCI Øi÷>xPÍ¸Í¹:Í·ò=T,â'LìùÙÍqÐQ2Í¼\r¬ñÌÒÏd¼­ÎÐÙÑ@ÂÑÐÞÒ9F¸`ùOf¸Ow¾\\hØ=¸}SjGGWAíL£RJ\$JP+Ó7§L¯v,Ó(ÌµÇZPìg¸ÞÔÚ&z+ ÞájàË7 Í·¦-vAÃwÏh Ú^9óTöODùZºC¡mù`OÀR¢yÓÏì!ëGvzs¥G\$IhYñçÙý58¼¹xFøõ§ø«¨Y9¨©iÝ8´UöCÚ[Ñe««Zq¥uA«Â1§Â?ùÙÌ9!°½:Úì¼Ïøb0{\rúQh`Md7á{2 Û²8ÖH`%Æ¸ºã{-ÁlÊCXk³H¡ÓÙ|\0Î}àX`ShÕ­XÊÖ»\r·æOûy¸X¸¤¸ :w7±·ÚÄ×nÆé²Ò#û/¢:4(Mº;­¢øc»D£z;¼Z3¼»¹£½¢Ò]¶ç Ø?.ªÅ¹\roØbOì¨^`Ïº¶|ªë×÷ÛÉ/ÙX×]¼|ü^!%XÙ½³8ÕÃÜ\$Ì;Äáz¹TåªxK·¹-~² 8X)<!Öèyïx«9ø¯ú·ª:ûÄ ÙFú®xz+Uàºÿ¶¼úA¬E; ª'Å%c­ùÛÅYßª³ªüw¯<{¦õ9úøV:ý`ÍÊÊ<ØáÁüGØ¡ÇYõ¥\0åZü÷U Zq\nmx¿)_¿}YÇé_©z­ ù­y\rÒYÑ,Û3LàÌÙªÑY²ÎÙ¸Ï»>MÒí	MÍ	ú)¸P\0u8 S!Z{Y¼äÔÜ9Î¸ÙÎúfV3oõOÏ¼þE½Ð`CÐ­ñÐà¿¿XU¿Õ}Òlw©0´}©­ÒÌÍÝ7Y3Ó¬ÓÁ4ËÝGÝJÐ¾&¹Ã¤ÙÆÍ­(ÙÎÊ-AÖV=f|ÒØØ@E/ß%\0r}þÞ®nnÇ\0äÇLy¡¶<+Óàö_¨|Êë#AÅö\"C[yÖÚEW³érW²f(\0æ¾äÐ>À)Â ÞÀÌ_ÈUëÂ,UØ\\ù#ýe½*r`ÜNY Û*£=aþ\\Ö&^g4ÞmÃ¼íç¼ýØe#èî^°|Þ¡QXNÜçæüIç>©ç¤\0rÆþí4®^YèV#æ)éþkì>¥×¾ËêÞÎÞÔFÀW^è%¾Ý\$+ÕPÕkY*u¢~ÖÖ,¶ÅMÕ×WÍhhGÀ¿K´\\C¬é¿7HmZæÖÀSZ_UÖ%æ\r­õb)õ´gg	qñûíöö@@ÕÍëóÝÎtä\rJ°ÇàÛÓ×7sÿ¤¹¯U¬K_1å÷t¾j&SåBi\0ØÂØ &\r¬ Ò`ø:µjÒFÀ~=TÌª¢¾gÏä¾íö!ûæ^hÃ^í×ð÷½ë/[{ùB¡É(æ/|ÍÖÈgñj/Èd\\ÞSÉï­9¡ÁG`u­Ì1ÕMÙÊ?Éý§3}òQ\$qIòm~°G=êoVz¬\0_põ§´!tár{ÛÒ^Z&§ü	îüuÓX¸ö1@ÀÐG{äøõÐ¬¾	NIÒäÓÂô¨\$=0ÀBu82ÆS\"ñ6¸®Qpjëov\r<®ÕÉ¶U¥\0.ù¯Õ¨EÁMÂ\n8VÒoQ\\`?à¼L6¬ªÌ=\rl±¥¨¶±ìÀà\"øàëõB2puì&\0åëÂ5¤\rj¥ª0VËA¼µ;v\0eH;ÊTJ¢Å6pH?/\\àHµ@!pp¸C¦Ê+5\\+a8;\r(*³TÇÆ¢;ÉO|¸^Ld&/¨ñNI¥TÈô|#ÈïGá©`j%ÇäDÔÙÛàZÄ¡4Énii­ 4·ó]@tÆÆ#5cõÄ¾÷ð	ÕZ¢RñyR`@à¤\$I{zÿèïé4| ¼¦×ªÜ@=hCEöÕH¶, ,ZîÙêiµµKºÃ P¦|,g°z*ÊÆñáE)AjknK\nºÀC\"J79À}4f¢*´4ë65¶Ãê­×«Q\\¡ÞcMáÑ\r{*Û1j¯­è­lFmð4¬ÅM¨* `âX¹GÀDÀA-qqab´±1ª9RÉH¾Åb¡g8+¬l/³¦äô¹Åæ ( ÊL\" 8Èíè0(Dc¿#ihc`Ç8±¹A1\\ùuK(Ð4!¶dÒ3¢8ûÎ¾ÑÑÆ®4¢j;#¯Ãñ¯Às8ÀÆú5,ucncFöNpPa8ÇG8êrKÄÒÑÆÇñÏÚkiÈË4A	£8TÒ¨Æ26 ;*iãXÂ2%MàBJG² &íC*1T\n4	-#è.×%ÆÚ¯'z#Ê8óA+@S.0Ó×üõáII`UºQ°ÎUdd\$)¤È*]¼ÍíTééãÆCèè9M*ð	\$b+ù·Ñ½ÎÄydt\0-ÂùLü8\$Âe\$¯Ú<AÉ!ëd\$p@]d£¸ß&ÉM+2E´yß((_|ÅMdÀvU9!ÂeD	Ñ(úªW=òÆ#øàÀ_é'´bN;¡'£¡\0²O¡<LiAÉØ Ð T¸£¡\0¾QÉJ# }Ba(ð/ÊuGB¼%-)Êhòu´¥~\0IæU°Pr+1©ª¤¤%51àÉL`ÜE'(/ÂQÃ¬£%TÉ)9ËOrTã],Ù?Ë<ÓaÕ	¯Â/|À\$Oð@Z ÐI®XN|±%,¹SK:]ha%¥ª)kÊþP\0,·¥»'à0J©:Òÿ	äÃ&ô¾ÝV£0ÂÒújÙJM¡*x£ôP)¬jKÈR û¦\\\ru\rÛ(ÃWÔÙáF: kºð\0ÆNJP!Q2 'H *\0g T|ÎÀª~g`D,ÍÏ¾\0#¨	³;(\0 À ÌõLôÕf¯5'ÑÖ`'´Î&t(LögóA¤Ï\0à'ksiñø&àÂødómøºP\"Ng`O&ÄË  X@²	£Í%shôäg_Üsb¨fÏ5ÉËM>s3@TÝç77À+ònSdÓ§5'6s\0\\Ôç\0O:ðNLS@ Pæ{;9ÚÍ¶pFÏ@78_l³9°\n¦)Rg³9@aç:iÛ\0þvSDòg®ú\0¸SàÁù\0øsõM\0BÈ\0+Oq`§×>ÙÄ4	 T9Ûç7=°MâvÓ=qø'y;à'LîfàFïf´) ÏwP·TÓfÍ>\0¡O|ï?0)OÖ~à|Ìþçæ`#N´ \0¦ù>'Ïª}Õ ¬Íç>ñ¢~e	\0? *P3¡\\ÿæ¥@ÓÍô5\r'¿CðP O¡E\nMBÊ#ÐºT;µç»=jPÞ49¢¥çùEz#NÆÙ¢ÎÀFYÒÍÌ\\½\0CA QJTVôí¦©­é7 \nvÓ0@_®Q¸LÙRRc!VÀ|zÒÍ6¿KKÑîõeS£4¥É\$aIª|PA+¸.qKD-çS ®EvbCO>¡H¬ªÙ<áí\r#äãLPÜsâ¥ºPÖ­20 =¥*ÀWL2dàt¤ \0Ø!ù<	àb°q\\pa@Rd ofKM ÝÓp ¡±§\0}Èöñêz\0Ñâéá2Õ¦3\" )@\\*gÓrM#!Å<ÀÉOXT\"`\n];S CÎ î5ÅBÍcPÓ²  [¨¬É\$4pÔ&¢\"¨àiÇNPïÓ Þ'J©\rE&8zÔpÛ@>¨áBRÀÝi\\¨uD*vzºSÄ\$*´ÌTZ¦\ndÕ6iª¢+J¥D1I:=ÐPÛäÐÀÉÊÍ\"q@| p¦övjoT@SSÉÚ¦ìÀ*ÏÃ'8\n#äùÖ +`ÉîªìSC!âè:QÎjó|ãgXÑÏ°dç£¬%aX^OuGò¢eø'óp\0{VúÐ\0°gQWxXæZo>×B'¡= 'Lä)v Õ\07Â1L¶ªkêT»BÓRt§GÕ,Ü §RMZRWL©UÖ	çÅK	ÚWngl,T¢PÈ\0­:§`*YtSW\\à`\nèÀS_È_Kîkh&[åª5\\Ôä\0ºÁU¯® þÛ\0î«^\rrC\\±;Ø5EÔûÍ?W%à:¶\n!PZ£öÌBwWW¤\0{Å7ê¢\$ò+[Å1hB ÚÐ\0ºÆ63.kw,l0öØf¹¶:¥5cÐ2XüFõ*ò­j2Úê©ÃeeeÛLº·&±²}7««,2¾Áß¯Åy]D\\¢Gå.g2Ä8\njÀ´][]ÑM	dcÔ}{ì£TéÞ+xGÙs,¢\"Ü:vQí\\nÛH7Úyu~ ïX=BÜ*ÐdQEsëMÙæÏu?Z¸FHü»N¼,ÛD_±RÌ\0Mhç/~­y|C»V©ª^5R³2%ÝZ¤ÇVs*lo{,[vB©µ1\0Æ¬>×ôì/ºèëZéRa\ná¶áëE¼ AØÔ*Ãa¸2Ý\0aËë@zç±\\70àar©­Ç©ÊvfXØÂ°g8èZ^6g1ÀN©o¤9%÷S×ît\\çOHHîÈ\rÞ\n·ðW^&ÚA¡ö/°å4­{2<Òv&Xi½_òÕÒ¬f«ÅZÏVÐ·\\tà&ðð0\0¦gbí;JI,Ë \n) .R«\nT-yDÑKxÂ¡_ /ç¶x:¥H®»W5^UQ5ÑësUZIUwfFù¶£¡RñÇSÆÔé±}DnzÛ¶FÈ´­\"\0\\ó1\nvÝ÷W¯++g2ÑµÝSRKªC¶bÁLÂ%&=7ñ\n	6¤Ç\0r )ü\n>g1¼ÑÞ8)Xo³÷à\$U;ÄÆPèV¯záJö¿}ãH;r+Ñdx\r-ìÖ%BA&\\;Ìå'ñom|za<nÃp =È^=|µ8á'@`6·¶teDðá)}q_d9\n	A#}µÚ_0¤Ãm¾`å{âê\rÉrc>·ç¯~dß°L§?b%÷/}ûú\r¾þÃ>³;\0\\£`ÖP\"-!Ûn!æ¡îþ7ßÏ¯ö71Ì¼@¬¼MþÄB*ùA«jnÌ\"`jû·òJÒ³°°1 E§1m6¥ú/GÜ_»­b s_zù£'~Î]L:ßæ\\ùÀÒcFa-8()éGÌ©añ®f,ÅÆÁFp¶Eÿø?¿XÍð)2³³Ö\")pi»á4½1ÐÂüªõÃ½±^¬¹pßX5ÒÀPßíËxòÜwÂ	Ïï·~@É5w ¿óÇ±&¬JáÁzÀjlðdÌ	`P¸©Æ§²\"/d{8¤þÉçXQd)8ÄÖ	qx3àÛÄÅb{,1àäqMÅ·>D\\X3gãââgÅ¤níI¢bá6xxÐÒ3¢büdnU^!&|k	9=Yv±¿LqÏ\"Ç:²ÚDÚ+¼ÓßwõÅ×yÜ#áÖþêë»Ìt.òX|DÞxn÷ª¾<qI|HbùÄýr-µänö°,ÐË:aÛÝ^Ú.×ÆÈF×Äü[_Ì­÷¿ªú/Ozÿ8ÙÌøø£\0Úð;åÀlÉHWÁm´O(¢½¨W¥É'@¯\$È°ôÇCt	>`tQBÃþ²÷Ä¤lmÌ8JâO¶m%`Å^Kp1³ÅQÄá¡ï2KÓ\0ü=¢Ëy)2×¬µ4±PÂ7#È³ÅòÒÄ-TQ÷>£Ô.%\rÜ¥ÙÒ¨²®ßkÀ¸6@Þf@y¾Ì^ô ;ÌðÈö¬áHKÃ>Êh)\"©R2E(Ì ³ó-H¯0e.p³ì\$¨Ñ-æó7%¨)voÉ\$/¬º+.íÌÊ÷ce;Ñ^øÊd­Wyåöv¤@q 47Fy;ÀlYï\nxJå§òªóÐñlÓ=ÌxÊ&-àò]ü*d}WA@.OMÇï;<Ú©\09HlàD+\0¼\0Ñ&ÚUR9øÛÌÅÑ&Ð&`cÏÎC¨DäÚlaÏ}Ü©¦=h(öD@ÐGÀÂÛhP¬W«f@ðoB'ÿ@&H\0 À;è;EºN´-
ÌzhoC¹9Ðø4mM}lë z&4v½\r{EzõË4`?\rh{F«Ín@Ó¥B'ÒÑdf\0èEòB#xZaÓv9=7`Ê¨°Ù\0âò\rÿWý½¸³>jª\rÙIéùf¼!Ðæ¿À°Ò+Sÿ<eÌÔ6æiìÏfºòTÔ¦?|û¾jHYéZ5Sª9çêð6KN§G}+êxàBðEn)DhèÐÓøn[1¨gÌÊ¥_Ù}ªvûØýU§ÐIX\0à:kSA@R+üûVÍí?åbÂ«åBÌÖjBò7Aæ¦Xp6j#GyñÔXÍ\0æR¯èXf6ûC@r½²âÍPáÚ×Ö¹uødÂà&÷×&@ó¯Òëü¢zÈa&²Ö%|hÞâYØGð/±ÀØìy(Å¬ÖÆ@ß|,»dáÙ5Û«0leV@Þ·=}^[+ÙWïlR±åPNà,Ù¶\nK´Q\0¥\\0¤0ÞÎCµ|ìÔûi»,Ùö0Í³]|íOg¡ÚÙÙ©¶²q'ì{ÑUD`eLIPæÚ!Wö¯ítIû_ÙCðbÇýKióixÿ¸Q;Õj¢zÉ)¹8 2å¥úb´¨Ð\0ïFO/%ùcªývóL!öS^\0[ñT!ÛÒñÖà=hÙ`F\0D@Æy2E\0ôÚA µÕIDFù¤nf³M©tíf¬i·ÕQb±J`êO\n©¯zÇWV&¡û(æÀ(PçÞYGHåµ®T ûÝÜ¡GîXpñI-\$æ@=ï,L\\ø½¸­¼o/÷TéªHêÃw!ÏúúXMz¡ÇãHÙû:wK¦-;<|×{ÂÍo\0uØXªdÜ?\\½fF{Ë¬IyÜ²öI/qÝ¯Çè3¢(ÝSÿ@\nä½¨Ìà¯­w¿	)	mÌjÊÄïTij3ûX¶Ô~÷â!\$¸@»GÆ(8oµÜ)ÍS!o½ä`{¯~It¹ªë<>Ò8r7\nmÃ|<¥¡?EàÞ7\0»9WðæVk¾8â¾û£ìÊ÷%ºQ¸aÜs}ÕÁÁÖ7±Ú61IÇÓMÉ]Ý/´0N#o«N4à·ôÅa-Ï#x×næ÷N^bmÒ¶æuE¾PækT\\à/éÈðzõ÷¶áw³ïÉ^Br_¼-xöE3¥ÀN\0+)··ÉíîxS{Þdó,×üË%ì­²»ÛB¿%´Âá½óÊßÃ¬i_¾pW¯ H,OãzLoÜQQ7¡q<FÜPz/i/3v tÆpñDÓlíîps¬©ri­k²¤W pÆé:ºpÁº½Èf°¡}\r8gAü +:\0lÈ®tð¨ÔS]Ãå©§Î½\$/dt¦¹P-ÞU¨Øu¤D¹÷Ê>îëÜ¶åÀøWT®eéãü¼½\n'Âp:e.÷?2øÓíGrS?<àvù9¤åæë{÷2R¹¥­ó«ºRÓ¯X8%Ç!lZât,¶M¬ÛSfäKwÁÈò8_>jsl\\Ýç77ðgÎ.ó¶ÝÈß:^ePöpL¸K#@õ`\\GLð5Ù¡%îðB@Õ'zQm¾íÿê©m\$ºö3âô!®\rV\nÕ	Ý\$íYTç×02õÔýwÁ% ËÔ,S¥]¸­ðsà:uó¶dc\r¯3Tá»7e(Ø¿vOr/õèhe.&0ºûÌns#ºåªïY>#)ù­¦c_RÑq¯§Ðx\nìZRjp5ÓránðQ¡Ð÷?½ =ïi\0x*¯-ÁàÆ{ø.û6F?#1G|à\0.x#*nc>´`8k£%`S¼SáA\rÂâñ0ëü7°\n¸àmºþÚ[ÄÞFõ;èFð\$\0TÛkæ_¹û_ªnvè>8´ÿ¸ÂÙ2Úüµ@íhÞþWÚØì©,mb*v\"ÇÛ0yvª¤p [<Ò#ç/®!+{ ²7v0ùO®Å_7ê·»¨@Ìø\r5~\\FÌ°ÂÚNºyï`Öz³}	²o/ìÃÐ»UóÑ»æÿ8{®#ZÏ£öuéí§íÁ¬·xkÓÐ7'{de¶©y\\GÕôýúÔæÄ¼ 1¼  d\$@9\$M¿w6ôW_dÙ@ö\\= )wgsPÈé=Ýÿþ¶=së¿^ töÈz§g¿hí}C­¼Ëénjz'¸{YÖwiù_ë}×ì4¹\r¼3Ù«\"g¨9óØï!rè¸:y÷§¿àº û%ô¯ºëépåCõÀe¬×¿3 \nwdÓÇÚ`pXR`ô]Lå¤ÃÕú¹eÁjDjUt×¼Áw±>Ì_ª«+±X=·Ô^BÆîÃ\rÏKþb¾ßSd4ö6I÷ÏP´,^9¹®ýÃ¡u,}0Å·x¶÷Êá\\ÇD!üÍÊÓ:kë>`1è`:°ºPí_\\To×\$F&ýT i0Èºý°_nàJÿ÷/[	~ïÞ{÷ÀÛ·pÁóC>õºnå\0¤Àf×@ØÉyÇýóä[?®DìG\0¹©4Z\0Û=õÞ,¨!ÀSÕù¨I\"ßÑ²ªÎF]½õ_%ùkß¹òï_\réÜðä;1¶v?t¯T®ä\"^ªþ8mÃêeç^S7!È÷äÿ«Üþ@°F	³xÁføÐ^#ó÷'¼îoqûÔü¤Ä¹äÈwÙæÕ¬E¿ë.ëóO©ºÅY£Éïþô(o³è¬øÊqNØþÿyÄ¤Ïp[nw3:Ýë´Ñ;L»Ûîòõ\0ý;*õP6)½*È²¿Òè¦îUæéÐ´=Ô\$ò¢\rV%\nRRA}Ô\"ÀfªP = 1Ø4É=&:>\$^RMpö§\$IL\0ö1ñèð¥ÒòÁ}Õa	HÀz	¥²	£@ 1ÚVÉ=&L	A	@ÌºT«îí<\r©^ÀjÀx.°%¬p.ï\0Q¸£9\nø(à.	Û@¦`(ìJ\0Ê¦>¯ÁÔ\n÷!@2DÀÖ´\$iE\0f¸@v\n*`£¼dWás\\à|À>H *©@ª¥ñQÇjÚP;À|<|!IÀÈ0#@=)ÒkºX0WÀ®1ØÝZ@æU,D@ (P_@t¡Ûp)AÁ/°c³0-AmäÉkÁ	P2ÀáBQð5T1A%{±©©=\$PI=K(ð°TÁV¼°*@sAÒ¢ÑÁÜ¦7@¿YÇÁÌ\$\0ÁÕ`.°9Áß¤ÐA\$ù¨Rp{¤ü¥PV<Çô(OÂ#\$ po¥°*dPNA<`Ä AGÂòð@TD\$©I¥QBLN©C©=\0¶åÿ[°<'A½	´Éi%ôÐ£\$BWð6%S	\nÐìÐL&°@}ÄpA	\\ãÁÓ`¹Â#t£Ç­Ñ	ô,p&'z[¨ÂC	`5Bº¤\$`áAtBS07A¥Læx	ä\$æ	û àÐbBA	/æBöU,,0\0Þt¢2@×l\réRBÓi/°¾ÐL.rÂää._X(Â\rC\nÌ/¹B\nôAÅ!02C5<,ÐBÑ.Ð¶<xðÀ-tê]PWoÄù N	ààÖ²×@öJÞÐF@¬.þ<Ðá\nC¢Ø`ãl¹þ\\\"ç !ðø¿D;`3^ \n@°'¸	C`( (àÐõ&¼\$äê\rL:éþ¼ìÃ°¦óÛAjCíô<À<¦vQÀ0§\0CªºXH\0002^;]	~â°Ü°òcüD1(5Â(íä49ÄFQ± ÄMÔE1@Ü;ë?D\$käÄAD#DsDGDôIÅfDJ±#Ä±TKQ .´¢ÁCüH©Ä­<Gq\0= !¡jCÂýaÄq 5	°ï²LúM±=ÃÈÉË@Oä;­Å\r@åÉE\"¡iqÄgO¯Ã49éTÃâ+ðúD5¼?0ýèÑÿ2¨§AMD§Fñ7ÄäE7Ä\\üFð´R·dRQ6DÃ¼N1ÄZÄVr³A+vE\r\\M±DáLV1YÅkOñcÄkÜYqXE|X@ãCÂ2ciD©lLQ^ÅYà'ê¥QoEY,\\1fE¯\n¦ð¡hÌ]QdÅ]WÑYEÉÌF)#h×põÄ<|*däÅ2x¶à7Þ;¦©G^ã\"\$h%Jv\0ÖXâÆ/¸+±¦w3GÅbÓ3ïqüFq¦5D9YªÃ\0Á.@øä69\nt+SEÄ*°³Ô4\0DàºÅqSé¬¢x8±¤Ó\r|g@!_Ôà1ðÀÆ\$4áw­~Ðø1¤n¿r¯\0*\0#E±°0?	'\0d¡ 	À(HxàFÙ \$ù\0 Hþ`(¦±¾¦xGDtHë!\nÉ ¬ÆÞFJJ|á6à>?)PBQFãqÎ\$`¬\0>/jÐT\rÂZ\\sã:	(+ ÂEnÈ!Fq^µÀÖ¬s1n2¢«dD1×Çx\0a/Ç~Fá´|ÝxHà>4Zä\\À6ÅÐ_ì5z8ñyP?¶²4 7ê¶\0¦\$y0«Zî¶ >\0Ä|ÆæGÎÜs,¥ÇÖ ý±^DS|s )ò¾Xú5Àq1ô(LG#[ùüæ'HL¬i[½ H&H	ÜÒ1 S[&y°àK #G¸fðK!«[i\0#5Ðiü+÷\$N(î±É-JfðñË&9.E¥\$ÐT åÇB3!pñÖ\$¨0 í´ôrµ:eÇ,ºÍ[0ÉÈÉ1Ïº|¤E!¢ø\$,¡,Kä@; ÊÀ0¡l\\~\0¬L1Q£Ù\0%x@3ÈÁË× 2G`L[ÀÜ¯BÖÊb 2dL\0èÅIÁß¹Än+r³î;È\n:Èà7Hî\riüàí\$lOíÅI4Xd\r)D1ÔrÒ5)ÙQ¢WJÖè#±êH0Õ<Àú%hrEüû QH¿\$Üs#	Øi¢W¢f¨gHa8!qÌ¾ÜD ý	^HðT±Ù9%ÌÊ\0àÀõH×!Ð*JGiÉ\"¥ß,MÉ\$´øÅÆM Y\"²IIÂãX;É)(ç£@s·ñPäö\0ØÊ/a'POïðÈðÿ\0!`2®ÀÆ±ÌÈ,OñÊr²®Ëø-Ä::L²D~-ÐC¢_ÉFèR]HF 	òx9TâÒr²²Hb*pÉ[(ëÙ2´B\rÈT;-(üGÒ,:Ø|¤ Ö\0¬óf¦ÊjD<fÁ­p¬@)(Ãòi2ò(üOº¼ü[¯À¼èLè°Ñ2¥\0èû´'ôH1Píg Ä®öÉP\nÔ\0úÊµ%k]\0Ù(b H¦(øaHH# I@)Ò\0Ò)+CþJì\rl¦oN\0øÌ©Êþ¿¬©Á°	üng,0nòª+ûH\"I¶7\$¡Itªà\r <Øa0H@Êå+¤¬¤)Ød°!aT´rÂËD«±rÕÙ,p\rD`MGXÞü­N=5 ZÆ¯><£P/ì^g`Ë*+uòi%m%Ó¦âe'L ÊÔÒ\nK.QãÊæ),ÁË«.Ø!Ò]K½.Ô§¬gKº\"4¡\"Ý(¡Â\$y-Ã¸ )øW¸rïÊ{/M²ûK¹/¾ýc/Â£R ,E ÃQ¢A\0®\0HQ°BL\"Ò¤2/\$ºî¿%/ô¾3Ì/0¤¼2<L,Ê¬s\r@	-óùâyÈ`;ôÄ2×\$N|Y.¤Ãoï0ÄÓÌc%ÔÆÒkË¹+¼¾@Úf Ü²¿^s/êî½´ÈOêÌBi%M×\0×2º\"W¿þ+A³!\$)ó%ÄÈLª2¨9/0Ë t,Ì33?'ÌªyÌ¸CófFûÞäØÄ§\0T åØ6ªpÈ3òª2¢ÄÐÀï'ÚÏÞëöÄQ#ØÚàýH\n¾ðUL÷¡/¡ÄÛ·\\X	ájÀCãÍM\$µùw²T\0ò)4ÑU\nLkßO{\"DÔ!Â\nôÑ\0Û¼h>!?®G5[%¤òtÑ_(kQLó>ÖÈ«I¨¼áÉ\$,fÈiä¹çñÇ¸ÏdÚ²Ç¹#ºslµ³à\n²\0IÒJKÃé\rÍm%09Í³Þ÷*·!­E9Ä­rµ¼*i¯ÜÖÌ2SÄw\$|\0èJÁøa¥j[+£à)ÏdK¨T #F8(¬aÇd§Ò/I.,MtHØ1ËP!H)¼ÑóñR°N<cõoJêÔ¨ï8¥j~É¡,{üZ¤@\n?Á8q²ÒÎi9ÄJä\$çIÎ8S´\0)GC9bó£\0îÓé²¦³Y%iv\r©¼)0à0&	TªÜ¾ÞLºGô|§Ð\0Ç-q2ÎÇ+d¾o©8ÂKL-D¶ÍDÕ%<¬³t^s\nk;Ä­&o\"^8ñ:ÌÝëP\"	é;s[3ÃNã5° ,9Ó5 ,ÏücÚØr_±ÌàYàc-<1Å\0ªÏFd°±e-,ßÓÎâ¨¨Z§-òÁeH-Ìÿ=±îÎ_-µÓÌ_-r# <M-BÓâÍ>@\rãÎ¤dØÿÀ:È5³ÌË.@9Ï­¨\rëE,`(81ÕÄê¸­^Ë	P2\"¦+q-N«&Dâ&!9\nô+4éÁ¤HT\r|Á§óê4Iç²#Y°Z;½w.ÄÁµMÒ¼b·Üä»KæÖüsÉ;@«(.JþÝía\n\0ILsb\$ÖÉÉN\nÉÈâso	_3ó8Ð5-tôÑÐ/-pC¯¥;AÁ´³)ôµÔõ?Sùò+\$1Ô4¨è³ÆDË\r]\0002\\\0Èü£ÀÖL¥)È\$¾2\\À2MÍ2«Ñø(¼È@ÛÐG2àÓÄw>dÈM1PÓ8ms!W(¸kQQEHTÏ\0\nyÂ`ÂÆÔÅÙÓpÈa'ÿGB«'ò¼sêHHT|u-×LåCØBñÛK°\$DvÅ¸¦ð3úË°A=¢W.l;!,B»Pé/ýëîËbÉÁjÒL/?¼»ûô´ñá«5T[Qp·d»[D¼úMQt?Ý´^ÆÍEÔQQ[Dð?T_QL)-:QEUÀ!¯CF\\ÅïnJ½LÎðÌ¡»ÑilQÄlÈîôø\rÑG\$ÅrµïEõTyÆÍFíT{QÀ4gQCG(9xQF=ÿÑõG\\±ô}Q¯H`7ÇmÄ¬ÊÌ-!ñìÒ\$À³ûÒ 8%TR,R\$lÔmCÐÀ¤XdëÒF%\$ ÌTÌÉ Ñg2VT!dÀ4ä(kT=²à+À4ÒZÅÀ %ßÊ~ðÉ£­TÀ¯¥+R;¨80\rÅäÃËL^QZXQÔ6ë8}¨ÅI9\"dewK8;tÄF]-JOôµÑJa¤Z\$`~ñãVÂñ\n-§0¢AKÁapÖ%X\0ë&üÈ)8ÛZ ;ÏÞ0A?IÀ¦Ø#\0ãGà¨M3³ ÉFü¢HÄÔ\rñMõ'HQHÈú\rxòV^£3ùM MÌDo0\$ÄÁêÉ0ôyÂR;ìÞ0I?!(DRÍ°¯(	ÚGºO®ó3\0\r+£È`RLø`üÇQIüóÐTr½Q\0#èk@ ÇO(aÓCÐ` mjÁÁøùÀAÌà\0>\0F%)]HïÜ\rÆ¶ÁÕê] ÉB­\rì3°Âf4Ù&\rRðTjê\"Rð]/dúÞ?\n`ÃÄx\n@ ¦r?C@(\néúT4\n@à&\0@@	ræ\nx©ÂÊ\nd?\nÔ\0§PòzUOÈ\0\\à ö (\nàÆ'æ`DHdÐbÀaRD)aÃp1ZÛFì'\0®\rO¸´üÓöâ[áKR÷PE@P´TU@ÉTT}Ag!¥¸@ C©_vûs­ji¤Fæ?Xý \" ?*0ñ¼&g!õ%Pm>ÐÝÓñOÕ?.Óþ=,,ðÏT e	¹bÈ\0^ì(9ÆqO}uJøÊªKÔSJJP/Ô¨	åJõET³TeKÿT¿7él\0Y¸'MTßP´úU)\n¢ZµMÔBÈ\0Õx	åWÐ»¥AWkÃePõ]ÄªU0é-*ÈùÀðWðà6Õ¨3\\;¬ÇF=VYõNå4=õhåVZ±%VÍ[`6U»,A!M3â)0­´Ý^`9F(ªÁÕ|¿W¡xèYUûWiÀ<´©WÚ5NH­^­ÕîÕaÜÖXaH\0É?ebcZÀ·XC¦µÖXÐÂÀ3¼ýdUV+\rdT-gXÙyµV\\dIVD¥iµzYXSHèVdA¨ËWÕhg«ÖÔbÚç7È9+\0«ÖEZxõÆ}jc@V«XåiÕ­\0[X¨P¯ëZíjìÖÉZª£5³Z¨:3<VemàÝXý[Oî4Vh<U¼L[­hÁM¤\0OõÖ&öHmu§,j/crÖ+(\ruñ\\\raf6VZ°ÉVíæ@7\0[X½,4ÓÖ\"ÁéÝ?¨USZÕL«ÈV`\roUÊHê¬òçý\nM<r`¢G¤ð@/Å\r]K äWlPè%C75ßE&-ø\rÀ<Ö`+s[â·j§ãîÜWÕé\0Å^¥kÊøórZ]%^ <WÁ^Õ{ë.5f1WÉø:ØWZ¶À&9^µwq:W^R½uú×]©75üWy_ÜPÅY×`Zþ5`0[Uü×o_ÙViµÐl¬Õá×q`=V×~¾ìê@À?+_³*6X2BßC=GòØ@,VØ5^Xñ¡¼X`ÅÖE\rBx!Í2Y ¬¶´/^=y6×_Ù64`my6Dhgi¥z)5C\nÝ¸JÖ[bv\"Ø¤Ö_­v.Ø­\\¥[Cç\nÜN!%5ÏÇ0e}AM	(gò±b2dö &9a(\"QFÎíc¢¨6\$Xï0e¹ö\nõjÀØVôÔVAI½ sÖCVE`â>|kG©ù5tr¿d°VLµZÈvNY\"Erú÷eÀ3ò\r #½XUTZ]Ýe\$Ì^\rsÖWæ}y?Øa`âV¡aÌ¨ØJrüX^]ÿ×f%e¦wcxCRSX+fwVi5bU@2Xxõ6;Y]øhvpÙ,¡TÙÍfÉÄ]vlÙÚÔGj~¹	g\r^ÙÇ^]!jXãf}~öqYÑ2\$\0¨þ ¬ÌuÙ±gÍK®×Øm}Ö|Yõhyv\0Æ(}\0007IàÀ+Ío,¢\0ýØ 0fUá6ÙÞ©Ú,	U£6[hë=AjÚ@¥×=cÕqÖsc³ùì®5h°ñ\n5d`Xç5ËBöÚs5ý¦`ÚkaTVQxÚiüPÖîÅaBÚ£hõ¦Ö\$6m|¯Y&µ¥}®j?J~Ö¿73QÕçZh«åÚÇXà5Øeh\r§Â>7-ymWÐ°ízµÑ´å]0i®YOgeÂÉW\0IõDUÈ4EZèÕÏc(Íuk\0ÛVÅ³wD9W³.íkà>\rÍ.\0æ­¶ÎÖ¢ùõ}`<dGýZlèu¶¸Ü,m]¤ÚZ	WmµàÕè6EAdÚ2ü¡ü¡¼.Ý¶ZmÅµ¶à,Am¨[#è­Ïö	1d=ºVØsn­t6=Þ!õ»3[§nýµSV×eºÅõãÛUme¶ëNç^Ö¯kuvçZýn¾6ðÛo­¥`×oÍ¶÷a-¸ùÙræ¢L[couZø[ovúØÊ\r¿@ä[x>q6	Ü\nÃ( ÐUgwÜ+peÂõYymÝ¿	ÛýnÅÂ×\\U]ÕÀÖ\\\\ÝuÄöÛtä%Ãc5Û}fdþUz9-pÈ\r×?g}JÖB\rÈN`Ür5Ç÷%Éábùö²VÙomÀ·Ü3v·)ÛÒÕ½d%ÖÝrÍµw¿*axZ¡qV7)UérýÊÖö\\ÇeåÆ¢Æ´^vj/a>¾Gsu_W8\\Ã[h>÷\\Ëq²½f[mb=Åw¼'X[!Wû1FV³¶Í[9Wm]ñ5ríÍõ`±×r½Îwmc² /ñu=Ï¿sýË:]GrÜe4¾ÆYVi¤BAªÂ½oÒÙ7Z2A°ÆsKõ>±ª>èûà)\n²i#&{Q`±@%)rµNê ¸>ðücV\"q\n]vÚn\neÛ@\$¦ÞÜhB¬¡\0éµv%Û ÌÊ>èé¸§ö@	 °]=3\0*«R-ÙJ«T?\n@#ä>ø	 (]ØÝ×r]åvEàe¥wÕ0÷hxx`)£Qhÿ)Gþ +1vMâJ²°	éÆ¤;\\©ø?¸0ªÔºÈ@\"Ý´à+*>øé¢\0¾gµ(>-E@!»vÝwvÝÆ(üu@^mxÅäÞ'vmâ Ñ]¥v 	Éþ^?\"qWm^£v`,7ªÞX×&rJp­^zíêÞ¥x¥Ù÷«¬ÞÈ	à!3{W­ÞvÕÛeÝ¾£-ÜW\0t-ßÀ\$¢Û÷²vÀ\n ÊDIé^wPi¦ÅíW®¿vÕá\0(&â\n@,ÞÇx]Ý×½^Ù|âÞÁwØ7¹\0WÅÜ×^W}5ów&â(ü¯Þí|Þ`&]ëyÝß¸]øÕß×¯Þ?àÍAûxUéC°Þ½{uõw±^/}Eã7ßxõçw¬\nEëãð¦{yEåW­yuæ\0Ò?]ï']Å{ýÜ7eÞÇ|²j¡­_Îóåß?}\rôwæS¸­ÖWÞn}ç7yî\0 _Õ¥Û÷¢ß&-ô¥Q}Êk7c^1yÅçIýàyöW¡`{®÷¤Þt·mTSQXû·oF*·_)QªgÕ^Ðp\\©è\
0ÅG8¬½Ú&´? þ×m_õòê¨	©`~?¨\n÷m'£|ýà×±Ôî=ÙbUH7í1Ñ.'â²ÂU`\rã!ÞÇ{¥îØ'x&Þ1=ETaìièÌ«Î\r¸!\0«|]ÿ8à¬FTUúø`é|¢oÄÊÞýÞ©y\0ÄTô?ÐÿÿG]vÆ×¡]¸\\ª&­>ë¥v ü8Fà!]DW»GcPð	i£áOüUù­ßóD^|]CjÔ^(ÿÉèÝ}z¬ãý¦p^éÔ7\nx\$a?ícïÞDÒi\$?þ& \"Þ¦xm­ðýláE üWÍa½}~a5ðÐ °üê?ÆHCùCÆ?@ý@(aXja¶õÞ¯v® 8}­{*¸8â\n.\r»)¶8mî©ôÙcðbf÷ÈaÕÝõäÊ}\$\n¼â5Åür\0\0¯2jÉ¬&zàØ#è=C·¬§Ü­@Â?à	ö*ìäØ;_fø*+Z«n\0\$áqQP5\"Ý@0\\©íáx@MØ±¹â.8«b|®¸­Ë{Êav\n¶·bâ%/ýÃøaÏ~æ8içþõ>Ô/>8¹bÕn#WÜ¦±PüÒâ§ðü8¾§=|zkjTú*møâú üeÔû0¨õîuÔJ¡þx&]çE¸þ )KåÀ\\¸_A~Ðw¡c?Ð*ØÆéë8Óì(ÿwÐTqú²Ð­6ã3~6éÂ_`é¸ã]Èxq`µ-¸Iâà&6ØâáÙæÔczuå¸cw&-âÝ®ØáÝ¤ õïØæÀ?V3Øëâ*­@\nïbÉ¦6v\0¨«Î<À«×S¾xõ\0V¸\n¸£cìÞÈ	)ø¦ox6>Øj§\rÕñø÷cáh\nxÂáÎ?ªz9\0ä.øû`#=ù	bE}>À+ß@rxØáçw0ýòeàì0øa¨& ×ä«ÆC]Ùîx¢â_|,|\nÌbLÕEU>&o2¬·bªÖN)X	\04|À&kWÜÕbzXÅì×Óá\nf÷vdIw^&µÞÏ¸£Äöàna\0!þD×Ï&ñy2áwX	÷ÇäÒ EëDùÝûuè¹7áÑô+ã\n¶.xÏäÿ-CÙá¡P0µ]:²s«-|\"æÙ!`tX@'àn?.R`&·¬?g¡rß}C·cKÐ\n  âÆ^SMë¯w¸\\·ÃÁ\"kÕ`éy8 /§¦? þ7¬´¬µåÞIÀaê \nà\"Ôï`	à*cBöX¹câ^Y@«&{wx	ÉöÇ¾?Ýø>_öÂ·òùe¡v[µ\\æ·ÜdÆ>ê«Sö4 \$§Æ]Ãùe¿Þ^!­eæXü&­Sî_@(dF]àÂå ­:µ*ÕÏ}zißåèkXÑ_ñza2÷\0¨úrCñf#v­çªá½ë9Ô1weá1¹áonùæ5¶d4_f(«^eYFÊcÉÀæAð0©¡ÀöX5`Ò.¦«Un`Þj­<¾D%Néâ§¥6 #]ÑY \$GbmèÃýbb}êf±Cãðá0üUTõÂ­x#`­kzªÔ§÷Öm)èÔ;^m*à_N á³(	øf-òn#ôc¶oòù\nÈãú)öpýÇÊg	Â+Uéý\0E¾pÂNqé&°5Ù  à÷T\ni  ³{¾ãýaßv.rJß,?FuXçXzpüí(ýRÉ?(ÿy\"ÔõS¸y±gH\rèÕ?«TªÀ#»SÐÉÏgs	ãó\0i)*\0qo¹§\0¥VzÀ\"äAjXe>ýñ¸(b?b´ªw0	\0/ãùwµä	÷(br\$ËÕ¾qÙ÷)rcóç\r~äA~a»y5êW(b¢\n®Éf×2hsÏ¦zh`ÞL¦yé¡ç¦¹¢²Ø0'/G`\"'ä Z*=]Å}Êè9jµY<Ææ­5E@+çÔL®}öâÊ?\0ø5\0¿&HyËfo¡¥îØ ý6jIý\0Î{Xg%¦~9Åçûxgº\$hSzÙ!ÈöùPå.X^zÉáp£sÊÖ'çSúµ·ÿ¨~Nhz3GÁ£VD &'Ú¢â~t[èã®ú·²ÂzÝcø?õô-ìÕ{µï½bÄ?&·äÞ¸­Vø´Ê&óÉHýØ4ÔûÝOX´ãSíðà\$§Í\\\n Éµ¸	ë~  éI¶PYÌhq¹ VixÈÔEPî1ùß\"N7ÈhñvVh÷dExÚj·»^ÖØ;Ï¥öC@)dZ þÚ-þ/¹ÏÆú`ø[æauøºhíýãzý|^	Àé¥Pþy++¦eF¹ôf'F.¹Ô&zRµU;+Z#PY·Ýº ýöâ5þWâªÏ|.UÙhUwö1*ñQ½ù*¼§åÂpùaC¹®ùH)ù'p­j@>eÉ#óæætwÐg#öjWÕÞÏ	é>ãíÙß6@\"@<ÐÆõÊâÇf`d9Ì»[!\0ëC¨\r@VO.(b\rlÇ/! ;@Ú-4ÅóIªLÐBïTT:©o\"ajÌ\r}&²ÊbTÙ!RÊôÙ°ÍNéRªL¨G`­À3jÊdYjÅ^hzrâÄ[J\0IS¼¡>¯z\0Û«Ë]\0­jë?&°¡Ï `.´lµi¥­±Ø{C:âRÌí%\0003ÍÒj· ©Wæ³ºÊ§­úÏk3@]­3ª*Ñ¥<Â×á{VcâMA[¥\0y@<§ ?yCþ¦ã \n`++O¾|ù¦böXþ¸c^	~Ny)úéàÐ	·ç#­ÕFà&µ¥zXãC&¸	k±^QYJcI^NºïëµN4³`þ¼Øë¶*}ãúk¯}»:÷kÑz%ÙwÐkà@H	ù¦¯Riö]µ¡åwÊ+,µC@ÃõÐ>áHJ0è>²\0æÓPUTÏå ¼vø\0?Û\nj±FIÍò. HÐ¶êá{4£É¨]è\"°ìU#¤Û)!U)¥!dê ä\0í\" ÔLìÒTØJ¶¹äêï@\$Á²K,Ó´Î%âQ5±²Ær¿MÓºü«P#%ÂÁR	01®È]+ø8Û4±FÅÃÇ@Ã`dØ¼<S)Cüê9µºñ1ÑlÃD¶rÍØ#\n+ 4kK8ô±¸K!Ê{\"*à>\0ë²5{%í\$µØkA³Æ¼§A9±ÄÇTÇ¾üTr\0ëOµ\0asÆmàÖÛhÈñÊÇ/fÑÍO`mö	Æä	¬tCz><tD&¢\rÞØ´ÇM@ÇRur(m )ÀR.¹bQ¦îjÂZµ£a)ô¤ L»l/Û«Û%WâF;¸8»ömØ/}vGaB|~ò¢mè+'ôvmä-¤s'íî6¬òPîÍ1­Y· û\0¾¨IB²Ë±²½¤Íl.8o[zR°1®Û1õ¸r\$ÔR@\$S½·Ì±û\0ÑN!FÛaX fÝRVh6sÊ\\¦P;É[G(Jc1jÂ>ç!Ñ¶\r®ÛönwV¦Û°Í¹Ù:²\rºLÙoÄÒm«é»¤\nË¸É:=¹í²bî}V¦ë[	_¸`J{¨#! ?3'íû¸¶ß¡¢îæí{íû:`hrZ77vàr8¸ç´½ATµ©xÉnJthµ³«\nh°aV{Q¼èâWÈ4Ì²F,DðÒC:±i[ÀhÊþ`ûùÃìÝ¼êee¥ï½3ÏLÆñ;IH¼El ØìdÏôØPÉæ/M\$X¤ÄÀÙÞ3|òÑÎí!Us6ìÏaÀCÐ#	6³Ò¡´s{\"ÉZÐVï²¡cý!¤Ýkòr_U©0VámÚí%¾õ<eóÈz²Õ4 ÜÉàØ¹¨dõ%C?FÂ[+Éó¿<¹!,ï»½ur¬[baxgÂÊÖÌï£%©ã!Ã#oÐZX8!Fí\$M :ðí2ml¸çOX¹ ;O»ä£û÷#`mwSS>êlrNk0Û@3NÆÛ3Iè>»ènáFDð`_Z&|C¤îì`ÚImg¶ÖV6eÊñÊu>Õ¾ñØÛkÌjÛjóÂ=¯A­GOs°ûdpåEð,ü'ì,¿t[QÎÄü@	_Âx%[IÂðCc3ç\n3ª¤6tì!,ð« ¨ÌìÌ	mDXr¿Ê5Ã¼3J}ÃP\"[½ê4ã»CNL+q	û<\$ZX;Ìý¬Î@è»IÜé¯.¬\0<ÉZ²V½r#\rÀ[	4\níëµ`Fí\0É¬ql\\ï ñk©Ë#|Zì%K0K.\nIZ¼Ì @ÜÐL®¼-ñPd^ñ¢1SªÞÌúÈDÏÆH.¼iZÃ¬}ªZÕî\r«Í¦më¬¯Ú¾\r:ñkÇ,sÌ\0óÇGö±êóÇW\0001\0È¼uñÝ¬_\\xqï«í¦mZÇÂG6î\rÈ×?'b2rÈ?5ÆòÈG@òÇo\"\\r\"o yqéIG!òâñ_Ç¯#¼k\0ó­['StëEÎ³ÒòOK7%2cë?Én´À<ë4'\\urfÓA\\'H\nêùúWL=/NýÛuNmUwvÝNªEÚíÀüy[G·P&2^»ÛG?Mç*f¾M8þ)ÙsÔÂ©c²òôY´uvk[EË: \0Ç¿¡ùøH9ËxdØñËÈ`0Ø,hCBÆò£ËÇ)½{ËJ|»ò 2[²ÜÒ,K¶s	Ì{A1ãuòar¹Ë>ø+Vaz#ÜÍWÌèÐ«Ê­Ë74`)Ã5+00\$,ìsEÌð8;\"îta	Wó[²ÁûªÍòpïÏ\nFÆàñÌdàà[`G0&ÎÍÛÝ ÒsÃ2\\Å¿ØÝ\\Ýs	\\ÿÜÍt/5µÈva ®N¨\"V	óg`+bMpGÏ<1óÚ(<õóåËÔDü²sÊ=ÞsÏ´),vN¥¶ô\0ÿ@²\\¤Öÿ=üÙ¹ÇÏxaÆvÍ<ÖôOÔ@»Ðu:±öoñÏA ÔófÌÏ6s[>}Jä\\5Îý¼Ö¥\r×9¤&GÍÇÒ¸Äý=GïËWê5Ðy;\\ÅªË§H<©Ó¾7\0SGs-ÍHä&ûÀHï!ÿÑyÍj{Ï\"B;álk5a8T´Ø¦W¥úZL .óÐ5ÄtÓ`LWÜØ×M\"çtÓM¼áîÜÁ½ÝIGM³óØfÓ7tâêÅ¢°uÓ9cç'\nW¿*ý6ÛÎÔ&Ú¯a¾\0¸/!Ë£á\\ÑÈáá>í¿ÓÈåöºÿ©ù°ôò YC¼á?O.àl@O=*¦@­íWýßõj@`½ÔÄ|³Ð\rÌp9àb×N4ÚWÔ9@úõMÏPâøÄíµ×Q|@ÈQÏO>]RKÏÕ+ãÚWÏg[ÀóÿÐ\\|ùsÿbàHôtÿ\\§KÈrÙÔè;ÝyÝt`/^]N]tÛ& ÂôóÏgáW\$×f_qOPý9ÐW\\`]C±A?FÝCïA³pj v%B¯b½ôÓ¾ÏÅõ# qý4ôY5ïPÝ4ç¸CÐº1Tã¥oqtAG\\&/k2}Å<ÆL°A65\"Wg,¸#Çg ³Z]1ÚÂ¤ÏIõÅûÙGS\nHk\\õ]Ýg=Øë(=CÊ/ÖòR«]9Ú©âm24ÝÉÌñ»Øâr«\\ÖÅú¶öîÝÚÕÎíÔ?kûwíÌú´ÇÎt·gPãu¢2u«HäÝ	ÃÍ=ýº?ÙX¼ü2éÝgkQÍÈàÐÿl5õÖew8l_Ü§nËöMrÅvÓw9Üøì4óOÙws>a%DÔ]IEÛe}ltÒ({Õ]Ù__OcæËvHýCÖ9)÷mæuÓM}=Û\0»@07B	_ÏSò½\\Ò2BÝ½ôøUï ¢ð¹e]nB°¯M-v´í\rªv\0ÉÝpMÉÌ¤Ýïu!@w¿ÎH&½ñôÓ²ÄtÕÖªïn­u¾\"_´@°Ü)N ÑÚ¿oNWì±Þs\nå¥öQÞÉNálw\"Ý|®½¥Øý¤AjF/-ýwíÝ4q}üÌeÞox÷\"9=ÌÍz3_Pü»KÚí:²fN©­h¹­QsÙ_XSjøàÝS¿ÛÍÐ5¦%ìÔvùÛå»AáéÙÊëâFB¥;R½pGG²\00063Ïø@61·¥×ÈÇ{%Û+LSáöÉÄ?+¼öÏfÄÆIu7üùtçÚat\r7³°º0ïiùØWiä?3¬@n½R @ømÎÄføïÔÇlg!ÜÏAÍF&vä-ÎÜãð«Ï×^\$ÒY¬éÜaj(Óò·_Î¸¤oáÿ?óÿÛ§õÌrKÃdC÷ñ¾3w¿IÌ¤Ñûæ5¾\0qÛ°öÖÎÝ;wjÁº[ò¹·îêaö÷596#ÎLækvo]áÿd~aï\\¦ÜÿÔý'V¿uô4 @ò`Ç{G-Î_üájÂeI:º²íQË¹Ðze{yÔ,i:v«çzÒ}`ÓIØ%¼äùÉ7'½ø]]¿Üùyý_gI;ôçR	vÝÏ¡Þªx\rc@skÜ¡½§&+½O¡ÛÊn%èµµrür¯#t­'òïÏ¶4¬WV±U«ú=Ï066ï]è§¥}kS9w¡ÜÔzbÍ9>Y\n÷¥ ØzbÌÈMÁUzeY2ºúç¯\\¾;³?¡Ûèx4M¥ÄÓz¸?>.çé¨>¬q\"U`úÓÜ7;2»*tÞìaa nüo%?¬Qõ¹ðVzÍÜ§¬K¼Î«ë¬>´÷5ç­AÇq¸'*Àùä>´>FàÆ¡FôKÑ\"¾ÄmÌ/?<ÿøk×)ÞtZ|«~É¬f~®¾Mû%ì²Úýæ=\r ¬t3<g\\yl|ï½åÑÔlîQ`XòY\\î[mÌ'óxÁþ±\$Â©¥í\\sCãº8D¾t\\«îÆ\$öÍÍ¯µ½põw²çg­^Í%ÐÝaöWÃÎþê´/îw>b§ÍÅ7÷;ø`[^çJ	§¹}s{Ðþîï]Òo¼>G\\ó×=Îåìî¨îÇûâ{\$òvëÖ'Aó\\Ïg¾ãø §¾ã{O`[×îKs¸W°kð5³ÙûÍ*û¾Ï¯¶{ç.\0jeÁÿ®\0000ïùÂß\\mzp	gAÿ¹Ï÷½EÙ½¹çÆ@VN¯»âïSñÏ¿ûñOÈóÇïî,hDÎûÙè*S¿zßòÅ=Òw¿+vÒÏ+|AòÌÅÝñP6*ÓävgBfÅ°éüÇßJ´WQÎgÂ²ÇÖ{ó(O²Ëbu1äêJ¬\$Êß!óÿïáü^ÿ!ÑÁgÊCTÕá\\Ý ÔÊ±6ÿx{ç¯Q<¹û:~G?þ¤{7çÍ\r¿KuM¹	X\\ñö])w·þÊ}+)¸}SIô×[à;ó¸¹Jç¨?HøÔ¿Ö_IøÒGÏÞÃýBüvGtM§×3|Ó×C\0¯p·gOR¶-ÎH ß@ûq;^ô¯ýâOIPÊÞý\rÎfçw3ùëÏï¶{!å°õC´ÝôÄ×µâéèHuÚÐT8{VývôêOHb1G¹k÷ß\n¨üCÖ8KfôÑ'ßªp§+×¾Rõ¿Ïÿá>XtøLÈà2mU!¯ÑÁ?K6O2~1÷©Û£))g2a¯%ïO\\£wZeÈ¿Zó¹øei_u?õáã×\nÄáW6}ýYYÚ²ýÑáPÅ_~=ü7\"¦+ÌøF½úg\\ÏÇ'øµbº&hçZ»êþ¡ÓIÏÐúÖ¯«þ¨\nD²_~¦eyò¹;\0BëÄEòÒOU0\$Sz¥&ð*oU¼FMSrRt2ðr/2¨e°ÒÞµ,TÀMeÈ¼1«aeEéþT&.PÐ%&íW%É¡u·ïÈF¤ôºSz¥½RT.ÿÌP6Ãª¿îõQ~ògï`þûü¿ðÿPðXÿ\$Üõ9D%íV\$jåÿMTEYðøCäYÕ«øBPðôðWc ËWÆÑ%'þ·IUËÏøáº=³?æCÁG2£f·ú]63]°§Ù[jöUòyåÿ÷\0ÙZÃ¿ú¿ÿµsósôl2ÂMÂ²ÀXX¬Àt\0e©«Ö9ÛVD´YfpèFË[É\0=_ºÑéáA@°¹\\è\0ëà	'VæF*(µ¾Í[@\"Z´ð]©¸Ð]Rc­Éu|gÁÕÊ-SEL¢Ýe¥ À&ºÚÍ<ãh°\nP\0ft³ö0\"J¿Q?×DþWûüTSO÷@ú¿ÊÂqþkþG2¯ô)?Ó0)g\0µ`5ë*«Ç\0TØ_ä\0(êLí*ÀG\npÀz;¼JÀghê&Z\\,¬ÊÓBëô,4°MÕy3´Ë,M\\J\n\0Oi¬Óoôúå\0@0|ø\n ¾Ã¿ÃUvºÝÿyo%^ë¯Q¨)~~ìå Þ¨ÒC\nägÄEv ÍÚY±td²ÀÉõÆÄÛÁröj¤£uFëµ516ÔÉµdEOËèÒ@ÏdÉEøÖ6á·`cÓh\n´ö2,´W°skÂÎy9ªï`Aùb2``ÉiS\"8;à`­¸ÊU\n`*Ì©ô\0E\0U®ÓRp)° bÌÅ=Ò§¨ (	°Aa~Ä!ÙZØ ,8 0hÁAæT«ò`±óihÍö(\"«ãØ¸A#^:Ê¸®( ,]`°éaU­»¨%`U?Á&\0VÖÍFæhlX³_\05³;æl%^XªÀ±erÍÙBcz ª1Ï\0WñË6X)ÌÕY»AS¸Ó¥³h KÉÃðA´Ãñtòv0C`­Abj¤¦0V ÀoeLÃ­£%OðGÙ¯ANEh¸.â 4¥aËÆ,PVºÁ^»Í/fÌ*`½°Îd°¾ÅÔH&cXsÁ8´¿x}ÖÌ<YÍ@ÔaÞN¹ÅZØ1Åb¨Ä¤Ö7Pi`°*zY|X,Ðjà\\\0+) X-°FØ1ä?37Ø4°H áÁzb@Çq¥4QP` Þ²)bëæPt á¡M+è4°fþ1,^<óâµ¬òZ 14cÐÅ«»^FXìS¯u\0ÑA3-- i´h¾];Füd6@×fÁ-Só'EEå\rÉ±ahÓÑ\\eúß²Ç+0MøV	\rDÉ¿e³vÜ88,#Þª#_3ô!¶6Ö/òkd\"8CÉÂòºõmÅµøpçùð^=7M{bÖ+RFéØÃó0f4Ñ8FPÛû¤¾VÂßa¾ó}ZjqfË11Và0¬Àj4gg6À@zö¦-ÍEÍAüTðÉ?0µ#AÀÚÌU­Ã Ùm²Ý^LÌ±;%L<¶\nG\nRPGFÔå%S¥umdåµª=vÖé,A9êWàå¶	¨C t£¶ÉGH½st@~[5),ÛF8þcîsØ¼\"m>Lj9ôqm©Ç#lrõûm#Â[|Å¶ãË6ËM¶llfá(Ø7#äÍ£6IpÑµÄ­ÂM;xHÞ0vÂ=ÁÏ¢lú#}´yóA±¥F¸õ+«ÆÀÃøÞ6a:\n¨U¬ ïÉÛ:©ZløÙ|\$i HløMK¥¨°­6~L7°2VüÍKÛ«îõ¦ä0ÍÈÛÌ¤CÄÕ²tPÌD¯QçÂòvÚq\"ëìc5o&ámÓµ·J¸1üÎ«8¶Æ3LÆà\rÖÇG)ü11¸FHd0ÉÛG¹OæÜ¤k+¯¸b¯C·îüºªS »Ð\r@;x¢g¨Ô\n{TíàT%]Ø põJ¸ÔÇÐ(¥.)¶¦ÉfÀîwT\r 8GÎígÜ7âÜa2\\RCb£DÉ°÷« ¨Á6¦O\\­ç²méÞrvpõ¾{Ï6¯0Æ¦9^-B¡¹Ît7zâý+¥ÜØ%¿;lHqCÉ­8ÈWíÍFÚ7=éÀ³§-ßø54rú 3·)Ìð§.G:Ü9b9#A(ä%vh¸Áøh4Ë	\0gK5ìc³g×¢¢=äHÂÚoEëmÚ£oï	6t<õÔé!æ\0s\0ñ¨vPé86XàÖàÛÖìn[Cµ\ràøÑ¼(RYãÊ´ÿPé¬ÕmÁÀêÝ M©·¥RnZçÁò´?çFÐÿ[» ÿ²äÇÌÊaj*Ú¦âË}@-Rû\r~»=ù?PÛþõ\\õäÈÛÜè)¾ÕPDò\\UtBEy1	Å¾ÆKèèh£ÓÈ&>ô¹ Â¼BØ¯É\"Ä)^TdýlC±¡jD/N¤;©\nD+ç«ûÇ²\n¢¼¢ò\"(!HYtÙ1ÜDä®c©\"+:¿`3EHÉ\")ÄOÆ2#E¤ÐKO¦Î¨ßM|Çàk¢7æni\nC~ec¢:½oNTô´F÷­æíD¶@Ø:\$ðÓÊÉâ>D¯¹³<Håeq#P¤D@?8cÈeÕ&éZª15	bp¥iå9\"PD9½å7x*6\\@k8õ8ðWºJ\0ájñ=t&ÒTAÂCa¨á»ð¨¾%ºôË/ÎÄÁ\0×\r\n@pÐmn{<9ê©	&k&ËI)âgÂ0]Lç±æ4¨R\"hgsxàÌAÈ±7Ò¢DÏ*ë¤?'½I\"ZºRZ~ùþ&Iò7SáyÚÉD,_'sJÖ¦-~ò2ýÐlN×IÑ/¢d@tKpBdDäín]\"\0dñ%ùX»Õ5µ3¶êø\rÀ<QG1BUàÅoÐàAôPØ¢íÂÝw¥É©qðcÈ¨¤¯¥\"7.s:(ìR§;J@©yT¾å`kçòÊ¢¤êà8#È§À·¢ <ð\\¬]^\"Jx¨p.wôtLH¨]_kûMåJ*`¿È«/eÞ8¿±\nÈ-`Ï`=¼ª)üUH­ KS¹µ°¦yÌT¸¬\nÁM*
CùR0æº+¯­TÀóÕÐz<®QZ¢¾K¤÷v+Ñ°±ZâÃ6(wÀkd+æ÷4	cÔÑ¿î§0Sá²à¤»íl®±Ý#j½Të'¹)¿-YÐüÍ½üYX´®Ñß>»tÿn:á§ª+_¢#Ã1Ìý5B,XÈ¯ÀbººCGé'ÌX:òATµqG`\n¤ãsnð£IlÜ-Ì>ÝÑøä]VÃ¡°ò¢÷nãetpüAk&Aýa+Ú§JÒÞLÅâ%l_!Ú¥íV(=ÀTM²Ø¨ Çó¶õn:´N³ÄkIAÄêÄ\0ôèbbØ¡oÎÔª:l¬F/Ê½T§Cwx7×utÜ³ìÂ\r^ósÙÊ-[A½.mj=XTÙ¦ÿÎòR°\n´0YÉ \"O×Aµ¤bLdABxð¸«?#SOèí!°t¨®°ÅS¶Èî4	§ `®F_ºÌK~ü¤nãx§Ñ\0Þê:ÉyN\r%ÀRgÃÁ&D¸øy»ÂÆ±w«ÅGae¿CÖ÷D/FaØh*´\0rGe}¬äf± mÀAtüIøìgªÀ£R/FvZØ\\PLHÎ@~c:W-7Ã¿àmK¢Ü&½8-,hà¬ f¥-?{I8r')Õð»årÞM¼2Jp\r`»_z¬ã­í¤j8ÔªTâFª{\r éô\$VñA\nß=­?[\rôJHÅÔmGÃîLæ¹÷¤5~\râ2±sáÁdé2u7)s7Ûsîý%³ýç-\"Ä¦ùÁÒ;hÀ¤	PNÚB\$»âç1&ãb»z|ÚBxI+ÏtL£Zs:z+{Ô{n^Ú\0h%ûÛ¯W~\nz@Ý\"ÀwÚè7p\"g;3^Àszêö-0,Y|µoAâ3H¯.{#xF^mtèN#ÈiÝÊ¶Ysån'{Øw=ÆïÅ²,ÞýmSÔäÍïH;Â(ðæ@=Wi=Þ²<ë©Ý{'ÓÎ\"ò¹òx`ÀõÉ|(½1Îê<0¬óaÏcéÈ©Ç#£ãuÈê¡Å«¼·[±ÞíÇq{PÏäqxéiÂö;3H\r©ö«ÅÜî?\0¥[Ìtç>Qf^ÅÇMüq»Ûò·°í½pn6ñê/kÊFË.\\Àã%zt¯\$6»ÝæQÌU¶1lR÷°^lju;*dpF¦Àð 4B8ÔÏÒ	^Æãz}Ïü\nÈã5(ûù©{äØÇ©,¦ø2Êg\n±êÚ«G}ø3Ó°±êãØ=Ì\n4èý@R6±>Ç¬uÌ¡V=«Ø(ÃÑï`¹ zx8*Qñ)Oj#äÀ	M%ÄkÇûÑó:¹ZAéD|ØÎ1õb+ÇÍ\\Uu\0ÔçÒîÀ¯\0q\rôh(«©tãó¡V?CÇ\\ü^ÅIÄÓ×åðþâÕ@e:0!kÅ3.ÑºÁ[¦õ°ÀÀLJ8níÉôSó·ò`Fm3µ÷FHíacÁ:{û­ù;áè©ïn¬6\"¤!cÁ)A*<|âÆ;û¹àë\0í«a}¢Þ¹óäKx¿]¥Å15(ÜèÚOj¸ ýíDfä!¾ëÐvB%(ÒéÆ>,\$¦B¨\0##]	HT°d·&\nXM@Çß2tå\\¤E®!·ä^8Ä§Ñ¶ä7 #¾óÕùa«×S	0ä7EXBÝà.ú¶\0MÍØHF/ZO`;ÊTëå?A­Gtvûº(ÌÎr6ÇuØåÍ8(ÈOj>¯5 r<´±£ÓH@Qy\"ÀìFgyIOÏö¸µ6nEaºùÏ3\\ëk\\òµÑZiÔÆÆ#xï<XûwËb£C9Èp3#Ê»²7'ê|b!wZþ¤¤Òª7õª©Ik&üá/åJÏìU*gUb2ISZ`\0		.*i0þµüC{,©K?HþÝ ×ò²4ätIUC#EýZ©2^edk	øT§#iUJà{7I!jCS'åVÒ<Év?¨~ýâ«¡ÏÚ¤ªÅ¬«G¤Ði0îx%ØÕÿKý`_hräg­D!nHð\$¦ôEW®>IpPÁ?Ë@í LWBEU®I5buZ(UROÍÄ+%VO\$ê\"HaÕejÂ¤¡ººî¯4`a`s\0âÉPW\$ÙbP6I*ÒS\\â+&´°5tr0)#FAÉ&[*mWy\$À®­ÛÀ¶Kaã·ÄÒXQÉc&\"\$YÉ-ëd?CVB²òIdBRaävì}&Kií¨`«×_?gG#=ÊDÖg¬WA*\0¨»±Ì²}¤ëIÏ0ö'<ÂÉ6°säÑÁÕbAqlÖ'ðR\0'11,Òz¹5UO«îÈÂ.jÌV¤Ba	ÔOÄ;fJ`a`3hÒy&èCì«5\0'\0¿½¬µö«õÎ®ß_:¿Ý}õÿRo½¯ªÀA²¡ÕÿàêÉÒ^'Q~\\i<,h×±¯ä]ßà¢R´íÊÓdÂà@K4Fhð:\n/MÅx4ò¬ÕW£×|É};Kì·ÁYgç%­2°üð@)3fbÍîP³2FeEÙ¿°ïgÓIvK5éRñ³¤aäÎ¼®3|Òh î´ÁTüQ¦&?¡üã1Jg¨Ë=²¬Ùñ0gÊU#?ò|ÅÃø´*ÖÁx­Q3Ömpföb4M¨>ûÖpWI³4*ÎÊñCA0²(À¤,,´/\$ËÊP£ÛFeX4b4M|­l°áÿ??cFñsGÖmí%W¹RÒ*Kfí'\$×íiÔÉ\\ (ãWå/	°j	§û!ÆF¬µ\n+LÔ1ùA(ÈÍ¡Ñ5vf¥`8z9AêÍYHØyiÂÓ¤ Jø\"§ú	Q!¬«Y«TD¼©rî0@?tòBvm=	°hÍéS®T@36m,\réáù ¦Ìí@¶l¬å©ÄÄ+8L©##£W\n8	[XxRéÛA¶§HlÐÁ­¥á`JÛûV DR¨¼Î2¤+,:Õn!×£©kª'Ûø¦ÎPºE9r%¤4§®¼¢µ¤EuLòî 47eö\\«ÊÍHúu5Õ9{ØÖtGã{ìÙ°(ÃIYîÆHa98£we:ÞTZ3zSåt÷äK¦rÚ_-STWÁÒb{A§;CI¶7èä711DÞæñ·r¹Ï	Ô>59ÁèæôîcDâ·ÏwöfFLH¸Ó	<Å\$N8ûIâÊb÷	·¢l~V&YLcpHS3)@2¯øìA).Rã¾ÐyHöøó·gÚ«&\$\\ðÖ[¤[µN±6¥6>¼·Hù!¬ýKNßP3è\rä¥>7èïAãúdõ:&äZ¤%7k®(×°MÁ£Ë[trp.I¿QÊÿÐý¨YÀ(ê_æ&5#ÜdÂ)Ïß)mºªÂH,ºáÈr?dlHð(ªr]ÒX`Ë!@Bí\$\\|»§ûKeèØ\0É\0I¸ëðh¦LkÆxh&ÓñË91\0p\0Ä¸Ur÷@8\0j\0Ð¾9{À\07!\0fÄ½à \r@\0006Ô\0à´¿²ú¥ò5Ë/ò_@òú3Û0IRýåòÌ	/¶`½É}`åüK÷6\0ÈÌÀÀ ¥÷Ëñ\0q/°´ÁÙ|\rÀ\0000Ó0J^ø êòæL ÷0¢_\0iòü%îÌ\0g/æ_äÂSaSÌ\nøTôÃ9³&Ì4À\0ÜÌÁI~òú\0Ì,É/à&!L5À\0ÂbÁ)³\råûLî\0ØÂi~%ïÌ¼°pó¦Ì_Ã1Fc4ÄiòùfKÞÔ\0ÒbìÇ)óf\0000\0b\0ÚcÁà2û&*Ì0 ÌÃÙ3\00050¢aP	2üfL&\$1h¼¿IÒúÀ\00050~`4ÇY{ó\ræQKìo/ÞcôÆ9S&ÌF×0¦_äÂ@`\r <\0c¢\0Âa\$ÇíS-æL|0e¤Â	¦_KÞý0~`ÔÃ)3fVÌ}\0b\0æcÅùS&KÌrÓ2f_¼Â©|2ÀLu·1`dÃÙó0æL<ç/ò_lÆ~ó@LÁäcü¿É`\rfhb\0jaÈYæo\00043_ÄÄ3\r@4=0jbLÀ§³5fJ\0004Ñ36^é¯ùÓ@ægÌ&90Za¼É¹ù¦X5Û/Þh(is%@LÅ/ºg\\Àis\rf\$M(µ4Nd\\ÉI\r¬&pÌQî\0Æ`ÄÑI³\"fKÞU0hÄ¿ùDæ\0004q0\"bÒ¹}s	&\0002\0i/zaüÉ)Ó%&\nLåÏ3^jtÓÓaS&Ì5*cT¿é¡\r@Ëõ4\0ÒfôÉSV¦5Ì>\0ÚjTÊ	®:æjL\0g4jfÒYþ&´L?5¦a,Ð	3!æ9L3faìÑ 3?¦Kó0_DÍTÎóf§ËáÃ2kÜ¿I¦3A%úKï¥0¬ÄÕ9|	fLµy0:j¼Í9©ó:&L\0c3ÒllÐi³gæ8M é5c´×y³.æ¼Ì×2fg¾Y«S\"f{Ì\0«3htÆ@7¦yÍ¢í0njTÑ)ó¦rLîLîl(«k	¢\0¦\$ÍqÓ5öfÕ)¤%÷Í¼5¹X\\ËisS&¤M4Ã6b¿é£³rfÍA1\"iÔÐ¹³[f0L<³¸ÙI±sX&0·/ækÔÉó~æLÍ10îc8²û¦ÍËà¡2J`DÒYóAJÎ\0¯1iÔ¿3+fóM%ë1n\\ßùSfM¯+4¾j|ÝiæùLÂ5jbdâI½SfnÌS5ÿ0å<ôáYó¦Q%W6þi´Â¬3væLþ8Jh¤Ñ)sGHãN\00BiÔÏéPç'L(Ó16müÜUbÍLqÍ0jìÑ9·@f5MQ±2BdáY§s¦tLHq5¶l<ã	¨óPæøKäg0nr´Ài@&ÍèZkþs\\ÆóÓçEÎJ3Ör¼Æ©ÈRüçFÌr5®pôÆ©¥S\0%úÎfw3¶clã9Ó&kL¡=1a¼ç)³tæ=NM3`Ñ©gÎ»9\ndÀyÑÓA¦Î{0pLØY(¦ÇNQM7öpË9Ólç\0ÎÊ2\"kÀù»3:&´N:o4zv´Ì937§4Mq%:NhtÛ_ó\n¦®Í¹2Js¼ÁI&ìMï5\nl,Ì9Îó'BM;÷8Nw|çy3¯&Lñ2fs\\¿9-fÌk0ÂaìÖ	ùæeÎmW/ntÝÉÉ3¼f¨N3¾l\\Ê)s¦'/6°/rÁyÜS	§.Î¿-7otÊéÓg'Ì8;5Xüè)»S§5Îë0eDÓÃsvg\n0Z`ÜÒ9gMº	0ªj4ä)s*¦=N\rs2vwÔê¹ÑN¦Ï%=;njìË9Åó5¦2Í÷7udÐÙ­æÏL¯=\"o¼í9Ås&D=÷/âcôÓyîøæËç:33fdôÝ¹çÓ¼æO\n»7{TÍiàS¦IM#Ñ6`ÀYÆ³+§¸O#9È©ìÈ	£f&êM\0k¼çY{ó<¥óMM%5jx,Úâ8ÓæAÏd±<d÷©¨ÓHåóO½<z|ï		'³M9®mñYæ3e&?O}3êgôóS&®Í3õ/o¤óó\$§ÌÕ>êdÎI²kæeÍ¾+>ReqSÙû ¦\$NÙ<FpüÓYÄ2ùæOÏX`x,ú¹éóõ']Í\0Ñ26k¤ÒéÒÓÂæñÏ>Õ:põî3âfNÆï7æiÄÄérùæMÅ2bfìÐ	¬3HæhLñÏ/²t¤ñ|³ælÌÌ:h\$¾Y¬=æ³N\nC=>qüÿÆÖ®&0«ËLîÚÂaÍ	Èõ§VÐ!]3ÎkÚSR(Î^2.n¼¿:sxç@Má1@>ltåYøsØfÌÙ6äéÉ¨\"%öLÕ¹3vaUÐôæLHG4ptí	¼ã&zMI@®xÔ÷Ys§rÏ©×1Bn4¾Ég}MÙ½4.}ÖÙ¦óQ¦°Kø=6z\$ÉIò3fÐGGkÉóöfxMt ó0ÖóY³[&\rÏ¸!0>mßéïf-M\"2®hôÿéSÿ&ÍÎ+9N{m9¼2ú&äOJ¿>Üüé²ç'ROb¡E<ÆjÄüÓ§fNg<h4ÏÉÔó¨æýÌÐ¡7=sì½é3æèJÌ2ÜÆÉ±®¦9L'?r¤Ç©©óvÀÌô@vg|ÉÙü³+è9ÍsÃAfzlÎ	º&LjÁ::ôZÓ©h\n@£F¢ ^Tk¶ê®þ]ÁÕEPþKµÙ©e¨¾N#\nÆ]@Y30®cnMÙ¢ô`ûìÃh2)hfÂ9°=F¬Iñ^ìÅþÅXP?iÉÄ¢8ÊN\rKèxY3Åa¥Âô(IìQ¨3naèÎQØ7,B)+¢NÇª&°ÙbBQ´ ¨È¸ÍÞÁµ³\r^¸ÉN#¶ORjá-\n´Ñø( ÄÁ¯jøÉ\nö¦wÔVÆQaDiíÖ8àÀ,À²'< +A3Kãè²ñ_`KAµ%lÙ(¯L½ªN;ú'(ø(Ñv^ÎRÂõ¬%FîÓ+Åªô\0@#Â(ÄÇw`)~Uñl(º¤K9ªÂÈü´da¯y_Íukq2÷ÙI£*M1«0µFìØIå_÷	Ò§x0ðcÚ}3_ùFÄ­L\r¸+HÑ±\0°Ó	 BõJpmÁßäMTÅTr!Áà\0±G&Eæ[ Ö òûm!¿Ð0¬õ¨êRq°,PèNH­/ÉDQ»^x6ÌE¨òÂY\n»Ô%V&¬Xp5Û´ÏÐ#vïBÚ\$\\\0P&C] À#2»¥Gü©4\n9K¥µ;jî,ú:@Ô|YS`Oñ|¤¦Pk@Ø¥2Á¤8ÈÀ\\§4|^Q#e&Ã\$¡2­0×´¤>Ê¦3.Âw x3\0P×-§JJ¶HÊiv¥¨ 65lZ4¶)«C0&Y.!½µIBe9KFEÿÍ(¯Q'IM -´YÄËQq¤Ñ±ó0}D¨·\0 ¢Ýó\nFºâeY]c»¡K3%Ýi/Rq`F+\n12Ì9èÒu¤ÜNxL¥ x?dÝ©8±d'&ÃFÐ=iCÀÐdÌ½QX ²h¤Éb\\ìd©MÐþdüÉÔ?¢t<p.Lµ;jÉ¼ª5#dÉm­ì/f6H5¦E¬(D)¤¶FÊóû  0¥cA­d(Î©Fµ,VR,ø:²7\nÂ%5\nw\0ÂÊ¥ªÏ}(£l¸ÙVVEDÊcö£²gWÆ¨qTpÐÕs(+Ph Ñ¥Ö@¬¦¾Ì¾KÉæ (jðÊ@tºè²Qf¢ßIúNäÆ5!ú@´^Ð¿fµv¿ôiwAV^²ÔE¬¥é´hÓ	cÐ!> óÑp^æ¨?ZbÐkiuAN¦HÆm ôF@èÒD_hÃ8fwèi2¢ÁLx9àûÔÅþQY¦^Æ²%\"B²¬éÁ\0V»Mv«V´Å!ÒÌc` U3ö ì÷ÉªÀä`¹M2¢øÖ@¡úÚòSXbMÊí©9ËºXZ/¦,½\ní4ZO{)+Rû¨a¨CH2tÅöµágH¶SÆ¾ ¾ý¦KkHbÌ«¤çÓReðÖú5ZOlhWËÆ¦,Åõ}uîl¥`Áx¥ëNM0Ø0^ilÁZLZ«ösÔÊiÐ4;aNÚøõOôË)S`N)}Ý8fÔ²?Ó¤·Kü\nµ3x0Y	¸2Õk­N1f+ì©áSs§ÖÎ´êpÔ¤`ö1gÇLY7ÐÿÐ6×Åf¦a8¨DÐo §Òð'KÉ¯*{lUý¥õOE;z-îi+J\n#ö}tÂ)÷AµhzOJc(4¬ÜiSècoÙv­1Z~´÷ñÓFè¨vå%Úcàéà§ü\0®%@úf0SÀ*Ó)§TÒî75ßØïÔ¦kL\re3sõé²t¨H½b,e´oj²)¦½fU=x6´ÓiªÓOd^Ò }CJkL´¤èÔ-b£MfS\"kµ©©Ó_¨5Ö½DF4¨Ý)´Á¥¤ûO²DåF[ØSÆ_ÍÖ«):nôàÒÔY§¥Oê¢ÃGö!Ôg ÒÓ¨ÁOqÃ,LÍéÅ±©§Uî¢U92äûÓ¨O£¦êð^ªÓ¨N=ARuiÓS(§Ræ]BºT÷)ØTg¢)CåHêXìì)dÔ§ÕP(]¨9@iÞÑe¨O¢U<:Õ(j+A¥§Q*UEz{óSÎ¢R©M=ÿ,\\)êB¦Ý/DÌ¹©lÔ¸`YO£t!ztT¿h·Së©3Oµv¼!gZ4S.©Âþò~M)úTÏ9D\"ÍL(C\0(Á/G^bÂý@jcUi1÷¦AH¥Mf+ßêmTëÃE2ª4ëi¼A¨9LÈ®\r3J=TF Ô%&S¶B5=jp/d¨PØ>íCð*SV¨{SªøºiÁþj!¿ªT¢5 x 5jTKbT^¢}Lz@p@i¸TT]×QZ\rEê´¦ªÓò¦÷G©ã*¥5©ÁU-aQ¨½Fq5*:0\nh=QÂ¨íG&zì9×z°¤&àÌ\"		8r,9×Ï07ªsN^ª\rGª¦Ôô¸Õ©R¬ªð>êATò¨1O¨ÕPZµEêFÒt¨¡U¤½NZAjPU©EU\"¨-Jz£µ*`Ô¬b®Ã~eU:[Á=êªTµ§¬½¦¨}±\0é|îÕaOzsR2i»êÄ2f^zÌÊ¬`\0ì\\×wú\0&¬­6|´¾Z7/tæ¼¬­R²Ì­þ¯1¥úVZ¬ñ7j¤k»*\0TFÜ»¼°va­;×Ð´Õ¢®-I;*)²kº¯jg´ÓÝ{Q¨å'\0ßÕÁ|H²ú\n¹4)³/L«oVæ³	\0à~QxºÏS&:DÕjI Rû`n|¹Aäæ´^jU¢í(\$u]Ê´yÙ³S\0¾V6­)½Bp`Õê«¼½d;V7Õ~j¶³q¦ªÊÚMmê¾Tné³y¢ñXµÍ3ÌÓ\0¾¼sM'ìZØJf0q=´e³'fgXqÅW)Bi)t2_äÍý¡y=0	*jUÈ§ÜÍþ¯µbzõ}øÖ/d^ÁN¯cfu|IÉ4(QXÊ-ÇMv+ÔQ\0Xnudz¼Ëù!\0CFÏO¸ÅO¢}jÑTÈ&Px¡>Ê~ôg+)\0Y©5YFÁ>Â²Ò+*\0M¬¿Yb³8é?ë(Vhh¨â¡Ú)Õ ~Â¬ MÖ°êRUH3¼`%LÎ³Ì´F«@/\n¬µJÝOÐ5¡+EV¬ÞÀ²³0¥ÿrI¸Õà_ÝZM#9lkLVg'Kæ§K:Ò´àXÑ±­(ÏòEMZÔLÿ)tÑ±¨PÃFó3B¬lk\$V³&oV±­kf\0õØn°à­kZõµY6-Tu °à¬cGm\rlr¬lY3ÖhT´áV0ü¹:QeK2«mPÚ!UB@#ÖÊªR6¶U`ì7Y·å\0¼öü¬o©ªÑå'=[¡Q±?¦Äá+²­ÝV°«Ó,\nÞää«{¨ô§µG½µ8êàøÉãVµkâÌQ+3º>âYõà\0W¸el*È@\nøH'Ø}û Æbu«3yf'Y.§C1ÙBÄ]Ò¬\0¹+1KÝé/«G[)åsµºÙ`Ð­5Z­¥^åE`³5®YHU¡«\n8Aük2»\0§[ò­!ªé,ð	¤6ö>}qÃ=­jÑÖKh@ÈáS»#¡EQ¼§Q2¯KµUÖb³¼&Vý#p+æ\0Â³k Lö®¢öuÚ6+Á¦ÊizË_Ö`MD{//®8©Y8\n¤â*øÑ¥³)C(UÏ	³^ÜØõx²lÕ­k×¬^V¼lâL«Ì\0T­^v±¥vÂMh£ß¨ç^q£%\"*K\0õ\0We^Sº**L+¹w´Ôko^n½`ûµx+°5þi_[)£àI¦^«àÀìªÎ·Ö½f+àö©ÓIxa=|åª@VK\0 \\\$­uëìVdê¾£UdµC²%Rn&²Lý#\$'ú«=RqbK2÷iFuüIÂ.ÊßFyÓªN5.¨ÊÒc<ÀI¥'R}4§	ö«&Á!«0õ´=Y¯@«eI¹}vóUÇ+ÍVKôÇÎµÅ*½l )ÓVÊ`VÀ¶¯y3úSu¼ë°Á°6N«PVâAAX¸´Ï{ÃÍG0?¦Ó^¨|&7XD´¸f;`yõ*ðð5*æ«Ý]!\0Bi*ØK­dÍ¹¨ZÇìÀ*Ò)_aU®Bgõ­XdØ[°,ÖÍ¡­K\0ì2äöÑ\0èäâ0?°ÔNI%\rÕÇX°Õ[ø>ýÖ\nöX!©ÓV²4kÎYí\0G	êÁþbð¦a³W&aÁÕ&IðujÉ³2`xM6:Ö«È?×¸dN¶RøSzµbéa²¥Î-*XvëX`¬Â&µe\rÀ¹\$btZYö,À<ØµÈÁmû \n½SEf¬¶ÖàaZ¯+jJaÊ÷5SºdhK±@Â\n\\)he_ÙªTÈÊø01ºI¼*¨q pàfÎ±Ô80@3Ê¡GØÔÌDÐjQt;aI*êÆ ðS3æ06%aÚøà\$b2I	¸í!n>Ïü¯öÈn[\"³lÙVÿdP,[# }ºY J\0ÚÉtÛ\$Ó.,Ù%²4ÅÛ#!ÂÁ\09²ed~É|ÇàM×];:31úÉà!²³Yl 8²0Ê-K'à\r,¤Ù²d&µë'¶QìYQ²IdÎÊ8{)¶OÙ1²­dªÊp[&ÖWìY\\IenÊyë-FfYke¢c\r,V,¸¯²çdà1ÜÔk.BÐ¬©YB²õeÚËå{/\0ì½!ô²ïeNËÕË0ö_¨jÙh²õeVÌ\r/3¬ÇY]³+8Ì­{1vQ¦°³7dRh]dv`¬©Ðp²Ñ4.ÌU, æÙP³Uebh]
4vd¬ÖY³?djh]ë5ö`æÙc³wfÉÕyÖg¬ßÙB*{e¶Í¸c¹À¶q,ÙÙ«fÈÅ9À¶a¬âÙFf¦ÎmÀ¶k¬ìÙÒ²Oe¢p-«9 çÙº³»eNp-ë<öP¦Ù³«dRÏmË:6z'\"Yà²ßgÏm>v{&TÙöÿe¢_\r»=vQ­\0ÙÐ²ûhÈµ;@6~­ÙågÊÐm©ÞV¬ÒYBågRÐ à\$¶-\nÙFågrÏÝ¡ë;öxçyYâ³ßhJÌµ¢K,VZ&aÙî´3h¢ËU¢«9ÃÚ-´1hÐÔØ»EmY²´qhÂÑ5¢ûF³1íÌÏ´vtfÒ=¡C¶¦ZG´ohFÒÔëHö-(\0_ÉhúÒ¬ÎKH6í(Ùæ´¸~tE¤«6¢-\nZc´hÒÓ¡»-¢-*ZK²5:\"ÒÅ¦àÇs¢--ÚUizÓµ¢»<f¿íZ_´ùiÍ]§ËL¶ lûÚh´ùhrÓ4ÚûMs­8Ú´ëiÊÓL½ëQv­=ÍË²wjT¨+`­Z_µ9jÔØ[)xíNZ³WjrÓmKS£mSÚ¢µ°Ê%kV¨ìÀÚ³´9jÎÓm«;N6¬íW«÷´SjÖÕõVV¯­>­Cµi*Õ£;4v²m''µjÞÖ%«X®íbZ½XCiÖ­{R°-kÚHµ²°²n]¬WÖmok6Õ¥+\\¦'Ù#µÓkRÖí­k\\mwZã³Ô\0LÓt1-gZöýkâiÄ½ù;­XLaµëk^k½°{\\\r¬-Zæ\rl6×dË;bÉæ1[w;Ø°9ÆgQÛ#lrj5²\\Vµæ´[ µÑjúhu²ZVÉ-xZàKlÓÅ¯ÛdzZ¾*{lr_¥³döµåóÛ8¶Wkhõ³^V¹¦G[8¶ikÖ¼Â[cH­¥Úµa1Ú]­i6ÏísMp¶m×d¿ivÑíZàÃlra]¶iÖ¯¦5Û`¶¥mzÚ¥®ÉØ-¬[ËmÚå«	vÇ%ïÛt¶Çk^_e·Klö¹¥ó[t¶[mòÛm±yöÝ-¸Ú¾5ÿlrbm¸nö¸&\$Û¶÷k²_¸oöä-ÀÛn\"Ü*Â°VÆ@[#XYd.Üå«KYjýÀ[&NÛMºyVèmÊ\$¶b°	%¹sö±m®O=nzÝµ¬«f¶æ§Û´uk®Ý=¨«x\0nÓÏ[·n¶Ô¼9{¶¶íÑÛµ½krÜý©s¹-è[¶×oNÝ5¼I¶ôíã[Ú·[kÒÞTÓvVí­[á¶oÜ]¾Kgvçí[æ·So_m¹Éü­ñüj^Þ­¿i©öç-ÛáÑoòÞÌ½Ù Öÿ-îÜ·[0*ßå¿[töÍíðÌ÷·9læàm¾»vÖÏnÛï»màm¤`}AÚám\"Üü¿ë_÷\0æ\\ãnÂá,[nÀ3ZSp¢Ý}ºIç-¥[á¶pÚßM¿kj\r®\0Ì¸-1þà,Çûu¶Ö®\\\n·m~ß\r¶7mÓÛe¸pödÍ¹ËmwmÖÛo¸qa­ÄËxÞmðÍ¡·9möâíÄVç-ÃÛá·qâÔÑVý¦Üd¸8Jã%ÃsS4-Ù?¸ë7.ã½Æ²dçFn\0¸,tfà)Ñr§Fn9,,:3qbÔÍ¿y·!­Ûù¸÷nêá¡{×mßÜwjfãýûyV¦nAÛÉ¹,¦Þe»»ÖóîEÜ·¥rnÞ¥ÊS?.=\\µÕr~`åÇù~·f\\´¹rÙ¥å/Ö5\$ÈÕg{ÕQÚ5^ª3hTÃAà\n`ØpU¨·T^¨åQ¶¶'*U\0OÂª­RBqL\"Ä/5b·UÒ¨+Cúé¡÷Z4/wcª¼½QxE®b4_sH¸-õEëRQªfQQ\rS©Uéàm°l¹Qâª(æOB¥9®ÕªÈeÐ{Z_Wi%Rö«dë°*·Õ¹©T^«®eÕG¿L²ñ=ÔíwÕ]´Ëj¯jM·JJ S¹~ÆéunÊ\\´á©þ07a»L§e8jk·iIA_¹M.éÓI¢lÍoéÐ¹UæÍRºRX®UÍºÁrè½8käß©³°ôÅMºÎ+9no±*¦-ujMÕËìq)¨ÝL¹ÖÌ\"\nÕÎêÒ÷S.xÜó§\ruµ;)PV.Pºou¾©¥j[¯>)ÎÁv¤åIâõÔË å®/×ºt®óºvWB+ÁS±º¾Bë½Øúá*OSÀ·)ìÊxTk®¦]Q»;M	½k®Ñ«I¿º½Y@u¥DkÝZ\\SºGRQý?¶&T=ê%UþºgRÂ§s\r*Îwn ]ºaÏtò ÓúlZä&su¢UÌ«¨ôóê%\\Ï¨Tr\rÅÍ:°mn©ÕUuuZOeD«¬9.o]c¨wnæíÎf%7:×[Ku }Ö¢µî¸T¨ÒëÕÛÊ×_.÷S¤ïU2¡5G[0péDR^;v¬«±ª>Ý§}R~NÜ¢U'îÎA×TÿR~í´Uê¹AÛ»N\0¢	6(j(êB\$®-J&æl¶¿¤á(¦5t®euû¿ÌélÒ8eìÏ6é5|tkñ.Ó©¥J6õ))QW~lB¥7G\n¼ Ö=,l@äº\0ÆRS¥­ ¼Â\n£+K\"è3§Háã´nLàQ.ÄK¡s[¥yr7[AÖ¤]mK+É»Ûeï´Q\"Âò%{E1wK`Óå·pr|!µ;V@µ°;bN-:bÝæÆ7!Þ|ÌHlë{»Î©Â/C6eNØ0sÚg(âP¸ôRì!DUéHW¦f^4	öôòÂA<w¦\n#HxØ-»õé|×£áôC\rÁç{Õðþ(ÂÄ¨u=å8A £·¶õúA;ÒéÊD^[xrëÙ¼pöqÀBè¤=Oþ¹A0ÄÀ`1]DZÝÑ_¼¶îRãÏ7uÝÒ6ØCÀaJ®rz÷(£×\\ÉÁËÍâOØª|ÌÕ:·¬þR½Æ^mÑýåöá@\0=5l!zQÑ|ëßÎ	ÉBúWØñô#d·ÝIÒÀÂÂÊhì´çÝå (·Ä\rGEÅ+§|@UíðÐ71Ç!;R9QC¸47Yæz_#½ìyÍÖ`\"kå7È;au©Ý¤±ÂÒ³^¸jëÁC¸sÇYâ]f6artÙöhÐØ¶Çpy­{ZåAåT¦¿tØfù¯èX®Y=«¾¨	)ºÛ¦È \rí¡ø7yRúÐc»ëÀ_[W,	ró]öW@nË>_a#|yÓø&äÈÛ¡ÍÞ_DI×ñ§ ?Î\0º*u6ÐVï ÜHßëúúV´c:­YL\rjÕù÷.§¯dh\nBû¼´Uá.oÉß½bYüµó«ô!ñÜ}¿G¡¬ì0'¸wÒ/UÅ¿,U}nKÂÀ ttGÈ¿!~Ág@ÔÒ/Ùß±\\40ÂôKÄ[õÃ<oÚDï½ÙLõó+öà ^ºâ/33+7¨ÎÇnzìfô ¡ÐàJ%ßÒ¿¶\"þè[è s/îrp,Õ¾eñÌäRáq¿Ï°g­þõ B½­8*8GÉf¡ÞÃÛ¦±rÿð8»üé%K_çüäâ\0Û7Ço/ÊÊ^l]î\0 ©©\$¾yùðÙ1MÏo7ÖRwzô##iÝ³[LÞcGzVèñ÷la L@¾H	\\HPÑødC·üá\$cc±ÆÉà.xhïæfÉ#9zÉò4]5\0Ã\0¬ÅÀidiøQ§OØ0`Q¤¡ªþüÁ.8Áyò2gWªg¸3ÀZàÍ¿åíÌ®\ráÜ¶\"Á¸kiZA.+î'£f<O7DØ¦¾À6O1ÇX`HoÐÅÀænô\"Ú¨§ß¨ÿ`SÚê`(«¢ð=ÞµÁ\rv¬>VJ«\0n½OUºÀ(`Î»jÊ\\h»G®¯JBò¢«JÂ=í|Ý°Qß{ AJÂþÁQ/~²T&jð^nEÏ|zÀCç6¸/°Qß6ÝùïÌwáp[ßîüÓ@a\r Gd6ÅHé×å\0000®­ÏÞ\$âx8ÛL`ã7kÝ¨!Kù×½ïU½[ª\"g ÑçP=wrðÖõdØ@@¢Ù\0GIQNÀ7.,ba~~lK!	»ðE)MzÈJBÎXFAùXÆZOA!±²ØB pxi´ÁøFÀ¢iÂQýgô»JÀcT/»+ríUSÈÛFs\$	¨&ö=Õh¡àV¦XXIÞ«¥ áJÈÕ[pø6T-q4Ð;ÄEPU\0QÃ\0ÄàP\"	½b{g ) \niah4ÜLï\n1l+Ðû@9ái\0NUÝáÈNk\r`OKJúgÖ.Ëcå?h²jÃ	»\rRïT_®_t,5 Ö¥Wobµ>U°æÂIw;5´Õ¢sÂ©a¨å§ÂÆ");
  } elseif ($_GET["file"] == "logo.png") {
    header("Content-Type: image/png");
    echo "PNG\r\n\n\0\0\0\rIHDR\0\0\09\0\0\09\0\0\0~6¶\0\0\0000PLTE\0\0\0­+NvYts£®¾´¾ÌÈÒÚüüsuüIJ÷ÓÔü/.üü¯±úüúC¥×\0\0\0tRNS\0@æØf\0\0\0	pHYs\0\0\0\0\0\0\0´IDAT8ÕÍNÂ@ÇûEáìlÏ¶õ¤p6G.\$=£¥Ç>á	w5r}z7²>På#\$³K¡j«7üÝ¶¿ÌÎÌ?4mÑ÷t&î~À3!00^½Af0Þ\"å½í,Êð* ç4¼âo¥Eè³è×X(*YÓó¼¸	6	ïPcOW¢ÉÎÜm¬r0Ã~/ áL¨\rXj#ÖmÊÁújÀC]G¦mæ\0¶}ÞË¬ßu¼A9ÀX£\nÔØ8¼V±YÄ+ÇD#¨iqÞnKQ8Jà1Q6²æY0§`P³bQ\\h~>ó:pSÉ£¦¼¢ØóGEõQ=îIÏ{*3ë2£7÷\neÊLèB~Ð/R(\$°)Êç ÁHQni6J¶	<×-.wÇÉªjêVm«êüm¿?SÞH vÃÌûñÆ©§Ý\0àÖ^Õq«¶)ªÛ]÷U¹92Ñ,;ÿÇî'pøµ£!XËäÚÜÿLñD.»tÃ¦ý/wÃÓäìR÷	w­dÓÖr2ïÆ¤ª4[=½E5÷S+ñc\0\0\0\0IEND®B`";
  }
  exit;
}
if ($_GET["script"] == "version") {
  $p = get_temp_dir() . "/adminer.version";
  @unlink($p);
  $r = file_open_lock($p);
  if ($r) file_write_unlock($r, serialize(array("signature" => $_POST["signature"], "version" => $_POST["version"])));
  exit;
}
if (!$_SERVER["REQUEST_URI"]) $_SERVER["REQUEST_URI"] = $_SERVER["ORIG_PATH_INFO"];
if (!strpos($_SERVER["REQUEST_URI"], '?') && $_SERVER["QUERY_STRING"] != "") $_SERVER["REQUEST_URI"] .= "?$_SERVER[QUERY_STRING]";
if ($_SERVER["HTTP_X_FORWARDED_PREFIX"]) $_SERVER["REQUEST_URI"] = $_SERVER["HTTP_X_FORWARDED_PREFIX"] . $_SERVER["REQUEST_URI"];
define('Adminer\HTTPS', ($_SERVER["HTTPS"] && strcasecmp($_SERVER["HTTPS"], "off")) || ini_bool("session.cookie_secure"));
@ini_set("session.use_trans_sid", '0');
if (!defined("SID")) {
  session_cache_limiter("");
  session_name("adminer_sid");
  session_set_cookie_params(0, preg_replace('~\?.*~', '', $_SERVER["REQUEST_URI"]), "", HTTPS, true);
  session_start();
}
remove_slashes(array(&$_GET, &$_POST, &$_COOKIE), $Zc);
if (function_exists("get_magic_quotes_runtime") && get_magic_quotes_runtime()) set_magic_quotes_runtime(false);
@set_time_limit(0);
@ini_set("precision", '15');
function
lang($v, $yf = null)
{
  if (is_string($v)) {
    $zg = array_search($v, get_translations("en"));
    if ($zg !== false) $v = $zg;
  }
  $wa = func_get_args();
  $wa[0] = Lang::$Ni[$v] ?: $v;
  return
    call_user_func_array('Adminer\lang_format', $wa);
}
function
lang_format($Mi, $yf = null)
{
  if (is_array($Mi)) {
    $zg = ($yf == 1 ? 0 : (LANG == 'cs' || LANG == 'sk' ? ($yf && $yf < 5 ? 1 : 2) : (LANG == 'fr' ? (!$yf ? 0 : 1) : (LANG == 'pl' ? ($yf % 10 > 1 && $yf % 10 < 5 && $yf / 10 % 10 != 1 ? 1 : 2) : (LANG == 'sl' ? ($yf % 100 == 1 ? 0 : ($yf % 100 == 2 ? 1 : ($yf % 100 == 3 || $yf % 100 == 4 ? 2 : 3))) : (LANG == 'lt' ? ($yf % 10 == 1 && $yf % 100 != 11 ? 0 : ($yf % 10 > 1 && $yf / 10 % 10 != 1 ? 1 : 2)) : (LANG == 'lv' ? ($yf % 10 == 1 && $yf % 100 != 11 ? 0 : ($yf ? 1 : 2)) : (in_array(LANG, array('bs', 'ru', 'sr', 'uk')) ? ($yf % 10 == 1 && $yf % 100 != 11 ? 0 : ($yf % 10 > 1 && $yf % 10 < 5 && $yf / 10 % 10 != 1 ? 1 : 2)) : 1))))))));
    $Mi = $Mi[$zg];
  }
  $Mi = str_replace("'", 'â', $Mi);
  $wa = func_get_args();
  array_shift($wa);
  $ld = str_replace("%d", "%s", $Mi);
  if ($ld != $Mi) $wa[0] = format_number($yf);
  return
    vsprintf($ld, $wa);
}
function
langs()
{
  return
    array('en' => 'English', 'ar' => 'Ø§ÙØ¹Ø±Ø¨ÙØ©', 'bg' => 'ÐÑÐ»Ð³Ð°ÑÑÐºÐ¸', 'bn' => 'à¦¬à¦¾à¦à¦²à¦¾', 'bs' => 'Bosanski', 'ca' => 'CatalÃ ', 'cs' => 'ÄeÅ¡tina', 'da' => 'Dansk', 'de' => 'Deutsch', 'el' => 'ÎÎ»Î»Î·Î½Î¹ÎºÎ¬', 'es' => 'EspaÃ±ol', 'et' => 'Eesti', 'fa' => 'ÙØ§Ø±Ø³Û', 'fi' => 'Suomi', 'fr' => 'FranÃ§ais', 'gl' => 'Galego', 'he' => '×¢××¨××ª', 'hu' => 'Magyar', 'id' => 'Bahasa Indonesia', 'it' => 'Italiano', 'ja' => 'æ¥æ¬èª', 'ka' => 'á¥áá áá£áá', 'ko' => 'íêµ­ì´', 'lt' => 'LietuviÅ³', 'lv' => 'LatvieÅ¡u', 'ms' => 'Bahasa Melayu', 'nl' => 'Nederlands', 'no' => 'Norsk', 'pl' => 'Polski', 'pt' => 'PortuguÃªs', 'pt-br' => 'PortuguÃªs (Brazil)', 'ro' => 'Limba RomÃ¢nÄ', 'ru' => 'Ð ÑÑÑÐºÐ¸Ð¹', 'sk' => 'SlovenÄina', 'sl' => 'Slovenski', 'sr' => 'Ð¡ÑÐ¿ÑÐºÐ¸', 'sv' => 'Svenska', 'ta' => 'à®¤âà®®à®¿à®´à¯', 'th' => 'à¸ à¸²à¸©à¸²à¹à¸à¸¢', 'tr' => 'TÃ¼rkÃ§e', 'uk' => 'Ð£ÐºÑÐ°ÑÐ½ÑÑÐºÐ°', 'uz' => 'OÊ»zbekcha', 'vi' => 'Tiáº¿ng Viá»t', 'zh' => 'ç®ä½ä¸­æ', 'zh-tw' => 'ç¹é«ä¸­æ',);
}
function
switch_lang()
{
  echo "<form action='' method='post'>\n<div id='lang'>", "<label>" . lang(19) . ": " . html_select("lang", langs(), LANG, "this.form.submit();") . "</label>", " <input type='submit' value='" . lang(20) . "' class='hidden'>\n", input_token(), "</div>\n</form>\n";
}
if (isset($_POST["lang"]) && verify_token()) {
  cookie("adminer_lang", $_POST["lang"]);
  $_SESSION["lang"] = $_POST["lang"];
  redirect(remove_from_uri());
}
$ba = "en";
if (idx(langs(), $_COOKIE["adminer_lang"])) {
  cookie("adminer_lang", $_COOKIE["adminer_lang"]);
  $ba = $_COOKIE["adminer_lang"];
} elseif (idx(langs(), $_SESSION["lang"])) $ba = $_SESSION["lang"];
else {
  $ja = array();
  preg_match_all('~([-a-z]+)(;q=([0-9.]+))?~', str_replace("_", "-", strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"])), $Re, PREG_SET_ORDER);
  foreach (
    $Re
    as $B
  ) $ja[$B[1]] = (isset($B[3]) ? $B[3] : 1);
  arsort($ja);
  foreach (
    $ja
    as $y => $Ng
  ) {
    if (idx(langs(), $y)) {
      $ba = $y;
      break;
    }
    $y = preg_replace('~-.*~', '', $y);
    if (!isset($ja[$y]) && idx(langs(), $y)) {
      $ba = $y;
      break;
    }
  }
}
define('Adminer\LANG', $ba);
class
Lang
{
  static $Ni;
}
Lang::$Ni = $_SESSION["translations"];
if (
  $_SESSION["translations_version"] != LANG .
  3098912992
) {
  Lang::$Ni = array();
  $_SESSION["translations_version"] = LANG .
    3098912992;
}
if (!Lang::$Ni) {
  Lang::$Ni = get_translations(LANG);
  $_SESSION["translations"] = Lang::$Ni;
}
function
get_translations($_e)
{
  switch ($_e) {
    case "en":
      $f = "%ÌÂ(ªn0QÐÞ :\ró	@a0±p(a<M§Sl\\Ù;bÑ¨\\ÒzNb)Ì#FáCyfn7Y	ÌéÌh5\rÇQå<Î°C­\\~\n2NCÈ(Þr4Í0`(:Bag8éÈi:&ãåy·Fó½ÐY­\r´2 8ZÓ£<ú'HaÐÑ2Ü±Ò0Ê\nÒãbæè±Þn:ZÎ°ÉUãQ¦ÕÅ­wÛøÝD¼êmfpQËÎqêaÊÁ¯°cq®w7PÎX3to¢	æZB9ÄNzÃÄs;ÙÌÒ/Å:øõðÃ|<Úâø4µéj'J:0ÂrH1/È+¾Î7(jDÓc¢Ðæ ¢Ö0K(2ä5B8Ê7±\$Bé/Èhò8'Í@ò£,-BüÆQäE P ÷ÄÃ#ðO»7­Ct¿\r®`ÊØªj×¼©ú®[z0c|9¸h»É\$>á\0î¼\r\nÒ=Ãàù\0x\r\n¸ÌC@è:tã½\"²Ó~î¬ã8_)ó¬î9xDÃjÎÃ2Ï(¶-xx!óHã£Í.-Dâ; ÐëW+­863@É£º«^F+ÓÄu¢ññ\0Üá ¡*,1,ài£8cxØIÂ¤fÓÛ£lZØ*£ª/c¯s¯.0Ì0µå~0ÛðÅYWB0ê7UÐ\"øªã:®3³xuc@æ#¸Ð¿C`2'Ó3Xá¹Il*8¬¦3³Ê®×ß·¨ØÐØè Ææ55×3#\rñ¤9´Ö{5ØÒUwxjÂ0B®Í¡.'(ÈúÇ§hò\$ÊF¢JÅ£ÄÄ¼éC.!n(ÔhÚúß¨@¨ÓÐÃN*ã¢ºì¹»¶05=RÏtå»P­5Îü&×Ä»ÉEÄ:e»w%·8Ñ£¢beUäzÄ+qø3Ãd·ÎÉ*\rè|¢7(ù[ÌÓ°ÍzÊ²cw<Ã]« 0ñ|]±G]MØvXlôÌ³?wÊÝú¢øGìøí·Öó¹è÷Wª:*1nªgQWÏÌÒ Ë2Øå<µÐ1_OlTý@PT%¢PwQÅGå\"¤Ãp/'!RÀD [	9®U§fi*á<&ÄxÞ«x{A¸ôp¯²Ehæäþ*P*\rB¨u¢ÔkòÁÉI)G2ÍzSj/äi\räYu¥ÖLOjPJFÂ\n¹³²zS	ìmçä¡!&h¯bd]ÞIÖÉFÉö\"Ò,C3DwIÞBÜlcgµA´¦lWÂ\r!ðþ+ãöäOôz=G°\0 \n@QXj\n[Ñ16\":àÍû9¦\\È5reH³_qIy9s	KÐnËà4ØülL|ìL«@FîLÒøìùQHÉ7öÛËJ1ä2óòhå³uDX!0¤¼O)¥8ãkÙxDI\ròF'ôF8d&e3¨[#±ã-kºÄ^Y+eÜr	9£É *áÄÙ·Ì`É+ðQËÆDÏ4¸Ê¾wb¶KL	áL*4\0Ã*Væv1«0ä¨K¤1·?R4s	5ç0G]#4o\$DTÔ©HSfDe20¤fòù­ÒðFhÅHð)òÒvbz7F¸Ô\$Âp \n¡@\"¨@Up\"À]ÚJÞ\\ø0¤PbJ1H&26JË ¯Â<ÍQ²æ¨LTñ2ÐSd_Nòl/¤ÌÅ¤ Ï!L2Âr¶Å_j#KB%îIª(À(+\"	r±È*3gä6äyÓ3Æ:pÑ#¤sP\\¤¶#OÑ»´Ææ·kl\ruæäõÈ&AÉ¶ª´Î\$`lY¤Jê°ÑJ»N%ä½Í©jË}/ªö\\º¢`EÂ\nÁ¥`Þµ,XÀxF­\r¼2BZIõÃÄHÏòX%u3ÖÊYBa­n[©«@T-|9Y0É1ÙG¡B)ô¨ê­y]WÅ~ª37gÓ[ ¡Æ\$xN6\"ðª@¨BHP¿aBféò_\r~XS·NÎdEë`EwZùàÂP¢lÞÁDU¯\$ðVÆy\"î¢úBÐËóµÐAB\r\r¢3áÑaFÆÍ]úMäÕ±´Ò×K°]óÌÈ+AêÍ]uSÉc©h¡g\nNImL\"`%°ó<½.ìñcÆXMâ<A\\2-¢Ie ¡IÕFÊYË;%øî+#	¸fÍøô>YñöG¥Ú¯¯ÚLá%1dÎ×0r%ø,&(ñF`fZdaàAµ·o¾vMÙ~¾¯ÇðíûlYÊ4!Sð^.¾ì°ã·[aA8Oe¶,°Ô¶qaô^Ój[dâJý605l^t^,BDîòË¾H){Ãö°ÜqõÈïJ(q#K·Ãk!g5ë\niÐá<|ØKeÆ­KR!ì{{\r§í|{QìmÜ-ÇßÔrÂÛJ|î³ÛOÞ¸|}l\\M´`Ó»ôÞÙAu%¿ßy'%Z`Ánxá§v\nYDßù~O½¼Ù4Ó9Fz1sµíbÙúò¯YÁiôÍã§Ë\0ì=Î/÷~+E0êÙqÄÅ\n¶.fÜ\n­¦7¯Òäd¼!Bi·=ÔêÆùFWæX.³Ía,QHÚmÛDsõÛâßYxçËlô(fÌIÌ&X/ä(0VfìÎ2ønPñÂll\"L¨ïj.¬ªú/÷#Ð;î#¬8WÀË¬²ñciK/ð,5DÒ*ò£Ëà9.2\"|GcNôo*ô°ð¨ÃÃc>ï°#°òÏg)aâ2_èÊÅP·êd*ðVlgv`Õd(2 ÞLÀÈW+>DîúåôÌÛ0UpÔp¤u\rîæïka\0Î¡\n²Bû«¯NÐuåÇX0 ,­Ã:\\fÇÙ¨ÂÆ%Z\rp\n÷±+Pã0LéæhÇ^È\$c1(|ñHWÏgE1ø±0ú3±d\n/eÐ\n£yp6GÑ31u°	ÈÂ	Äbd	c~E*]\nbÀÞìî°ÂLï\rb6!cæÖÀ¸,úO5¢'e°qb.\"Ú	q³eßPóí:ÕñÎß.µÎùO\\ä4\rV£àÒÆmÄMNh3§d`ÒÇj/%¯¸Ç\n ¨ÀZ&5Âî9­LÓ/\$LÙmÏ®	a#É\"qåÖmkèö²âï!PÊ¨ £j/1(bî(L!¢\"g §X©Å<ÙL°	c:):¸`®N\"Z*ãÒW,.rF!.VÆ\0ø®G+ÀâëBäKÙ+/5È3,,Î/Õ,ïr6\$´ÁîU+\n¶Â+lLFºïßÙ\$ìÀëê@ñ0ËaÎÂ\räÌ 0«Lê\$ò²2êÿ.*¢Ø\"a£ÜQG\nF³rr#þWËx:`³ÞË6\0äâÐ¾@ËhÐÆl (BÔÊ¤,E ";
      break;
    case "ar":
      $f = "%ÌÂ)²l*ÂÁ°±CÛ(X²l¡\"qd+aN.6­d^\"§Åå(<e°£l VÊ&,l¢S\nAÆ#RÆÂêNd¥|X\nFC1 Ôl7`Ó\$F`Ç!2öÊ\r°¯l'àÑE<>!!%ó9J*\rrSÄUT¥e#}´Jü*¯Æd*VÍil(nñëÕòý±ÛTÒIdÞu'c(ÜoF±¤Øe3Nb¦ êp2NS¡ Ó³:LZùú¶&Ø\\bä\\uÄZuJ¶Í+´ÏBHd±Nlæ#Çd2Þ¯R\n)èÍ&ã<:³\\%7%ÓaSpl|0Ñ~ (ª7\rm8î7(ä9\rã@\"7NÂ9´£ ÞÙ4Ãxè6ãxæ;Á#\"~¸¿2Ñ°W,ê\nï¤NºlêEË­¥Rv9Äj\nV¤:Îh\\p³¾O*ÚX¨åsò')irÛ*»&ÁVÌ3J;îl1åBÊû+lÂÐø>ìj\\ÊzÇ1,Ìt²ñ*Åï4ÜNºA¨/´ÚH%-¬=lLHBP°G)\nø\$¤R2òE¥t£,Òê]4é­óR25 k×(ÓÂã3\rÆ1¶CÖ3×51Aí(î4·¬ü.0µ0ô@9`@Y@ä2ÁèD4 à9Ax^;ÜpÃVÕðT3ã(ÜÃ£?áx\r°KJõÐHÚÝ\r#xÜã|¡1mNR*))ÈØU8I\"TLì\"8I°[RÒ3QÓ>,Áj¤\$ºWB¸Â9\rÕú\n0!VPH9ÌCMyRSDBY({*QÅT»ÀÂ:¸ì0¨ÊüEÝ\$ÎìD¸ä)*0)0kZJJÍIä16ÅHRÒá°Y.©\nG¡ôü °³¹KÄËk\$#kchá5è Æ0ÓÞ5+\$Â¢&-k[Á96]MÊTlZTá=&ÈgØ×NéÄìX[»Ì1ürÅÛ÷/tñÜè²ÙQë!×oÝÆ#»ÚÛ¦R±J£ñ²BÇä{!°ðLþëÔàP!N}·sàôYthê9Âö¥ïk®i\\ÀäCË`Äû0|Øf\r!ýXØ =hí÷(i_	@¡ºÁ4ü(x7a¹_0\$x¢ÎRPAÈ:Àg34¡¼3`Ø«Ì)ùTJm\"ÄE`\n\n¼Ô/ðÜA\0uÕ]«ÐÌÖ\0l\ráõ5¤H!0Á8&Îë¦ì0RaI1cSr¨HÎÁ}-¢´CD\rýQ%_À@«pn@ËH1Â`ÒÔ4+]l­µº·×\nã\\«@ `\\º×jïBPp:0Þô`²÷À^Ä9aF¹&°#\nHxÌ|2¼ÈÔ:S0ñå*U>Hj7qíx¯5 CÀp\r+]-5«#ÒÜ[Ëq.@î¹D]AÉv.åÛt%ëÜ\$ÐàkÃjî>ÎÎM«ñ!­}¡Ä\r§S.¥ÊFRÉ¢.d¤¸ø#ê\r*º¬,pØä¥=NrC4ÃC5yD÷%¢¢XæôÚPgH s2u¹	Ñ©\nFâ¼bDBhOA'`jC@p\0 ZI#®°¢½N± ºA9;§ª5¸ØVr¡a³7èUeÐðÞêñ?a®½©É|©é­N&hÐÙ³YÍ«È\$\rêû\rÁÂ1¬Õä³ÙÈw7Á4FÎ·¤¦Ë¿0ÊOÒa@!0¤«7\",­åGE*(qhËÄ­ÙIj¨ô© %\$Ê8äRb:F:Þ¼¢`L¡6>g,AKò-MÊZ3yI2K(I&}XÈzÈAtð7ÆÍk¼ý\0fA¶?Ía`P cHBÖ4:lmÑhéFÙ ÂT\"Å¢j`ð.\"×5>±÷sZA3\"\$µ({÷R©ùd9ø17÷\\êðY©pD·4WÞÍÂ. ( gnã@Þ).u X sÁ2ôÕÀ@kV`F\nAþ3Ó9ÂÈ½7®ð#HqÇG%VËrJXj_yLQë±pÂp \n¡@\"¨rþa&\\ÎãKÊe\$a çúíK6{Ï)Â¾\\NØñIÊÅ\0TÌ¶y²jÂX\$~ÄIh.å¤Tª LQü>Ô¼K«[Í´,²Òêëé#lÂëTÍDiá`³ £´ùrÔ,=Ø¾zdu³ª-3]»ªOæ¹{î0ñlcøB³ÖF/+-À|c¬~\0ê³D]ÂLi'g¤{¦ñnD'õ>ªví	uoìümÖû^ëq,mè£´8S\r!é¯ÀÍÚøS¦¾«UçCH\n	½sÕãR×i	t¬2ÎÐÀxµ6ÑW;K0\rÒ¥Ðß3ZC»(-Ì¶cÀ¤|;Hë2;\\Fï:1ûK<=ÌöJeI.% ÉF)\$PG<¬f\nt 1²\nÖ¿)¥]p5§w\0ÃÑ¾½cúGötòH^ËähÝU'-AFÐ]ª\\¡þ]y©Ý\"¸3 ÜOæ?W_>-4ÎºrÌÁP 0/7*BÊï«`áX\rÚ\"LªR~<2^ñ±3ÊJîl¼((ÀÁ\0\nÌønØeZ!>ÁNz³÷ëD*ÒöwNigÖD9Ã.UØÉ¯öF´ãF½rQÅ¨÷{Äiï¶÷À'lååäHp	zûµ5i»ïmáFµ·Ù¦/Ìd#£}úÿwd|A\0î ¢\$º¢T(nà8äØOÂVÃ-¢,C(Á¿-¾¢ÚOP\n#gà%IdO*Î\"R0l¼ý¢M¢V\$\"`0k%¸Âdh©)AV\"'c²\$ø¯LwÌJ&Gt3\"\$!\\xM6éØDTÒÕB6¸fT!ï´vNSRq ö@æýd`-²©	ác\n0(dÅO\n.{â]Ãöç¾wÄgÉ\r¤SÂå\r²ÏFb¤æ¹¥4Îç8êÜ-¡lKÇÌA\n'~Ê\"dOàlètwþzñÄM±\0:¤¤J°ÿ¬E\rÐÒdpèP®MD,ñº\r¨HNìúæïTÞ®ì¶0XÕpTj-pÖv\rPG!*Qå.%Bu¤ÓÑx­\n/tÄhØª\rDc;q¦yyN!\näQ|çÅÆ7Lñç¬¦DqÐÜü\n+¦)FÄ1¥¤y\rÎÑÑ\"eðùEg´ñì«QêÄhH¬®ya\r¯D} âERR1Æ<1Êv£ê²'!\"ËH¼#­Pî§PDÕ/¡P+Ð&/e0p6þÃgMe¤(ò]&	d12fÛðnKø?Tõê.lçRT:ðU%-A2ÏPÛÑ¹æ[%æ2²'ìèª%R% ²ëÏNlÞ¶o)ÐTmíÎb#ÑÐvp¨ærMÒ\nÎRõ\rÑØ/ðß#cÎiÒSr.~F\"Ñ1Ó´¤[2¤ws<s\$KCcÑ-±à,®n8Ã¸c¤ÌSO5#\rÐÆSá\r1òðFn13s2³?6Ñí7Ès2Ôà-ó\"qbú{#%0§Ã£%Ä¿03A96qL\"°h&3Äc8K\rêùr/:3. Õ<Òï\ndi<Èù7Sá:==A>³SÃ	¤HhO<í4þMs/¶m³¡@b-@ëNÈ4\$².:@É\n&ôN¶®»BCf¢%ÜW&²¯5B#ECô*¥NÂô\"@4FkCË?jFÝ³u=ÇFä8ðSx)³:t>ôÃÎÐNpPb12siåEE´s\rR×JO#@QèP´°N(Ð×BhSzSAÊ&Ï§v>ÄæBÑm9­­¤hlC*O£i-	5Æ·M®Eodg0VçÔh°ßO£M¢ÎFüeoã®ùbn;ÒëH°\rV¾@ÓCªY¶k£x ÞÒÈY@Ì|~KÐ²H&\n ¨ÀZ6µD=¢÷Ç°O/Æ\"/kktg¤!ÕÖPd°æ; T5F&~õ¦¢v1COã.î'r\0E>b2M[B»-­Vä¡Í\$&ÆÅòZå8.\$âj=¢è\"êðþ-ÄÒ,ö/±nð;âLJGqÖàÎöí2,0Pé1TúIÒÐ(F¡aö(ãf4CIBB\ràà¥\\å¤{öu .F)Nhmäµ):lýB\$bC2Ôñ'rÃçcfÅGÒHæy-M^ÕéW¥\nÅt ê\r¦B\$q81§B\$gtIöéÂè/ã``ã¦14¹Q£\$²´ä¶â\$Ò6¸ÒHu`ÞÉàî5cvNóÈJí3+åL&än¤¢&Dt?f\0	\0t	 @¦\n`";
      break;
    case "bg":
      $f = "%ÌÂ) h-Z(6 ¿´Q\rëA| ´P\rÃAtÐX4Pí)	EVL¹h.ÅÐdäu\r4eÜ/-è¨ÖO!AH#8´Æ:Ê¥4©l¾cZ§2Í ¤«.Ú(¦\n§YØÚ(Ë\$É\$1`(`1ÆQ°Üp9(g+8]*¸OqJÔ_Ð\r¼ú¾ GiÙTÆh£ê»~McN\\4PÑÊò´[õ1¼UkIN¬qëÖÐèåº6Á}rZ×´)Ý\"QÚr#Y]7Oã¬¸2]õf,¤éµ©¼D5(7£'êÆ1|FÃ'7ÕêQþßLsâ*nËø÷¿Ès¸¶æ0Ê,¬Ë{ Ä«(H4Ê´ìÁ\0\n£pÖ7\rã¸Ü£ä7I0Ä0c(@2\rã(æD¢:Qæ;Å\"¼¸ë>P!\$Âp9r·»Åë¸îó0³2Pb&Ù©ì;BÒ«C¼°2izê¤¨RF´-Ë\"Ø-ÊK´A·ªñO©ÅJ<¯ä\$iØ§,«²ßJãµ)(fl Äã§hQÌ´-Ârã:Hz-¾;RÆµ*4l\nÔ»K\$6hõ=?T¿ÕavW)\n7(OÆ\" ©OªLÒf\$hªiiÔÌ;´P;l# Ú4Ñ,gÆñÌwÇá\0Â1q°çp÷TgEÑd;# Ð7±ÐÂH#\"Éåþ4C(ÌC@è:tã¾,6õÁÑ@Î£p_àcÈJØ|6ÅÀ3ElX4ãpx!ò½\\¸èû&ÊÛNz¥e7ÖiCT.)½>6N8:²ªbzâéÖtÑjJ£Âh4ìSÉb¡Ê°§(Ôê(²B¸Â9\r×\nÃO4¬ Né»%í*M)©ëTHí¦hpäÂÒ£H1 ¸)<SH§Édñ6¼t,m?\"h&ÃIý%¥.Ãó£ÎgæÍÑ\"â¿	ÉqYKo<\"êUp£së´·4I¿rØA,4ÙK¾-htäzÖ Ís¦Í¾÷ôj)Î«;íÚzì%v|Ìê¦ÒkûüÕ¹Óð¦(Ä°hÉ9ö25÷´£¢¦=md¦Æ¢²ZwBgTûuúZÔk@ªå¢ÚT\ns¦ìÊÁV`ëÔ£ÁówÛÈ\$b\n¬Â9ÜBÅA'¦Ø4àTÏ<A;'	 <(~ûð BVP¸æÀ6z²!8\n+`6Pæ3*a!Ð:·ÔÛxr!å³gC>lÔ3ÏC,mÉ.ÊHsEÁ±E´Pt¨ÞCÅ°Ê£!]ìÕ3*ãàO#·\rÐ98RPcy%É­ÐB¼Þ¡ÇpðaïÄÂEÁ;;@¦taHq(ÀsàRLD#ihÊ¤Seû}1®û¾ÖÐ}JÓu*pÂ\\UÂ}í]Ê	g-K<¸TÒìÃËØlÉJcËå{2y¤ânI£,JÕ5ìÊ¨'-ÜXrÄâ]\"|LÂ h(y³\0ä\"òðuo®ä\r!q0`@ÂSaÌA1F,ØÃ¢Ìt91öBÑ¤Ñ }LYnñè¯\$è²9íQXôÖçÂI43ÍÂ2ÕÑ>.Bø¯)©HIIRdZ­=>Ì2\"èAóøØ \\(õ al5±&ÅX»¢¬q2E#äntÕ ¤&iÈQÄJê>=;'L*=ùLIU\\ñTïÌAºÌ°õÎåK³ê´T8#¦-]Nw!¢É-D¡X\"Qè@hFK®³P@¾`lKChen¡3IÃªì]Á:Û ØÃ<~_è/¤e ® l&êfCd çæ2AÄ'(\$×þ+¦Vi[)hdJÈP	Bó3ûÐ¥SlzÂg[\"ÄÎ9åÂé:º¡½ ÒL÷	#ôo QÀo_ÖåÜ#SÐ=d\rTõT¬î@\rOºYr7_áÍ ®Øúî¶?õ\$\$É­¸h\r!4EÈìÃ­Ú2È!0ÅàËÊ6)Ti%ò[WákÄÄhâEYÕÄÈ}T¡¤Ó*dM¢TJ°!£tQ¥¾Y¨ôlHq9¥;\0·j¸.oh«G{8býkÅîhI%¹cæHË+eyÇ¥\r(óT±ÎLÔ0N¡ÛuÎJz'J,ÇÕ)ÙSØ)iPN¯Eû9ù¨Õå'WFi·+å´²)³CaqBÖÖ-CVüíKEúR1tÉ(eÇFÃ(o^¼3YW¤\$òM\njÚgV»ÜyIø)nòöÿJªi*äV\0/æ3>Ûî[h,X qª	mö«2á}E;R»·÷no/`QlúLÔ ÚÅ¢+²skòÛ§88B±P_2É.­u?ÆË)äìÎB!<FOÌ7|G	¨7¦tõÝ&Bè[á=¥ªØiAÖJ:Ê©B®×©ÌFYÎA#©¶q®Ï Ë×ï­ÅcZ;J­u£Vf§¯ú×öå\r_F¶>\$øXeÜ6tUîä]>Ýê©Í]?ãò±Å^©ýdÄ ²¯ÀßÅ³´	¡Vb@A~¯ã¨¿çÐÃî\"\n4ß0¢&ùbÒÕ÷«+d£w¹ªë³<ÝB/rxùè)Bt^p7ÌôùB¿!£ÙO*Ñ!07±8`uº5è;¶dûG<Uh ÷C×Ã@÷-^¼UzÍÉhçBð§¼ú\rzúâ*yÚÐ(å`jh÷P û¢Þ3½ÂÂWé²(X¥\0éî ±Âãä¶@Æ\rb\nfEdÉË'AÂ°.¨b0PÛZ3B\"VæxôHho[\\{\"(Lðq+íj4h~óÈ8£\nÞÈø\nOèhÈHÆÁ	tR°À(Å~ ¨\n`\0â¤J\r\$l]FÄ\$t¹ÐI\"HÅ2(jgÉ±-T³ÃÂFêàAÌ8¦ôn¥*Oø<¥²uÍ\0C\"w'Öá`I÷ªÂÕ\"(Âð	¯âC1µQ8Ícl(ñEèTÚÃ«Â(BîKÆ9í¬fíå#1<¬Oj91 q­		®oq¡Ä\0Øqcqf0ïÍñdçÎ Á6'.¾#¿§¸Ü'ÐAË<\$pÎ*\\àP@åq5H0©JpEJ;Èï'VÙ¼Ëâ-+êV§râÇÎÂL­>½GÏ¢ÂòGâA.#µÐÍ(®uî²Øb Æ\$\"\"\"n<Jª¸r\0003eC²|®`{LhiGTñ¨`xãü\$n<B)mD¬¨K\0 Í¸©å@B¼BI¹)\nµ)bÏQg*
2qiôÎòzÎö«Èò6B¼ùèbú.novÑúü2ÎÐ-p*²Z*¹)­.¨lrñ-î*w/2p²îbhF¼ÆëDÔbg>t+&Ñ!huc#¥¶NÊ1á 5Ç_Ä#Â\nKîç34XYPØG<)³(3ÊãäÍ1ê¡,'¨ï2ò0+ÃrpÎfè°DüoÒ\r¯Ø¾Rï¬e:îhd«O­­).g¨ðöî ¨S¶Ær­/CèáÇÛÈã3°uíÕ<é=\$á¨+0óÇ;3ÍsÑ/2á8kÔ½Sàh©-³»*ðUþæêzP±)T0h\$Óõ@¨ZÿMÕ>Ï.ó±ÿqðù%Bk#²\0ÞNvið¶³F\$k) 9ñyçe0TNæ«í@;E³3EñBñÙ+QuO\0sÅ	Yc\$ã´CìÔ.4>ã«JNN°Ô?@´Ø[J4'!T+0ÒúqæÎìå¥Ob<PtZcÀÃD´ï¥â^ì'\$¼pÄiî7­é9P,ægÿ Äª\r|é¼Bh¼®.Òóô3éJâK/1Ù8K×Rp9(pÅÐéõMsRû°O´·DÔ»/¶×æ­VÝ;Ô§.QUWPY49LÓ6ILñPÍ((j°	éÞ|ñùA3ÕCSÂ*©Z#ZJZ³ õ´ÝËÕN4ÏY±SÐ\$k£I]Á²]1ç]ãw^%Ú\"DÁ|oäº#pxcxãïúD¢ñ©è5ëâè|\"naO3EÔBTtõ´©.tAE#H6'Xm/uÃ1 5ð»LXÌiBõØkp'fÝ£ó&ï9eÝGö7d¨Y?~QÖ^v£õYHgcVIHP (Ð¤ÌÃ+'Xe9ÓfwÄb}bÕAË17 Ý|'Ã03½eóM0!Î¥j,al\rwjÔVöCc0Då.f¶ÑO¶Õkö©\n¦0hnÖºÌ6õ2°«8õ-[ÇMÖ/X%ËukdõÑXóeh5e¤ØTvgsPhk > ðr\rj>F¢d\$g3ë}WE7JFëyuÀ¸ÌMt0atvàDoßÑ)6\\PqOqcÖ+.\"CJ¢Ë-7×#jöA@ux­í\0w¡W²¯l,L7-påÐ°76Ü×\0~!¥>¯^uð}ÒWðÒµZ³Áy¡ko}3l`47ë[ô=æw~,\\ÛwéqW¹MrK/394g´Ii¯¶s)1g¯JNOcÿK´-BQ'fÂÛXõp8F,Kâ¼p'ïr°ë§'³fêÆÖ,×Âé¥ô½)8U,EF´BØbÖK¢>ìdâ³'m«FÞü±ØÛdÎ,&½NÈbµN\0@\n ¨ÀZÆä,ÀuÂgqoõ.áî\"YxFNlV:®® \ryÒJ\n\"gØg]`÷ÎÔÐÎo6hOú²('sp6@î2o\0Ä)Èo	ùIf\nøÃ6:¦>¨<ãÊ>.ñS\$Û_c½>R¹z%dåÃ}pN¤xr\"Õ¢â,V\n²Õ/öc7ù\nÍØÔ1Á/ÓÛm\rÕHêÓ!/qTMzîÓ,nh>³yÝµyv1ÙïÉÌ2)msrÓ©3R­EWg¶ãçVÖ6Îg>×´Ûe\\=e`¡ SO© jîõQ=+¯­90+7¢¦+ç(4ôVìCùG' í8Q UÔZwæµ\"6ä qì:kmFù¯®µ£o¹L%¯'Rk4âñDfù>O ñ5Q­Dò\n6àÞÄJì,7õnç·o3Yon_BÜXuöæ%avÊ½Il;WØQl";
      break;
    case "bn":
      $f = "%ÌÂ)À¦UÁ×Ðt<d ¡ ê¨sN¨b\nd¬a\n® êè²6­«#k:jKMÅñµD)À¥RAÒ%4}O&S+&Êe<JÆÐ°yª#FÊj4I©¡jhjVë©Á\0æBÎ`õULªÏcqØ½2`©ÜþS4C- ¡dOTSÑTôÕLZ(§©èJyBH§WÎ¢Jh¢j¦_ÜèØ\rmyioCùÒZ²£N±ôür,«N®%Dnà§®ÐµUüõ8O2ôín©Å­r`è(:¾£NS7]|ôµÐÓ8Ø2É¼ê 4NÆQ¸Þ 8'cI°Êg2ÄOyÔà2#£Ø:\rKô:#ì:E3¨©Énm §;KÄB+ñM	Ð¬#©îêG¥.¼S9hç³åò6Ô«mTëÅâdÇ\nÙQÁíªè D\rêI£lëjá'Êú@Ep{¬ºÎÌLÛDÐ¦õ\r#pÎ2ñ*bà+\n¼D±úN¡ÄòtÉ¨Ã+Hð*Ã[Ü;Á\0Ê9Cxå0o`È7¿/hÞ: ðáAR9ôÉC­â7KµOÃªxß«¬ûNî¬ÈÜàêÅ%4è­²}4°k[Æ¯#m¬q8	äCP{]G©Ò÷:Â\rQ-R(1T4Õû¥7í¢âAMõÜc4	Ò®ÔDa»ëÉ*;6Bj(ëÓcÝÖetË¢ãZ ê-QÁ·ElLÅt}CÉEX'Jéì]6\näñY4]f'1OQa\nB*9z,iÔlK°Lõ® ©¶6JvP®;BÈ6Ë³øAEÑ¯u 9ÒT @0cò9Ë£>ÐÃöãLóRáKÓ#Èæ¬4C(ÌC@è:tã¾Ü9¾sAÐC8^2Á},9ÓÐ^,ÁðÛA=èÍA\r°Ò7Áà^0ÔåäSe¶CIxÉµ¶ÔÈÒÂ|E§Y@» ìBº×YQM^\$Lp^2bóeó¨ì³ õEcx¸RãÙCÝ¥m4C.û4ÕË'ÞØ4B¸Â9\rÚJ\nMr>Ç´¶­ö-.ÜÏ§Wók*/zN9]c_ºu/asINÜ*kÍü/ÀPDlA:Å l¡q/²òpÛÅ-ÔS*·:r|¯E\0:Ç*HN¤¦LÔë¤'eåºdÁØ.\"çmU¨K!©C\\@>ÔÜîS2CCB|ð/&eû,ÕâýF¡°ý ìAa\rÀ()QÔGÅåÂ¸¡ü+Ck6!AÔBÈC2e×Äåbæ\\µ	¥ù%ã£©àîÎ;g?]´\$XDt.Ì\\ñQ1­\$YÂ0øÙêË°é²¨cÊxäéPFÒy*\"A@q'xFËì'ÒBC<FT.1¨ñ	iÚU¡´:5 ×aÕê44úôÃa.9È É ÁðCq4y¨fà>,§é.Îµ¢±ìUª	?éã:ÁôU¡<  ÜÒ`srK-l 0rGó½3ØÃ0f\råÉ2r\"AØyoAP7÷È ³ü:´V ( \r¼3¥Ðæ× r¤!3Û;^ó8\rÁÕæ\n\\<+(Ù¸æFè°¦3tf\"A¢,î2c8ää*ò~8r¤M!/mNû\\t4Ft×Calm³¶ÖÛ[{q¬\rÐ97fðÔ\\ö-¼ }^\\R^53AdÌ¿bf8v\$-É[øLÕI®Quì­*Afj¥á¡rj@Ui½·Ö´Àp\r-MµÖ¿Z#fm\r©¶6àîÜnOà¹º·vó?'õ\0¯®\$Ðà}kx¦|\\¦öOà q!­Â©TÿGîUbPY­Hñ`¼Mêýáª;,p¬rqB'\"ªÆ^Ü` j!²*#ØýY¹/d0kF¤i3|¥RÒÔúÔP!û\r°9¬¹A¦Ð(h§<MTéU7:·Êæ.Á@\$»,H<-^%_d=Ø;  ³êVØÓGº¦Úr'n·A½ÕÛ¢|Oõ>áì©0ä'aúgÍúðïD®ö\$zÄXñ½­ fÔ³FÌ©õ8PÜ)ãWoeìt\nÃEÒ­_l{\"ÌÍ®H¥Éi)Á\0C\naH#ã¸]IiL¨\\ -H>FÌÜÈbfp¡º8\\±6)¨Ë#ô¼BÄÉPßùHJ1z£\"¡VZñ°ÚÎÂLÊRÒZÜñYí¶¾éë%Z½á(þÎ«Hy<Ìî²¨æ~Þ.?M8À¹2\rµzÜ³¬ÚC+Q8+3)cðä:%%JÅ£@xS\no~¶FN8\$q¼Ñ©ÆÔ@¼MV³¢,»¹G5>++Å°»w£/.Ó?­¢ÿ#sçmÅú©[qiarº7FJÑ«`©¢ÓÙ\r7¡5-Ñº\"=j}gèA«QåË%ç¨aQ3¬K>°÷îª5]yÛF Ä¬F bÛ*õ0µÞ\\d¯O#Æ­ö¸4JYtÕvRòg`ÆËRuf¼)ä<JGq+G§nê¨Ë¾Leaï,ûÉì\rñ'ÉÂ´=c2yþg³¦\$v®¢|÷q!¸LIìêds>ò´Â°¼é+±¢È­R°\nîÂöÜ*£{G/Þ4ÓÚ¯Û¾só¤`§LÃé»Ø¸]à×ÐbNJ6vÞóÁúÃó±OçM}»bÌ§[\\U¸bÙ\\\"­+«ZN(*¦^8\0Ùí`Ofóª¨ì!	<°§ÌB¤ÒLxª ÂÜýdÒHK§\n`Ê>ÌÉÀÊ¤L¯R@\ræâÉàL  æÂ Êo\0æ\r'M\n2m;É4bl´Oì(¢pL{#è î'GX£uéBZä%Ï6C.|¼ó{H2VIwîðxNTÏ \"Á,L®ª:¯ï¼éÌ84OÖoJû¤~l`Ö  l0Â\rp·ãTã¤ìã¿D¦*Q\rë<uOàâÅ~-Åâá£4£p1äJëÐ@}ÎÚ+iJvè>bDfhbÇÄðPF#/[¥êNeð ç`ü´	xfj\n¨ 	\0@Ü° QªÛÅ\"@E7ÍD¢x)­¦òí|ËBÓð{ ^ÚEeô4º{\$5cÃ\rCvËíkNCÃt¨T\n ²ËÈÕ^)@úãTÅïoXaH(Sqg ~9_ ±ý\"£ÿR,1EÒ¨§vâJ\"ÖW1õ#ÅFÊ³Û\"QúJã>äD<ä*0\$ªh¸ÈÀëíFCQÈ_¬<Ejþoó¯&RJò²r-®m(Y(èÕ*eüô1s)Ý%I,×Ò¢bò¦s²ªØ2,ênÿ#ä®¨f	èÅâ\nWG·+,3°4#.WcëB*J4ä\"ì]'ñ&±Ôª2Ûîñ,r¢ºÒtÇ\$ÌýìÁ)Îç/GÀ!âÍ02öWYÌÁ&YP.³Í/%ôî,bC.×Äzx«D£Ô/£iEµ5ÂâØ¶*0bGPGÆâVpé2Bâd8S0®)#¦.9Æ(ÖÉ#.ø^'TS8}â÷Ðe6O-Ó¹-rE(RV§<o/ ¬|³D_1Ò[ ó\"^ÓÀËç=0ñEa\n\\×\$2^Ô¸8sÌ³ÐfõbásÙA¢Ô|)j0Ö5Pö3õ\"søX43t8û/·é#BÔ2IC4ÕNÐîG9qd%âuNÃ/IDSÌu(P àcY;.ñðJónÓhÅ)Eë6¯c¯p^(-J4.+\rFs·F¨}Ï3æaï{;\r9&s·D4/EÓÜóÔÄñ}D(IÄ@Úâtîí( îô~4¶E&àÐ\"KpiB´%ÔÙ&ª¯tJ«4NÕ\räCMTZùT3Qµ;E1YµDtÛS®ô:v0ÛA5D²5SI5IP´ÙPóÔø±M=²ai6±.2OEtøÅÑ-_V)árÏLó´ÇÕ1XC2'EoJuX8{E^&RHÑÃAG`ÈLm%\r,c\"T×SR1ÂâñÕ±AU]T»JuÀÕÅGÉ\\¯¾YµÐ/ç*Õ/Nå./¹V'Ðk[UI&üÆ1\0}CÕW`Õ[?vM¶ê\\õ1[vóÉR//ÆÅ£U½JM~LÌzïÆ+6IZ!cPN¢ºB6ôF/­|Næ²¯­e\\-5%©6dDs_MÐÌZRW¤Ìd.Ò/êÓk]qYLñFÅSgb ó½½t3ct1&þh§rÑÔ68pó\rö02]ñ8ñYZ¯½!LuÆ/V*ñÃkTv3&pKBSÐLÏcM·Sò9XVrw.7céwÔl2¶­GÖqöq4Ô»tMtcHÇûYÕ-uuuD×¥±rÖAvw3v´3ul8Ò&UF3¤e7YFâßwW?A«<VòÜY\"rhA_£k7Ô2÷¦+w«]L½Z¹LíZBVû¨Ôs¯V´)V÷%w××8CTs¶'UVï\r×1~u¥Ï­G7`÷ûDWÔ¨AîA|ìù.OVõ¶îGª}oËEWòo1p×Õ4)w= x±uÌ\\Gö<·/o¼îâ/';g'ÜD­v\$t\"ôÏr	Ù5sòØrE[÷x©×Åu42ÔX]xw`F[55Ô³Ø4Í wcx×Ò¹¸2åyâwP×â|ø®øøC4åRôQX8­WåÓ©	bÖüuXêDEåww;¨ù\0ËCÐQû\$ú¿¿=#ô¾ÆðÊFËé9y\$ÂÄ(gLñ±%ïe¦·áaUw¸[9cÍ7%í×ÓaçrYgù7ý4a&Øb÷}3½+4UÑDìî½xpQí±íØ\$<ia3ÓqÆ\\ùËxK¿.Åµ×?¥_Xâ\\æå9TCa­@7I0)o×Û|Zr²{U+só'½^ØNY¯Â,ó5ù#?1V_^Y]/2Ù6'z;KõÈ{Ts178\r/ï¼ÕÍq¢¤TA!5½zúA¦WszOi±c<%cg¥%Á_ò#µ`jb\rVÞ ÓËâjÈD£ÀÞÒÊBjDÎtÎÏÉÚ\n ¨ÀZ	ê¬zÄKÄ¨ú\$Õñ\\ee_ºã¸Zr±£\\:ø:ü}©anúGbúk~=ã'o (o¥RÖ|â®F ¬:Æ2Ý Nð]yëM|iK£¡i¹}CçiR(c*a_U d|åâ%ÔYÉY¡îdplÈ@[®¤¿%ù1M<:tCÖÌ±ûG#Ñ\r)¥¨y84O2ã°zmC[ÁB(þ(\\ôPø±÷²[Ñ²ð³Ð4BÃ;I¾U¾c:]\rWK·OªRÚ\nâ?CÒ=y# Þ\nög¦r\\4ÁLT¢þg[ 4Á¾³ý0­|Yg+\rhD¨I±vÂòJÔsP/qôûR3î¹2öLÕE\"Æ¥«5gÄ×f+ \nÆ ê\r¸l3´I±v¸ÿ;::3Þ½<E¦HÞìðíâô'Bc¼|b!rÑöUN,sekï-½Ç'¾¯?Î(ÆóèñçÍ¿`\rîãä@\\ÓÈÒíh1|VÃSzø+·%\"]W 	\0@	 t\n`¦";
      break;
    case "bs":
      $f = "%ÌÂ(¦l0FQÂt7¦¸a¸ÓNg)°Þ.&£±0ÃMç£±¼Ù7Jd¦ÃKiÃañ20%9¤IÜH×)7Có@ÔiCÈf4ãÈ* A\"PCIêrÁGôn7ç+,àÂl§¡ÐÂbdÑ¶.e¦Ó)Óz¾¦CyÛ\n,Î¢AJ ¸-±¤Øe3NwÓ|dá±\r]øÅ§Ì3c®XÕÝ£w²1§@a¦ç¸Öy2Gào7ÜXÎãæ³\$eàiMÆpVÅtb¨M ¢UìÒkî{C§¬ªn5Üæáä9.j¿¹c(Õ4:\nXä:4N@æ;®c\"@&¥ÃHÚ\ro4¬nâ\rã#ä²Ê8@ @H;Í§*Ì\0ß¨ ë\r¸Ã²è±P¨©«Êì´.\"k\$bÃ#£{:Gòsäºh²l5¸ÏªÒ Ï Ê6 ÏòJ9>0ô´FÃ®,&%iÂ¼­ÉJØ3¡*²ÒÚ5'-ÔÏIÂÈÃ)#U.çAÐÂ1mPæáS<P(@;ÄC5IB#'\n\0xÌC@è:tã½t4:pÿËÎ±xá	BÈJ\0|6®hbp3.cj4ãpx!ó=/ïó;¡è =½ãìÊ¨×C¨ É8Ó?ÆC;N2^wªé|6Èíä'2©LQ9ã\nÑL ¡,MNC ýáé!>Ã£dvÈCHá4±rý4#ª ´kkÒ¿G(Ì0£d ËcÏ	p®P:¿à\rpb^ºÛ¶òpÄÓ5£8É¤Ú&!\"®V:5©;À#­¨°Ç'­Ö¨ÎMæ5èT5²H¶ÂC£hàÓ±ÈfÓ ÂJHJ¹nz¹]è_^×Å:=3w\"ý9ñ|kôÌwjÞü¹CÌF)A1¥<zÀs¥»àªO*X¤#\nØå/·<¯]GkCMÀÀgª\0@6£PWg2WùáKhómÛ©9Â²3,®g¨EmZqð¨m¢þÓü1%Ä|èg¹ý#(ð\rÔ½²9¤dÄfÓC2fØQ\rá3BH{SP7Õ¬É~¡ÕJ)`ÌÍK\0oäà9ª©CM|o(TA@s\$*°ÊzIÊ%PfXC:~\rZÔQDiL¢ò©oÜ4ÕªÌâ®V\nÉZ+ep®º¼WÊ%`%±L¡1>«efè²µÏß\$Yu\nj¹)#	5R Qcn¸Ô.rXêd¢gä¹Ê©VD¥b¬Õª·W*í^Ä\0åb¸nXÌÄ?i\$³^ÀInÆG¾úR¢DÄAã§¤àræ :!ræCàÐ,Ä.4JKEF\rCARqlþ*\0Ø©J?ÐèÆ\"`Â£ÊªÌ=æ*¡/³;¬4wð^KÙ*O	Ì'r*2á&`ÐH\n©ÔPPI 0ä¨¦ ÂHÐn)!ú3Xh¡¥4æ¥)ÄjÔjá¼;È\"BL²L;ÈÖàË0òÒ5lc©S\rÒ\rÁÁ*UN²ù³sÜÃue2fÙldjÑúB/ñÆaL)g\0§Â\0;S¹GC*kf`u\$\r)k9øF°kRëX6f*ÉS³%æ 4ÏrÑä&lÞæ4û§8I aäÍØTqfÌÕ âMRÈ\rÃõ~¢©üdz9Í³hMM[2DÌ÷` ÂTN¦¸ÊòÆC\$)åÉ'XRývb\$1NâàS\\\0n{¶´%²¤\nï4à?ùÀÚ0oWªiP@Ûð { Ò'ÛMÊDâØ«/Cg7)PhÚ×	µ =aL¶biAà	á8P T 2hÝQ s\$©1B Â\n@Uó\"À~¯åþ=éJ³¾l\r	z4ÒípÐvB%µ)Ò¼ÙñvÁ!Á¶VÖÈ!Û2u%s¥#]8 ëGeÅL£®«dõàÊm½ît©ÖÑ¹3.s§lÍ¼#Ïª+;xUç4çv^É©å'9\0íql\rêW4.{aÃq/¸d¿#¦jÓ>P`éôµ IÌÜùfPØ\"¢~AWD3;\$0£üøP0Nø¤^ÛÞeQà\nèð7xÓvîÈ¿¶âyAêIÁ^3DÇ¦Ás2ÉUDó¤ Q¬éc.et.ú}PÊñªú2i	Ý\$àíbqKùÎª_´c²pÀ(!c~åWûXÅç¦¶ÌKÚèÛ1Ö-¸d¥àÝÀ'¥ÂM^ãÚÁ¿{üHÉÉ%\\F|µHx\n\nL©_è4nÚ¹OÆ¤9¡+yQï^aP*\\H\"\0ØKBÉ=%TfÐb ^LP\0Å'0ó©ÄÁLTB`H\rkÂGÕyTó\\FSÜ!¸\0«ÐDà½P.øné¨ µ5µ	êÝc­th_×zI,ì#±ó³&ÁzÌÂí\$ÎøÝ\\Æ|ôàYÍÑ71èd3¢~q{^ê¬SÅeB3Öû^ò=µyN¼gñþ°÷Ó	ÒõSú0ÈA\0r\nAï:EÄM'ò,éß4\$`í{`O\n9¹÷ÿ6BíNh1^Îün'ßeä¢(\\eÒÿá@ÏËW2è(îCkf¡¿à	Ðù¨=p÷ÂvXú³i,èg´2ÒY_4Æÿ@òè&.\nbüÄTxÏxXê¼2í60ï/ ÿÍG\0gØ°/hËl@Ãfö<\rÊ\rã~ú)æËÌ|ÓhGã&¶ãê+Ä\0Â-PP0ë`.0n!føL¥À\nÐ\\`=mÛ²Ê£\nÎ`F ¨dM®H{L:~ PÅP|Nxµ¯ód±¢@ÊºuBØÈæJ`¯ýíÌÉG°ÆÉ½\00003	@tMto÷0Û­PóAPñ	ba\0¶%n.Æ¢Y°\$^*.êP\$ê',êú1\"'\"1íã&òÑ4êb¡§YQBác(2Ñ2ïbà¼mÌCÐ&Ð1ì-ËËÐ±wN¨În¼íÓ?¯N.b:%ñc¦>! Oã6iãê8¢BúdQ,\r¼'1¨ÃÂSçJ°¢ÚNÒ:ÂRoqÎéºl	g\$\"!Q¤¨°\rñrÝM»QâÙ§JØáÌa Ü^O¢ÙDMÑ²!p)²_qòGjÉ)m\"i¦¬8ý\"âáòFÚÒ%QurDâ26ì\"@Ü\rª%R^kE#Òlâ°ú+ÒrcâOâE#ÖûìÅ\$dc<î`èh®Ñ\"ã.áq+rY*r¡&0A&ðæ\"åå+ÍE+âàéU&²¼mÁ-_È2bÏ!Ã*-£±æF¢ù\$±\0\$õIs±/òWc&nöÚÂ,v2r½£1fCù KÒµ²ÄÚÄ¼+Æd'\0Ò=\$ºdMÖ­Ú3#!°f#Vmç4ÃV0R.èò£Ó]4×6P²MàåRffÃüÞSpÁ-ûS&ÿ¨8±0£0ó£2óêRjß &ÒÝ:³P0ä%Òx7ä®6-73 äÒKË*Ó\r¤;=Cß:@	ß0Bã*TujÔ=`)`õæ).#Vè¹!©ZçTòÅÜ'Tóª*@Ù\0004@Îúl¦îÚ ]Ä4&1F7æ(`ðóAÂÄÿQîÏ:Â¤ît`ãêhï JØhÛ>.pa)
PÎ\n´gÃMÇ¨Æ\n ¨ÀZú\rÈ0\$\"/Àj§AT­n|îñdè·ª>xè43kPxFxLgsä0Ú&å=pó	³Ò>z/c6ðr!¤H3ëj;ñ\0é¼ë Þ=,ÆN`«P¥Lÿ¢Ö)eÜd¢îÇ fÎâ;E,ª×®¢Ó(Ù!T\n Ln¸kÕKSÇÑEF?Q4®¨µ]U­hÓÌ\ràà>£\r?ðYN:\"ÖiKÈ]lC7GðLÄæÏ\rì§ZLR'\"pÛÊ#ÀÄD£CîKÄÆ¤vÅu|x@ëä\0Âg &\"hÃRú2<àn-õ2L|Câ35@ê,Ìºrúø0ïä1bà]bäLàî.\$\$'à¤9Ù`«)O~ùp\\H\"";
      break;
    case "ca":
      $f = "%ÌÂ(m8Îg3IØeL£©¸èa9¦ÁÒt<NBàQ0Â 6Lsk\r@x4dç	´Ês#qØü2ÃTÄ¡\0æBcé@n7Æ¦3¡ÒxCÈf4ãÈ(TPfS9Ôä?±ðQ¼äi3MÆ`(Q4D9ÂpEÎ¦Ã\r\$É0ÁÖ³Xñ~À`°6#+yªedy×a;D*ÜìiæøÔá+ªâp4(¼8Ë\$\"Mò<Àk¶å£Xø¼XÄà¯YNTïÃ^yÅ=EèÎ\n)í®ój¼o§M|õ*u¹º4r9]¸éÖ¡í : ²9@ü9ë°È\nlê¡`ê«Ø6=É:*¢z2\n«&4ì9©*Zz§\rI<H4ª²H¿£*ã¢Á®Ì;I¸!/HÀÒÀðÈã+Ð2»\"*\r#&¡Ä!<&:ÉOhª½\"D×³06¨rðç0.àPÊ¯ó´<²\"í.(r\"\n\$ÃHÆ4¤ìb¡fðQMÖ&£ÜíP2%434ÀC|	7<0c7èúþ¿ãº>44µYÁ£É8ÐÊÁèD4 à9Ax^;Ùr%L¤Arì3îÐ_!u¨ÈJ0|6®Èô3.ÉÙxÂBØËRÅÜú ¢j:`NÊ£¨Æ:-ÌäÇ£Ì5êµê¥ü7à\n±Lc0Ó]I³+£\rP£ @1*¨ãxì9¨Ú½RÄ´Îx/8Ð+\"0ÓÐ=2ÃËàê2Ñ}Ù²\"2+AÅ((3³õ%Ei\$ãÀ6-c°Þ\r`ß	õ³´ÈÞ0Ü9ÂaV¥4¨¯7æ4#¬KK]P¥l,HÂß 0Ül£#*\nbºÇZ%áá81Oj\"WxWøÓÐ·¸ç­¯HÊÃÏ²oÒ=+?Y`xß2sr0 ×6dh!².³ÔC7váK7G¡½,üÇJ2Q	ÀÍ´ÊßíI\"/ÀÂ<Ü·:Iëaôæ7#Ëk+ðÑ[û(ÎiÁ¡ÿrì/|'¥ûúQ\$LCi\$	qPØ@Q¤4Ä`ðÌâ8Ü1¢&¾Ò¸o*\$È7Õ*Æ-zòòdÐÂUÁ3ôûÊ+S(h2æ\n`s¡¹IOÂë\"ä*CäPBgx)uÄ;!è«¯òÀXKc,î²ÁzY«=hà^KÚãÀFøwd\$å¬µ3NTiGòeÜtT\n#\0µI¼SçOñU#+Q èñ*Y]³È²°VÅXë%eÆ\"Gþ;P5­¢]*}¸E`Æ¤!­o \"¤È±\$HdqÒ¢¨^D´T%ìÊ\$t± ©#P UÎ1h¢I6cA3H¤H\\#lªé\\n8&ÆpÊ¶ÃØ_&bC1·âþ`ÌÑ0éñ'þ»JP	A¼%àAF%\ro¥ÔQ2XÇpld¬wÅ	õ<D[oòF]NÓµ ãïÄ¹/3]h)SC	\\·rZA*ÒGsÃA(/\nngñk¾Z`ÂF¥T°à@#ñ'FA¸×4R×	IH@´×Èý_£ÈåÅÒ^LSá!°3f¥	;ñ Ä<ZÆmfêWHUéùM:ONìÛÿÔu(lÝ­	áL*E}\$íAIW@¥\$ZW^¶a?¢^©\"Ì.UöZ\rqE/¥~¨5^ UH>ÆÙ±%TÃqÄñúÕÐ@¥RLhª#5PáÈÔ4ä¢BARd\nXmò	á8P T®\"ö@-òMsðÂµöLÊÂ\\#(9éZeI)P´¸àÓÅ°êÃ®vNròiö¹ÚüIÜÁå<&´dì×y³/T¬PRÙë+ÜT´Ï×1å]-bQfSÄCîÈÿ¹¤ýS°5­D SôHÅ©P¬HH³E`é÷r½Wº%qØ¢P*Qò¹-=ÙôY[ÐÔÙK>DI=4F[N\"fhÅT2ÅO+sHi\nXNAaÑjÁJºç 1e-\$4þ%3ë5á;äük\nb5µ¢EâÂû\n«å	·êÓ.£;-·9S\"p²µYÈ3â;xÎa­:sPXko± ¦äÓ¶	Ê\"¤FÛíTÔCsa#SÌ(8¥¾%M%pÀÔb ¡ÔdyÝ8eu(e7¦âC	\0Òsg(ò@ÎúcðýÎÓ±LüÈ²\0^yÆ2±þ4î¸ã,i¼P©.´4D¯ìA¹26HÜ×ÁÐ\0Îà:2\$!³8]­±åÄrôvJ5 Æ}Þ[¦LÊr\$äÌ¼2'Îy/<\"üú t¼¥9¯VäpÙõ.µkzìBëòåàu3\"	i?!Ý§2*EÞ)<)¡È0êyñD)ù@	ÆÝå%ÕadÎJPFEäeé7¶½#]·³Ö¯H³Ù«f\$ü¹ê¸|^FZPu÷?;\\Æ0ÅVÐØòps-VÑJã7û^ÌG°c·á|C¿){ö]YÙÓ\$YW0|Â-^¾4sÐíÅØ¼°K:K\nùL3=bÿlOçåý¨Üô_ÇªVä±ïA±myK&_fò1.\06lg*\0PàÎi \n1 À\"PÐPLðÿì ¯Â´Ò0Ä#mh­l²\0NÖ¢t2æH¿èn>­\nGøïÆÆ¬|È§hOÏÎ¸J»b8õÏìÆçiª]ã\nÙÇcÌsÐég	¬\nÂ ¬húVÒð~þ´ygJýÃ6æMcO»d¤­×\n&dÝoÖè­â/nZãã@æ¥£øæ°¯\r£pv å Ãnä`(PÈéI\nïüØ£L%%OÑ\nqBÍb÷°­2\$£f¦Jdë0âì/\"0G4_îQèO(ÀBPÓ«T=VC¿ÆNvà=ö½(îDeçß®P8ËV(K3l&2 ÜJñFi~»ÀÑ<JïÚÇ1YìðHj¸ImÂ]ÑD¿ã#\nñýGb`¨\n1@AÑ\$õ\rñè¤­ôÑæÔñ0´Þ®i\n1×Ï Q5ï»!\r{Ý\ròß`Ë LÒbMÈ×ãsäåÒ2dÎ«Î\"¼©Peñá ý ¯âPÐÿÑëÒO%Í7+hqÏ<t\nÔÉeL1æ\\È1öÈrl½2ßrx,J9ÆTKg°«/ùñã\râÖ#°aQ&ò_hæ²¨­+S&¥+«*é&m|v2§,²±(±ù-r½,Å	\$¯x­\"wÄ'!/ã.ðRrÖRøerÜÛrðe\rcN(Iªâ'\råBá 0ó¨®\"/2\$0²BÙÎ.È#- Ùré,Àp\"Cõ Å4ïÚÒôúPÛË½/ç,óg5Nù\".ó7-¬e'JXbÍ\"ÿ.¯¨KêÇ5(MÄà\r3bÌ.âH	üÍ\nÞ\\@*´æ¼¢?ú@A'Eâ\"ôSÈÑòTÂ!ItlFPd\"\"./ ôSÚ«ëdæÒÆ0A\nsìúÅ((M¶dª\rV²UM¶h/úÿòT@ZlBHòGê\r§¨¨¨`\n ¨ÀZð£U~º§|þ.]E@q_EÄFèyân\$N!BpÖÇÚ«ÅxòÀòÝâ6jP!)~=cÚÞû­Â1ÈM²	¯ç@# Â°æ/VÑHd[¥VíàÞ\"C½#eV(öe%<DjRGn(épuHt0ò.ùCfa´wO/B­ÆòWOìõP3Ô¨óPãyQ1@°ù1þäD¤(C53(>Ó=RÔ501lU¨\nNF¬'+Zâ)ûP\r^ÿBKFìXsæUfnä»HyD0FÂ\r\"Üh<HÌ çLÁÕ@C¬ÓÇ@RÂöC¤2+NÀ\",ã;\"íuP/BþèöÈÅ\$x&õ¹PÄä÷.«ºâî*¥+²¾Pd	N ï%¸fÜR 	\0t	 @¦\n`";
      break;
    case "cs":
      $f = "%ÌÂ(e8Ì*dÒl7Á¢qÐra¨NCyÔÄo9DÓ	àÒm\rÌ5hv7²µìe6Mfólç¢TLJs!Ht	PÊeON´Y0cA¨Øn8çìd:ÁVHÉèÉ+TÚØªù¾X\nb¯c7eHèa1M³Ì«d³N¢´A¾Å^/Jà{ÂH òÔLßlPÌDÜ®Ze2bçclèu:Doø×\rÈbÊ»PÃ.7ì¬Dn¯[6j1F¤»7ã÷»ó¶ò761T7r©¬Ù{ÄE3iõ­¼Ç^0òb²âàç©¦p@c4{Ì2\"&·\0¶¢cr!*\r(æ\$Bä%k:ºCPè¨«z=	ØÜ1µc(Ö(êR99*^ªF!Acþµð~â()L££H=c(!\r) äÓ<iaRB8Ê7±èä4ÈB«¤ÖBã`æ5kèè<ËÆ<§²èÚñ£n99ò»ZBDFoðÅ\0B4ùB9·£*MC¾¾Üú»Iî l4©ÈHÉªhLÆ\rxá[f¶!\0Ä2ÃLbã~£0z\r è8aÐ^õè\\0Ò4BáxÆ9ãr9é\0ÈJ8|;%A\"ÉÊ1¥à^0É­0n=EC{àóP#¢5µêò7CkH77¨LúÔ^w­îÞ¶lÖ: Í[Â·\\d+0}¨PÉ(ÉS0°b°óâc*:.o :(Æ\n\0%Ê	éF PØ\"\"L>9²ÜÅÆzü^d\rÃzþ÷W@:Ã\\ùÀÎæHW÷QCXÉ&#Hí4!¶#A*CFMBbNÈëßÂãÆ@WæZt.z' ÐëóLB¢&%Ì¹[@V2Ü3Á:-Íû{4­íÏ;#Ó(^¥ð?6<÷Ð\rÇÐ/-C;O0Ðìò@6À¬X<w/wJè¡ÍvþÚÆbWÔõ±ír=\n8@6£WZC²Ë £x@Ëo\\	¨9ÂpÃÃHÎ:ÀÐ¿¸DB×ÆÐ¿Ú§#î`6~ÐxôvPQÄµç 'òü¨\"¡µ1?ÄØéÁ \"DÃ\"OæE=)ÁZNNÓÏh¡ Ë ª)?(ÓL«Ô'~ÆÄCVÍûRA¸Ø`Id &¡¶ÂSôaL+!ÐjÈa4ÁÚ\"F'Íî!B ß	'.%ÂÑC\0â{ÑdF)ÅXtM1ä\"}\"!Øàs(ÀÏá,0Ôß(nZi¤:ªÅ\\Â²VÙ\\+¥x¯XaÉb¬p^y¦P­\0|¦ÃHs=µ¾û©6gÄÀ5¤ %	6LÉFÀÂ«%B}!tGd +PmA²ø:4Rz%ñ¢%UKétY%éûUª¾H«5j­ÕÊ»W¡Ý_¬&äêÈ*\\7KÜ´·É\"·Õ+Þz=!E£ªÃ\0ItÌ!äE2e-îÎx¸<÷¢Ð¸½{&¬¨áRHvTÁÕ­ÌbnÖå	]J£Hv1ÐÓLX\"X½RQ>Kã´qäÈT´ ÒHÉ{¡¯ú\0\nÒ\0È4u\rúa©ÀRäãSðH\n\0¢º\0©h((à¥§²3hH²5âC=UgÔ¨74\$©i/¢­ÍCi\\Ã§\r\n1PÊcºBA´¹ò'DC¢¬±ÅÇÃÎBéJNdM(¤Ò~êLó5´Ä¿fS\nA0NÑ2~Ëd9=Z&a«E!Ù8òc8Jz&VÜ:&wÅm­O,í?bÉ1Ððs\r¨\"_tF1ë\0³\"%*O%ñ9ª2)aKöüÍ|4'®Í¦Ã\0½eÑt3ÂxS\n¼¿Ý&t£_²ç²êLHH[Ú1C|ôKMK8^\rz?\0¬Nà½	Âò<Sl`¯±ÏØr3DÃho§ #J¶oÉ;¹8Än0Yâ\$¤a\"a¤=V»¡pË]TF¤:©Wñ¥4å=CËkP­ È> ²:xNË¹¢»#m-¼ÆÄ²z¢uçf¯eHÚa#x´0H:³É°T\$ÅÚõÖ|Q Éxÿ¹UTÖú«æ]©=ÚF94ï3ÁÓª4LÍ1(n9(/ràxËû_ÃáËptýÔÚ\n«j®/JQéiGÎÔÕFFpÜQoÖû'+u2Q4aé *<3c	Á:',)ÜÑKù	úáêø1¥Üù\"ÅÀÀêÒ*b]NÎÏÒ*Ò/¡ÍÖRDIºSPÙ¹b%ôL\"ójì£k#&lÖÃÆps«ü¤^ ÐdAá¨8ü\\ôOyÈæäRZwaÄerwÄ¸Ýû§C¼±îæPÃ8gM9ÃuÜù¹ÒlP°ïâ\"âc>l (#-¬^PzA-ÊðÌÓ±Ï§mähèù¢íy7\$fÑX !P*\nuÞ#µI×*/ÇOË9¿¦ÆyVcù­¶8eäc~Ä@ÖÈb<Ó<	´úrO`j³7Ûü¢íb´5-°,E¸<â<¤y·ñóÌøß7¤Ãe\0óëß±<±© g¥ò>¢ëù_/ë¾Â69åîDXÇ·eÎ 3*¦äiDo-dÒXHÇÐòHsÞú¯1ðcü>û.ÄP»å½ßó_ý§\n<zý^¸Ûu*IëimdoÂ\"-ÔÅÌ²#N^GÅ\0\r¦hLL((R¯¢\râDB&PL^B\0gî î¢>ZZÇäÖ`ºEDçâG-b_,ª?ÞÊeÒ]g4¢`êÇÐXÍäâ#bJfª#\"0âÊÃzq¨Î^f7ï:7àæÊì´9ï`7çÆ5îJÒ\"¤&'\"aG	\"l7f´\rª6\n(TÐ^ðÄá°ÌöcK\rpÉ	|:HÔ0Â3LÆ`#LJH7ªÊfzgèHäÆÂ¾,PÐ]Í07¢-@¤^È4r¢`ÈìÈ0ëh>LLÄÑÈø!Q0æÀhLN*\n\0ó¯døÃJÓFRSî,*DdlÀHN¶ÍfËq8Ò\rÇÐ¼ÒÑv«@ëí<±ðæ¸\0ÔgP¯'`ÒP¼ynÃñupÛP°xçDøÏ\r#K¬y1±Ðì¯ºuÏ:ø¥îøð\",Âb=\rXêL+\r±ÔOÂ*ó|±¨ô/ÌÏtê#Qð¼<ç÷	±èý0Ü\rÏjôG2hµ±ú,±á & ½ÑËÑÏqðôlÌù:Èå!ö³åîÈR8äì#ï;\"¤4²IP£\$Àxg.4\rêÑÃ|>èþâ	E¬z¤%¢P¢|Bê`gÌ`ÌDKààU*orzZdT¬¼d¡ÒTÆ|:åbH°®ZàÜâvèãÌ®BjÁRhB¡w%²;&ã²¦<0õà´ä#Î#*æJdç8jòA °³0RSÍÙ\$náe²h\rÅÅ1Îdàåø\n®¶ Í·ÓÓÔ0Ó.\"	ñ¨´,ëC²fØâg*#j94Æy2NÅ3í·ãÅïüedà~·EæÐd<Ü¦<æKíÒ±10Þ±e!°äÒ	Ñøês§²òâ@êæ9®A;Ë¯9Fæ\rB7³ÀiÃ=_3®}	g¬5²/ÆT Ö&ä?­3qÓö&e­³eõ?³«/>P\$EASÛ6T!@d>9Ñ[\$1T3%3R¬/Sð\rA=ã\\eStå©­9µ´Ræ³³%TåfwFÍCñ¡;³Zætj&&PR9³9%oH\$Q%3	HîsHQëG!<tvçôMÜæT¨è\$ZrëS`M£ KqCTv£MÄ³0q©LôÂì³;qÒ\r5_!°^gö2'äK2=z·àë8a´ï³æô´þ -Ë óÊAHµOâ#QcGPfô5ÕBD	b ·Gol@g#PC#OèT:¦¼ô#\0ÛQ\r)Târ4Oi\"òÌÕ\"·Â\"5ubv¯öÕY#@]ÀØc¼'FÎÅ\n&2<'eX^Òªð{'AàèDÐ1-bZ²Kf% ª\n p·êÇ*ë,5WVµZ5Zçq]-§ ñò÷5ÝWàÇM§W²÷4 å,!\"	yfxPÈ`tÝ ¬Bþ? d2 ZqT/Äºh¤¾Bd*~ÒÌC\$15ªÝSn©;\nÑLÛò<%ÖéâAcPI\0'*lð®OUöÞÞöEg\0öÌ¨ìð¿hí­h2'`à%É±Yj6\$3 Ö¡h¶3éF<¢s\nÕçköY&®åàòjåÌ?Ã.â2?\0a5Â5\r²'vR=>'6øâ²Æ*B\nÂb¡²' Þ/03ÍÄÇ\n¬v~\$=EW.\"*ö6Ì=edhÒÏöÏM(ª.OLÐ#V©j×.×6M,ià¡CuëHG\n@@";
      break;
    case "da":
      $f = "%ÌÂ(u7¢I¬×:\ró	f4À¢iÖs4N¦ÑÒ2l\"ñÑ¸9¦Ã,Êr	Nd(Ù2e7±óL¶o7C±±\0(`1ÆQ°Üp9gC¬9ÁGCy´o9LæqØ\n\$ô	)Å36Mãe#)Õ7¸6é¹ÐNXZQÊ6D®L7+ÌâdtÍÚDØ 0\\ÈAÂÎkÅ6G2Ù¶Cy@f´0aÊýs´Ü[1ÖÝèØZ7bmÀï8rÀåµGS8(ªn5çzß¯47c×No2Ä-Î\"pÜÓÞÐ2#nÓ¸Ê\0Øµ%ª0 hÂòÁ&i¨ä'#z¨(Ä!BrFèOKB7¸­²L2Bè.C+²¶0±2´b5¹Ë,h´».Û:#¢<¨Ë0¦À±à-£°Ü\rè³ÿ5cÛ	2Ù\n	»\$\rÍ&µÉâÈ6­@Èø>O¢û¿#Æ1¾)äÐ4ï\\¬÷ãHè4\rã«D0¿Oãü9`@PBd3¡ÐÌtã½4\"³BÇ-8^ ïØæþ¿áxDÃjÐ­ÀäÉ\$ÃxÜã|Ì(ØÛX+(¨\$V:c ëd3JÃHÊ;Vmº®ëx<´M\nã&¶è(J2¸27=Óu ¢XÞµ P© Å×ÚèL7\0 ÐQ\"â1¦Wã\"£0Â:ì·±uzSWàPY#Mlè7ÚuÉ¥îèÐ;-ÊÍ¦£rÀaCcPBbC?^ÊZ64>h\$2c\$ábù°£ÆÉÅ\"k,¿5Ú¤*ï'¶v­24Ö«+ëV¸æ²@PëF&È0Êbk±¶;` ´Ú+ÊÖÀ	#kð9%Qïl>ÎËèëx!E;]¢IÐ@A4eXËÙp\\#Íq]#|æu²03Ùc/6B®<*Ç¨«²óÔ­W\\ñÝ2	PÜVÐæl£D8lB,Ð-L7Ã6¥æ®0ë\n|I/!ûNs¨ÍØ9ÑQ_ª0ã\nÙÕ]Ôõ2RË-´/Ê:#b ÐÉ£í¿bS±¶LÊx¨ ÆïCI'\n0(æ¤TtRª]L©µ:U\0rTJ5ÕjP>¦r:r6Õ;L<öx.¹¢ ø¿@Ü×)¢Kd0Ä²:ðQ§ý´\\Ká.Qj5GÁ(¥Âêq3Á\\¨U wéÞBVçÛÉ6%¾ºtãÑ¤BÄáÇ+#@h9/k­¥!+3'!:uI¼88Ô[\"1!aÍ\nVÒgÉ4úùw?DÝYÐêo{*í÷¼PSê\$²(9±ÒÁâa\r%¹õ\"Ñ\"ÄÑJâèüÈ³ÖBÈó0g\0P	@¼ùdJ@('@¦R3E2-1Ð¹	*\$:Á¥ºRæº	òX©¸·0î_HØUáå÷ºÅ\"I\"ÓÐþ9>J9´èêú°\rÁÁA(UN¡×@w\r¤1Zé5R)¥À:§a¿4A) K!(0ÌØOSPD5ÄpDI«3á²ÂXoI-ß6fÉa´8A\$è^)~]	¸Ïþ±Iqb(\$3rI\0àÄ£.ê|ÙsÔ¶`ð¦É«´èÓÃ8þ|+ÐÖzj\rkÊÖ/I24¡XËl7òºÌÖ\"DR\\BZìBN¨9J°@ PD¤1½2øæCüIÆ±f%X¸æT2¦øMÙO	À*\0B ElÖì\"Pn\nP^¤ð ¸¥3}I©<&Wv\$[íÕÈ3J°l[ÊðÞ20j&é>NÕ¹¾4Ä^P)õ:d©ü,5}{Úù5lÍ ÑW´öoN¢Þ\0¬¸¥]÷:&­e.cåNáP:u+EBL)1 LÈB¯438u<YPmÃDèçRfx)(TgFêÓ20¨k\$Ñ\r!é:ôúÖØ`ç\\8<Ò*_OÐl;~ÚÊÔ½´iµ7ÆßeZÇ¡h­7±ºÙ0\$X¬×(dBD³@0Å)oÌäÉ{ÌKÃQ·µåöØÛ<zfÃAO ÀT-8£çPÙÅÆyEe¨à\0¤»b-à¾fhX±hV()((0A¤y¬õ¼á>_áP*R·Åãº-º\0bÁ5û\n¬pEÎZ8ÕÐÊjî1b¥.ÕÑ´Ìi-Ë¡çéøxKQ[HÆóõr!ïà½·,6ñ¢Ü;kîR².éÝu¢¾Ú	
ð'xG'»¬FÛ±Ëy°]µ	ÛxîA£5fæër\nOù\"dU]ÞEe&lÎ\r¦ª{jH%«D!\\2\$\$lÝÝÅ.9r2K p~§bõ|Y6ýT`ÒÐCW±/,ÕÈ¿ð<rVCi\n[¤âB#KøFÂ/Y¤à7¼Qïïa!Nß®pD¶þqÂå7íw³ÜFª2ýØÖ^É:úöÇL*âÈQSA\\RÅã\02\",W=¾'èöRââ\$Ð|ÀyO7\nbj!i\"ã¯]õ4sG4_ßO×Æ^dÝ¬^\\ N¹­·÷åÓ~O]÷÷V¶âèþÉÆwÇ*ó\\ÛåK¡Hì¦_Àü_{3{×uq-Ç±NéBkû¿O¯}¯Ã÷:ß«Y÷÷w3-I©¡\\Fâ3zÝí¹÷ÈiÝ]ÔêRLþ(Zþb`êÛÀÿ\rôû@Ï`ÈÏ®ðë¾åãÏKïÔõ°\$cÐ¯¢ïï¨û@Í Êc®Ìâj+)Æö@¢c£ ­#B_Hþ3älÃL.¶gOðqìà\r ÜQC¯b,£za\"&Cä3Ðn(Bxp'kd/\"9\$ª&fÂÍ\rÒól[åZoºû+ûéÎý/ð0Øüo\0ûE¶aÅ¤ZRõ÷ÐÞûPòBûXïmPÕPö­pæ¿¬æ!`ÑÍpfÓ°ôÙ¯oRÉC	Xl°°CÜ.æD/üM0ù/Þÿð;ñ	°\0P?F¶4Ç¶<?ãL:Ì/§JÕîXcoEDè+è/obHàÖ@§wpúüÊb1ÉñW\rB7nc/kQ¿ì_Nü«ú^ä÷ÏÈøq»cTö1ÄHÐm£Ú¢BèkÊHMqîcä.éna¢ò2()PÐÍ2'\n­^Í8¡\rÒYOÖ2/#¯¥1ÞZí&ÑCrBD ¨1lIr;\$ÖDYµ&Ro¯h FRM#ôn~nòXÛ	\r¬.m(±v`Ìëî!ê)*Ü Åé)âÈ¦@2§[ÂßÝÝ-q-OôßmE`Ø`Ö@à×âé/Hhß'N¥n@²Î0tVx\n\n@ÃÀJ%È#mÈÚÌ)Bp.Ðáó&1©2ëM\"+å ¿ãTî\"\\/PóB7ÎªG¤5Ã¦/(Üà³0\"Bø/ÍøhCºXONÅìÖ\nlB¶¨9 &b	ÆÂÌ*úBÂKò¯&¢¶ñÓ4Ä&õS°åÅ+³¨Ç3¾³Â4F¢K;®îõ.çù<Ó¬¾Ï4NÊ\$Ã\$@ä\$ûSÊ2«FË®<deBp«h	 ÞðæÚöæ|¤ #ôf°ªöÔñ\\\0ì>Ãºlóô-Â0\0¨½Dp\"Ú¼LÖ/ö&DC,¶0à<r4 (,lf[¡=\0­äâÒ¼I9b\$i Î\0@-jpÄP!@Ô";
      break;
    case "de":
      $f = "%ÌÂ(o1\r!Ü ;áäC	ÐÊi°£9ç	ÇMÂàQ4Âx4L&Áå:¢Â¤XÒg90ÖÌ4ù@i9S\nI5ËeLºn4ÂNA\0(`1ÆQ°Üp9Í&ã Å>9ÔMáø(Øeç)½V\n%ÅÍÓâ¡Äe6[ä`¢Âr¿bÆàQÆfa¯\$WÔún9°ÔCÑIg/Ðá¯* )jFQ`ÉM9ß4xñèê 0ÎY]rgÎxL»SáÚ¸Â­@wÅBþ°òx§(6ÊnÍBh:KÖC%ìñ-|i¸éîz9#A:Îù¨W ª7/ãXÂ7=Ép@##kxä£©©¢*PÖæ@£È³L±Â9¿Cxä©°RfÊ¡èk¦¤1CË¨¢:³)J\0èß¨HøÐ\$ÐÂþ±¨ê6Â(´èR[74Ã£°!,lÐä	Ã+8èCX#£xÛ-.+	Æ£3,qâù=¼#(,ÃËÆ6¬)pì¸°£thÎÊðô@;ÄC¤oÀ&\rã:ÆPQF;O[ jÇ9®.^C-sH-©ÀÒ3 cê´ó\$\rãB÷Ãôµhx0´.\0ÌCD¤8aÐ^öH\\SKÜáz­	5áLcC¦\$Ã4DGhã|£Ñ\rÃòý\"Â:C«t:ºp:½²;ÒÈãKêþÚ`%&îK(ë2ã*=B»?\rÌJ²CÊ,ãa: ®BV`±rñ1LosûÆx[p\rn[C\nÖG.(HÒ·²l¸A}¤H\$Îkt4ÜÔ¸Î£:+¦/n8êâ±:1&ëÕªKóÇ~,> 76ØÎ\n2 Ù#¯¾£ç,/5èîN/ãà33ÔNÀ-ë¢&¢^Y~Ã´\"ú4Ðì\\ç	àQSâ¥XNÏ­}\n\\ñ¶-ÍÇÃ*T5-X\$£\nß½(ñÛD}ÃÃá:-ÔÆâÃÐ VôF#kò9Ý¶½äú:ðÈcZ:¿×EÔzC}Ç\\)½ð2üAòèSJ£°ÚXíß,Ðlb7ÅMÇìþ\r¯'Á9K¤oõù¨´tzbô,(4´àÞªb\$1&0ØCLb¤\"4·Ðä¸`g\r¼Ø£p-\nÎ«°\\È1>oá½®ãHhñ?F&4hOq\r´± ØL\"~æ\0èQ\n¡a>åCC\rÒ¢RAÊ@Pã!©øÅBb`nÐn\0Èàá4W8©v%Xk«ù'Êaf bDÊ¢|Ý]«ÐÊ¯Ö\nÃX«du¦TÚÏKEi¹ÜÔÈÙÇ BLke8¾Ó>bHgaU büGI\rÆµ®F(L^Ñ¡¸QÙG IrªEk\$tn¸URêF+Å½#ÖÂc,³\$È.ZH2öØbËL¡[OÚÞÍÝóíF¹ýLt´Èi­Häµè0äJ©B6À5HÔðt22Ä¦@P¡LN1A{þGqMIÆ\$s!áÜ4¦¨Ü}b\\V\rð¦=·«0àüx~*ÌÁKàñ\rµU2:yò\$/*l@Ht I,eÁe*#ýS¯	¨ÛD(F:0!@PQI?(-9&`Â\"\rf ±Çv2ö\r/Ý &â+Ðå@©Bb`L¨;20E~SÌ×\nã\rTW £qÅÀ9/¶°OÒAc&j¨5\nke£Ð@*¡jPZ k'H@ÂFP7c»`åñÏB|u!i©vfP	*6ÚPBNJI]~UDÊA¶Kêègú¤b\$pâ_#\\	Ñk0a\\øl.¨f>ê¾,&Cä\$tØ°9håÙ\$4Cô\0¡mä(ð¦kB¥ÞO!¹©áê3ëFÉÉ;'ªÁþ§¹7p)Ürö)4bëèM|X¹^O­P¶@¢al\nY^rõÅ`¨\rJ!rïHÑEXÌ;ÄwFfÁ6ñx+06,¹ëH A\n2@.k;,öÆ3¶(xú&5<¢Ûf	áÁr¶\0XÍ±æ2a:+Á#S1÷À«£°hîÝ¹  w_ó4Â§«ÁyjLêuAÆ±\r%ûX0èEOÎ¸tíQ÷§ßAR¢ ¶­@uë[Ê!÷«´å-Ø±_{r7fô´åÄBô+6zÇ¬\nl\rw	ß3\n|udLÚSS2Y'Á@6*ZñÎhýC7ÅûÓdÜÏ¯EìnÓ*#º¥¤©kÿ¸;\\¿Z;2Èu£¢sÀ*Ò6þàÒl\$CB:6òéÌ°4hVdLC-otó£TÞ=Pº4òãÑ	¹:|Õ´ssÎê}e!éBâÆ #ß*àOò*ìz4SzN=~¶ltñO©ýt Aa!ê+äZ*\\×Ô¡U4-BV\$ þ<`AÝ¸²^UYbl²±4O¿v¾dúÓó#PMoQHÎ#%éæ 	ñ~mwnúýô~\\ÎúhÈªµC!î86Ð#Y+4 3þ+Íw«	é[P-^·ÏÊq¯ãúUKWÌþ}Á³óëÓ³3~Ô¿¶s:ê¶Uvûzvþ³CÈræ[â#9Ê)è¸ÞÀÄ6,¦bÚ\$p§¯ÀíBðCDÞC	Åð@äÇ(lGDD-\0ÇZRæ:£«oÐàF&9íF'Ép'D|ÕG­DMPP§:)ÅØ&F0^!¨âù ÎD°r­*ÔâIâ}+÷Fñ0èPM\r^xh\"\$Lg\n¶mÆLÉH9IÒ`LÆrfv)Â|gÆÜj'z\rJÒÂ-¬øGm\nR¨=ªÏJ£j+<ã#°fû±mZ\nTdÐÐn0òí'7	ðu\rpE_	|=ÐjÓäé	Ý\nq10O	1\nDt¥y	S¤3îNþqUqOÃð¾EV=\0¤ì\$vVpó¯¶ùÏ`(oeÏJ/ïN÷F1ìQñõåsDh'Ñöé¨åç\\Ë@æCb%Ec¸DµnbËñÏc/æËP£ÂH\n&0 ZI @HHäÌè!àÛÃH\"\$CÔ4.\\ ô5¨yÄ@BªÒ0\rË\$ãfØG\"Ë´e|Ë9ä²¬)l®ýñëÑîænR0\r~ã\$Ìå]féçú_ñõFÿ2-ëÎ,Þõr*Fw.Êër£R++]	R±²®(££D¸&ò±®É+²É*£×äF=¢@²êÑà£ðo^Ï îÁñ-bIq0°¡)TD¨hÖØiM\näF\n\rg/#-2Í.\rd\rã:ã§4QDz\0¤{½n\$d Ê&m0U\$ó6RË2?6sk7Ò¿w8s0a)7m5'+æß³0ÒÍS9'\"sDçÇ»OêHÈ À;Ä'8±vDÍ7ð¥=NvsvwîéR³)°}>d\r=Â`³ð:s'=°?³µ?í³®=s,1²Æ ®ÖM3êÒÄIAå6óBó:Ó Ä>-`É0sAþ=\0®\r\$T^©tFC3SH¢°( Þ\rúËEh«øcFbðfÀd*¿ìPÀ\rÐ;BzÄv4ÂYïPFÀ^Ø`3¾6Ä0£­SVZdG0z\0ª\n p4§øG(£ôUpHô°%,¬Gin±ä©GjýÄI/ îºð¯Â2A+ø\$ªånZäcRÜîk#\r(ØüìV4êçµK5\":ãHÁKâ<\$#dV]­ò1!´«bÜKJ¬LÄÀb@Ú:N´(u4÷	\"Þ)À@\rÀÆAàS°²þN!(xFÐ¤pN!éTíW#Ws=Õ~ÈZCYå`ÐSZu EüÞJ¢|C=0¸Ù2SnÒ'\rÌ'±èÄDÍ£~dH|-NÒ3/Çê°æÐdvSM½3C#E4%dìÝ£RZ®\"=\0²½X5\"ÀRãÉEC,ue^ÄÄ|keûY.j­qÕ¤ÛË½eöÑó8t:)Ü@d\$#§¾ßBA /bÆ";
      break;
    case "el":
      $f = "%ÌÂ)g-èVrõ±g/Êøx\"ÎZ³Ðözg cLôK=Î[³ÐQeDÙËøXº¤Å¢JÖrÍ¹F§1z#@ÑøºÖCÏf+ªY.S¢D,ZµO.DS\nlÎ/êò*ÌÊÕ	¯Dº+9YX®fÓaÄd3\rFÃqÀæÐck[)>®Hj¨!Üuq¨²é*?#BÝWðe<\$¯«]bè^2³¥n´åõ>ã¡øz< ³TÚM5'Q+^rJÙU)qs+4,eÁrÎËÄ5ºÆ-¬¹ç©3J7g?g+¹1]_CFx|÷-U±³¤tLê¢»î´)9n?O+øô¿ë¤;)û©îò©Ij¶èãtP#öþÁ0\nQ!ðsß'®\n|W+ÌÙ¦©êâI¦HsÙ¬H<?5ÐRP9î»~É%¤3ÓÅÙG(-ó4C²OT\n£pÖ7\rã¸Ü£ä7I°0Ä0c(@2\rã(æK¢:Á9@æ;Ì\"ÎP#K[ÉDrç())JNë¢O1~ô+LR0=ò8¥¾*Âªqt¡.é:M¬cÎ´­izb­®m\n»­ËòÉ:ê¥ ÄºÉQèn§¢´±Ir\"MUqÑÄ¤ E>FH	>Ï!dh»ØÓ·kAF¿v%ôÒPQÜwK»jÈO½zÞ¨OT:gEö[º4ªL¤¡]DÓhºöTAr,ç©Öæ®	z]jh£2N)uÝw.¢JbÈ6#t»5Í³|ã9Î³¸@0cÜ9å#>g5Ì4Ñ5ãHè4\rã¬ä0Èç=Ï¡`@i@ä2ÁèD4 à9Ax^;ìpÃå2è]0áxÊ7úV9xD²ÃlÁ5e#4À6ÌHÞ7xÂ6+«®]Éá|ê\"ØÂá¬1A^ÅÏÄEz/{&õãDý¢TFÍ¼|¯/t^å'<òô	C[®j3W»(1;ÄÂr|àPHì+#Ý ¡(ÈCÈè2>7åMØ¥ë\$Ç÷ÉÖ^÷d¡ñ½ý=êb\$£}VÝv5Éô¨ÊÃ¦)ò¢v×6ÃðÞ¥ ÄÛFÌ\"¼\$apå\n'|hûeÐù]y(ÄAÀÆMÑ)µ:ìE«è\"32vfE'/hLÈ0GM*2.Ä\n¨78æ!\\,\\Ô=J\$Ôu`i[ù!Â°ì1(fP{L(Â\rÑ×~K d@QêuGÏ gBëÈ³	YªÕX¼úUGùñÄ³IA{ÅÒRL#aÏf5¯±¡ÎZHá%Òq(àÇù\"FqµHr9#Ç~D£¡§xúÃï BzQáw¡WàþÒ9d´:4äÔ£SÕá34¶ðCa.Ái`ÁðCoá4yheà>T ªTÄCf\rªU&¸¦ÔMóZUPðòÃs8oêþÔbØ8lÉsg:&+\\ÏÔÅsuç5:ÉîÈØËÕ6PL§Âç%á}*È8c@ªÈ)qr5*ôzÂÓÑcêÆI]jOQ÷1L.°Q	Ðr.üÊ\"¦¸£­ bh¢¥Ú©zÈ§Ë\$uÔ}ÆzDEé!í¤ÔÐVRJÜE-#b±YÏä!RJtWSÉo\"×ßCÉÁ&ÁP4¬ÛÐrµLÊPriÁqÈÊÚ jMQ«5´×ó`í²WÎKké²lFüÛ>²ô7yYÄº!Ø®,O)²iÔÍ\"Iôí\0¨¦¢©I\"W0zýbÞÀ!IjqÆ	I°iíE©µV®ÖZÛ]kí±×ÌÚSlÓqYé\$(jQÇ¡¹|1Q*=L²¤üv5ETã=\n5?³fÉÈä§^¥xÎ½úªâ«<aÙè6¯Ö5Z2bB®µ¬4&¦ieø h!°6<Ôíq\r¡ãÌòës¬Õ`ëC`oó5 ´0@ÐÓTÑÅ`1µ:þ¬°a\rg, D	 nák¡§«¾QRä|ù*BéW ÂÂÙñsY§ =e\0\0(,¦¦àR¼QÅM^%Pæ(².-É´Â\rÌ¨1Ùk;Ã{IA¤;¦ñcINé¾h'\0ÞÑñ\"YÅ5\$b â)ÂH*]zNÞº3±Ãu¼&öÓË6o k0àÑJzOÃÒÃD«­ccü;­¥#)ù4PÌ¹S<±0¦3\nd&,\$£ÚÀ\$%ñkmË8¥.°ÕÓ@TIô	ÅvSp,1-g¡ôAìp¹;l(Z ­r=ÔòÒ¬BAûp8É<káS0\0Ë¢®\\ÚÆ¥ÛV|))«3ñëz*³Ô\"GUCõ*­sZ\0Â¦ÚJDÏ!ø¨Ïme!G3Æxbèü|úF£¦FØ)é.d>ì!gc°¤©¥VôwÄÐä¨©,0RËx©DG[ø=?U0&)LúI\0F\nÂ×#ÁÈÑ=D'ÈÜêò6Pî:^{ÈãcTÉwçß{+\0U|Äõ¿¬rZ9'\nèNbÿñF_Óp\$}PÎ´1ÀLúth(}<!öá¯µ\$¨<o_'rÚÎN÷²2cp¬/|§)ÊÌ^ï³ süeß5-¹ét9}Ý¢~´Nt+	\$)¾îÓ<~§bçAXEØ<ÓÃì¨>Êh*Ü§ÌÎäMLô\$.äWäÃgð}ç¨qJKEª¡	22B|ËP8Çü5	âRËéCp9\"ÂþDn\",ù\"6ûOLâê¢ÊÄ¥bx2æuàt½Ï AÍzíD*ÌÜÎÈzL,qÊÄâî\$¢W	¯NÈh~OFÄ\0Ü'fPhúbxñdú¢ÝDîëÞ9+ò¹0.=Vnä^ w¤8`\ræÆÒ à\rêÇiÂ@æÈ Êm`æhè¨ÙâÔä|mÒEÊ0þçJrÎn8aûHuì;(Þa¬:ÞhÍeäçF\n\$p\\bq:)]P&Ð\\H¸uÎýG\$¨O¼>ïÂóÛÇpYbD::Âª l:`Ö  jdÈMM±¨b8\\¬iÃ¬Q5Û./f8ùf>ú¢,bª\rÃ&¢«ªYNØwÑ8ø\"#gge¶Àâ¥ªa¯%Â9ð<0@&E,_¥Ð\$ ¨\n`\0â¤º\r\$Üff¦ÒÄäÆFbGOÇ¤HÎìøÉâ-ä0X¯47gà@GÍb)!Xy§'üu\"&d&¥,SÃ©%Ü5Â-4¤\nx%.Êÿ,?âÇ(%D(ò|¦Q&B](ñ)(Ûïî?)Á*¤.²¨&0s+.c'§àÎÌ¾ÿb+è3ç,êÀ2,ÆHÉðTò,&\0_gÍ3\r-ã-R¡+Âq)3)1ó\n&3*ð+)ìòìªµ\n]E\0éHæÃ(²ë,0^úpb#£>í°´|Îò-¢¶!kr§är¬ÍQX=~*´\"ËÒþd6¯úïF\$«¬¹Ï\$OCÅb+âÔ¥oóªÄ@ÊCy4±óórY/¤Ôo\$.¤X3ïô¾öâÚX:¨@\\'o,ó\0\"Qè*Ï\"\"¿QÿD%Ç¨¯ô Á2RNØZC2É¤K\0Åi\rÒ¦B6l*MÜÊ\"INW,îwdÓ¼E2|wÒä«AD´T#òÎçèWbEÂ©E´^~4bH£Ò¥eF9#Ô&sn)>¡o#F0%2b1HÉ:þ8<(ÊÁG#ÿJeb8S£!è8DGBùeæ¶ÇjÏåT¦9ÉL¥¤§\n2~Ï C KààT ~|È-óªý1ÅL¯Ü¯P\"HóqR¿rî^ðG'\"«N£Å*?Ô±¨ÈzèXçU,`¢\rñ\0\r±seäùU\\/wÓ©IYK%rÛU`í+æ_B%.5h÷HÂÈÆ©´ÀÅS´DÿX	J{8c¯SEÈ1î­PÐPRµeuzU!QµR C5¡L¾AéHùµ2QÕ7Luz1Ì«(>´'<ÔðÂÎõ/)@#Ú(ÒÞ)Õy)Òw+rì«c^H¹^Á^Ò3ó5ÕöuÒ«_òç`#)/µ1ReÓ§ÞåD&Z!_E'xÿ´5Ö0Îé\$ó1;NO<\"uSD\rYõÙdÕ§e&¶Y?fáXmEvkXòÖAÏ)í1£é_DÂ>U¨¥ÅUïØ96À(BRü>r\"Îf=]£Êµvª¿ïÌ>òÎ±N|®&A\nâc?Vk	9m\"ñkvpï6ua.taÊ5/e1ÁDcg]dEMT»6¥HÞ!âÃ*)oÁn/©tHtÏú(º<öiXÕ¥W×AçNs4dt\\äýYu¾ÃXµ¢V\"4AvMu-ðÌ2¹6E5gfÕÊ*Gt>s]t}tÔ¯wvoyÒ?\\Vu1U]U÷z@p;\\Ã\$)|´>êËuÂiÊ?×íB8ÐnTÑ/înQö<so;Åê2GD·?`â^µcvh\r\"a7¥Y×«vN·ñZ÷{{×P¼q\\d:Þw}K§;gZþÕb¸ñHCqK{s},],è!	%ï!×ÕXmE8`ÊÅï®1C\$#-Äé±à\$uAovÓëwB­\$)u÷d¸\$ã·*.î.­Øh83W¸¢zmø«\"Vc|W©\\u·J¸[PxÑÒ!8gâ¤ËX¥8ï\"HÞô©÷¶^8!WSXsô½×·|7K¨Ð~£=dØÇ)9ç>Q¥Ö°DÐÄÆÖMtôÅY1©ÞQéÔÕWyF­¹_,V&Àjåq»y®äGÕWO!]XÛkÉ{ù¤Eù!h%zy)Íò%±ÁPã×qìµñÓQvåÒx×µREøJ%Ó°ïd­-yæ_Ùêÿ8åfy³9÷\"¦{xõhXBXæt­\\ Ò\0.*5ràâ8J4â\ncÑõAY-*Txñ8T>?\nb'B¬pMa¶'¤ÐS\":R5âÎYDF8æJv«çNë/A¤%¤I\rø-Ø*Ú`ªúMyöGGÏ,¦,§_º¤).y©ç}bèr:ó\$1`Ø`Æ\rcÖRáNÎ´½ä(ðî©ù¸¼Ö:=3µïjöÎ´ó|Aë8I @\n ¨ÀZlµbªPñ4LjabbÊH\"O²4r9((LìÀT!L,âç2ü'ó\rWHô¸·8xJ]XqnJC^êyCP9%85RêV £Qç2Ájä/¦u}¥*¾9;øß®­¿®:ØòÂÓ3Îd\nD\"Ò_§.\"q×~h«6Ñf/&)3Âdæ50Ä\0üûvøï¸-H&ñ^Ô Õ'µWN´6æ\ri=¿VM/è£%vÿZ¶2U³¼X\rJíôÁÖÂ:A9Ø³Ââ|ÃdzS,)Í¼Ô¸1¡ÊOüHÌï\nà§)tU\"<ÃÕfA¼b%n#\"¢\\_hT%\"aMÏäûõCÛÃ;Æ\\óm§«É{p^»C©{Ô(\"-5
ÿ3Å5Bvz«uv§rknµè³6^(\rÆ?n3UUbÍÁDB*«­3ð·u~p`í]<mÂ´O \ràìK îÑgºWøÔU[ãN¦êÇ¨´3ËÎx½t/6v%â]LÃ4\$` ";
      break;
    case "es":
      $f = "%ÌÂ(oNb¼æi1¢ägBM±Ði;ÅÀ¢,lèa6XkAµ¡<M°\$N;ÂabS\nFE9ÍQé Ý2ÌNgC,@\nFC1 Ôl7AL%ý\0é/LçS¼~\n7MÖ:8(Þr4íFdJ¦xç#&Ì1¦*rLç+Zí	¼oXË.ËifS {4ä¢gØÓ¹C¡cpÆt:Ý\r'¨Ì*O{0ßdd}ÈÉÞE·ç!æ(o7-[ØNNn2Á\\öÔAj ¤üH}CÉ2f5®Hl\\ñ¾S9ã§+/js1ò\ræ3OFF&5£ü¦¡~:5Løæ7¡®ÓZ8/Ãî· ·3È·\0ê ÃÃs[ó ¼¡îB'ü@¨®+Z¤,ÚF'eÐÚ2²àP2£Ík4-ã!)¬DOPÒé\nLã¦2½Ã(è9el*\r(j°«K¢Á¨Ô<9·²xÀÁ²'D,<QãLè±ÔÒénD¯¥r0ÀÐ Ã ¢`Þ¹\rÎzá¸îHå@202\r«TxÏ!\\Û#Æ##¬DN%\rD R)Ã,:5£C3¡Ð:æáxïaÒM27GrÞ3ì_p(Ià|+SiªN6\"Cx!òF+F­ÐÚ8ËÁ1:L,Ú²Ã«^²HÌÂ°áL¥Þ¬Ã#+äJ£*s(²²»¥KÖ.Ó\$þ/ÀPJÎCÊâuF,HãxØ:²\\¸ã±ÛMBHÜ1±4°B\\P¹OÈ\"°Ðê6ÒéÍhtþÄÕ3ù ¬S)\$Îë Ã\r&gFº.& ÈÂ{|É1Ï½G§²J`&=;µ-;©ÄÄÔZ§gi3r ­>¥.)\"`Ü®BÆá1×µÿ|ÐsW:qWÅà8à¯xÆr|¬ r*SBÕ\$åCÎJÓ¬Rz\n\$´ûä<uÌG.2à;½\"\$:÷²®§Ds¡ik1lDâTÈãè±\r}¿p¤~8æiÌ*ÇI^}!lÀç^#>\nP¹C·Ò¡\nIöC1²_.y<³lë>mz= 00áa!íIFÕäYj/*±ªFÁ*dMñ1ÌHq#\nd)¹¤Âl¡¿R ab	T\r7²Â-A)uêê!äÔâ+@@­Ê¹Wjõ_¬Ö*SAÉeu{UPïÕjè´r[ÞPD¼HaC©\rBa5©¡WETg)!BÈE	à@JM±e2À4©3üñª·Jñ_,±4S«1g?3\$bÂÕzî°8&\\^óàéXÁ ¤â:¡GD#ÆúØJk7¡ñ@Ã§m1¤¢RPdÁ\n?æµ#ÅBýK¨¡© 3Ò¡Cp`©|Ã³|N n\$¢XB¹}Ñ©)!l°Î0ðÒ]&gKèÜÂt;TÈÍùÛ0HXP	@ ÂÞIÍùð¢¾(P25!¥¤ËA!í£HHAri-ZÉÊ \$=2AyjÂ;ÁbBQùyæ¾ N3Í!HA£ôHÚÐZFùÊÀÆ¹¨å Æd?MB o\ro!0¤¸nÄ¼ôHp¬ÁCcî¢FHÓ¢Ý\$Ô#pâ3ºLOÃÂ£0pª¦·ªDcù/&.}W[E¥si?°!sPÀadu Ò§*J¬âTÔzãj!bE:|Z+?\"/ÃV£)¨P	áL* e='ç: ÅÃBjQÔ=TÌIÉI+!®Å\nä*@}³ÑH½¥è#-3E0ÎÍ*aÈÃÌ/N\0&[E&Õ¥0`©?&­AÁÊV{\$FÎÊw-ð>Ü?(è70wBC\nVÿ´B»¡' ¥°mF1Ð_¶@È>Â(b^ØSc9Éµ\0 ;ö1&,ÆÃ^qp@MÂ%=/-&:'YYo1Ð¸ÔVèÕe@Çrò1&ârÎFt	Ì»<£­rlnúæC÷KÈ(Ve\rÝæÀråè©DkSý[3Rã¢y/	]úó7ilÏÉ3ÍÙð ¯8Pcíü (*;b\nÍáoI±B<u>K1A¨ÉÆEÂcGàÆItË»5¥ª­ºH³F\\êNuÏuüÑØ1Óà»A^ì\0â%ÇÑq2RÈ¥¥Ú¿iX¶:«ìXC\rÎ´×0ºvÞÝfô(Ö'9´îÍ	;·@Ý\r®Àñ#~y)mî/52ØS EV'%:JÒöø ¤¥Á>ÐÏ1Ê!°ô*L,ù0KFæ\\Í;jGv\$gAyOc.ôËö0gyc¿c³¹~CQèjEÁÔòÒ8®SLÀ%§rTg39ÄiC¼¯»þÐS7hä½hm)a	é:*rrÂºØ »kLúeî°èld ö	ÎBû7ÔãÓ>Òx	ÿ[;ynÊÝ]êähÀ©9Æïéz¥£ï.Aèç/ [ã`öF>Y;y@åà\$è5ápì	|DVY.=	²órã@ÞÇC¾jEÁf& b)Î`2°} ²RÇè@+ãsâg°È©þù4²¼°(°Ñ¥ôd³]´Úïë¿¹9#J7=ØN;ÔÖmÍÓÛ¦C¶¬\n\"ÔA²À,ïRf§ê#\$PjÂ/Øt	O\0Hþg¢íz×ãã\\6\rê-K¢h\"m­\$cÌ.LFm ê1zqÌ²5O.oÊÊL5C(ì4ý0Vì¨\"1,±oqpps4û¯úPsÃZýwP'\$ú@D¢RvNæ®ì£~énxa).¦üè®âÈé0¶çnüúU \$¥2H¨	Ð`¦ JØÞc[\nÉõiþ¦¤\0pDd\$L@¢ÞÙBô8Ãî:ô;%\n#e2bf4C¢j wÉ\nD(X­³ì(ì-JÔ\$8¤Ìæ)VäxÆP±@F8(Z\rËèÍ®dOÂNClÙÍM¤ÅÓd\\MÏ¢¦± æÃÑñ¨ÖP_nLàNÏôÖÀàÄú¦_	`r-ÔO/-&GÂÔHíp\"6Ð):JÂ2o¢òG,ªä=1ÃñÆBÒ3Ñ¤ùmù íÿÑØ0Æ_ ÌhÍÅòËÒ(;æÞÊË#\$!­ý\$C¤dp:gÐ\"t\0ì¬LjÑÅl`¦Vñ	'I'\"ü²<#\"&òzHÒQ(òxb%ÒrúR¡\"ñÚ19Gi8HíÂ`¢ð`Ö\r´Û¼Ì\$¶RÅ+¤©'ï¤IÎ#\"=,¨ÜmÖÄ1!OÉ.Ã(ma/RêiÓ±Û#\"30	O0LVic1\rØ1¸sÆIIò¢Ö*%³úS3-`¦àæ\rf\"ææjå 9!Ãc2@¥å\n/+ík«!¤aPòÊV3§ÄA¶R7b4sÑÃ\rÐÄ\$N_8f]\r°Âû#0d®\rV¥c°¨`C9©v½È#o!xgÃóRá@¨ÀZFé¼¦èSCT&G]B'K6!ê,,Å*ÌÎ\rxr òúm2\"O#³8ä\rÞGA!Ï5iPs\rn]\"P0àQJð&l6µìDI(w\0Â^µ\rzFx&D !P°ÐÝæßãp/¢q*°f|b\nFN¯Ä4Äx£É¾ýã0ûqTAd/ÏÁFÒ£IOÌ[©RÚÉh|D9E2C9Ii¾8Ñ¼b CåószÑÆþFÎÄNÊnLðLmp=Ð@w*y2ÃÐý§>,ÂT êdL¬6mF\"Ê@M¤DT2ús1|²hSiOçC©Q°¬\ræâàÒ²)&ã\$Þà§ñ0J±Ì-P*Ìà	\0t	 @¦\n`";
      break;
    case "et":
      $f = "%ÌÂ(a4\r\"ðØe9&!¤Úi7D|<@va­bÆQ¬\\\n&Mg92 3B!G3©Ôäu9§2	apóIÐêdCÈf4ãÈ(aÇL¦A®0d2à£¤4ÐiÎF<b&l&+\r\n¹BQ(ÔDÔÈaÍ'8Ó9á\rfu¸p¿NÑI9dÞu'hÑ¸ßµ¡&S<@@tÏNó¤hégáPù9NI9á°;|)@jßjC¦,@m\"ûÙ³qßï¦|ÈÏîôFã=ZqFÝÌ¶µ`ëº*yã¹¸@e9­Rr!eX\rúlñÒÕ#ü8+îµ/H:½ÌZhè,Ïò\$4¬k¾Â§C|7ã¨Äß©[Ö¾HÄÃ¨Ú1-i¶ï5NÊ;:*êÂ-\"ã·#HÈKpÂ9B²B9\ra\0P<B8Ê7¯èµ°\n¼0¸)xQð ±²¬>¬\"ÃxÞÔHàÎHÓÏ.1²Ñ>HìÇ2³:\n&\rëjÁ°PáºÂ¹*¬+Æ2;@ý?[þ8@/ðÂ1hXî\róX¹¿\0î¢½4À²pÈã|4C(ÌC@è:tã½\"ä-C8_\nð#Wáv\r«R4ËPÚû\r#xÜã|È ÆR°'8j+|Ü¦ãZ-jüß2h Êÿ°\"òì£·ÍÂÞ­ä­`PÁ·Êðä5 P¯!ÈÀÎªä>¢8èA@¬ñºË\n´©*Ì41¼ò¥ÌÂ\ròhB±\"VÃë£ð^*%²L\0^IdÕÇøzHBxèLÑ}£jÓµDkÍ#,r\nHÒ90¤y£cHÖ5§ÛJ;nºXØ64ë<9lc(h êã@PÝvºfÂ-h(æÞ7-òb \rÌ¿Ô3äýzÞè\$¯^xm\0Ïá8Zm=Ý<ú5¼cha«bq#lB*WÝ­8@¶+È\"LGÕH£&'n;¿X6j£OsP¾#óp\\I=eivðÍÛV¿\0}Ä2úb\r5è­O3ù}¯¢2\rhÜÓù'aèõ³eÂC#A¼3bF`!dRÃ\\SQÇ1åá¿ðê§ðfheæ	?õ`ààaä ÷>æDv8eÌ®C\$R\$0Zâ2nIÁ'\nêNÐ *@`Öñ,,Éêj­ÕÊ»Wªý`¬5V:ÉYa¸§2ÉVòÑÑnÇÔmTòÝ[À9£Î\n:°@±BBÚi^u0¬!BRNJMÊYÈððY[ÑUZ+ep®â¾X;¬HFc	Ök-þ¡X\0´V¹=GÔû©¯\råpÔA§`ûPð[deÇ¦lGIXtñö(ñ	­ªÇr§olà¶CU+3Ø¨M!Mð6BULª\r<ÍriT5jXi\$iÞ2^ËÉB¡ïÞIÉ#ÈDÒ³P¾æQ\\\n (O(¤I¹IDî§Ë,Ï2Ô8FiL{`'&ÕBÞÃxwNõ²Ô­Zi[s«¤¾D3àµÉÊï@uöã^µÃq@j¶FrÎÃAÇ-êjN4à]¹µ\rém`@ÂRÑéâ8L4Ëj@È9JbÛ%Äj\nCÀ÷¡\r!æ½É4s;V¦6?9èHy2êMRU,alé¤Ô8È*\$mR}ISóÜç)±T¾p ÂT«¢X¥·S#\nM½WjºGÎ[¯Òk`Hû<¶uøàL\"úVÁ¸Üj§,Å7É3\0¦á	£#óÈ`©CWCíwM\\\"A'úÇH¤#jr©(3 ÐÊRem\0\0U\n @ÕoÀD¡0\"ßäÊÃ\"í¡!È^\nNíÉºUÊIJ;büêx²!Ä@5æ¥×;{8»ÐÊ¶Ãa.(1HLªfôL·ØXæVØQ\$?ØTðPÈI]µOÌ:¼LnqËh>M^8Öü[[ <áÔà#ð¦Ì3')Á§-àtUNsâÐÕ«,Úg'fBÇâQó\n²\n¾2«çB¡p.Z1 ôÅ_+º3øÌô^Ò\"ºPèÇÇÂÚÿÀSV²TF¿U1°sv¼0Öy½jÃN+2t¹kÆ.\$Æ£ÄÄæÍñ_G¨ ·¶äV3¢º¥Êè¾°L4äí½å\$,5°ÆJè²µ>Õ?g¶ÐpÎ=npa\0ÎiCPfø8mI¶PT\nA\$#p²ídCV4 Ëise\$Öa'âôËÄ52»}¸B°E	ÁP)¥w6²a©'tóh/B Aa XÔJi UgH°R·)YÜâdB&æ2Ø`/@®¼eÉùMÍ\$4b 5`Iù®\$s`ëàKÛ)\$Èt<IÙåå' ¶×i¢®-÷Ê÷UÈEw¬®êêU».NÇøùr\nOÈQÄdDV©Üñ®DAwh/qaÈÓø9'FAãðÊùöHzÓ&M¢aµñ0ÏzÅæò4&Ñl)Ú{M1¥«h¦iÊìW9Ö	^;ÚGwþwó	'ÏsïÚÿnab	VmlQúîÄÑgÐeÀùùÕ²ßÍs,¸ë,\næ?ùýäu÷õ>[|ûYä&©ùM~µ%NÀ+%cÜÞ5{rNÄ²Ø\nC«QxD>AÁ4ðd¦®îHp\0Ì¦ Á(°Â/ÖÞïîàÀÖÀDCZÉï²øÏ¶hj÷ÏMl4æVZ|¨ø\r­0æn ýnÊ°ù¯ú/hìðZOÐa#ü!¤VRL®ëbù¬sïÈBOÌúMD^'J!p6¯v÷îz6§t°úºúP}\nPaªìiÊÎøËð¶&Ðºhº,ÎéOò-üÊäï&á.~è/¦æ¤|èât/­¼âüp¬ÈîÐü¯\nÙiØ¶°.ùÐzO¼ìðûâ×°¶#Z¤j%t3à(éîDID¥¶F¦m)Ö7ËjKÆX\r%aÀé¬ûÆøÂæÍH8íÌmð×Hc%@4D|èc'¢YâÖ[q\rí0Et«êõÃ\0Üb>eº\\¥ôIEü`\0SÑ!\r©ÊÞ_¥þ`1É²Õñ¿QÅ¾üè\"âLÆî¢5F0.R'NÇÏ\nqþ952Lø5© Áxà¢³¯¨×	 çnÉäÄxLr4' Pá\nZ	BÃt	(VaÒV¡ù8ß\$*N!â\n\n¦à¹!pdÈ®Ï'þÈéÑÉï¾A2{(È©(I°&\0&ËG8£²\rçÉ®&CÕ*Å¼â) 2\rïªn{DPÂ(7Ò¡ Î	Rß/ès¡)NÏ#»(3/æTKðE°°ð`©/ù*,/1-!, Ùª2I/X*RoöÂømª30^.2ÈJo3³?2óD²í¤5°~g%\$©*ÚïìÜæJI2ã0òÜÍ´/±!ÂøÏj7Sk.Ò¥#¤Û3k7Ó#²½8sfÝ\0PÝMØÝÍàLdu\"\$<¤¶8\ro¨NN-Ó{/bý)bO<Dé ìàÛ\"Î\0P	°k`ÈÞ2Îaös,\\Æo)£uÊ'>à\\,.Îã@¥@±Ç*ð`àÜeÐRé(>Ç5ÔbF(GÎÓf@R6û£ód®\rV\rbJan,\"|ºR?Âè§«ò\n p~È \"£¸\$éTA!fDæÍJú­h¢l¡\nÂ%	f\r Ì)ü\rî\0PÎ0åñN1BrÍE9#Þ Ò)Z¥jRt4É(q ¦¶ë;±f+1/`í¶4æÅð!A/Îo­	°cx\r(¯\$É	ð¾¬}\"¬µLQ1­Qp+ ÞÄKQâR53;òüü4Ã430=Õ:d¢.ÕHTs¼_3Xj°SîÉç>9T*®uJn0R °<DUÂ­^Cî&¬\nPòÕàÇYÆª\\eÐ	e¼Jâ:!Dö\$Bkð]Ú,ÕÆLNNcN(RsÌzÄ.eÕ<a ÞN\0î-c[-ÂþÛãÀÜS¨hLc1gLðNèvà	\0@	 t\n`¦";
      break;
    case "fa":
      $f = "%ÌÂ)²l)Û\nöÂÄ@ØT6PðõD&Ú,\"ËÚ0@Ù@Âc­\$}\rl,Û\n©B¼\\\n	Nd(z¶	m*[\n¸l=NÙCMáK(~B§¡%ò	2ID6¾MBÂåâ\0Sm`Û,k6ÚÑ¶µm­kvÚá¶¹![vÍÉM@¡å2¹ka>\nl+¡2HîµÂ#0\nÈ]SP©U!uxd)cZ\"%zB1°´ÀC2êÌ©o\rä*u\\¤o1ºÂgØæ{-PÍÓsóéWã¤µ>·--¶#JìÜKËæÄê<­ÖTÜçsüüF¡ÑT¢Ì/\nS0&ã>l°`Q\r{US!\\8(ª7\rcpÞ;Á\0Ê9Cxäè0Cæ2 Þ2a: ¨à8APàá	c¼2)d\"æýêrÔ¢Å>_%,rþ6N\"| %m¢T\$ÍS%©æ¥¨êJ>B²M[&%ES<¬ªÀHÚPW;æÂ¹'ï²²Z%nºôS´,Í+>'.r%!ú²R @µÈ©bÒ¥êÒ¡¬ÿ'ä,ö2Ï¢8ÅN\$#¬¼Fê0ÁÒ³øÌÐª­@XÍO,» P2\r¯\\\nÂðÌ7Ãñ@0c09½c=o\nÁÐ%\nãHè4\rã¬80çÄá`@Y@ä2ÁèD4 à9Ax^;ÜpÃVUÐ\\3ã(ÜÙÖä2ád\r°T(õÐPÛ\r#xÜã})ÇËÔÏÜäCHIAh¥HS,²ÏsH3\$Ì»8~Æ	#\0Q%Í©<Ìñ^\nãä7W¨(J2<nÑTæùÎSB?9+ì 2KÊ¨­LñZÃ)µ°3TùºÈD¬%öD¦2å¶ áH±Q,O/¦,ªkÆJ,/EøÆM\r/,Èã.àèj\n+bMyjVÀ.2ÌEË­À¶Ü<¡8·ÚÍy2oA*(p¦(*¡ïLlÄè3Õ#úìu-Ö'¬©´ÚN¢ÔÿXâdKÞ-ã-Öé'â­¦0Ðß¬S1s«ÚaÏë¹%Ãì»Ý¬ï¾ÚÁ3wê¶YÌP!x¾7¯Ç¸Z &A\0Ú:på©{Úã ëÖð.dy`¿pæÁ¤3¿°Ë\0ÁòF­# ÀÒÐlBÅ `Ý`Ú¯Ä2è»ãHäÈ\$B@Pr°ÀDCÅ \$4¼T`èÌùÈ0q&ª4ÕÌéWQ&8@r(îJ5&äyÑHÄEeªl&TTB{cÉµà¡¢:g5* ë2X ¢yjDl8A±ÄVhW¼¢{´ab1×º4;)!ï¸ÑôÉc(\n\n 0 2¿C ~ô*µZ:Òp¨4E^µÖ[in-åÀ¸ w\\Ë¢P. ä»p/BÐ|:0Þô»`²²37Ib~KÑeÐ£%QQ¯&Ã^©2\"Æ!È3Vpä|Z!IDõHTXñGR¢U-¶·Vúá\\ksÉôZí]ðèS/×»Y%5#;»{LbtVB0x%H»ÈôO¢aFELVÏòQdòòK	¹.A¡\n+zÀÅ\r°\$(<\r¡Í	äÈsªå]`ëO`oðQb¬p@±Ð¤¨`1­yL¥èa\r­Ø'fdãcjG©ù¸\"ç*N«+ü·öt£Q\n (Lª>J@F¤d\nd9QIÅY¢X S¡/!ºP9z{*¸oY¡È4¨4ê\"ÍD(f¡ Þ²éÚ¨MJ·6Åà¬eDÚÕKä_hef4F® «8,¥2ó§! 40Ðü bÜ§¨Rá0Âý-\naH#YÖ»¢Q±<¶º.Î²W;Y%!PªPRå\"u(IðEÔ²]©F)&6¼¸·8£ê28­ôU5\rò3îººé¬!âI aåd¡YKiâ\rÌácÚU®CªCÙ;-}X@¡¨¡:¯sÊ#´\$6\rb¦=G%â0lçdRÆÅ`þ=wfnïâE:t.Cµk_É¢Fh¸r\"éMÁÙ&HÀÑA>¹)\$âJçÂfB\$l¯£iÔ|NÁR¾Þ¦äCK±ÒwÑ¥BO^­hE6!)[*I¹,qQ\0PO	À*\0B EÒ:L\"PtÉ¶Ö´¨|ÊQ¶Õ,§ôVà»¡gÉWLþßUMÉ2\\E\"Õï°üëâ¥Éù¦ËÆù´ÛdüRöS²LÅ7iÅ£ÞÇ{|5úñ°°Ç`¦¶DNçiðê¨üìöë«³%®\rPS¹qG¢èèÃ\$-#?G¨WWâ G/n;dÒ1´o¨TT ± ÖlãQL< :*ätyGº;H>3ÓißDÚó¹ñÀf×=¡°:Õ:]°¡dGÒÅí~Õ¼vßQH3®oMgokxÓâvLìÖ´óy¾_YåX RáÎÏvæq:ëëyIú*Ù Mk ¡\rk ä)s/yK FEÎQÛ÷p!ú¶?jåkP·Æê¢!çþêÆ¶qæ\rIuµB_^l0Ý\\½ð_¦Vkf\n!A8iC\nÝkáh!«ò¦ârJ¦ðÁò¹M1\rgçä§ÿ6>z\"Æ36ÈùÛòK;ñÔÑ^xWÒOÛsé£¤©S¡®¦L÷çýFË>®ãþø¹8DÕ\"ÞpðóaM£LÊsOø#\$´#Á°û\0¥8ÈÎ>éÄ%O6Oçâ?Dê#îú¹ÌlJ!<hºÏ°0Ox ÈÞ.f:ËpLSDxä¶ÚN'/¼{.ÖÁÄæIÞäÊem¿-Â90xePD2IÄÇLÇ\0Ï¬~2ÞÆRÔ|<P1fHØð~æc87ºw0ê­Ýó\njÒs.#\$ÌEI|pÇkbBk§ÌA àHÀ~ãÃL®e¯2CMßi ìmFeb+0Ñ\r© x®ì\"tëCeî¼Ü+\"é¢¢Àqî²HçÚçí^dÊÓMÞjÛÆ7­Ï§Ç\
rö3pjÛ\r¢SâëæX?p±\nÝ%<:é¼íågt}Cnæ±T5çéQu\nq,·áj(B>g²Ïí~Þ¦,O+	C&ûàqg¾°°1¸)ñ¼j.5.f&/û&°z-¦ÐÆ\\c\\î¨¤ÙÑwNç¨×'	r|Nè¤oKð¿ã¤.B*2£¾j¡±çÄsîG'FP#jÀBLÐ lFâ_!1øQ¯/Jíã)ñÆñH\\hN¥\rÃ0±¬·\nðÈÐq'Äò±í°HÈþÜ¿)%=qq¡)ÐÇ(±wGs*ö{ã<Ý¯>eÇjdðF1x>¦àRd°N+ÌÂj?A°\nN)[#ðóQÀ8r})²ï*Éõã*ÐÉ'2¤xnÌë¢É\n8õ­×!¤0ÃÉ+rIóÊÓyäîOÊ`:¤n®32r.Üò5ãÆ9+¥sÉO2½6&ò*e6£É½5ÆCsthÒ­818ï\0Ö\$\$ºkÀè¨Q8¯8é(C*|¼\nÄ©tîó£9¨èlVï¨O9à\r«ðeo±§7òü5óÛ=G}s}³\nð¹6PÇ>cD>DÙ´JïO3»4ªe@¥/3­\nQ¼³ \$LØKdÊyÏ6z-J²n¤TC¦t§²g­êÞäéÃØ¾6&,ÕâzR1ä+(Â³E\rò°ÔcèþúÏQTô²£A&OÉÜu+²Àä\rVÅ8ëà£°ãGôp3­#±ö\n ¨ÀZ	Ì,É#¦ýQÓM°ãG±*ñî\\GfÀæ\$%M#Ê@eóXêº\"¢è¯\rý5ã@ÍÃ(iP& ühQÎ¾°ÙJP?\"Ç·IC,Slòýâèå*!AT\$ÞÕ£BîYL\"7SÐ\n0N¤îChQòÏB®cÁ\nÈDÐå(2MWNLôð×°ông²YWÄâkQÄÜJeYµFñ)Xu©#\$7¨µXC_GWÃ§'ÁPÄÐeÚ5da\"LµÁD*0Ôq¶ôuèqQ3u\"Ídr5lÿ+ÖßÅ3£E[mðJ6MÏÆÇMVG sKîÇROcD:a\"Y4îÊÝ=Íá_i!?cëY\n]bÁRâ0jÔàÞÄë8õa5o<KöODÝg	¼Czc6";
      break;
    case "fi":
      $f = "%ÌÂ(¨i2\rç3¡¼Â 2Dcy¤É6bçHyÀÂl;MlØegS©ÈÒnGägC¡Ô@tB¡ó\\ðÞ 7Ì§2¦	ÃañR,#!Ðj6 ¢|é=NFÓüt<\rL5 *>k:§+d¼ÊnbQÃ©°êj0ÊI§Yá¬Âa\r';e²óHmjIIN_}Ä\"Fù=\0Òk2fÛ©ØÓ4Æ©&öÃ¥²na¾p0iÜÝ*mMÛqza¯ÃÍ¸C^ÂmÅÇ6É>î¾ããå;n7Fã,pÃx(Ea\\\"F\n%Û:ÛiPên:lÙähA¡Ü7Â½£*bnû½%#Ö×\rCz8\nZ#Sl:cÙ¨éÒ &ãä0p*R'©(åBJõmø@0³ì@¹¸£L7E^Ô¥Îâð+G	è#£zJ:%Ï#ÔÔ`´#N	K`å!ÚÇ\núB¯KÖöJI Ò#ñ\$ý;©ã¾å<²Û`2 P¶Ià<cË\\53³D÷«» C¸93I¼\rM×'úï&HØ²&,	!`@Ã~M\0ÛGâ4C(ÌCBh8aÐ^ö\\QË\\7C8^»èôJ;Ù ^(aóØ¶\r`x!òR+#í;ª#l@'µÎ»@Ý SÑ!ãrØ52û¦¶Ýèã^Ø±¨ë\r&ïÜ\\[O:£ xì:8fÙMUÚ*2¬¬ôII+á+-Ã¦²`èÆ\nâ½¬H¦	pÕ7h÷iÄX ã=j3äLY=Ý¬3?B#îP\n´ïN·9£+²Mb*Ü°Êq?`÷úãÒ°Éviõö¿dëd¬5½XúT(j{!«s²Ä#cm%Õ2þNl=Ý¼¡î6æÄ°;°Ü?sýÁ1{>ÐëøSÜ §\"µ	*ø¿ Â[ÑÃNÊfêáqV#\r«Z¦£`ïÆc®<ÛvêSYZ¢3\r#:Z2ö¡ô#Â\nRðÃu=Ð[!r×³wÖÑ5±[dì<,Ýßþ¡á¥<Ë»¥EÈå;ª\0Ì3L(Z@3ÑB°¬ð/¨[4=Úå\nZ7ÞhÒ:E°7(EMNB%Å¡æELOM@D±¯7`ÄsyBÀ¿CdI¤Áÿâ!\0 \$\rÐ À£S_¡°,<Á5\0{ ¹<\"&vX<Pl!4PØÂcfCaÏÖ¤\$AH`u\r(!þÐçÏ{Êz°(ÅKb¦\"Ôh­Âºñ_,±°rXë%eà^B»6ÀkPÐ\rq-DT¡îjÅ\$x\$¤\$ÅàOÎÈn?ÂH/_±6­Ø»\$H|C-TI¸ÞÄDD÷\"mq\"hÛÕº¹Wjõ_¬î°Ôlyk)fPð|Cr(+MjDhHãÁx`µ%ðÈDªSj§ÈÅµV\0Ôv^hjJIb#LÚ![!ì©CRsaFk½3	ÎéT2<ÉÃ@]ã! Q`ÂeO( ú&/ýO¦Ëa&¯qÐÒb9)/@\n=¸fx×q§êHîÅ£ÆH\nMí!z+IÁAC*FWÐóâ\r2% (,« Ê\\&¤TÐ'ÇB LâÐ¦ %DLÉ©[êüi»U\":E'\$ ã*Æe³3g2¨!mMÔÒ@r²ZôE;j#\r¹Õ³î¢Nc&) ZyEHä\0[Áì£Ð?ìIHnAÝÈú®ÒI±w\$ØÅ¯d´kÉ3|aæÆ´HvWÓ-!Ï,Â½G,ã®H/e)Z\rh¶³G¡µM=b·§P4°÷Âÿö%¤H(ð¦jå´ÂºJPåÍ±®\\¤=¸`çl}²!ï<çxï3S(x¿ ­#(á8Ô+R`Ûcï¬¤ì`¨Ñ00ÐÑT2.A,óIäx\"HHå[.l.Âíl8ø®à@BD!P\"âP@(L¸±fA9´±sºqÊjE­dÂëûf¨ê×@e-Ý¥ë\\ ìrm ¯\"vùa \ry8û Q<TÜ­ö`¸ÛÛhÎk37ÔZÎª%SÈm¿ÀþpÑt¤L¹Ì t÷©M+£´¸êÖÇú+±%[49CrÊ´äæÃ±´¬q#4pæhy34H·5Cû\rø3ò\nÃµ.áÌ4\"Ë];ú§\$YSJútdf+Ñ{\0¥Î)6a7&ÄÜ×è«\n_\$<íªdÌMkìrC®Ý·I!]ì^øhgx+Sò\roì4ã÷D²\${õ!£À ÍR£½gòAL\rà()D²^ÍÉWH§@Köá	ý6M;@ÂCnIFÚÛ^IZ§P<óMgÍ~?ËF×Äx al4È#Ñ.hiÓÜÎ½ÅxbéèG¤b_\n_L=:,õNú]ê¢õs×Ûo7ÓÙdwÇ°\r¹)bKÓõÂkú?<æºõÓ+=Í9jH7ÀöNvîîÒÀgÒüÄcnÉÂú=¿aÖ*~LXX·lÓ³e¬`7ù8I|f-¸ïÌ¤K]<í¾e'¤EZO}2dödïG¾ÚÞfÍ\n\nqu³íWN©wÙË ÕaãëWK\n	'¶ÓGw8¦Ã47ú9Æj9/þY¯À¤àIÍ£0 ÊóÙES'iìÓRÿ&(ÆÀ¿WhXo*ÿcbÍ\"üAÀë\0FÈÈ,vä!¢\\ÒXB(aL¸¦éô,,ÀB\$¶JØ+aP6gÂ`ÇDÖ0\$E¬ÛÀÊæ\"¨ÏôÚ­®ÃNc àcgt/ÀÚ JÂjk@ÈL¼ÎOüüD»pSpÇKV²Æ¾Í	fû\0Í¯ÁÐ,Ù\0ç>ØýnÌ×éÛ\0¯æüÐÈû¸üÆ4IìðN,¥O\rÌüê)®ìF\$ì«4.+â\$.S®¾ì.îìpôÃMØÃ¤h PÃ\0',ÃjNÝÈ]ð×`¬ÝdID!ðÍËÊ¤d'­Ê@¥Ô&¾JbúQ\"3\$\n,Èq\$©.Ñ¬zÆíþBj\$äå @Û°lFKÆTÄ×¯ð%8TÃØ=f3£gñ!DÊì\"ÿòÜãÉbbbÏÚêÝ1)\0fÙí¢5P^ÿÍ1Ðdð¯Ç\nÙqÜ64¡/îÍqã Âãq`Í1+ùÐ£p_î8ÕÇ42,(¢`â\nÕâä&¢\n	Dä%Bê¯hcê#è\"ââ1púü§û ÉRâð­\0Ò^\0@ÆhfÃÛe(Ï¿¯¾s\"&ÈÔ2'b'«¦Ç2Pâ6\nfª\$)`ÓPg*\0×*Lõ&0±+´CNøÿðet:²²×rÀ-²,Ò¾Mf0cB%PÅ\nr\"PÌýúoRê1ÒptßHÝ ÄÇ\0ÈEpr!\"RÞ ÊÞsF3æ.sÞ\0\rÇ\rr­îS\"IP\ró1möMüàÑòÿ0±4\n·%±<Kqá&Rò#a5cüäàþ/\0F±íª=äcU.PÙ\r¢-Ó 2ðLÄ æ2îìÄö×\$ü]Êø'æ\$Î~ÖRy+h£\"ðï á<\$4#J;°A¢(&üîdhG)ô13ÖN¬êf\$odË2ëó?ù/Ìd\rV6åxKÓ#o#¼ÉÈòhJdñ\n ¨°àp'Gå.RDýòè¥1\nòðþ£dR(Ö&¢Â&fð^Tª|É#0¦ý:]Go¶\$&á	Gô\$0ô(/cXË4 |+÷6Ñ¸¼c¿E8%KO.¬\$¦LÔVáíD/Ï,ÁH['®Í\"Ð-Clµp4LÒCL£MT¿M^÷|`ôÖ7Ò«5½EÊ©ïJ'\n Q°ª}ìú@Â@KÆljM' ß®ÚN<#±\"pmR¢X ¥ÀÌÐ@¤é#Ê(q áP*68¤\06êöUDÖl\$ìúÔÐ7uF°5B©Î0;ô¬Ê-<ô´êH¦8ÂiÞir½-@F§6O,¼-\nÓ>dÞ\" ";
      break;
    case "fr":
      $f = "%ÌÂ(m8Îg3IØeæA¼ät2ñÒc4c\"àQ0Â :M&Èá´ÂxcC)Î;ÆfÓS¤F %9¤ÈzA\"OqÐäo:0ã,X\nFC1 Ôl7AL4T`æ-;T&Æ8Ì¦(2DðQØÓ4E&zdÈA:Î¦è¦©\$&Ìôfn9°Õ',vn²G3©²Rt­BpÂv2Ú62SÍ'I´\$ë6Nè\r@ 5T#VÍÞ§MÙKáÏxrrBáè@c7ÓiXÈ%:{=_S­LÈäû§\n|Tnòs\r<ì¦æÑ36ÎÜ3»Pªð\"L£n¥ÎÀÜ7;N15¨h»#s\$´88!(»VÖ£pàÚ7¶ôFª P¬2©ZÕ°\$\r;C(ð2 (\n)ª`çE¢pÞ6L¢\n\"(Ãª(c@ÂaÌ\"\n!/£L¤\nLØÊ0 PÉIìB ò8CªVùÊ²Ð).qT73ú2Ô6ðl9Ï´KÂídXP¬T2C0Ú\nÀË´£J²ÿÓÏ\r@ð»-Z20hÊ¿jî° ë\"Òµ\$´ÃÇA¦Ó`ðB9Ó}42OH\"âN420z\r\rØà9Ax^;ÜpÃXÖn,3ã(ÜÚCº,2á`êØ\"cpÌ%Õ@Üã|/ÊpîµðÅT¦«þa;èÀ½^Ä±lj5´1HØ°Èx'*#(Øï#c\$ûûP¾v6h£ @7hÀèç¹þÝ ¢Þ6C5|í<K~hKbLÍ9 A6}B8À\"²e-DhÐÈÉ?Q¬þÉ!úÂ:3³·;4ØÏ¬õÆ)¸ å¿M*)ÊÃy\\Î£låHÆÙ!vnj&\"^&;0È2©D|/û£à)vlU®Æ¨N8õ~ù°eT¬)\"bò¨ñNÜù>vI#p-dS8ßäÐòA2æ°î°h'¬ù½ ÑæE]]aÐ×\nRí>\"ð8ñôºÖ¹M^!ÿ¦V6Û¨\0Ú¾ÉêÖ^æQ#0é\n	!å0bI\0>iKø½±¨!6)dC6~Ï7,ýÎÂ\0Óén}wÆêPÏfFxè%öw\rX×³	L0ÀñBTBGÉDvTÒÔ&ª0<¼oÃqP5	°¬ÒlÑU#\0¸Ä`ß¼J'¢u\\U5FøÒE¤ÖçC\$^Hú0ºæÂé-)êpk&ËýÃþ\nènQÂhF° [m-Å¼¸äëI5Ö»WzÉoK¼éTxOü\$N)¤\$2Z!EbÓ\rÚâ¥d§v:EZ3þE©iN¥ÃX:fMÉÕºúá\\ksÆLbî]¨þ¤%ê½ßC2k+¸¨.ð_òrh]þ<f¹2¸Ø­V³ga¡FôÕBhDÃV1!¤­	JÆS<<DjÚÙìX»xuÚkA2MlÔ4JMk±7&Áâ¬r!ÉTÂj£2èðÓ8\\ÂH\n\nÙ\\u\n\nr4¥(Å2iÈ\$¤*jÕÉµ|D¬ÉÃ6i9JWmp½Z§&§Ñ¶(1HYxyk`èSX[\"êC%³-HÕuJkX\r4ÕoDÍ}ÕºUöB o\rk¸0¦2*HD^IdVe¦*£ÀëTE¥:¥(ÿy¡¶\$¥âNÎq2KHÉYêhh(uìÐî¤Tÿaeù64¦++µÝi)ÄJÆQØC|63x9-Beê«×IhÍii6\n<)BQÁwY¤Ò?Ò*\\â5N¬·Z}Kz*\rná\\KöIìÛ'æÂ=XÓ²G`Hª BhÍ±¨Fä7®w f¹\n&åî»@ÙA\0F\nëBÔbËW't]æH\"KT2uDÑRShgPiÆÉÑc4A<'\0ª A\n%äÐB`EÊl­'½¨ÒÚn\\¶q²R*\rá%s'qDðàÀÙÁu6ÕÔ¼S\0T+äýÖnk^mYë>ÆBN±ÂÅ?èþ)G¯M&EÞÕ¡Ç¶l4&Çd§*FÏaSRÍ¶7ÖÜ'ÛR÷¥ç!·àD5SÄÓDÓÌêÐ\"ûEÐ\$4ºÒ\nÌûjEáÊ¢§ú¡v\$èæSC1`Ê£S³±Ò;(JJm;dÑ=¨`(çA«ÿÔìNºûuËÔ¸LlmlÇS6ô6\n)OêßPûI£>_7ô?al;f¯YìË7¥\rÛ`&IÅi:ÌT§-Xòb)u?\"Ø*1Sèå¹«¢µÈô9Q3ÿÕz]o²)ç&´¬ÅkÏ1 Ô²móPæEÆäuÜÅK²à9*ÍÔÕÊtn]U*¦¬È(Ã!ÙAFwucîÕÜÆº(µt|P7h*@ÂB qÔG	¨Î	sx4ÝNäè(/\0)¢ PÎa#>ñÉ4[ªõbæRc8¯Ôñ°ø\"v:PÜdî4xyEØ\$oªÛÐ\nçè¼¤¥¬x,ÈÀmU^:zã¹#EðÞ±%y¯9I}¢¿öÑåyoh¯¯óoÙyãô£ùÑû?Ç9EeÌo|äTáüFNOéý~[!rü#í»²Vìò¬lÐrEn\$¸£`sª%\0 @\nàÊOÄóNBýç\$õ3J_2AC 1+;¥3 ÊkF¤´  \"ôl^£òÏ£ ²\$jONüª:f&fæÓÁRïÇ¥!R(prF´\$ð}Í«bK°v)©êf¶AÒJ¦u©¬~ÚÐbWïü\$Ð°#p~ôÐµfPu°µ\ng2ORÌL½%^YäülBéPq¦Òámù0. #ÂÔãÖ'ÁCfà\"N5p`Mwí1î;	Oz/Âiä,_®HiEô\r£ lÖOÄ\r°®Ö-2{Ä9p¦Ô\rðÁºYqX¤iöjQ`xJþÖQTX0¤º&±%Ññfûk¯xõ¾yG®zÖÓðúÔPµæ~âOã¯1§±ÍæD\\ü¯\nëåkö\0Ê÷6ô-Bª0À)äõññÒûqÖqÚñäM/gîjÈ.qññ± p¤æÌ_Òê¨R Ò Ñï0ÝbÖÌ¥Îö6êð:Å'úÂe0ðx£Ë^ÌRCr%ïbVC®CN\réz.FGÅl´Ò>iBÓþ	VJêO¤8k\0ÌâYrV5\r²d:d¹\"Ò\næò2Ýp².Î.äZåì>¥ò@0®dR5	1¿\$d-·\"á-pÉ#S\rîÔb\r_.°2NÈÍ±Ñ¥\0ì°K07-«{/áS03Û1±\r²ó	Äí0S>òöýÓ72fjÏB\\ðPfUeÒK½Æ¢2t#FÀ¾j@!	Ôûp¯a.Ñ[0\r3Ót¤s.pVr/ß3'V_ä£Ú;æ4Õ²øæ+O9¥E3òú=:D·DT\nãÓÄ800Ó{-óÊ;Ï8ó-SÚéóÑó&* %<ÎO;S­?AS?.0²k8óÕ1ëX2±´1ðÎjüz4\"ç\$²è)4DÄá	ðäðBà×C\"mCk(&MÌ´A5NwDC²çÔTa<;®èKXé®ê50³yA,èèô{1²&4uHfÉA;AÐn~tqÔwHÿ,¡I.OÀ©¥¾,nÇ1L	tíÂ0AräÑLdöQËH²ÙHðODM#sã	?qëÑêUàæÆjoÒ²oàCÚ×1}	(3Gd·¯\"ÖâØ!hnïõx;#h¨¸k¢RÞP-QÍP¦aÓMïOQÆmE5).óü )ÀØl\r0\\5C9/¬AclGO-¢F\$¤8þâ¥XÛ2\\õ¤1J¾²ª\n pù/sÓÆJ­ÕýM7YRï[Qôü'Ð!B\$.='ömóò~N?'KødìàU:Mh¬#\n²O^àN°ÃqAã®¡Â!W²°K¨|Â4àrY1{5V3¦k'Ìq2ªpM63£>°Âü¤8~U¾Ó²î¦àÜvHÂmd\\¦q­ 6F×Ö_dÐØñàü\"e¶mUð¦ÑeugÂ©d¥\$C1	(Î<%d¬Ñ3fÃ­-B\noüoÊòÐ5õßM¬Ô?ìØlÅÕ1lW~ãÀ©ÌúAä>c\$Öü  ¬ÒÍç*¢D\\âX/àÜ&E1ËïÃÖR; ÐNÐÌÿ?æ¸µñeb4èl÷qciIp1È¢vô\ns÷=Õ\"@\"ËT,a\0íNkJ4qÃæp#²";
      break;
    case "gl":
      $f = "%ÌÂ(o7jÀÞs4Q¤Û9'!¼@f4ÍSIÈÞ.Ä£i±XjÄZ<dH\$RI44Êr6N\$z §2¢U:ÉcÆè@Ë59²\0(`1ÆQ°Üp9k38!ÎuÁF#N¤\n73SuÖe7[ÍÆ®fb7eS%\n6\n\$sù-ÿÃ]BNFSÔÙ¢Ê ðÑÎz;bsX|670Î[©¤õ«Vp§L>&PG1ü\n9¶ÛäµllhÝEö]ÄPÓÊqíÇ^½k£Á0óÍà¢äå&uíæQTç*uC¼&&9JÕÓ¢³¨: ¨à@Â9cºò2%ò#´&:¹«Â¸M2®­2CIY²JPæ§#é\n¢*®4«*ÚÌ\r©ú?hÒ¬\rËØ!)ÃØ!:èØÒñC*p(ßV½ Ò4ÉÂ@7(j6#ÐÃ§#B`­%³*~Ô¨£ÚÎÈÏJ0\\6<Z(¤¸C¤o9Ã+dÇ	ú[@iø@1¡@ñ#\"@£MÀ°:ó64Lõãø0¿È î¡G£­8×Asã\n43c0z\r è8aÐ^ö] Òiø\\¼á{\0ÁUlJX|6¯.°Ü3/)jxÂ%Êc¾Ò¯än Úz8ÎJ#«d¸=hÞÆ§MÃRÝWµð·\ráBÄ=<,«\"q¨Ý?Bº7.0((J2ò­8+³Tæ6£júJÈv='°Òôñ²#7OÕ\rU'¦j\nË¬&)(ÍG<wÉs!x\r\\Ñ\$#;63È1¸lz¤²qjRâ°zp9Ñ.~R2Bd¦ »Âb¶x¢ÃôËDNÀðÉÔ\\8\"yÃ`lNOCíâ¢&U\"õ7j¤Ý\r7Ê8!OYêu ¼\$Ü#¨Ë4lÛª9ó¼ûØµt©o-RòÓìn(5\rT_(½6Ç£ÄÇô#.Þ½ »u/c'¡Üïb\r°Ä	YZLÛf9!/\rB9<ãÍ»o¥æP	ÞÍûaôL¡*[õTÏ	ë¨)åÂ~\\>À\"DO²ÌðXgÓq£nF(jÒ0h4¤péQ4ÇuÒQ{JVQnE\n£0))6fHD¦	>@ÉA¶Ê`ó\$z±¨M I\"\rè`S'Q#z!\$-¶vHa¦[q*\"±C°¤@Ådª·W*í^«õÖ:ëd, ÜÊ)}[q¼èâSå%	\0002/(¨KlH\rÊÁd*ÔêJC7¡dQS©/¡°ÓuÌ¤£Uª0:åb¬ã\n¸WJñ_,±#Yù«-û¢´µo~>R:óZ~mï-t(¢S>(\râi¢;tD¤à1B5àn b5F9¨HÕ	²\\àSÉ)3fSF¬P0b´q×aÏÀÎ¼ÜU®lôj¢ÌÙ8ap°6?¸\n°xD¤4aº Oâe` \rîí\0PRÁI¹`E!â^DH{!\n ¡VG±\"a%Õ7Ô¸oSx7tçÉ\\ÁQdÄJ°/ò@óÉ\"ëFN¸v&a±.¿¹®OÎ±¦È)
D¾èµªU5ÍÀÐ`iêå@é6¬DÁ¼5Â0ÂF}(PÔÃB(n©Bµ=ÈÆÁH#ÓÏ'IoåM#ipM	³j®ØÓPªlBDrC!b8YcDËFjõ	Ú7DùL¦zØ¥¼W%Y´¢|Hu«Ì¾¤q`BO\naQØ\"ÛÚqákòy\$ÇÓnGRÍöDZ8fæÐvq)ñ/F_P\nBM}Í(¨BJ<#Î&9ki	-%äz½P(ÖvÄeZòGrMæ=@ì_»ÔÁà7x>¢Íy²ª(É½@)i¨ 0¨BL	!h *,Âp \n¡@\"¨pÐA%A' ¥q>)&\\_<÷oøY²<_A\0P#dÍ(fÊÚÊuIa<8]ö^Z;G*¯Ù¢Ùz.{Ùòè^ÏE©LÇ¹Cþì<Ê:ßf¢,uÉ&;\nÓ*Xbåâjy\rÉ¹Tøæó¢Ð¹¿@#sÓjÊM)\rêy³ôûXéjQìàÈ\$L9»<õ(TÝ@íqI¡ôD¥²zHÔ[ÒÔÔµ\$c6ÝÓpXÁ¸=ª|%­è¶\r`7a ¨ð(T¾\$¸1âêì*nhJ+ÖSsjH4µ¾YàÒÛô:<G<êªMKK¤ +?×¡µ6ç	?ÖHð\0PÃ§<±×4ÀâSßy­¶É\nÉ×<{ÿ^N]¸Àú²0HÃk ®÷x(¾0ÑÅ5é·LðüµG½j;f±õm¬´ÃP9t)]èÊêAIU\$R\np;Fèå?è¬T[L\nS@éKÒ#r1Õ4Fi¹i{LÂÅ³<FSà±NÇØX¡ Ø}ì>6þºú.\n	U\$Â¬×j6ýA(P éî]ÐÐwxIÛPZ¡ïSuÊKÆ¾øB6J×^ð^WhxVôö3¥Õg¸x*å¨ªô\nÑççOFwe^ Ë'ogUê2ìeg-zJ1sF¦TÏ\n~åÿ-¸	.¦àîJÀ8S`Ä½w÷ì»ä£Î»ÈKUx<û©Ún#*ÝlÎËÓ7rÛïÀW¾iÚåR\"KuÆ%c¢P2,Joû\0Ð\0OËÂÎÅ'\0¬`F	\0Eîr\"CÃ|Ñ\$ÿ£vxDe.pE¨OÄÜg\$ÀØ\nRI@ÊìØ(¢\nLL.O¼µ­¥ÄÝ¢B¡¢\rÆÿM«Åª  3â~Ï'ô\"ZàCSÉ¬Jì\núdæ@dB\nL\0Üm¤lÚÌîÿ0§XÐíI\"¨F°9\rMÎ\rw°âb°æÑ§÷\0pE\ruÇ÷N§8Üë}Ñ3þ§LÀ­MÎ.¾08-äç¢úïÒ5NöÎü1*Zû4ðÎóÊ¤ñní/÷î\$\"/±QºÑbâ§C\r.<B±d¡qvi\rðº/¬þ/.Gf¾¤¨>¢À+y8ZW`LãCB®ÈDñ\$ø^Gk\0\0@Vª \rÊwÆ{\nòcÌ\r)Îdp<»qáM\r©¤wLâ1È¥ë\"uåö#Cðû!±3r±x_MíPÑ\nB¬è\$ý²2<ÑàË\$r@à\nñ\$p@7£ KO\rÍÞ#Å\"æú7ðÍqÒKè	©ÏP\$ÒfI\")e)ÒR× @hñàL½£q(§&8³+RÂ4í'+îd°rËµæzBÃ>ÿPÏã&Ú\nty.é.ë|á=²êsÒ³/²%S/Z;Rí0n\"(b/1	2/Â92æLâå\rdxñ)`É+,Bñ3³>Ï Êbêî³335*Êf\\wÂµ4ÒQBvã2q1®Räl	+qm)NPåDÛ2S~äCÏ93s`9W33£83/&HsæM%òÓ#S§æW*³¾é9R9<Ê@AbFwí](¤ÜI\$,á¦5+ÏÆ#àÞòp(cBíð5¨¦*³ú³&=cHFOÂ4OU(\0îñQ@\$îØÿòt*`J Ø`Æ©Æ¢@Ä3io-¦vò1ÄÀE'ª\0tª47+Þ¬T\n pó:Øb*÷ìôt~ñ\",¢¢!°¤(xí¨y+?2Îv\\cb:âEfTMë¬© åè)4V÷ÎTy*Åà\"QäG±ê{\rN\$¼KxH­òC>&Ô®ð%Ö)faæ' Ïo°ö¢ÏPÒ4Õ¤@àDACQÈR\r!ðÏPõJ% à(båõ+\n,ùSÇ÷QDw\"B\njmqKiU½,£ðõV°ÅW´Q&ìA@ô&ê:\rê>~RiDòþÊD6ÇxobL.Ü)'YÓæ®TMÕ\n\$£^ÌÊN°üÉé[Â~dõ&jÌÊâô;ÅÅ1jw	æ9æ\n#ÄÐ!ÇÑ\n¦¢B¨\rÀ";
      break;
    case "he":
      $f = "%ÌÂ)®k¨éÆºA®ªAÚêvºU®k©b*ºm®©ÁàÉ(«]'§¢mu]2×C!É2\nAÇB)ÌE\"ÑÔ6\\×%b1I|½:\nÌh5\rÇ;* ñÂbJÁu<UBkÚÓ0i]?³F'1eTk&«±èâéG»Çä¸~_&¢0E®A¾dæú4¾UÂ¤ñìMæB¥¢°i~ã¬ÍÅ´\"U Éhn2\\+]³í±[´vGÃb¢Ò¥E¹®ì(Å·MÆ³q¼înNG#yÈ\\\n\"Næe\ræSºtN/àà÷c»Ê2<è¼\$\rCªÎ6ë\"èiJ\$±\"Ék¦§'*V¡£*Z§9Ð³w3räk·(²@s Æ5Kâ%èäL-LRúk¤{0Í¬Ñ<Z\$±ì\$ë3iH/î4v-ry®¯É0b>%©zZHiR[¸ð£! 1ÜÊçÀÎS3i|Ä¾# Ú4Ïâù¾¯»òý¿¡\0Â1o ç<ôâõ=sà;# Ð7¯ÀÂ?ã\0yKÊ3¡Ð:æáxïWÃí<<AsÊ3ã(ÜÓ4Øä2áz\r¯+á<Ï(Úõ\r#xÜã}£z6Î\"ÈäÈÝ¡ÐRT#Ap¢+à\$4É²éÄäÂ#Jâ¸Â9\rÔB\nrÅ!MrO!¥·B»&ÈhBH¦ËJ³9fºÍ9/ÍÛs4(ò%zØæî²Î	|dkÎ£w!ºS#Ää\$¸)ó,\$Ë5E\nêH½0íPÍ,È¢ZL]èJJdélN]6Í§7Rê,H¦(LÌØ7íÈÍËö¼ÅmJ{Ç\"#Òâ\rsç[c78ÉR!²\$Û2K´f·\"Éz¤±Ú^2_8q9C¼jIî[t\rIè@6£ñOØuè:ßÁ{CóhZO?@9Â3\r#?H2õô2OËÎ\"\r#Ø6>ÓÊðÞúøüÐÊ<Õgp+,£µËèä:\rìk¡¤\$f¢Äúº±#Û¿³T¤é£,ï|*\"Â9Z8\$e:Q²×%¨(d\"¿'.	©	¦Æ0hBp!¼è\$4ÄÀÈ	GI\$²ÕdÓÈraÐÅd§Á¡TjSªV«UxwV*Î«`ä®Ð/>O\$:,Õvñ!,F0N»X¡ª%F\$Ê2Mù­3·@PÙÂZ3mÜáæÜNéSÊQ*ELªR¬UÊÂ«CÌ­ÕÊ»yïD7=8~°ài1)7rDIÂ}\$Ô3ÃrXMKkkÙøc QÅ:d)Á.VlGQ%¤ó@Ð|=YÀHÀØðd ÚWèaÏF0ê¡0f²6ðÎïT\nHø<)^¢êØõLqvÈTk3`E¤| ( A=2%÷`ìrypô7(èÔÀr\r!Ø4¼@Ï,Áý>¯ûõ-)ô°@­ù¤Ì_±\$£ù<x9õRáÌÿ¨Wxx&%\nUL ¯å h\r!49·j©åAð£!0¹ÐÊ_á(@Ä8²¦Æîú_©Nïôç¹,tÅ\$¢E4¦îCôN8 x fËZ4È8 ÖÂË2(Ä@t¨LqbATðè7zªb¤-N°='¾nIy\"ÕhÚÜNLC\\Õ%ÒIÅam-	?\\µë\$\n<)J¦D¡f12\"­©Ë`Ñ|DJÁµÂf¬i2`¶®¦38d\"}	\"a¥8:¤ß\$¤8KHCnoq|µÍÌÅ%tè9 5HÂfHQÒ!MÞJ3å95Ëce°ê¨ÀI+6	P7âTZ0ì¸ÝÐÆNB& £3N ®«jHRa§½µòÛ1#¨Br!o)Û²A]ËxL\"240DQ!7«U¸ÔÁm35n¹\"`õ±QÊJrXA3àÛ|gÇ2}Qá»díÍ3ÖnfÕê³~Ò©_¾,CE¬ÇÌÉ	µ©\rvfO]Ød(ñ\\b9.!]§&õe>l²þ: ç2C+ÒRaÑ6Ö×É)/Î9tû\nlHø +©ÀIlË©I.6×YËF¡§8¸2^hS	\$±õ	\\\n{_\rxCá5P¨Qð¤J[öà´Hªû\"rHÈ\naÊn,'`¦CDNhW}f&qÙ9u15áE0l;)«Â B T!\$0ç¾©Á*#ÉIÂ-ÔeÊ<E° ç(ªNR3±	µ,c÷\"D2L)Ý¿-¢\$LÕ¾®Ja\"'L¸TìÜiÍÚpÛk÷bm/Y.èßH*WKX¥¨¥ê¡4+ÏjPQp(ðïûVã°:ßañç%\n!äÕq\rT;ÊËÂh:éP¢·xF¬x¥PôÞH!dâVÒi3LÄ®·â^¼>6:2±)vN6?»½UybÛÑ¤Iá¤3,ïfLVì¾÷Ûó¶GXïg×8µ®«¹Û/)\$%²<üÁ/cFmÈÉið¸\\ÀD<[p·néù.nûûi\"Z=¿D©\nK8]8ãùÙÎòÐmË¹ñ¦kÖ¾¡½£)ùLìp»|â«®ý`;ù­ß{NØ/eýk%¹uúy&\nïËnk_s²¿¢Ýs;õî¬µ¸Þ]­;c#Sî}ÝÁ7	ùÑ:ÒU£¹¸~Ó~è¿V~º+÷Ïâ\"Oæîobª´»ýC6Ð£h­ÆLdÍþ%D~B %æx¨/~Â'þÆîÄ¨LÔ¹HÏBWÎN4PK.!+,D¯f¹@%bóOúÿ\r dní\$Í+«ÒêæíeÌH¯²íðØÄÞBBúnøO¬Ø¬ÚÑMt\$«	\"+°²\"Ð¶ù0°c0´úèÑ/H×fÞqíÇ\rH¯#¨~° Îº-ÿKüÎ@ûëìÙïèíM1nloûpùgôá0¦ÏÄgÆäª]mö¼§ßÎë\rcefFÈKÁáÐâH®®Åä\$\"Ã ¸o®\$îëãÑD¾epÌøoÅÐ¿Ôlqcð¨\"1w¯Gz\"MXQf5ãDÃ©ÎÒ`Ö#Ü¥%t>/d	í9 ÑiS©M)CëãÀÌ\r3 \rh î.ªÔñ\\7¢H\"T\\1äÔÆã\nFA!Æ@æmK1K¡îø\"Kn:ìãQìJ² /wØR# æÒ1OÂílzêjx®g2àÄ\$*ã,n%¦6<ÕòNAÂZ7\"£¡%Òf©BÖîîÁ-è¢ì:`äàV òK8ÄÐ|7*û\04ãN_î}rÀ@R Ì q'oÒ4 q\rêÁßñîr- 8Êxòã\"j¬Ê&.mÅ\"¤Õ-à3D%i21FT®¡+(&ïZ,Â1/ø-Õ¯4Ä³b¦0ä^\$¬~f*\rP'NÚCÏ«Û3íD!F0ÑNäëëèó<4ñ]	ÓFã£A4ËÊ·(ÖéÊ5LÎÇ#p\nÌRdæ[\"0[jjçó:ïP´¼1M-~GóóFèÆÏ+ow.ÒÊäç\0(ÆhqHcÍü}jpè%löiR¾*oOJs%6×5bÚ\ràì<@îG¢gXòc9!L~!.öÂ\n! ";
      break;
    case "hu":
      $f = "%ÌÂk\rBs7SN2DC©ß3MFÚ6e7DjD!ði¨MNlªNFS K5!J¥e @n\r5IÐÊz4åB\0PÀb2£a¸àr\n!OGC|ÔÅ ¤ÁL5äìæ\nLÃL<Òn1Íc°Ã*)¡³)ÎÇ`Âk£Úñ56Læ¨Ô­:'Tâd2¢É¼ê 4NÆZ9¼@p9NÆfK×NC\r:&hDÌ7Ó,¨ *müsw&kLá°xtÞl<7°cÌêô³VôAgÃbñ¥=UÜÎ\n*GNT¾T<ó;1º6B¨Ü5Ãxî73Ãä7IPÞ¸oãX6ð*z9·Càæ;Á\"Tý¿¯ûÊÐR&£XÒ§L£çl¢R§*\nÀ Ãh\" È¢\$ñ\r##9±E³V÷¬/ñBØ­âCþacÓzã*.6.ð51*e,\$HáZ8«xÞ¨-ì\nÕ±³Ù2RYBR4ôÐ{{93£ú\"¯£=A\0å ¥mî¢kÀá\rIèÂ1l(æ÷\$tì 1BA\0ï\r5L×ÃÑ\0ä2\0x\r	ØÌC@è:tã½4ÒÁ8^ðèçÄ!xDÃlÖ.ã46·£HÞ7xÂ%B ÃR#b/ê5£c¼')zh°æË¯nëà/©&	aã¼aCR'£@P¯y¤#pÎ·£#ÎNC¨¹.OD\rC '°×°áÍsk¾É28òÅ>¢ê²]¢£6¨èëVú-â Ê3#ª6×¨ê2B[dÿ¿¸Ã¬12ÀÖã:v3Êè>ª¸àï­9V°\0§V²@¹çzòl Ý%pbPåÒÉ5+ï¥ktrí8Æ0Ñø¢&W0¼LÎÑEýLÐó¬¸ï\njeÐK1GýHú_#-jÅuýDãÕ:=Ð(6-è3#­Å±#mH½ðkx<zxZø¿GJjwß÷ Ü7@ÚÄg­§è\$Ú×÷vPG0ÁCóvÝéWâÁðC]a4pêNßà>E»¢ÄMµh0Ïè\"kl}¡<àÜª×Xs%A0 FÌi¥4ëÌÖðÌH%MYF(à¨Írè4@¯!Pª3N)A½æAõz¾CÉ#áÀÖNK»?  9\0T[` EÐÔå/RÉªæPñU±à@âÓW¡¤8¯ÍBÂXd,¥³ºÐZD0-U®yÍ	u­à} K'¯°ÂdJA=6¡¨*Uze\r©ÍRjT³1tñÆ?Á\\-µtÈ àp\r\$í+å*ÇY+-f¬õ£cä~[rB	\0·àÑÖHHÌ!&àHd:9Ø6ÈÙH	àe@hEETºr+\$¹Ã°SÍ\\Z#HæL|Ñ#r+0ØdéQÈ² Â¥\$>U+n ÎÈU7æ°î@åaÈ\r+ÖâóyFMhúâïEÌI#E1¸³:^×K,\0(Â0£gTôVÈ*A¸¤O5XY\r{É6Ù=2jpUõ^ÁÜ¹¡\"Cé&§IFz>\nhÀÂjIU01ÈbW(Y\0Æ\$XÇÖVjÓiÅNªJªTÂRÄ=.·ZíÅ.,¶ÀÈÈ#°Æ0Â©+%¤¼3 SÌáé+Ò2tS¥k\0pö886à:À(\$0òiêI5¨]»óNÃSdô3 ²%Üc¬È 1¯jqoªÄÅ£ÕhP	áL*UszA\0C`âÙ¾ç?ªÑ<Äåù£µzÔYÓEµsúsþRÝ¹³0ª» t½­P76IjP¢`#J`æ0zs<ÜeBãå´.s¶è)	g5g8\\RhÁÝ¹Êp&4cæt1[/f!¼8pÊjÞ:óìb1pPzeáÁ°6&ªÑâ3%Je¢îÑ>(&4»L£ÈÙÇ]×úTJe}ª×©á¨hPNzÇðÄÔsC©ÌÈµ«&²ÂNòÌ¹­Þ=ðÞðPF:áæGÚ²±ÿ,(Y¶1 g½\rì¹ö§ -?W´¸(iÞ­0p5zy®%LQ+:ÄDò0ÇØÂbj	éE\$~egrËX1e@Þ´K¯\rÖ\"ðçD) jÕeúøvkò¡wRQ2f`³¡Ü° |^ÙÞ	!¸¹µm7\$vÃç¸»jsï»]ÚÖÂN\$kW~#¸fh1²ÞÉÙ½5¼2ðSàl[)áðrF=ÿ¸Á	ÈÞ¸\0ÕIØeÚ9?.ú\"ÓB^\\Le?%ð2{Á!P*Á!¤ÛÂËv¨PÊ©§è4·Ww6©#x:Ì²p^U9Lu®¸¨PfTêë©¥CvûªMßZÔUïY»åNU\r}D§%Va;Jío·NàOºïs']Ô#^óØÕß}ìÝý¶v¢Û\rOo=ÇÄÆ>ès|i*£Æ=¨yËMInuÏÑ^³Ú\r¹µï}ÉQ]*|ÇÊÁ©d[Â17\$¤kåÕ\nÂÊ_êÁ&£YM§/é3 £8¸eGJ¨©ÂQû\\ùLüínî6ÝL.¬ªvµ´\$x\rØX4]«ù]ÁñÅÇ&H°¼\\iÖÅ¶0C¢0@ä0Öøë49*ªbì\\# Ø5dm4Â¬Na\$GÛð1#ÔÎÕÂH#MyX/fÝfiË3ðD{§zlîçµ#ü#ò´Â\"üQ«N#C\"`Bz¤0ÆÆ¢~-@ ^flÖm\"j)ã@T ÒÈ­Ç0±LlÇ¿­Î%*.íÊD}\"dÑc´GÍÆÜ¦.f¦M\r¨>t*HrV½,ÒxÙ¯§¼ö8\$ÇÉÐñÌÔ8,ú°eìçBË\0wQÍÐ@v'Êwdv'q&³1@vgXCRfàÎ\r.?.RhDtQ\\ ìðîäóÏôî1b±iñpó£ÇnìñÑc%±nóoìt61x³-ò6Ì:ß±:Íï§¬8ßØ¢ñSL5Ñ¼¢ñ#¤lMâ9îÌiÌÊFd«(,þK\n@¢ô2äØ`átþ\"¤)°\rez\$Ü\$s1æ»ÌjgRW£Äþf¢<ÒeLNëÉÔâUöÃÑ¾àñßüÑ\rÏïÊÆQäÆnãp%n`Q1PZh¡S&ÐIÑ'Rmâ;÷(1FwÌÿ&e*ý._0M)n\\Ì(1W*Pa³.Z\"2±,º;Ò»)±\"|	bT÷¨	²s-@##(o,Vý8B ³EõR'ò÷\rí'm/ä(Pf³&ºkæÂõrÀ¿& Ø¦;o,'#1Nm,²x3!2Q×+2Ð¼\nÌNKCRã\$P)`Aþ<Ò{C5ÓM4ÐGóh½sFæAsk1­Ý7³^R,Æ>0å/«©934PA9÷2³;Ó hàN»Ê®ö!dDNá³´?¸~Â>u;#Q<ÂÎßmáSÃHáî â³sòS©'%òâ90Ðh\núsÿ44ÜD%**¦æædÌOó÷B¤öæ¯1w*¯Ct/326äH3RÕ%cÉË Ãîo-4£U\n&ÔdåÌ;\r\n@Ê¾ð0/î¿Fíh1ÿG0Î~ÐtsHoÝG¨fOä\nÈB;\0oÒABìTÒEQïOKO?K®¿Lô3Ä_ Øk+°]eZj-®¨r! ÒÈEâTÌ«®È\0ª\n p¨ØÜ%Qr¤CcÿHÕÂU&U!\$Tµúb \"B(\"Â\$}Ô,g0\$BI%;ôéNÎP¤aC@û0äHO£þ¾`:Ä8*ãÃ¸´£AV/í0,º Ç~m~dEÄ«¤à;ÀY#ÜC®7\0gã®`å\0p:P¦^`f~;ntbíÃD#B.H¯¦Üúþ£Æp4óõØÕõÞb!#]m]]Óc9è.ßÃT5ÞAhhH Û¨{uÜ<Rj&Ç-Ý\"uÉ]¡B\re`s ÒnÂ	°òeÐÞÝEk\0gT\$´VÕ8FÆ\nÅN§Èv^¢Ä?ì°2ïF \"4`	CTad\rÏe|ÑêÍ^CÅ_\"¢»Ñe`-ah´^Þ¢ËÔ\räðãb)Óq7ÍÑÁ8)ÀÛFb,¥3¨-afÓF@Ú\r ";
      break;
    case "id":
      $f = "%ÌÂ(¨i2MbIÀÂtL¦ã9Ö(g0#)ÈÖa9D#)ÌÂrÇcç1äÃM'£Iº>na&ÈÈJs!H¤é\0éNa2)Àb2£a¸àr\n ¡2ØTÔ~\n5Îfø *@l4Á©¹Ña\$E8µÊS4Í'	½ªl­¤÷dÞu'c(ÜoF±¤Øe3ÉhÙ©ÂtÆ\ry/s4aÆàUãU/l'ãQÖ!7n³S>·S«ØÎ/W«æÂ95í·&n/x\n\$NX)\n3 æâÐ©x(«6ÇçÓ¼å\"\"CîißÇÄyÓ!9Îþc\$¢9:A*7;Â#I0èÄ£XæÐ\rËÒ|¤iRù¡(ÒÚ+#:>%ã:068!\0îAmhèÉ¬¢jÁBS;¢8Ê7¢QZÒ%\"m àÄNÛ}£kZ±(H)¥ã\"Òë8mèæ	©\0ê5RËæÇãÚ¢jÀÈ6¦¨ê÷¥ú>ÉÈÆ1¤«Ò`·3ïXæÆãKDÃ¢sðý?`@-Ã@ä2ÁèD4 à9Ax^;Óru4\rÈè\\óáz&ÐoØä2á~\r¯3Ï4Æ£¬à^0É Ë\r(Ôò	êErÜ%\n¬5+µL³»d§²ºâ£tt¤ã¨+¤ãsx£\"7?9XÞ6GzÜÍÄ¯%	)µw\$H@'Â¼B%0£b ;@ã¨Ë%×@#\"Ã:ô¬ôZ1q¤¥ô,ïM²úÜ¸ëXëJè( Q\n ¨:µá}íi±#.#ã¦²º4)\"`1Knbô×µ4ÁJµþ\rr+!¶
ç¨Ìmn°·i%~(2l«/=BKTóZsë%jh6_¢µ©C'è¨Q]c§\rO\05µpo£|!¬0ÒÑ|0}		¬&ô/+/Ê<Á'Í1»Ï;¼£Ä ½,£Ò3]àV%0ÌBNÆã0Ì´S©ê²P¨7²r\"Ïç:ØAté¨çCEcÊrrÜµÂ¦­°ÊaJ±KÔI¨êD*1I²YX¥£x3<Ò9PÃRMTCEÑ´}\"¤ÔªS*mù©ðä¨Uî@Ð7*}ÙrEÌ¯cÊ|H%÷^i\"KÆ*SøçÃvQgõC¨ø£RYLu4üéçT\nºWNK\"©Um8uÜä¢9\r½q/0Ô\\©÷#¯CÐØi°OhHÅ1ÒXHf|á ÆÇ1DpÈ×ØDau©ã'CòòY°oy¤á>\" a\"@1Â¸ªP:¨:bÖRdHE{\0\ná0JA,\0POÁL/ò@8ÀÜ§c;ïÆHÊc0Wk°Ò&äº¼°C8ÍyFIDc#ª¾/B©84J¼73îH\"§DÆ1¤ÐÔ)æ41°öùÃ{Q') (LÀÀ%&^!³à7°Ü\\Ãªya+àêP@ c¥1 ãS?äÌóJDÌyÖÐ\n\r!¬¹vÊ@ÃÉ&+7'²µÍ\",ÏCË<MS,5Ðé,!Ædô @xS\n¬9ÇÊJI1ç¯dòNR¹L\$r Ê9,!¹Ç9ëE6ut¢r¸ç@	ÒW*[0T	EÊ¶päDõ&G\$)àÈ°c^ËÔ²xÃÂxNT(@(\n° \"Pl@\n	Ò·WåÓdÓ\$ò2ÂDÊ\rRe¤*H%]\r9vH0Ñ2ÈXÙÑ:gUzÊ/§I(VÕ+5ÇÀ½Q\"R1VÍ]0°ÓOQ#j¤A®Ûkê×y<±lc\\ª*!çRêÔ\$OÁÓ\0Ø2É%¤Á?K©h§³õ/\rê\nf`ÕÍ_í%Ønà=0wjÒG2ñX¥w`o'RÀÈ¾XöAC¤L9Ä°èÜìÍ¸½¶êõÂIZ/êâ2¡;¢lÑÓ^7ëNMqa.´ãÚ³ÖV&´ç\$7W¢rèC5CææCK145µiq¡=Gù5m62O#jbL°è¾&¬x\n\nFØ­CkJW¹K\rtaÂ Aa R6xf\rçÉ8Vùt:XdîAÏÂâàD­&»1%!ÉðeõÌ§´1Ù_ImË,ôT¶LYI³¡Ú7Cé#¥niÕ\$¿U£-TwV\nh\";S¾éÇ%lì0Ò0út+&*Îúk¤ØrI(T¢Í¢Q4üBhÇAc(HôT5lÔ °¹Ñ9e¿ÑTGBiDä»2ôétZÏ½=Öªµ¶®·ïÜ3¨Hª×Õ÷\0Ôµ²xG\r-çðD¤pËÞ«´¥ÇIëÐ§f¼w% ê²=úäX4´BH¬Ánmà('k\$³±Dçt\"PàÊû'­åå{2;úGaüÆÍt>Î)Ze¬ËOâÞ%­\0§ùCjn¶²óGo*?ê·[»ú¦±#ìÇYCÛõ6íZÇEnÏÛíöä¡§«éð¦Öû¸cÀñä¥ßKï}[Yò.³fc.Ì®¥#V´±>\\ZdÒBM8¶týxÇµï xÙâýV>¯ÏÉòo+ì}cïö¼¶å»¥Ï\\Ï4MD%crqN¡Hªð5¯`Ôºíí	)ußvÇ[\$°&ÑófN1DµZ+ò\"ùK®õçÜ^D	7ÆÅÄgÛ,²v6~»Ây_òóøàÎBOôÛïå Íb Y\n5Ìðí¬Ì\"oøð®w&¬ÏG= `+ZÀBKkæ(ã¤gjÞ:í¼@Æ¬f°J-/4rÏw\0BEbp÷p,ÌLÉ.íoxåïæ#G\"iã\$,I`d0=¦JkºPë\$|H0BÐç¹~Ö0l_°ºÿ_Æÿ¯*iëÆoð=\rk:]Nµ/\nÂY/{`@ùîµÐô_¬1\"#(|GãúëÌPêÂg`I0ÿ'Â%0(ÂL P±\0ÉoÉÐäàævÊÐÃ\r1EÐ	B[FÚL¨[dP[PL¤ÍÄ	d)\$ptò±|IÐÉìºÈPx.Í¨¿#Ñ°pÜmÐ¢f®_\$®n\rÄßbúzèªÒOL¼<¤V\rV¥ÑT!bÈ«æ|£Bx('¤OÂDÊÊßÊJ,\n ¨ÀZ<Ç±æ\"DÓ°~<ÂDÐ\$FùO¼W£J» `Ì,Äf¥êCæßbÊ;D¢£\$ÊâÝ#âìîgZÍN7*´å¼'ì&® X\\EZQk{&Ât9ã¢'HH!CFIKæ|­=,Å.F÷bÂ,fq¯Þÿìeíñ*NT÷bLwRãkR¦÷`ÞL`ØãNTøÙ®¼0o¢Ðàæ`£XdãheLÒ9ìe\"`ë[/ @\$ìíº¨Ì©þ´RÊ@âúI\$Â°@Ð8pT0J&-%Í*<;¥w*Ê*ú2e.¬`#-ò@ìÊ\räã&4Å\rdNnN+dNª E#";
      break;
    case "it":
      $f = "%ÌÂ(a9LfiÜt7S`Ìi6Dãy¸A	:Ìf¸L0Ä0ÓqÌÓL'9tÊ%F#L5@Js!I1X¼f7eÇ3¡M&FC1 Ôl7AE8QÔäoÇS|@oÍ&ãdN&(¤fLM7\r1xX(-2ÂdF}(æu¶GÍ&sá4M\"ÂvZÂgµZ-(ÑÄëJ¹.WCa³[¶;fÊ 1ÇN³®Ì§±Æ­g<	§ ÄgJþÓerÐKÁDSd®×³&ZÌûÐQTç³\"«úH&æ9:ÉoÑS!W3G#ØsÂÑ©8LÎg{ALï%,BRµ¨ÓP%Èë&¨J\"t¤©jh@µe:¡¨H\"=Î@´7Îc´4 PëÃBÊ¦B8Ê7¡±f*\r#&¢ãrI­£`Nô¡Ñb¸¦©¶ÀÌº¦¡ñ( ý?É\rÃ£à2#Ò^7D¢`Þµ#ÌàäLl°2\r«[:ù «êû¥#Æ1°È*¸\"=%/Êi(`@%#CH3¡Ð:æáxïKÃÙ7ArÐ3é _A?oè^'áôlaL Ü3-	½xÂ\$Â³Þº£Õ\0=Ì%tÃ­£M|©Ò:+Íð½/K0¦YËÊ÷.5KêÙË ì¢,7Ëÿ/Ä¡(ÈCÊÝWeÜ¥ ÃxØ­Thû^£jX³K(\\1´ýÈ+-«B4Ë±ÂÖ\\ÃYß\"30@Ò1¤J!°+èÞ±Í4J'ÊN¸½\$Hr'5;3lX&1<IcµMEÈ ãÒÂÙ^×ëZ9%-]·~#pÈb,³ï¶¶­¡l=¯{YöºòÄ0ÒYÂßlÒ,÷­IÌ»2Â	#kì9.Nç¹î»Ë¢ÛMkf\"JÞÓ#8Î(Ù§á\0Ú8ÃT´`äi,óÄ05­nQH¢îÛIÍÉðçWtÉ0ã.hÂHñ3ÐÅ×5=ê¯#\\LËãJLìÈS9Hñ»óL	 Þ¬ô¯¾¬¹7lÅ\\3YTÈ%Aï2PÞæÆ{¨K-¡=xÍ£sÓå\"§¡g(}ë-~ÅíøÞôÆZß	cÉ¾b¾l!k'Ë­ö|wL©¡3\"µMÁH0éÝ<`¨dCpktêÀ9\\8 *m0%yqª (¨&£TzRjUKu3\nJö[\rÙTàú#¬Pé	3& ò©£ÒT0aKFì'¸^ÚÞyd@¥úfÈHn\$gä£1SC\$WÎ:¨¸p¤RÊaM>Ø§Õtá<\$Ä:©n!À¯÷Òé4!®)\"û.0&¸ÍÐJq'#G) ¢n·Èâ8ÖW©(IAcÀ\$!(Pe;n¼óìróßFÈê´¸30eM&i0ÕÎ±^bDw80ÃÌ)4@DÝ¡	ïA\0P	A8§6è{Ð('à¤7tìË±'3äÈr4 O»mÄdµNG£tLæ×·¦^,ÿÍAnétµ¶fÏÐoPºUËÆÒfË)¤EÒU5'Y!5áP7²S\nAL@ÒÒã*-f`ÉÂÙI¨¥oÌÉ¶äï§	ÁoÊÄ0_K©Hô(È:vOqq¯xÓðÑPV¡ªhÈB¸ý	Cd¨µ|:Ã*OS%CÏÑ¬:y8BxS\næè°\rÚ3Õ=ûI8^ÕÐ¬Ùf\0PP­Fà%ÃÈrC;DiDh¢¦+\$h#IÈÃL¥GáÉ{¬+*å©&u~¦4äÑ)¹oÌô'à@BD!P\"«z E	âôa3²ø[>V4VsY¤(fêã052e´æ1íXL9[¼æ¤µPZÞ0q¶SZï¡s>\$ê¾ÕX_mwé­·¬ÙÔ!Ææ4ôñÀS!2´Â7¨ÁÌötÀêH×ww§ä'éb-¥ÆnHnÄlhÐ¹3fçíÚè_Mô·C>uO®H#¸lY¡Ö_)MZS!\$FÊà=ö±adÐêÃ@C¸ELlk\rq~iD(Â5Õ¢«Ñ#+j~P\\ggJßkYhÞÛ\n\$¦,RàòA cD²!&Ó yé+ÅX´RJ;0IôÕ|9Ë« (#+6|LM\$Íèa01Èzhá@Â@ dÄ\$¥P æGhro¡æ&Eö²4áË?¯^\0Rñ-!x.²Ò°eaÖY I9¬ÃÈ 	á-Cl£JZ&&t\0´¶¤TÚÄ§lm­¹ÌS6}MÓaN(MöËvi	2ñÝ¥*]Z=¬y[l=³Á%Þá+[Í\na£Þ;@Óò¸ÉÉnã©qr4Ë0¦uEµ?)j¸eVÂ¥ üG¹åß&+d@8'¯&sY+\"è¡3«ÏÏ\"ýrY#ªó³2ØYAr`®JE2Yy7ü[#V\\n\$áSÕè¿X¿ÅÊcBRs¦&¥}¸ÜhS^\\\$FoÀ+¸t´JÂ¹	9£·Bä'å'·E¨ñ=\\é&~|s=ð¡¸f2Fâ»7py×XúöCµá3[·tÈjT¾½¨ë«óÔiFÖü%[÷3z×²ÀÇÐü¦àÎ\rý÷GäwVöz¾>Qî\r_ÑF;gV6 ÔS2Úex±¼6Z®Þ¶\0´nÓ¸û¨wnìã?â;ûÛN0¬ø3õüë=³úOîÿ/¦kÍÈÿ¬öìú­²øø	 ^àÜý-ni@6â&)®Ú ÊÀcÈÚÊ\$Mn5OJ6ï>\"d¤«.#ÏN!IF­·K¤{°*ý\n(&n0äx°%¤ÀÜ¡G\0ÿfÌR\rìÞRâ\\\r§f6mÑðaãfÊ®Þø¯ù\nôíc_ ¦È<\rJ`Ohìb#\0%¢=@\rø.¢kð2\\mob\rÄò8MË\0Æ2\"¦Ï¶Ò½íBýùH5ïÎMÏ@ú¯Ó¢îø¢,bìe%+äð&¢RÑ0&@Á±:Y«ÞEb¼#Àè#8HæM¯ÿc^bQbWñ)OQ\0OùDýì_ãEõfGF=£ZGíW/é& µÄ|iÐ¨Çwh&añG±Í­S¢#Ã±°Ð\r¤\rhj&eØª^æa«dÐ1Ü2'N.^¼L?ÑØÐQßqä#ÐÄ°9c:ÐàÖÑ#@3°Êø'äÒ(K\rLLr'!èKQ·ÄE#QK#íxP²®«\0«ò\"=RSr,ú£ÿò8\$À&dHJq\$IÉ<ÎR[âú^'ÎÁðBÜ-|ê]r0Ê¬I²àÇ]h\"ä-Âþhb¾¨ãÖ1M2ÂéB'ÔÞÂ`ÍÊ(r¹Ò¼\n,%ã,dà2J`9Ø`Æ:²0é¡oac;%â^ääÉNéjº ·à¨ÀZýÇÈ%mvü­¼ÞKS2iBbL}ÍÖ[0¡B@&­\0QR5Ë¿0 Ü*:­)0\n¥¢<L)\"¶K*kåVUªê Órãâé£>eRÐ%cg*ã\"#-\r7<.©F×â,bÊ×E9éºÖéRZ\$Ó¼4ïB;>ÃrFÓ±9ÓÉ:3»:W<Sµ<³¥<\$?	ÚXïèí	º#E¦E@F¾L	»=¬æïÂNõÀÊg²\\²ÞFqdðöC&:¢èE/ÞÕî\r1>	óðXË¸sâ+îö&Df¾07Ó¬n ¦½mÀrï|kt^\rå¨^³ÔMnÆÉlâÂ ËÀ «tÎ©æ)¦äãHZàb\0#";
      break;
    case "ja":
      $f = "%ÌÂ:\$\nq Ò®4¤ªá(b¥á*ØJòq Tòl}!MÃn4æN ªI*ADq\$Ö]HUâ)Ì ÈÐ)dºÏçt'*µ0åN*\$1¤)AJå ¡`(`1ÆQ°Üp99UÉ÷B[Hi[½x9Õ+«A¡£°´FCw@ ¡«Í~UMÀ­ÕÚ^_¹PªPU×!É ²ÙF^Ç! UÐR<ÆÔÐI'2mhæK,/PÄ[P©t¦Rù§W^°X¥ÎEúvªu:ÕkÂLç[&|	®ÏW¯~Gºë×*)Aåí¦mÅä©4ª¡TO;%é~sC²\\§10G\$%R­eK8myC±d~©²\\¹#¡%{A¤	VråñÊ_éL«¢Ì(ªCe\$\$ÒÈi	\\se	Ê^§1Rºeê&r@I	FÆd		\n@Æ°² 'HFÄº-:êÂ´@«ò±Ä©`ªéy.RÄÊ\\àó¡ÊDN¨K¡,¡U1	)dDKç)<E¡pAÄF¡%U%J!1<AÈêäMåñSO±ÅG\rpê<A'9PW%\0'ÐñH1f[¤ÄDu¥_5l3XVPûNAiy`\\<g95ÅHÚk'VÈ2\r£HÜ2QÔMDÞÛÅ-KM]YÚÌ@@ÒíÌG¯C1R1l_Í¤xÖ2Ë\n@4C(ÌC@è:tã¾L7EÕvÃxä3ã(Üæ9ùpÈJ(|t%ÎZà^0ÁMW¥å9tze,2I&dKzZ1\r«nÜ§ÁI­7ÀQÒP§97{Ò8sÐVÇ)\"EA(È¤Wê¸Ð'JhM7ÂÑ»1\0Qä­2U9Ê@?GN\0sPÒKSa³3¶.þsæQ;%Ùvs}+'WÛûFììôþ[hd\"^KO¥i/?«Á*[ËKJíÚìO°HMµeHá)Û\0P¦(gO!<EÍÈ2³àëéwä9\"zÎ·JÄNO}[,I÷WÄóÔö=Ún¨¥î;°\n¨¿ òPnÅå+çnÛspX!>Âô»rîe¼5ÜÅYðé¢5à3Ô%ÓFi¼BL Na2fU7àJ\$HEÃná\0sgâ VªxT!Jâ,¶a´:7cB\$a¿ÓLO 2HII0^HtÄùBÐüÞ	Á{bÄIJÉa-#Ä, K\rÐ9)U2BÐâ([a\nøJT5ZkÅEDÔ\\uæAbD»1äÍ²B5oIüCQ|dX­\0ShåâlO3jÈÑ#Îé^+¤±®uÒºÃB\$\"Q?Ît1Mf(@ÂcWËlMAÈ.ÄÁBb¬]±¶:ÇÙ#d¬ËöXË2á7àÂLíg @p\"b;J!ìÇ É«`BpO¿ôþw%²ÒI­¶Ú¤¯:;á÷°Y(y\"tR(%\"T Øg4¼Sm1¦8Ç\"d;²U0k/f,Ì2èÌñflö!ä]øEA\"©0 §)8·UD	CG.mÎ§\"'1Øznb\n~(¹üBh	&¾+Õ¸eu_%b	² °'Ä(âD9jSa±ÎHÈÅ¯@¢ÓáDR­F°\\åu¬IÁ\"¤tÇBM3%@PHQËF;2/Kð(¥úÏ¹ú§+Å¯+@È%UÀt\rq.â\$\\d% !!`)\r0j²:@±¢=QJÃ¦¸bEù I¬,µ°å%\nãD)Oi)¹ÆB4¯¼Èû0BèsÖ*b\$÷<ï#Ê®Ð¤^\$0úÁÊ#øåq5 #\$Ão¥Ò:HëX!TÂRÄ%G*5ÞáIhåM@@B`ëUÐW*UrÅ}§A¤ÀBC¡·\"H¢M±Ð-]~T¤¦£Ò(Ð\\¾%b**¥ó+%ð/U;à4y\"xsÕð!( ¡	 Ñò£RæíÍ¢³}cL®QBÏð¦ÒÏx¸@Y< e2H_9Ò~ 	»¢Â%¥¸bIw\"\"âWÓ3´KÈ¦XärfR³°ªzïd®Â	`ÁP(qj# ±»Æ³25aVdIÏÊøåäÁH.ÝÀv4K5QJçw \"\rçÁ!E;¢v£]:à\0U\n @¶x &]¬àúÆ.\"<A9å±ÇKÃxº1SSÖ{OyóoÍJf%°eÁÙÅW¾uF¿äL³ÏõX¾}ÏÌ¹;´©òÅû¿ò{\"ëØÃPÖþVo´IAâb5zÔÐùÑL)ËcsÆBHe(QTRôîá5Ì\"ð ´BÒF+0c4Z¼Ø¢þBEdýSüÒ}²¥Ù°5¯*O&0\"¹ßsåÃÃ\\7ñËÂÉú?Â´táyÙ0®¯BWirÁa»ÔÉ·p½.´Zöÿ\"²ÿ¶'ÀP³Ü1-Lµclb§PÆugNÄ©D^%±\\üZávmJ«f,»÷)ls{ì=Õß¾ð1ÀAR1ÝýöÙBÖßÇð¦O]'ÇüC>!6^ÅüFýHJ¯¹>{Ë×¯	q¾q×å}©RCµ÷õU/½÷ëj@ÂD?C¼F#·5·L§wxøä<GaB~òfô\0^òocLR¯æúf±âè.Î°jU\0é\\ÐNT7Ð*lIc Ñ{i(E6¤ìGö÷èjê²\$Úác1âì4¢ìCdvçr1¾*FÂ­Zä*=oÉÕMbLo\0¸Ã^Öcn9pÀ¨Þ²j\nàÊExDð©­>è,^ÓÄÂ}0°+\0&óì´Ë²¡máZuÿ\0©!~.¡PáH0Ê\$ð¦¸ÁlþbÎåþ;ã43<læÒmgÒí ²Pd,q mCf7f¶8Ð,G\0ç1%@»ð:Ç,kÏ*7F±R.61Ð4õqcoÑlã¯|QràQX7\"ÜðÒ,{£JÁrG(¨Øìãðí0*/bú/åDãD¦ïhü%àWÑ·©þ 1³L`¬ñ¾qrcôòï:Ññq<·O6ópglÍÔÂâXÂ\nsíø|Î\nâq+1BBÅöáÎ4DrXÎÎ\$±n\$,uê-!®3 ®6Iñ~n1!å#ælQç!QêîÏ\n¢ñ'!Bÿ¯}èLÝãÝ%òU&QnkrlnÁxúâMÆcnüök\"ùJÇqdÇ°séMØMð]£)jèØ/]*ÅG\$Òu+.õ²ª~\"ãÍk%¤uçÐ'ôÂõ\rZ¯Ï>P¨\ní nSð_2>ÿc¥!ÅG.EÒìEQàKcPkôàgÌ¾¬ML¬\$2þ\$3	r¹,­E|bSqlT}hJÆÒhjAò0]j#,N6¿N6-6ÒÏ#òÒ2²RñªjòÐR¢tüo ós-R´ÿ³¥)óg9fZó¶|ýrw6DýfÌÁ¦0!jAÈC<Á^Ì@äLâÉP;GÈûÒ³t¢Ò!2RÃ:?¡?ïÕ?T9?;2þVnf°Tõs¾«±>m³|ä æBBnFQÝ%AÅfÕOªãqöR´\"!Tïn8ùA/ûF!FrJômTSrô`C4xä6T/ÐTÌJU%VRr[9s-tTeJ6ÅOIÒù5ñFô§9¯ÏJÔÄÔ<4e!¯'´Äå'8³ü÷N_%Ô_N/kNt*p÷4ò¬Ïý@òANôNUNøÈðù4£EÔûQO62OKñtÂµö[Gå8øï\ruSu/\00045;ÄòG6sßÏÉJ¥Q²RM%IOs§V-VOÓ?5O fÀÐ É(\$O(ú9\n¨6I®>ApÕçá-üN°Aë²ätXoSË<éQïüÖõá¹(Hõ¶q[³Ñ`@rl\n7}Òlhø\rVÊ\0öëÙ'(Iá~Hk¯	dÁ ª\n p)3è;(Ò×@A\"(×(6õ§VÊ-4ñzìÅ	ììÁCË`GBã¨·Q3±6/?`(!`môªËú÷NÏeµ¾0¸Ò(NÖ\$0òíÖ4¡T#ðí¯ãæ>¢Ì4\$%âbÔË¼°ö¡ Á<)\\½jÝcÖ¾á\$ ¡v§SVAÇ´!(Q\0005n«Ô[UêQLi\\Å,Uaçm¤ÄBxüªÔt§OfÑú®f[2ôCMÒï4ó+rc0bIÄÈNLÁlâ®èÓ!6~Yü7\0ßð÷o¬\rk:>UW3'è²8\0jÄw2SZ%`ÑmO3x§pà#¤Cz`%HeHNìÕQ8e|c>O¬KT¤ÿ\\";
      break;
    case "ka":
      $f = "%ÌÂ)ÂRAÒtÄ5B ê ÔPt¬2'KÂ¢ª:R> äÈ5-%A¡(Ä:<PÅSsE,I5AÎâÓdNËÐiØ=	  §2Æi?ÈcXM­Í\")ôÓvÄÄ@\nFC1 Ôl7fIÉ¥	'Ø\"é1üÉUd Jì	þ.¬©æóüeiJª\"|:\r]G¢R1tYg0<ÉSW¦ÂµÓKå{!©fëÒÚöeMÅs¹ýÍ'Im&K®ÙÁèÓ=e×\"±r'´¾Q+ÚÅøË¿ðÁü}þ-ÂÕâèî<^ûï}nnZ,ó:ÏK<Õ©è;Ý§
SVè\"­z¼©Ðq=oúÛ³*#Ë\0¶LD¼¦Î¶«S¼ä:÷-JsL¶\"ìÂÔ4MÚi(N\".Þ@è9Zë7ËÌBÔÅ´Ï»´¦ì&ëèªVÞál7RR®ÇrÂëF\næÓKté-Y(Ë°Kp¶DÉóLÎ£*ëxú#	ÜÞ¨¬«Sj2S!RÅL,âÎ*´ÊiìÝDO/³­ºÈÛÃj\r¶1´ÞÐ§ÉK¿Ôë(²£N´#VJsR(TÄOTSÅ)HHµE:êó1	%iúRÖÕM%jtfÝG®,>ôCª*^Íµú¶éLYPÁ\\ØtÑ6\$í\$ü5;üéÈbÈ6#pÊ9JÍ:'TËt¶ªêÔÞYÑRoe\\°]Jë[©º²@4C(ÌC@è:tã¾D7ë{Ãxä3ã(Üæ9ùPÈJ|ÿ(W|ã|ÓM¶Qj§ôÓxéÒTÓ§ ádwm¬ñNÃ	=kRÒzèßMÃ;cÚ®@êëñC­±ÃMü¯Hh<ÀM½cJrÓ©*mòÿ£\"+q(Õ!ª1lmNÜQ´ìÓ/×ÄO.ÓU²/§]03µa\$ð?V«Í­¢ÞsDj¡6¨ÍÛj7·ZN´C-º]%:oü¶»;ë¨¿yoÝGªÚº³ý(HÌ§t=ºîýÏI£®êéþØ,ç¤	Bhõ!f ôúÍÈËòøÄ¥iEÒJV÷{5fÐ¾¾ êtzÄÜ¥À¦Bbù-èå<u:Þ!?<+Ø\$L\$j(¥¶6TúUsìÅjÁfànúY«V¾ÈS^!Ó+¯qÂ5·Ú|ÕI2#%	^°7&Þ	[êZê-Ô,Ì¦Õ!8mFáúÁX0eÚD 7\n½Q4dBTÁ\0m¡Ì:1\0ÂÎ¨t¡È7\0ÃCrA<´LX:a¼7`ÒãeÀù1êI4Që<Ü%°mi+L>CºXúð]Â¢+E©«eVÃ`tNÐ±·h}ÈËd(](G1 !4xfúH&éD§^1>R8ÿ\"N+ÒK%gÜe ëT\ná6«§ø×|CWÆ*fWÁ *N	»ØDÄ+ÐÙ\n¿ro)aºn}\nà­8i)îvJ¥¨ÊÞ{ÊQ^KÑ{/¿3l¼?Ó­`°´JNü«DâMé\nàÕ>LE±V.ÆXÛcì²YèÊS,eÀ¼2G@ÂLtgí°Â· mÈËûm6=Éc­%Ðb¡-Q4ÜMúÔSÙizØvB¨+Êp°öÞ¥!4¿,%zS¹þ¦z4ÏÍòh\$Ò\"F(òLÞ\0«Ó61F,ÆÓcÌvHÉ§«)elµPðps¤,½:HÞ©áC=Ì²¢çb¨&òRÊb®îy¨-ÏÔZé³SY¸­gôfI@IÅ©°B§CôT4«Ë§õg&E­&ó¹ò¦ºd¨N#xYÎ²qX,ã-)¹xPxv=ã¬\$¦ÑL§I·ôM\$3H¨ù	4GæáÿÙÖõtÛã¶Fî`¢2üOñá\0 ©L¯_ýbmìÛ»CªÍNëª­ÃV·æ\"ë+ÇM×¼uwj.Ò¸\n¥Ãinú»zs\0­LÁ'[HÑè?öÁ`[VOv ¢ßíÜQIÖ>Ëð÷ñ&éÃjiAèC\naH#/T Ü{#6HÜ'òA7¦ÂW^%i÷ª¤ðÚùµ°x)ÛUß1ó5ûymEÄ\"ÁK?©v2ÄX7P[ÕPGB@%{RñÄ-Ypö¥\$Õ0â^xÊ½&»à¬÷l{	ÍPh\$Ö·s?É åÂ¢4¤Ä(ð¦«\0É­jUt,bKtÁSWdÊJ_MQ@Óe%OÍe+sÕ±TºSê`Àµa^¹mD¥9¬:ç\\?¿ÙJD3M<&B5ºoM7M¯¡ýú®A(`¨ÍÞ«Ö¹\\Sa%£löIKÖcc+zIÜ4ÎníQÿÎk£Qw ïÊ½¬ë®®±2oHS>ÜEÅ	2·æ¢Yð\"p¡âÀÄ'%°\$»ïx¤ù¹°ïÒÎÛ2.~þDJ¤ÛÐ)­>§t<0æKÀÖ°»i£UÞä#XZE|P´6ý×@>ÝÄè÷0ëSH'2æ¹X®õ¨Éu;^½ àª÷w5QqÃÄß÷0uBHÝ¶ôo;¬ ¢n¸Vð]B+ÖjÈ]¼©ð®|Ûá¢Õqå«l^aï+½|ZõâbÉ	#E:×t<`od¡Á\0ØC:õ ¡Ì1±ZòxtÂ<C	ôY\n^si^QLär½ R1òõößä[ÆªìQJÓOØtì)¶nêÌsñË£ã¤d¥5nóØ«zNüpGËf'/!4PÆÈ(Cb´|2\0Çûy2ãå}xçÿFÎÿ¬Lýá* \n²BðÉ¶òêùM¸\rZbe¦ç¦ð¥GvÆgÈ-GZU©\0Â`)Pý*¢Z¬,mì0xî*Ýhn ¨\n`_(l+M5g¬JFë¾1¢¼fîÛ¼mk.\\\r\0ð|¤Fp @âßX~míÝiëc¡DÅ£¬#(:©Bxt¢hÌ%7	£pâÂ~¥Ov#tgà«Ö»!\rÐ¾VðÂ³äE0îÌDÉPïúáÆÀE'ÆÃÜvã¨\"í¸-Â¿¤¼<ô\$îÖæ.HÓÄÌm%aOxæG SMÅãØoIl+ÂR8±FÖK\"hÛÍ\"Øù£yne,NóÆ¨F\"æÚkBk@VÆâK\niø«Æ×,~Ïþ2klè\\dKÐ\"æq®ÝÀxæÒ!DÀÑl0Ø4ÑßÌ'	màøÁk4æñö(é-C \rr8F®î0ÏäjÅÍr«¨<Nr\"8Ê\"\"T,ºûJ¥XÂVveXæÏ 1R³¬j(iù'R(HLC\n«Ër¸z°à+ËF0BÂi(#¨±î,ø'Q\0Ú'I!#ÖCMêÛèã4Ïú.Ø´§\$¯Ö\rïN\r Ü ®ØäGã\$nWn F#Ù!±ìCNê. Þ¢nîrééÙ£\r,h²ç-Òú7Ô@ätopÁ2f&é}0ñ¶(¬Ün©Djè§ÄÝ1\"Ý\nø³¨N:-Ma-pPòÓE+ìÎBÆç 3ªÎA!\r	\"®\r©©\rò/x1à	ZâÏÍ%Ójßïü½8\$O1ó;\"Ã8ÏÇ7ïÊw³9/ñ¥Ãm'fF¤þT/¸v¿#uGÛ²R´\"3(ÆÀnñfZ'Ü&åBr\$¨,û®Ò¿\"+<XÒ¬AÒQfÁ+nªY,È\"­½EDù¤à?Êmj^&k&ØðV`©W:K9/9P¼+?<3à7O!\"O¾ú®1QP´:÷hó¥DÎÊ°q/É9Ó0·ð6rfkÄ.\"ô`M´d{+:²:W?8tvñ³ï¦2<Z°Fvèi.4àe¤ÝK×*S\rK°EKô)LESI#/TÎrÀ'ôÔ¦ì¡I^&T«JLaÌ×N´IEªZ.,6ä;r|ö%²DúåqòøA'DÝÓË\$b|ÄDëI6µ1SSKAS6\$5Õ5Õ6]4ÙJtÜ+´I-OÏ¨|¥U52ó^lô!Vôî5fwftÿ%¥;UUD=ÏXÒ¯PZ0ø³ÐSå4éÂn¹Ãp%/\0L3éµNËÈvå¼í4k6tYÈìmWuÀÖâ*tÌÉõ¸ÕCN~%qÚSµí\\ÕàUÕ-[S³\\rÔø6Tµ×0UõQ`¶Oµ÷:È&ýÜ\rf&f@@@ÊeÀ@ä\rààH&68ýö>vCdhÐ%ïe`e¶<\rAdVHõ ÊþÈ& fäþÏð\rp\0\nÃ&´ÌÿsÞ¼uL=ið»jR7FÔÛU¡Ið1õûi1!7uJU Sªr)tñM¿£+nWrrjÍSöâÂöèX,5aÕé9îJVÖæW6êuU_lãL¬FÐX/8ëUF]5Z±)KË=ïÃ¤½\\SlÀ/rC(4Ç·m«EåX^	§rµ&ÌNajpí³o&ë7q`±Ú©<«Q¼aà@ØqÌk«ôlÈzb.qóyÒ5<Ë?;«E@B*ñm³q§Äª\n\r¯sÍíe4OðÝll\"ý\"U\$Ñ0RÕp,ÙM]?isn+ôÞëkDõpZ¬åsÙ]d·e¢i±=uI¶4Á%î3[4W.Wx=­áwäÉJX&zxÔìCAl¼%mò&ðTÂ1X¬¡1]©þZ¸TaK¨÷÷àê´§GR&:ÍÜÒ3Ó¤³ÂºÀ½3Kz2ìFé¶ÿØ~Í£{táY#×#G+aA3øzdñ, íä÷¨-E{éÈ\\wäx­!QO4GVãG¸-éØ°?ÌImÄðêîFãùë?N]´VÃ:¡RM}ïI4}ÌY)°%2´F8³]+fX,qã¦Ç¥È\\«å8ï(l6ñ¼å/5H7¤ÜèµsµÙ)ÆôZ2QÉB¥ÂéLÊRwnåYI²\ràì^àî@Ò¯KÌ§`/*·ÐR#2ë<Lð¬4`";
      break;
    case "ko":
      $f = "%ÌÂbÑ\nv£Äêò%Ð®µ\nqÖN©U¡¥«­)ÐT2;±db4V:\0æBÂapØbÒ¡Z;ÊÈÚaØ§;¨©O)CÈf4ãÈ)ØR;RÈÒVN:J\n¬ê\\£à§ZåìKRSÈb2ÌH:ÖkB´u®Y\rÖ¯h £ô½¥!a¼£±/\"]«díÛ¢äriØ&XQ]¨¥Än:ê[##iÍ.-(ÌY\nRÌO)i®¥ýgC#cY¬çNwÏæôú¢	NL-¥\0S0&ã>yZìP',ÉlÞ<VÑR\n£pÖ7\rã¸Ü£ä7IX0Ä0c(@2\rã(æA @9£áDC09ðÈ \$«ÃçaHH­¤ÁÖAGE)xP¦¬ïºàv	RX¡¥ê3bW#ãµgaU©DÌ¸=\"øV3dñ ÓbSËÇY´·a6á'Ñ0JIÑ`¦ÎS «A\0è<òøÜ7D!`u®j*FRO+9:³±e/TË-M4¯Ç[ÛDi0Æt#ZvÐÔBèÖÑkâ*uìÙ:ÆI	ÔZÀvâ(×õdÆ# ÚùÁ°Ü;Ä1KQÂ1pðæùá\0Ã\rÂ´1\rãHè4\rã­¤ECY\0ymÊ3¡Ð:æáxïÃ\rdB0Î£p_tÝcÈIð|6ÂÓæ3Bl(4ãpx!òWR·5=S !@vdìEÈÎ\$:×aB¤¸/i;<\0¹¬kÙh:e¢£eiÖU/!NF\"å\$í:én¡@£#ÈX6e£wZó-E:BQòÉÖG(!LN½py#¨ÙÃØ:´W¡©Q+1NH£päædJøU¾¶ü§Y@V,Ä»îD?OÃJè§\\ÁLì¨b@VS*Î´­`P½\rô,6CCÆ0Ïx¢&<R&P<ï\\å¹´K±U4MXEQV¢ÑgiRe9¤è7¡ë{ÓÒõ½¾UCùÊO Vðo:¸.êö¿«;ñÛ!Z»^ÑÌRjMü\0Þ-/]ìqØÊb'À6Pæ{^AÐ:\$ïÒ!å²WC>lt3ÏC,%ÈÐ4hHsvèj	¡¡Ü4ÐL2èÂV»l¨EÞÃfI#´JAÖQ¡5G%`«¤_ÄØAP7ø(ïÈ ¡¹-E¬Ð \r¼30æ»C r!3m\rì>k2æ\nY¯ÂV02ÊEÔ¥¨Â. hw­±ûÀ X«7 ÕÚâPi%wâ¼×ª÷_+í~¯ðîÀX¥`¡É°^\"\0tc(éÆÏ¤2%p<ÍRk`ëmPHá/%J±È%G´u\"/f/ÜÔ1ÓëE¬9àW/]ËÁy/Eì¾Òü_ÌJD3aQ#ÆÉÄI\r¡ÁÜÖ!4¡Ë7µàéÐkbÈ¥±©J¤Ë4ÜÍ¢@HB¤*¯m1nL4´æ6;PÄrÔ9¯Í:tn]QÁÚÇ8ê×\nãOÔÙñ¡á¥¿D²NiÅ5d  ¬DàRiSXì(êjÊ*iPäÂ°Ý)CÂ>ú3\0äO¸á¯-\0èáÊ \rëJ 2Äò:Bìu	¾\\bò³%re±d>¶Ã*Z°Õ®6,æ]®uµàîHc\r3¯pAOêwpX2Ù5g,ð aL)b4!X7\"xÉp]7\$Þh&@AÔfôä9HÈ>Å>\"#\$Èb7ÁG¨üqf/\$¥|døðR:Ù@ëHèS axRI+	\$<®T7*¬\"C!º[>¼à3 ðÛ(¥ÊÉµÈ,1Ç43Tì*±d¬(ð¦\"¦yÁºAJa®\"ö]vHùÉ¹9'xú\nBþkGP±Pèò@û²(/!ÀXN<B. \nnüØ°M0T\nçÉ2Üia\"dTÄ%óLREüK¦5#y` 3bc8ô¥61éf\"àukÜõïsÛªsxo 0Å<æ	¤¢:ôÆ ´u©²ø)Ú°°\"Í	Äi³!§aER=:Q:#u,Âr+RÍ#]Wç(¸ä¢Í®¬ÝXÉT>Vú®ÍÔÐ:7ª¥ô3\">çæüÉi´Èö#ÍØê/hÁ[8°@¤|Ìcl÷¢ÔÚ«þªþf(·LÅ\nzN ³1ËÔQüP|»k=iµ¬R£Bù7uz¼WÄTòNoD¶àëÁJ8cZ®B£D¹¶lLXÈPêñËsWîNÙÞ9´\n	½XÅÐßêxsNéyDÈ5ýÇØDïÀÇµ®°\nÓ¢®ÀÒ¹±ò¡Ý³ó¾ÿù\0Þ¢'õ¾÷YfÄ-èÒû=zzBhb5@ì=ZÈ!Ó`Ç'\0PC^HRáw`ÖB\ræ':ù±ø,O;ä=ÊÕöv¯UûRÞäà³b,(sgQÅðQÚ\"ÝvI±Rm¥iZY0«¨C	\0 ÐÒ¡\nY¨\".Yú¾À®¨\$\0âd/\rp2Â2Zìø>hÙZ>Õ¬`O¡#ÞDûZ×ý9|³þáÅ~[]ùÄéà_ªL~Ákü}'ò»  ò*FËËèÏêðÍE~Í¬j)N®©®þ ÎcÎÎO®v¢oÜR-öË¤àÂ%Éÿ¯¢øÁJçVidñnÒ¤{NB4gI¼LÇ49Î`?'&r¡\"=Ú;p¦jFM¦ÍÎ#Fiå0¥#Ø\"ÎepÚÃVpRR£@ºöÚî>Ó%°£VëÀ6l¤hOÅ\no¶IPÌ2Â¥'²k³0¨90Þi4R.2dÐN¬1cØHAààFö(O>7)IqAbÓPâÎ¸0å\rFN¾lîò\ræþ\r¨Ù[Í<ÙÙÇéÇEÍ´ÜÏoÀlÔÃ7ÐÚÛçÒÛÝ4Î%ÑpÜ±bÌÃðØü|\"Pî1hÚªÿ®­¢îcÛÉçµQ<±È!FîCôO<ô	TC6òÑÈû¢44:zqÖÐ­P\0×¤ï°½ÎÛ 1ÑJ±þíÇÁQ±J£1Ap:~bjâÁ\"\"¢lmá2%ÃÆ1PÅa:QC*J`ÖÇ­Þg+Öu,ê \"áò5%Ï¢)Bª]&*dü 0¿µg¨l­b|£êÅÀ1Ór¦NÍ£p\"¨¸gÒµ0é\$an'æÝ#¿2»,²¯!¨¹\"rÉä6/FlnÀt²&åDÃ:D\"i¦Óç¢H\"þb#A66§:/92C1Ò	+:óó'/3-2PGAb6pìý)Ä¶8/'4BA-/Y-d4óX¢ø0àb@óRY4¡ÚÚâ?/¢á7s{ On³ÍÓy7-ò½.§AÓHÓÝÓbåA8edçQk.ë;/53=4ç5PRïTs7:õsdî Êîé\\C~¦CqP\$^ï.ý>d>§óîoÉÐ÷óá>I5?óìÄöóö&öY+u>.þð¬\nðí£84B¬!p×9âCo*«é<s½D-	CÑ/E4G=Òø.!`RDôÿ=ÅBó+Dôn@FÝ*ÒDzSs,F«.q@C\nl#3à8KÔ<Í£\$áØçìoþh¨Ö£¤s¼Äd?\"	0)¥JB{I#®b ³ÚþMD~tÊ½tÑ0tèùá+p\"\n1Öhò\rVÄ«r\r`@§E¶o¦þCJ.*\r Ì%Ä%`\r'p,DKÀª\n péP°þ/ÔQóu\0~ô 22rè(#aðFÛÆ\\üíJ4ÊÆ¦Ì#ç0N¡hhc®!/é¡pGXL )§4{R5ÕòAÚ'¶¥mÀHì®T¯Ö{!!FôÔ¨*øÐÕÎýBFõäÎ=ÈÒ²rFr¡&7ádÆï)cdø<ºRðæ LbéVµý#j\\.BaPóaN4o)¹\nvCé4Ä5@\rààEê÷bcÖ¾ãÉ,ö[ë»'ÍÈ}dr£¢M5ÈÕ#ZëÅf}qvÍxko ,Ád>ô¸½àn´àkÈTm1-äaj}­ÆÆv)\$`­)f®%,\0ÒïM\rò#bnmmÞõÊì&Æ]9SCtÏ!*úôdM\$Ö";
      break;
    case "lt":
      $f = "%ÌÂ(e8NÇY¼@ÄWÌ¦Ã¡¤@f0Mñp(a5Í&Ó	°êsÆcb!äÈiDS\n:Feã)Îz¦óQ: #!Ðj6 ¢¡¤ÖrÁT&*4AFó¤Îi7IgPf\"^° 6MÇH¥³¦C	1Õéç\0N¶ÛâE\rÞ:Y7DQ@n,§hÔøË(:C§åÐ@t4L4æÆ:I®Ì'S9¿°Pì¶h±¤å§b&NqQÊ÷}HØPVãuµâo¡êüf,k49`¢\$ÜgªYnfQ.Jb±¶fMà(ªn5ææáär²GH¦²tË=û.Û à²9cºÈ2#¯Pêö;\r38¹9aìPÁCbÚË±fiºr'Ê¡¨¨è¦5£*úÂò?oì4ßÌ`*Bþ ¢ ì2C+ú´&\n¢Ð5Ç((2ãl²¨ P¬0MB5.í8Ò¼2¤ãí!¬¨,¯,¶Ê\"Ö)Ç#úbÃz_ ¨rÝ.½ÒÚ\nHÒ5®û\0('MìÁTï¤kX2\r«Cì\rð1p4#ÆÏ¤Ný@?ðæËîÈÐÈÁ#A°xXÐãÁèD4 à9Ax^;ÙpÃJRÃ\\²ázäÁu¨ä2áh\r«#,´Ë\"Hãpx!ò: ÖâÊå\$S³¬]£­Þbc¶½/óxÈÈô2_î>â.+&à Í1c\nºµòÄBï ¡(È=ÊtãÙ23£9&?»¸¡\rëKëX(®Oê°#«B	#pÆãOC \"¨Àê`(ê2WeÜÀÔ(Ö1Ðc~x3¸ÙF±*#*º°å:9B¬4¯ï\"I:-£RF(åBbGGó#K<Ô2ªÃHê£`ØÕµ¬°æ1#s¸(\0ë:Id?Jø9:ÀT6ç½¸û¶¥÷Ï¸Â8h·4LØ§}²Ïd1ök¶\"uSàÃ?éGg2,TÒÎ.+8Õ­9£l,­b*CéÈçqÝ.ÎLèâ¹ÞyåÛ%°	 @6¤ðMsm¸Êäúr5î07=Òýã|×(f,Îä2¿ |Ñô&èÂã!õ-.6÷ÔCÁt\rÄr¢S¸rÌaDQ´\$°oÁ6)eÖ	æ\0*òIpyrÐl:ª%HYp\rå\r«uÛAh\$Í©PÜÏ((`¤¥øJä1P¡hCÃLhC(|	Bá7ä´p@¤âqöVá¹å.®L¼WËa,E²ºÊYñg-¤yì-+D }\"éßÈ¸ÏµPÊ!iDR\$¡ÅÇAp yQhø¾WL»#yD©uª¹õÀß¹CÛIka¬U²V\\q¡Éh­81 ä[a%é BR5	ØÓÃr@·ÐK2PáÉHbHJ9*ÁåÃwrEü8B\$Êí\0cV¸¦@e¦ÔjJL|0bèoÔì>AÅ¸=q§É)^ÙÉa\rxÄådð\"¡¤7Ê,eÜ#\r%ÉÀJÂ]\n (!Ô>è(&P@\0ÜO'mª5åÐÎ^ÐR%©MªÃ¼¶ ÉØFÒ,fmÍÔ³s£1ö[ñ`GÍBß\rÁÁ+4,øw5.ñõe=èQr2¡TNù:) 5HiD !9Z\\r\"v\$]q5ªªSÁ,²:ñH¬\"HIAqsÉÐ°ÔºÙuK§a	B¢	y/¦ÒL,sÈ\rN&>j\rQÆ-<ásðc|Rõú26ªèQª!4ü._\r¤¢	áL*Xòäc·mâù%Êk¼êì#\rdAûglo)¦!¡ºuøVýçþND3ma5~¦¬µN5ár\\ÎÒ%d¥-°Èié)ó³qLçØ×ÒÈÉÅ\$	Nó\$ÛQ¤:·\"Ò\\Îi]|ø½£6`Ì¤Ê³`¶Ë¨±4Æ:Yi!ÃÐØà3í¿¤ò<¦F¹Ù;wY»9Ö+ér+¬»*\"(E¢d.ID1xZxêYt¯¢ó¼'~¬xe´ÒÚËdPèß>,¼j¶nbÜ5»áY2~|\n7ª0çiá,Ø´Õ* BåÈ±>¤ÑC:jQRÊ]uì&GE³J_ÔZ­vbÌ\\`f'È¬&=ê2ASzjéEÌ\\g{ÏyJ@Ö]@£Ð´WqsUíæ¥K9\$#¤~Ñ Ó°úéYÂ¯Xu9¡¸27èDéY½Þø¡(	* I%T¤ÖÒÂn¬sÔ½\raãÅ{\nÂï;a\rÚ2CMN1ü®\\T5Ì}\nÉçóÓõü+óÛ3\\¡ùE\nCv.!q*£d¾ï@Á³±V;
íFì!Û!P*Ãä\n[Ä\nÅJ	s¾9g0I9DjJÅX¸seGN×Úh³=Ý}Í+\"!Ïiýµó\\ ×®Îu4:À\$*÷BOÝx0ÅQwËßûoð¢ùÌøs§â»×\"]ö&vÍ\$c¨ñoï¥¹)Ø4yxLÓySeåÈgy(Ý¯ÖúÝýÓÃz`ý÷:á½ ðÜA*!ÄBõx[IÁK¿0¶üúôçoæh9&8¾Moî}ê:¾#iÛd ÅÞH±r(%Ì¹íî½¹!V²}pµþJÄ7\n\rBßPlÍú0HB¨ld<IÜ0#ð,êñÐP\$}E:Ð§¨Íà.2Vm.bo-|-¥fókø#°:aá.@°4ïPHÞ,èÈLxÈ¦ì*ÈZ ¢ô*ÂºwÇVi0åè*2nG|9?\0àD´Eã|8eîºbJ;°@æD(E	Ð¬D#2!LD,`×¬èÞmê\$,B!0P\"£^ßbBnj ¨\0\$ÀÚgDÈäQv×dðÐ,äxfÎã±LáÍÉµqhíu°[-\rnOM°Ôv`\ngÈ\$@ÒlÏ16VQ z]0OFýt.p/kîñ0ñ!QNï¤J1ZçEûF>îÓAðç1^÷ïó®à)cpÃäB%\"³b0ãÄ¸,F¯Q/Ì:1¡EÞ¯QGíþà\$p~\"®\nIÆæfÂÊd	Ô\"Æ~!\"*@B*@èp¬Î´§H±äê\$&MÉÊ-Î6J7í\0ON £ê'âm±¿1¥fi­S¦ÄþQÖÉ¢Êäge B ÝÎÝÑj¿<Jò[ïµ&FûMÐÂ,`²Z;nz!¥&­*ûR;'\rrÎ|5QÊûP`op½nQ'§¹*²VP%´?ÂÌ,CÌ ²çKÈQF6c¡\$ÍÑw¢K(ë«²Ü-ñ»-Y-±t-ò¢#§^(/ÔjÆ¾;rþeÞ©òé/oÛó>9°85/Kî;`_Á\n´ÁLÄa.l?/³<¢ñ/Ñ0vD}1r|ÃKÖ9ªAsA(ê±0](=0¯òX{öân82jôBz>É	FdB\nk8#(¨Éþ©N(®-8S.Jã\"é9@\".|,ãÎM(±¬û@¨,³Ç)qP«Ðä'ss5#O=®N{S>n?> GB×)\$Mnbn,gô\0M§¼ò'.ðØÝØKruï´ßàÒÀä/â×1^{LÕ0ìTbåô}&?2m¯H]£gqnd´HþB-Dâ±D1Eè÷ÔaE0`ëeÊá&JüÌ£ gÔcCÒI=qðÒðt0ò1q¢Ql¶õ@] Øk\r3ª\\¥O\0f§eº%àÌC²&ì}8\n ¨ÀZ\$dâ;JM&b\"ÊÏgNÌBù­FqIâ:l½\"pJ%d?¦\"ÒC\n	¤íB òm ò+®B8Æá ,Ú/*8®`cBwÁ3!(¼Õ498[®X,ì²ÆòÚ[ÅP?µkãÀ\nNN\$Am\nàDÔ±¤ ôicú{0V×æ@ ì¤´rôÒ¬õY°fUZðKè!©hPÛ»q¬Ï@¨Ä2ó°\rääomÑUÊFæ&cÈl&Æ`r\"»äGQí~j×uüCPð ¦!§a&ò# çZ\"PBãC,\"bãÈJàÆ ê\r 	õâRµ=¥<-([deU\"`t ©Z@PãíP¢,½*C5ú½ZÑvkOãË^\n¤= ÞM\0î,¢é3Ç¡2éú*Â×H.\\0Ã\$M\nBø=\0ä";
      break;
    case "lv":
      $f = "%ÌÂ(e4S³sL¦Èq:ÆI°ê : SÚHaÑÃa@m0Îfl:ZiBf©3AÄJ§2¦W¦YàéCÈf4ãÈ(­#æYá9\"F3Iºt9ÁGC©­¡ÎF\"Û67C8õ'aÐÂb:Ç¥%#)ø£DdHèoÍ±bÙ¸Èu¦ÚNá21	i@ »ñ¸ü S0ö¶ýÿMØÓ©Ë_näi2¹|Ï·È9q#¶{oÐ5M¦þ·îaÅtÏ5_6ÌQ3½¡2¯èÖb)Vù¥,Ã¬HÊÁCØ÷%ÃÂ9\rëRR\$IÚ7Lóü£ãsu		jîýµCj\$6¨C\"\nbf÷*\rûÂ4©åàÒõ0mZ å	ºd¯\r#Ö¥ ¢ö½ P¨bc\\Ê7£(è½¶O«î5LhÒ×·êr.7é\"L½ ¯´´ÐL(¡	Â²l:°¤õ&³ð HÌ¢H`7GbÉ)CAÐÌØL#ò³NbÈâ\\4C(ÌC@è:tã½\\4ZÚËÐÎ£p_	cî½xD¡Ã²89!ÃZ\"7ËÒj@­ »¡ x!ô.=!¤(P¡æºNP+(#/päºÖÍÃ(âÜUÝ/\nè	·ÐÝ²ð%.Rr¦K!YPÕÇa(ÈA±ÀæáØ8:\r8¦ã®!\n%ËÊ@§³øêÏÙc]C,Ø07ÊC¨æÌ¡Áº9)å\\x[¶þ0²%NP×h¶søÏPèv|)ûÎôC(Ä5¹¹Ë»-!ÀP´3úèËAû	C¾ÑV*FLp@3Âqôx90¹óè(#¬UøÈ¶aÑJÛ~(Ê8M¡ ¬vÍn3°ä_ÕéqG³ËõxCWÒ4Lã­ÿïC@IÍ¿4² <Ñ¬#hà½`Â*eÛç¥¸»~ôB ßõÏ¢ Ð\"æÈÍ=bT+ äå½ðåÄ6½²úc|!ö`Ò3¹£/¼E¶\\[§}5ö¯AsùA>ëç£Àè®IåEÇÄ°TvÚÿ@HÄ÷1òä\\fkm?7´B°¡=9CrúÂÃihÜ¸)Z¨4rÚ8e	¨ÄÿÑp	Ïü4Á6CÉ|/ÇAÂ(HD'\"FJÂØ^1lV/DÎ¡Ñ\rND'â!«Ì^\$!4áT,p¹DEcá©mñ^\n² I¡ès&¢®ôÓDj;(A\"a\0Ól2Êlÿâtzú\n)FE8±K:\"ð¢;Á¤§Õ\n£TªTªµZ«ÕJÔ9+urÞ	caãôePäb}d¤)@Ò´	&DEG)`Ò%¸sS¼²Ã!\rÊ%ÈÌ§ºJWÉî\$¶\$§ShÈ[	n\0¹NÊD©2¨UJ±Wu`¬l¬Êéþ¿ðÝ\0UÒÃ|NÕVa5PêZ}d½7dZ§	º>sLDI9î#édÏiy/\nl§H!¡½k|3àÚPuA©a¡)IPR\\J>7Ù!ñÐ\"Q3Írk\n%(0¦Öç¸ \n (uF¢ô4\"DLÙê*+\0 ¡¾c&a±<R!J!öwKQ}yµUÜU£c\0`&8xBÑ´KòÖP#¹`ªh§4(üÜÖoÅÀÇàÒ§\rY`Ò/\n\ru2´Ð3\0ÂF7ù7ÏyÈÌÃ7CË49B4·Ñ\rÄ}PÓ/\rm¤ª5MÍ£hâG@Þ¥(¾&²rHÈÉC%,Z¬*èñ¡Ó1Ä8«±ªtXÝ°ò8ïÒa,ì 6 #ÂC{è°¡<)J	E]eÜq,ÞïÆüRX±*ÎEu&@ÞúOíú»ÑüùC3çk+B_·Û¤W¢P@b1áH \nnÂ>HI%\$çjòlîÉ\rä¤bCG¬]#­%bF¬\\ÑDM9%Ä{2c.¤¹9êÜ¬1âKN&§ef°k\0PGn9'Lê¹6íÐÖÌùÉ\$L3wEFp6øÁM¢Æ(Çâp¥àæ:z´£D§²!2ºhaæ.X¸ÓÁGzQà}0æØPB9Â,BÁH*ù/ñðó¹(V\$ÌÙ\"Ðtûc5K©µZ¡ÕÁx\n\"wÅ.þ u¾ÁP(Ð5ä*Ó°êN7µß¶R!@7JI·zaî>¡½XRÌG£aaPÀ\0Ñ³H>¦®µ3*ÃJ!&f¯b\n÷öÒ_§Ð\$de¦×«#%´&lÃ± Õ\$]drpç±Õ Û/r\r\$tA4PÆÂ¡0h\$1ò^NØg\$î['Ìym6lï8\"ÿ×bÑæ\re[Ó£2«¹¾¤\0ÌÑ³>c} n	Ç_§ð¨C	p]ÛødÍo±h5²®n×>n³8³V×X¨/*LTÒ64i8a³ïý÷;ö[÷Ý¯õ.!íNHþD©§ºÁnëc-/.7¹é3äX!î¶ñeÃø0¦<ÔÊÎw·CJ!äØÅu4d\$×È¢kZX=±\\%ÙÑó:ëCC}á°øQæ=Âè±ñý/ÉE_,|ÞÞ\ry^()pÂ>ZQ!×:ë31!e_óÄ²*ë/<(¦\rãÖ}\n:}¸À9¦¢N^jûB2#b:t@FØÎâ@fún´E&º\nEÎrî¦,þ@0Ö-\"zÄffza#42,­LN;­JvB©,àBðR\"ÙZØO`\r'ª ÏObàFøÉð6ÞÀ¨Úb\\õ¤%0àhüëA) ÞËÆåæø\"0U,þf¦n#´âzgã×dZ.C>b(rèÞçFxJ¢°ÆzÌìº¸LÀ\nÐ¸vFà®,°pìFB]\n¢\n|<\r©îFà]lÀµà0'2A\nXÊäÜ*èGA0p½bzcñ\"uQÓ±3m¢^ã-ì\ngÃÛ-8ð£§HgNñÇ<ÞÍ±Úé@ÈéüËÃóòòòK{¨uQx!1|1ã/?pRômD\råaVËõqõÑÌxÇÃbg#ÈHÖ%Í!-ìÇ¬à¤ó¥ÑqÄÈËÍì	LIE<²Â2©*Ee8T ô2*\0ÐBg\"Z.¦s &VYpïÑÃ¬-ì_(o°r®Í®jÍN´¸Dm±¸ñéxßÑPò_%;gn² ìtP<¯2dêR|¿Ñh]è4''&Í'G.DN¦FÊ*(B7ån@ñl0eß+gÎ§\":äòãQâ?(#qª]Â?(°1¨érÑ2lÞÍTsÂ4ê	~tRò_g.q¬]òp6î\rÆ/²§&ÂÍ,åÞP\"@ô#0üH)RcRë*úH¤Ñs-3ò[¥·+c2î¯*»5dS2fBU2ìë24ÐªtS2Æän\\Y@Ðo¥,W%ÄBà*%.HäÓ}8{\$SäàA7â!8#ÝàÈ% 9åå³æÎæm`çÙ4rÓÔ'fâSJã#=ó'-ðæBöcÈó(L¾Mªö\rSÏMÎ¸2\"S@ÄáAãc Ö`ÒÙ.Oär®dþ!/äãCñhJ÷ÎW¨°No5ó.bgD´>|æ0õÓÔÞ	câ-Â0ÙËÖþóN0ññ½è°CóE¾ôT)Ô^ôkAbÁ.èµðÒgÀë¦b7@K`Ø\0V,Â&u¤¢ï\$Þÿ*9 *B\n ¨ÀZ.ÄÄR<D.ÆðÒ#\\ïH4ä£O\0004ÃgOyOL<aîûOðx%ª¾pÊÄMã/¸\"m Z@T0F3oQ®Jº#åQÐ*(Ìçé2õ0sÀOÏÒÇ&Ñ3ÒI±øB@4Ük ¬'!!éo¢ñç6,ò>õ\r\nM¨©Ïr4÷Õ-UX`Þ0ECÙYµzGãÛ&:	¦¢Ç'2(8ÜþtB>Áì3náÏVâa*ü¶\$´>»OÍb&&äÌµªßtnÏè`0Oã-U®8£jÛ&J@Í¬Þ>â@\$BÙIí>ÓVX¤Þ3U:±,öÑ4\$Ì'Ã\$feÄ¤q!Ék \"à";
      break;
    case "ms":
      $f = "%ÌÂ(u0ã	¤Ö 3CM9*lpÓÔB\$ 6Mg3I´êmL&ã8Èi1a#\\¬@a2M@Js!FHÑó¡¦s;MGS\$dX\nFC1 Ôl7ADtä@p0è£ðQ¬Þs7ËVa¤T4Î\"TâLSÈ5êk­÷õìäi9Ækê-@e6¦éQ¤@k2â(¦Ó)ÜÃ6É/øùfBÂk4²×S%ÜA©4ÆJr[gNMÐC	´ÅofÖÓs6ïø!èe9NyCdyã`#h(<°õHù>©TÜk7Îû¾ÈÞr!&ÙÌË.7Np³|+8z°cî÷©®*v<âvhHêÞ7Îl¨Hú¥Á\"pÞß=ëxÂÃiët<(ÐèÃ­BS­ÂV3¦«#°ûÃBRdÄ+éÎ3¼*ÄÈBÊ¥LÞ®c\"!Pù	ØØ;Qj·iÈèêzZä¯àTË3¯È{1/«c ÚÔºÃôþ?Ã¬&ãÆý\$bn>o«î;# Ð7¨T¢°ÀÐ@XÐ9£0z\r è8aÐ^õ(\\Ncs =ã8^ð%\"9xDÃkÞûµ#3Þ¨«Hx!ò2(\r+lLùÉ#\nÀ&Ë¢5´Còá«ötFÚéâè»'@Pç¥0ê\nñT ¡(ÈCËÝ×äýJÃ|¶îÇÍR\n%ÇL!®`FàPd¡²ÊtÉ6HÒâÎI\$ÈHµ0ÌlI|P©ª)ºLÎ\rÃ«¢Á		Ä¬2¶«ªX9é¶­ÂK|ð·rZµº¶9<\rØ¦(S~I#pÁ/Vókaó\rªÂÂBï!ö¼ê{²Kî<Ç³I+:Ï°Yô9Pî{¬\\[ímÂ\"HÖÆ¤J×ÅªÝ¦Ñ<Øv*3Æað´ÃLLr¡ò|ýµ=âÄ>cïÄ=á1u]AÖñ(ðçÉ\"ÒÄ£X&·\rZ¶®®o³tã0Ì¶ÕHÌÖËÉhE¬*:Ô&½º|¤9ná,ÌÑCÉ©j*Òi\$î¯xpÎË')ÚPä®w­SDùß1-Äw ]IQzrÂÄÞHo±!F rRolÍ½°nÍ	£R K©6§Tú¡Tj;ªx ª­Uá¸BÒBCIiVÊäC H]	²?FXç ô²kéa6>7 ÏËiJé?È0à,=[ÉlÉ@\\¥²SJqO*D©2¨*°9*å`íÃºÝÌ2`] kmÙA*_0'PÙòÌÍÍÙö\$9%²\\\\V iÆîCÁ7j 62c!ðr3¼0c uOÊ\0003R&3¥Q\n((£îLärSØ7RtïrBþ@£{/©»\n (fA?2´GòZcI Ce1cy~Ù+éñòòÄÛô­(ÓDg#ÜF9táûx¥?ów+§´U[(«I¤Hc'©Ì3©×ý+dtr'H2À(üL\0C\naH#N¹£;b¹³Aa ¦9@|Ç>É|Ë·ÞFQr`&=Å*Ã(bbfZ.ù»9]ôh¹VÐBNbG¤8¹³8ãºB£Õþ½µRÉ±°PN_U=Q©ê0öM=\n>Q0(ð¦%kÕ'O\\ý\"8®ÀÃÉ9¢¦4'OÀt9=1WLðÌ*ú´\r°¶\nGM;iA#(ãÖ2Á*LTNmå­fY)\$ÂWq&ê3 Æ¾ÜA<'\0ª A\nÕÚÐB`E¶l)(uôIíäJHÕ¼q5Fkqd,¥¸%úÞ§ÚO#¨ÃHMÒ=1[³4fÑ{3Ç!%ËJvéú`P2hú0³oKj§©-oòêhDè¹°!«¶´ÚÛÚ¾7ý²;ætcÁÜtT(V:§efÙ\0L9OÓSáMºb±\0S¤VP5âö[@S°À´£ÕÀ%ðiIX4´Þt*ÊÆ×ãÊ»Ã[}îå¨50Ln­ÓQkÜO:ÚV13#XeÁÔª	ugãfýI]\$®OhlÎA¾:úÇ­Æ¸ù=\rvË@rÔäpcgA\rLï@.@%äþh-ÐÏ)Gã¤³Ti6MëFw¢¶ÌFWä\$%jXÁæz±,Ì¥ jU\n!ÔèünVø´r2­JÙ+ÁÇZ ò¨/!wcµµÖð\0¹òvHÉY\"%jMzìã÷'ÃPe.²àÂTVRÁvû]Æû?³ÄÚDÄ9mMÖH·nÚ\rÏ;|mÌvj0 ¤Øbb1Ç!D3Kèå*>(Á ÙíBGìèaI§5åSY9;#¡#¥©«¨¤Z³Î`jå|·Rb¢ë¹Îöbku¸%iÄ¼°Û:èWÔ`æj\rQé!µÄRéçG!Ìý-îoÎïëF»«ûF{:ëß2à+E\\9{T0²wIXynä7â 2ÈvÛ¢&îå³|\rªî\n?Äø6Ãîj\$}¯³>ÐÏÞiÍä|f.T½ñwÁ¬^ªéå:òì;ïÒ_ë×|ú/ïô³ý¨í±[ÖµsÙCOgpYWàuÿVl7À÷#â_N»ð¯BCY:SÏ!Ú»ï\rsêªÓ½wa´;>ÚOQñ§@KÏÔÌÿ¯årÂVýyû÷{¾WÜ¡ÎR¡êh'ªBc82#&2¢èórºd RcRóo±\0#è¢æ,ÏòÿÍ@PË:\")äüØ-NÒ\"p,BÞög¦º,ô·ãº;egÞø/,åð.ø¯T¿LµOìl*PEå³#´:Ú0pïÞIMT(°o¡VöþìÚ{p:­_\nÐ¢ ÉL:¦b¦B\nÅIl\rZ~CuBÄF\":ê`B\$lú|üðrÝíM/øPêt¯Îx\",-ÐªÁ,eÃ½	J]Ý15¥¬_¤\nM,C\n\\Ó/¬ö/ÐÝñ(:&:÷Pùq,õÍ¥«ÆÀà±Jc¤{ì³g\n°Ü%ýâÐ\r!ê8?bb¢\0È=Ä\0Èâ3M«±|ª 'I?bÐ1x,'ü¢­A x\$êÑ\rÑ­:På	YÍ,Ûð±ÈÒ<íÐúhQ¾ «Êà±Ý¢úB-S\næ0%©\nN»1ÆJdñÑ ùqØf,¢ÈðàÕLG¾Llp^¬,äîøP(¯Ò^£ìfzìKc¢©mþ&BÞéN¬ÜÂwE`Ø`Æ-±Bò\rÒ£G\0p\$2±#8å	ÆÓÂÌDàª\n p#-ºàOP³è¤æ¹ÃvîB&½­¦ãCªYßã\0ÁÈÜQñB¸òr4JU\"¼5Nî¬ò'²¦(®%âÔf'ä[D ¬¹*¶Ý²@z2F[JõLkìLnõ\nxÌÎÉd\"yÑ1±[23ø\0Þ6\$î³1¤N6§[>ªN~#zo¦ ÜæD»5é¬rº%fl%¯4ÍmZÉã¼à@\nÉ «	¤3³?ðÛ¬t1\r;â-Ó1\"j ²¼'/VzÎô«åNî;Rz86y\ràì:\0î0ã190ÌnY¢¼~¾>Ã~n«Ú";
      break;
    case "nl":
      $f = "%ÌÂ(n6ÌæSa¤Ôk§3¡Üd¢©ÀØo0¦áp(a<M§SldÞe1£tF'Ìç#y¼éNb)Ì%!MâÑq¤ÊtBÎÆø¼K%FC1 Ôl7AEs->8 4YFSYä?,¦pQ¼äi3MÖS`(eÅbFËI;Û`¢¤¾§0ß°¹ª¬\n*ÍÕ\nmm0ÍKÄ`ß-Zã&ÃÆÎÏ.O8æQh6çw5ÖéÊm9[MÛÝÖ¿5©!uYqÓæoÁEkqÞÅÈ5÷Ûùäu4âàñ.T@f7NR\$ÏY´Õ8±C)×6´,Ã»BÑéèä¦)Ï\$ó=bá6¦÷£Âh9©Ãt¢jB¦È£^¨K(É²H«È¾£¢X8- Ô21b(Ã¯CÓª,7 ¢rä1kûN§®ã,ó½+rt2¤C2ô4e[ÈàQkîÛcËø2 P¦·8cÉÃs_2ðé®Ñ¤¼1?\0P\r¨bDåHhÔ¹Ìüý=jô·Ô?Ê¥ã:,3¥ÊÈûO\0@=QÎ4ÑNp0ÈàÂ´D£0z4c£ráxï[É\r½Arì3é^Ù¶°,J(}>ÑÓà94\r Êã|¯êF¡¶ËÆD®[ª £Ïo£¬Ô+)Ã»\\XµÄÖ!xÝx²\"r:ÝÃJ585ãæÑ_Ó°£ò8B#ËXÞÌ¢¤µ%m<úÍà3Óä7rY>®ãrè)§ØÃÀYZD	pÈ#?ÖþdÆÅL´¹K¬Ò0¯¢íp2\"ÌËh»2ËnLj5¢\n3£SÖm.f#V1.H ÆÞ\nbF å9.ºK\"7·Ît»N?µðåmÉU»ÃPô¼2;ªÒÐ\r{;î8þ0BËR2\"HÚ8hks\"\"å¢^mkÁEÍ»4\0P×(¼ë¨ÕVTKI«aìãÍ©k\$½Hæc#t¤3ÝVªC\nj¢¤»ðÂ¶Ð¨.ÚðÑãÞ°¬/ø#zÒ¯I(?XÙs3×3hôZK£O£dÿ¸w«àù)Îíôó\$u@à\"TfØgWá/EíávºO	\nX¾óâ´OÏÙ?@þÃ{ý8Ïý}®àÅ\0ÎäÑâPÆÈs:&¸3(gÔIB£Ï\rÍBgNÁ\0AQ*,9*ÆUÁÅU*¬4*Õ^¬UµVáÝ\\Ãµx¯ÀXAª0ÒU@>¸Ò2gfµÌÂîL`#[AVüÀÆÉ\0qÎ6.?rNrN3(ÒI\"¬ª¹Xø­ÂºB)åÜWË%Û¸â<	-Ñ|çJC h\rç¸Ó  ÓM*ÎdÎm^¡	)çHûcã{\rF\"SU-rhq\"_2d1³8M\n!S~qÔÊ	;/`f=Ä-\$ÖBÖÒ@[MéI_å¢~o( \$@zIÃðÊÁ\0(( ¤ÀåÈ¼&©º|·öC°i.Eä² sd¡A(fvU©RE)&!uÍÄ[fú>[d\"dDLÑÁfÍi<)LTÊ¡mMN&²»£^D\n¨¼\0ÂF%Ç(	üEIÆ-%Ag¸ÛÃp\$¤²ZsTg}G\"
SÒ|êqnD)>pØPÃ¬×í] U\\:Wj0´%\$r¨&/Äd_º«Sf§eÂO\naP©²+D£;'µ¢²ä^¦¹h1Åð´1e[Öµ­LIâÝÑx]QX2S2Í³Dø6g£)#xLªªÂ0Tç©äW\rw±V54âJ°e/Ô¶¦ Ì\$V*'\0ª A\nò\0B`E½lÁzB+2·_p	TIFáiP¬ÄÐX1ÔXåðÛk0m7eÒ7Áj!ru¸céH}ÂÇ'\nC[møÛ?7 rÂ<\n	fW^}\"¨¸ºTÓÜ,Ó¶åÏÈÒ¨×Vü¯ì2,fð­#½YËäð5RYé[¸¦£L*wÜË	(ApØ_¹`hmÊ èA°ÍEpÍÞ­2Ý^ik#\$2s¶»r¹A\$ª£N	Înf°KËê{^æÑy3;³vÁÝÅÌô!ÆÔ\0PCD¹ø7mNc¸g!lXg¨ÌP-Ò¦L²ÌÂþ1éªì-F±oÞPÃ¡KÉ®okz Aa ëñÉ84QFV¤½×ô»oàòp/ÀV½Hq\n;|9&1ÄU8Ê¯jm NûÝÛÿ ~¾³®ÉKNjÛ2r8/.9ì3.Ã·ný <Â·ÃC .Þûw~&.G	ù,ÒäçZcò8ñË\n[ä&ÏJJ\"Dt#ÐYO¸Ænfá/;fÀÛ\$ÒXÃ(bê©¥Ü\"Òúy|å3FØÒ`jÇÈXfû zJz¥FPBÓ¡2\$jÿÄY¡Pà+Láº^Ì¯6<Fµ4â?¢W¯âÃ,pfîñÐW!û\0¨¾ÊRØ	e´8åætf£\n)p¿.]I,ÒÙ6C+4ÜûJ[.Ñä<yï7¢ôibú¥Ä\\pU	É¿¥ò7å|'6p´´ºÒ^~Ö óÆÐb_­ñ¼[pþ×ùvüÝÿ?A_½À¶òÞÃ?6\n»\ncëdJzáhàBâzlïÊ-ÙpæmTð\n2y\0ïäÝJÌë¸­üûÚæÍ»P0Ô04ý)ÐA-üñ/È\rdM*¤`§m0\rkê\r¦6p\"^¢ ì6\n¦-`ØK\n>ÏKe>!ÀÜUB 2bôøºFØ´\"Ö»kã	0s\0âÏÄ®Ã.¸#Í@»¤ºÓ!¶-ÂÍ+èbª:ã\"]ÝæÐðþ/ÊÐðêsl^í­ ¦ýö¤\n90óOÒÙé3ÈùP+\"ÒM8rqFzetì¦ØÑnìD¦@K¢MÃbnÌ\ràÒ`ÍÿíGôÄ^ÙP\0G±kpÉPVAFh\rÊéjÿÑm\"7;1Oqo1g§uùdÎÝ±úãºû'±´ý¿\"6þÂÿm-¿ja&ønÁ±Ä£bñ-)ñö#oï1ïìÔ§\"ïÃ&lÎÎJK ÍN-h&Í\"{ç\$æÒ!!&¢jmV#NlïÀÕ­L1`¨Ö-oo/\$ílQrô2Q%Ñ}²XÖRdÓq\\Rb8Ñ8<\"TIQ 3m¨¤òòãÊ£ònàñu(äÙ'gâÿ!\"ÒF¤ïL1¢Ø\$næÑÌ-zaÈ0z\"Ø2Àÿòs,nÌ\r(P-Íè¥ÎÌ\"¦\0ài2æ_ÄÉÎTaÒø\r¢%ò\\ÀØk<=&È¥\"jõiH5	°nþ1C.	ÍDA	Hè*\0ª\n q(~8¨pN.9£¦\$®!Ñ\$#a^¹ç.Ì²þâÓ6ShO. â°bkeê(Æ[ðÜÓM&qBd÷&iÒ1CÜ×â ó\r`NQ³ 5SKV\$ÊV/Bd}m4¥óÎ×+G\"âDcø;f&.L42&§¾ØïDË3^\"ä&\$Ë?£ -Â=>ÚK83LG°hT1¤¯@ äòÔ0tA¢,¢Th6Ï1?Ðî ¦w+.§l@í¦LôÄ¼jO¢IM	Æ¦j´b\nÃ*q.xê`Æ]\$ÊÀ>!£m.Gr\\Æ&¡¤ôMÀØLT2ÍÏ\rL\0;	ý@âf¢f5ÍÍ,XITª7m¢¾!\rÀÞÇãD2¤~LÑæ\$i ýP\0.CzóÉÂ##¾Bö\$`";
      break;
    case "no":
      $f = "%ÌÂ(u7¢I¬×6NgHY¼àp&Áp(a5Í&Ó©´@tÄNHÌn&Ã\\FSaÎe9§2t2Y	¦'8C!ÆXè0cA¨Øn8³!Ö	\rà£¡¼Ú\n7&sI¸ÂlMÆzÂb'ÒÉkfY\\2q¹ÝNF%ìD¯L7;Ôæg+µ0YÎÍ'ÜÎqH¥¡16:]ï4é0Âg¶Û©Hr:M°ºqÎÿtÜîõý÷é¡B¨û­¼Ìå½JðGÖ\n!½ò©¸Ön7èS¦:D0ìLQ(YÞeÑú9ç3¬^Òçæ;­#\":+(#pØµ¢a\0Äñ\rmH@0ÉjôÕ&²iò¡#M|:	É(ÚÀ¾(@æ\$ãHÈÁð-¦LÜÌ è;'ø2¬Ì\"ÔðB	Àè<¦<¨ë;9G»Âñ§pì7B±ïîú7Nc|¶ËpÉ!Cs69h jÚ¤¾ÏÀ@ý èàÿÂcÆü\$K&ÖËÏlãHè4\rã«0çÀ¡`@RBf3¡Ðtã½L19ËHÎ¨!}%JC ^'aðÚ´¡khÌ´²I@Þ7xÂ@é£|2KÀP¬£ Æ:®¸Õ5°Ò2êÕ°Ø6Cbà'.+\nãä7-£:\npò7ä ¢Xß65àP©\$¨2ÍÍWS ÑÃzÚ5¦x èÇ£0Â:xì·¸6àP\$#U%#n	bæÁ&øÐ;-,ðÚÍ&¸x¦ÿ°5R0Xc&ßÊ\"'ìh\$2c\$ã÷ì¼\\Ahæ1²l¢&£^¤àSN8!@R\"Þ[Í[ëBÌ¾Ì\rcPÊÉBz.Ë¿{nÞ0Ë¯è9îÛ µ[KÒØÀ#löà0\"*CÃáû¹iëË!g»vágÝíIÓË/j¨L&#Íc£¼èæv43Ú£/NC48÷¨ËºõÚ´ªt§qZsà£Â(7\$VF&(È£¯yÊ¥`Z4&Ër,Ñ½*3ÚZ0íõ\rãxA4\\¨Cù?P6(MgKF7+n0­½µìªpeÌÐª«ö7ÈñqèT\rüÑ«òrT	¾N\n¨)`ÆòqIS\0M1:§Ã¢¡TjSªæ«r°\rÀ¼ûåÁ>§Åv»2:0ÄÀk9ê86\0àsÁh z)&´ÄtÄ\rËÎâ\$ój³@¥ÀºSpP*%H©º¨N0½V%^¬^+Çy0á\\¸Pà^q8vhISoÝ¬4Æ 3¢`kj\0LæL ÅXt&¬Ó±¬o2/àiàO°ÝDBÒHrÈxÌóËðRoÈ6?DÚPJ\$ò9²RÎ4\r%½\"èPrÚ1¤ÕÌbê\riBâJGð -0f!°PNÁL\\E¬q·]ë­5á(\$TwK¥ÒOóF´ÁoMÜ¿@`ÎCaPI®Õ*È\"\rÒ¤ÃF¤OîÙE«ÐÜr@HZ¨ÐCh}fdçw/Ø6ÔàÂFz½`ÍTPoX0B!M¤V(¤3bÓ14e(P¡ô£&Ëõ>/Bs ÖZíË/t¿¯ðZÌ8±i&p¾ Æ=b4ht1ãJDÍ*Ì6,¢«ÖÙO .ô®b¸«á®RÆÌÕr^LIÝ& µ\\ZÖ­	yEüÄ)ã`Ly^\rntVEÐx'!M«Ò5-VÒÁP(\"´ÆáÄ.µ¢£PB3\r¹PgAjea<'\0ª A\n¸ÜÐB`Eºl	~&WT¾yÉY,Â_Vqoc·òÎ4î~£\rRÓå}R/Í¹u «?è	kPê7¾\\'!PævH¤Uck-´fâÜÜzÀì¿òl¤ÐYÓ\nËªaàÄlV£Dà9Ð tím ófmÌµNß!t9él_<å\"ÁÂ´c¾Á·+µ.ÃHz`.µÃ@ÒÇxD=¤]0Âü¤C`u~Ä©2Ôc¿ô±v×öîGHûÎh/)/´·rÛ[¡S'H\\Jß0ÙþÃb6Ñi§.\r¨äÏ3|õHÍÂ/×\\²àÌÐc\$ (!3ã/ôÎG-O¤­{kFi9&ý\n@/§ñí«u°RQÐ¼­=¡¾AéPëÝ ¨BH-ºk*úzgR6C\rùøäÄìÊrö1az¯Æ`B\\ú\n83HsÂZÜF2É­ äCà	Ý-;vnàA¼7æËÄæO]í¾ìÎ(Mi5Âq7é1|kÚÑïÀé»Iï	`»r+-xÀßülHbbn8§¤ôØEí,íc¼»!óÚRK°%'Æ*ÜzW¡Ã%]µñÀKäÄæ×£×I#yäÑ3YÔHy¥¥m¯â`I©ÛwVÀôÀB^=dç£«AÚMtâÈué#Ãñ;g<:G'RÛd¤UÏAÎ\"Ù¼}íwÕºZ^o \nWd¬´£º\"Q/è£-¡ÂÆF¡E%\"ÿ@J1k#:þÑ<¯\"ªÛ:åËBÏ·â¯´É,N/²JòçhÁ:ï¢6P°RÁ§ïZ¡¯ÉÝ¢ÂÅ?«S~ïÄç!øØ«óÏ§ðæ\\6ûONù±WóXÓ÷0Çü¿Çh7Ùp×S·íî¾.ÝÎ\\É<4î° ÿdæñnBÜî	\0ÒÞë@¸&Þ¸fZ!ï\nýNÜç­\$(ÒÏúþð9­'âhÿ¯»b:	mzkl}m²Ëº)),5& JDì>&\0RÉPn&â-­²pzÆµ`ÜRÄXÃOlfCb\"&cFÒj(b(âf'Ð;ð0#Í>G\$Ñ½ÚðXfÂYå¸þ¢sDÏPà ×pÞýïMNëèÎåº­|ÿ¯\0Â\";I/qññpRþFÊ5Ê×Ì.qãiòÃ;aàJ%Ëéh\r0²%/.:ðM)Å®Þ×qXþÑìqaãÑ\"!gä<ÏRKÌR#Ú&ÀÈ{FqÆè#&A±+ã¸ÃÂýÆ0ªHä0Bz5°ïÀ×mÑp0qºÌññÐBmoÇ!1°_ÆPø_äîQn´âN÷0­4\$âê5l­8Ô2ê Êæ*)\r©òr.©­>Ú!âr¤2\0ÔMT)ÏÔc¯È_ÒM\$QíðÐMRÔ¯&\$f\n\\hïC&JbÃÐù'ÑeÄØ÷Í	ø@\r1\"pc®\n:B'ª>f^ÑÃêÌ°\r+PBÃ\0_^ÂÊ¨æ2ÀÌ#´ÞpàÐ\"á;Íë/+@db\rV\rd!Ð.¯5*	8î*cÄ?*ü\n ¨Àpzæ<ÀÞ¥¢;.ÉÄ\$gî²öàsB·­Ó¢Åf8\\(1s+Jôc®8ÎË(¤#7¦ó55Ã^Åob¨#lA¤N°ãb:¸0HB¸ç` ð¦ÖQ)o\\Olù,QEÆ´q6\$7;Ï#+ç¶Bì]O<<\"Ïe<£fCp5=&ì=\",%\$Ä.êÉ³bûÏB\n\nj®ÃM±¸:à\râÊ:ï7éCCBÆ<-FÏ9®àÄâa,ÄÂô\0-âIFÜ!ÄÞ-ËæÐ¢ú@#¬MóÄ?ëï&­®2qXÃO¡F¦_@àöBÐJ î-KãQ½*¤\\¢\"\"L£-(E¤^!@Ò";
      break;
    case "pl":
      $f = "%ÌÂ(®g9MÆ(àl4Î¢å7!fSi½¼Ì¢àQ4Âk9M¦a¸Â ;Ã\r¸òmD\"B¤dJs!I\n¨Ô0@i9#f©(@\nFC1 Ôl7AD3Ñæ5/8Næãxüp:ÒL £ô =M0 Q\nkm¦É!Èy:M@¢!¼ÈaÁÝ¤âhr20Ögy&*ðu8BlpÆ*@doæ3Q¦xe5^of!hÂp¤[î73qä®Øíúi¡¸èy7pB\rçHÄLíõû>\r¯Òy\r¯+ry;Â¡¢©Ìë¹Ó\\òb@¢t0õ.ÚÅ\"ìD)*a=KðûS¢ãæ£;A*ä7·N@@ïÊn) Ü2èÊßMÐÊõ¬èt'ê5B:¥©¢pê6Än3Þµè´Ãòr7¤K¨ÒPØ)¡¸#£|h:K*#½\n0	£65 P¤Ã?-HÄü6ÂFNâ?.È[Ê\$AH¸Þº¿Òã\rPØ7ÀÍHæ4¹°Â¹9Ý2cU\n 69?áÀ°;é+CMüöþ¢pAÁcXxÐÆÁèD4 à9Ax^;×pÃHÈñ\\7C8^ØðPæ;Ø ^'áòbämB7Ö\"VÒ\r!à^0Êß0#õ)Ê¨ÖÀ¡-PÊ6ëJÏ,#æ°ÝnÅ\n-^LíÜ6P¢t¯1¬xÈ	êLH¨\\Â\0Ô:8\$ßâ¸Ï²C¸%ãdfJòÎ?)a±ÛÔ#ªz·ñóâÌ«°z#zb¦ÉÃ@6¢ÄÚ[0ÂB0ê7\rm\"Ö ¡àÃ§IcÕÔÄC8È=! Ø±Ò]À°iXØÔ>î&\r+èÂ@îy­«£rÉ¹ï2V3¬)\rÃ¨tH9Ì#³_ãK¬À;Hh¦(PÞ:ìVü·/]øÎ3Ó8õ	?såãÐ ê9;Ç4·}Va%öQÕ@®ÜÝ0ªÌ,Þ¹Âsxé/NXè\$º.tÁB*Qålo2Y½Wý\n! ÑÕíù¤9>ºÂ~\r«KMVÚ#Ðî92CGc 1¹õÀ}|)·£0Ò~Àù2aI W\"Â	´÷ÆÈ0>bÀÞCÒ#.2X\"@Sl)&Ø6%CÂ£«<RÁ37CÊP\$B\\gÐ¡#aÕÒªC2d 2HiUH~,ÃÃh®(^Å.9\0¸ÃpÉOü<Tgú#ÄØr«^ï²\$D ß\n1ùEF(ø©¢Ä;±r2Eä:âËb±4ØÕ;.	ÅÐá-!\"5ÃDc@Ï\nÇ¤#¨ì®UZ©Õ²VÙ\\+¥xeüK\rb¬pØÍ.¦3¬ð|hÙÇ0&¥KÃ¸yp\n¥UdpÔiÆE³>ÞÙê§N¨0r.Ç\\!3 ÕX«¥\n³VªÝ\\«µ{*V«XdPðÝCr«AýæÚCÈã)K~yºÃqô|D\$OÕYzKe%Þ¾ô0CIqGd\\¦À¦þByL)*Y7^Ôa¥á@\$HWÐBáé#¸0Mí!Ò)4ZJ¢t;DM%´Þû1?ÁÍC§Çr©)Ó2¥ÞA4ïÓÅjý\r\nY¤A	Ò>HK¢Á4|ïÈlÉ5YÑP(æA?*VE6/C@ÀÚÐ§ÞBtô¶pÕ*5u?æõ2ÎHc¼&!;%ÌCjIs3JDÒDãî`a´¸àÕÊd4BKÝ©¨ºÜÑ/«ÂðT/OQñÀ>ö[\$l¡\$ñq°Ð@ÂRÀ¸8öjf\n©Bëd9>PØÎK|ËwÈ:´£9%d Ê!AóAe\"øÒÓ\$¢2H-´\"\nV`¬É»E!SuPIR3âß&'LE?!´5³ROT¶\r\r÷qFMÒÈ\\­vä|Ç;¿\nYº£8L³¹£[àp¾WÐ¸/&õ@ëÿ¤\$1ÕyàA·Ðò\"iL|zÚb îB\n¢49&Êf÷xF\nqï1ó=Û`!´¶EYèâÕ,ël#iI;Æ\nî®~NÍ{ Ù±,æò&ZZ\\5}É'²ó#6Ùõõr¡è\$n	TÃT0¦Ôå8u\r°Ppç|Ïyªæ.'yiã\",È¹ÜÔöF\\ÙÚåávå{'q[ée\"ô/<¨*yÏIo­µ®9½'^íÝ\$[%!¸`ÞµCÆsì5s£³ABº#ð¾8Õ!ö}'éú/¸²½k¦Ý ¡>Ê	¶ÀÒd2fÖKfÖñ¡z]å­1çÚßiGÇ(÷µ:Xî ÖæYæb[v&50Ðåõm¾3WÊçböÙ­f¹\nXâÜ\nö_üì ¡¶¹^fJûêë¼pÜ¶Úkù#óÉ^ÅÌd¸)Ü!®÷N¼Ì!Ï5!¯/ôY9¾Aíc=BÞop#\090+BÝzÏÒt¡0=w%|ÑÄ^Ç)àë	]`F[N³MlóEÊñ¦Í-¦ÃÌaÑ#0)t©(úa7AÆ¨C	°î½ÏO2°Méç9ãHuLXö,ÁVk19fUêÒ5þÏcUd=6#sKL/éýaþÎjaü%3\rÝâÒBõÀ`Áçá¥¯í}¾=¦²'æ7ôX°ÔæSV5?ñÏåßoæ|omò=þVøF*o\"¾Çð'ßËëDOêýðìOnÿhÿ£:\nb0fØmÏV`ÌÔÉÇÂ\$ ÂÌÐ\"'K±I¬Î) ´ ÖN\\(ÆÉpBD¸#úJJ­OD5Ä¨+¢VºèÍÀÈ'`Ä>a2ïÖ\\x'b&äv6ÀÙJÈ·&øy¢þl`7@ò).\"ßÊÊZí&;ÂQ/¨LP®.²\"è]`ê%Òääö\$Ú`Í½a9ÇJ\$ò/b^×*&ñ¬JA®gÐÄ×I¨ûÏ %Ðèæï^eÅ=O·ü%çâ]î\$\riº4	,gt&§Ü9Ïîá¸>.'âÌ\nrf+qG\"åÑN8ÎüÐþÍÆuÇ\nñO	.nç1,÷«²Zî|;ÆNd@Ú:«Ñ°ÂDò¨ãýÑp¸¤w\r\nÖq¥±ªùÏ¿ÍlOCùqBg0XÑ Mqñ÷Ðü\ngj®Ñ©÷¿@vÑï1L\n±ëÐÉ2 ë`ÚåË¾îåÛñN..O÷÷ÐüOë!è¯\"1ÎÆr\"±å#Ã#p®úî¥«#ñR'\$d¥\$±Á²QÃ&Ë®Ì,\r¶×¬Kì½!nLk Ðã!k'R'àð¾®2NÏãL4ªOìþÐ1¶BÒtkÐ¶ÇàC2¨hp£~-Vh¢b#`ÜEÂI¢pç¤+ã+dZÒ¼¥?É,*Ù6Bãö!¥`DfÆ¬n)²®6Ðê]ãôîÒ¸ñEúÕ&:úÔ.`1ÎY%ðóñN^åS@ÌÒÒ.]3åòÏò%*@ªuÎ¬!Ó`å\nO\0hÓ3-|~¯ñþCÓJ×ó³6¯NvÓ ó9£·6ç¦z©:ÂÖÞ\$|>ê<¦@#`Æ<Ãz8fÔã-\0AC;k°ª\nfÍDe\$ËOE%!4Q¿8úAÎí%ÓW&±å?ÒZÆsô³¡nDuöitèåü33þÆe;\rA®ûB³pz´%@ ä_gÖjd÷\0àIc\$@Ü4Q®Q1çDóµÔ 3£Eà×EU@2Ó§oF45;'aFRÄJ?ÝHô3£ô{-nªz´¥<n³EsY]JÎ§Kî£JôgIÔB<ë§7®Æ;.¼6R}IP¾ëØàÒ)TãMbYNÿA2G©ìBÓsOôæàb¸ANÿ=â6MÄ*xi/÷NH\n§x#N³Kõ)Qµ-R|4æ9EPâç;²=@Ã¸²RàJÎ_Ã03UVL-DMb\0ï<#¨2ìïâ&Àâ5uV{Vá\nxf:0'GbÍ<;Åå!Õv&+ ýíém§&È×¥óK&ï[[2ü´ö2r+d¨\rVé\"ÁÂaæø¡\"*C\n8¢z;\"nË£ËL{YSPb(BÂp-\$v&+/\0ª\n p\$¤Y´ê,±mÌ2{L\rbË5KbÃ\në9ì¯cv\$kx(È´NËT­fB0È¶ªÑ&^§¼ïÕèy6ÆËÒHâv8¥¹^#&èBC³fø.:äZ£RØ ®á,;eÐAæI2æ<ÃÐP¢+°¨;s&lò+ÀÞé÷Âl(ý¼{Â6JõmVÆ h6Ám.imv1'ôµo÷oQ}@à7æÍ\r6ïöþ9¥ê^óR#Ì7F®º=\nV¾Tfjdú/_DÄâäg1¥s­ýÍFBª¥ä\rGb-fØGÄÏ\\oÆ<Ðg¦pkÊäÇ¹Ã4²te_\r'Ï«%v¨ÉlãVb6C@Zä6LÅBØ·36dã`y\\;DÔH¢ÖNª¼,! w+HGÄ;b6";
      break;
    case "pt":
      $f = "%ÌÂ(ÃQÄ5Hào9ØjÓ±Ø 2Æ	ÈA\n3Lf)¤äoiÜhXjÁ¤Û\n2H\$RI4* ÈR4îK'¡£,Ôæt2ÊD\0¡Äd3\rFÃqÀæTe6\"åP=Gà£±ôi7ç#IÒn0 Ô¸Å:¡a:LQc	ÎRM7\r2tI7ìÒkÜ&úi§Ø#-ÚMçQ Ã¤ÂHÙ³:e9£©ÀÈa¨l])#c»s+ÃÆ,óqÒïÂXÌ¸¦Æèq9W|Ò=£:IÁE==ÜÎ\n\"&×|q'o§<qTÜk7ÎæèÁÊN9%\"#pÁ0£(@¶\rHè6¨zÎ0£î¹H ¹3O¬@:¥°;\n¾ªZÁ*\n£'¡\0Ô²ìÊRCjÌPå&ÂcÈÇâî®¤é®0Êø¯\n8\r({c!¤#pÒ¶#,Ú9ÏRÒ¸¬CfHa\0Àð3o.<k272 ÒÛÌ#LÆ¹Î)|6M3|p\"¹±ôÊ°.sÓ±²Sð jî³@ã|üÁc¹ÁcÆ²¢/2Å0#Ø;#¢`:Ó°#	C XÐÎÁèD4 à9Ax^;ØtI\rË0\\¹áz2`2á&ìà@»ËZç9à^0ÉH¦£à¼MpÄ)ô21xÕ¬|1Sä£j||±iØ<7­î'\rã²%³Í+£²ÐÜ3 ¡(ÈCÊ âØÆ4ØHxØ:
¯¢ÆÎl¸1ãpÆÎe-@ Õfçk2ÃC¨Ù­¹sµ×SÇÙ#«äÓ¥Å( w³8¸Cf±J\r{½	õ2ÇHy{`²¹²hõª	RX&«d¦áÂZHØÔ­rÆÙB\\¢&L¼îË_hô×P7rÈ`Hóa­¾\"é|ä~\"ô#ªÀ±Í<Ý7rÀV¦4µ¨bí	#lÀ¸¢(ñÛ±Üò8T¬\$r!=/N3lp l;Ö¶»8:^Ïâ\\±?Ãóq\\O¢9Âñ¬ç¸(\rïôÀ,Ã}yÆÆ1oß=ænÈçu°ÀVÖ¿+?8¦ÒÔÃ0f=K\$BÌ²I\"'\\*òbKÊ2!ÕO\0æð k¥ØÈ«3ÃCg/­õ±Õ«((`¥sAâøT6ç,°©¥@ÉA*4ÇÜØ­Ò<UYY³\"Ô¢µ4ªá]+Å|°ÄëH©4\0³pnää¼.Äôd7óÃX&ehp\$É0«4PÛP®¬Ô°£aaÂ®BHP¾Ç#8U¢¶í^«õ°Ö*ÇË,9,ÕþbyÌZ«]Ú\\Ì_;éy©D1vdKCZU\$:â2	HA60H5ÚEKQg8¶¿Ð¨IcÀT7¶0ðrÈÙÍ aô?^¾µPªÒ>Ø²Hy^Gcý æX4#	0DÀN#!¡:G9Úa±\n ( ÇxmÍ(( ¥yD`bKDT6ÄLÂTLÈÕ»\0Òk9Ó!Êqõ0XÙú:d¤÷MõöÝÈQwÄÌõxIÑ¼QD²­³øª¥Pn\n²A-9nÉÍ.ªòcÍÈPéQ%A¾TÀÂF¥ÔPÒRP!9\$o3â:\\#é,õ(#Ò&DÐ:c]±åT¥ºÔSK)K¸!r<QÑDmöòMKh(LnÍke=rÃ¢ôÙ?®¡åToBÁ<)EIYkû]À3£àÜë¢KÔ´ózKIy\n°5 gPI°¬Ç,7>F­H+ì§çðÚµÔ¨Ãup®Ðô\rT\0F\nÕÁ­ºÒåXf±Ö9T\0\r16,ÓQæAYË{g0ð¨P*[Û{Â E	ú§Y¹ïK\$Gì\"ÿ ` GF¢FÇ¸»`L/hpj\r4Å4eØCÃdÔ¯¥>,]ä!0i	)D]àêº}DjVRê»¬K`õC£\rZ*ë98ÁÊ§}Ô:Gý¦V× ò8\nÕ\\S0ñL	9AX³NPAÔ¢7ú®tÂ%ËÕÖz>ü]m9&ù ¨÷¨^TQÅ|A°0ÒÜ`óá\nié&¾'mE*\$²o`o¬,^OXHACfDÀ:<WERñ©9ðâphF.FÃ(wËfa¬çþð´ä9G³T´ºQÚX!w.:ØÇ4ì,rBá@×^eï²WHk ®x2ÎIk%Zö\"±²ÀÛ×JÕsawÛGvò·Áqjy@Å3ENj8@YÏÄtòÎ-ô\n!@ÖË¢	ÖMoFÏAþ!EâÞ)¦~Øè/*luäfÇ¿3ûLæqdvSWJ¬ÏÞ<B¦rQ]À¸Ð~M9NY¥òîFX72®du¢xàÍQç\rUÜ;¹¹\$_tÈèrÏ÷2¶¼Ó§òV|`eS/¼æ:sÖL[=ã²VÖ¤Y'äN©Ãc	áâî¥>c,{ÁG­IúKÈèHQÝNið¢?Ü¼Un#ÁÜ2,Üî»mÍñÄõ×t§4XLl@Nåñ/ÃRi?¤=£9ÍîØÇS§[ÞçÌÈ²ñ¨½ÓÊy¦%ßÃ9{Ì;Oà½\0t±éì¤Ú¿>|5ôÀ¾Î0t}<ë\rïÏDÝ3å²ò\\ÐI«Iâo¢NIkÀån&qÎÂdï¢*ê#¯ôïÂâÊfº3\nÖ-tA\"núï4L'×,*Ð¦DD\"NÒnâûì^É\$DÇozÇÌoÌd3b2J§(ÉNÌsï¦ÈXúÏ'HÓðBúH§LD&^û°rxÄCpfø\rmìôëÎ.ìn¬6îXç¯æ,²%k	NíçnZyCc\n, Í«Ê(ï ÷ÐS£PÙ'Pd_o§Í^-\rP{)d9fF/r#íz##ð?)n3CºnDlbn6Dª\råfHpÎ/ÂLCâÃMh#ö,NÍ°ðÆ-ÏWÌ#%Ô´qÙq»ðÝâo¨·püØL=Â¯ìÀ&J\nÎ<,p\rÂ%1p_Ìy§äwpÚß @_£=\rð÷¢÷ðs¢bøÙ)^!Q-#ÀD»æZ\"fo\"lHèuÐNEàÎdÿ¨:bmñý¤-mëÑvûÞjã­òûg0f#Æ)P Ç\"Ã\ré FÈêîiR\"R -íäîh\$0M'\0ÈN&°aTeÂ9ï¥²D²I\$ÂÇÒT\\òG\$±G\ro±%Q¶°²g%Ñ¾wòu%h#\n\n% ²#3O\"Ã)í°\rcJDcÈýä¼B«ÀÚ2ª62®zÆ2¸Ú\r³+éÌÚÒ¸2Æ#ÏÛ-¶.­»(Ò\n\nÆ#Ñû¨/2øs/èÜÀhâ=0@ÎÛ¢¾­1@ªßåÜsQ ³¡åLm/å21)@	\n\r\r\rðþGê®-È±è8¦:Éè~!OÙ¸Òæ\"*Õ61  k¥ØF;f0\"ôþcc¥R3P¸ÔN@[lÔèÀ< Øk(\r&· ÊD\0Ä[B#<Ä#¤öàÂ¦ºEfüyË0j\\¾\0¨ÀZ(g#bn	Î^ºÉï\nP²åçÔºð¢Bø'ä¬j°¬0ÅFûLù!ò÷LøÿoÇ)o3s-;K\\r©/c<J0:%-\"¸å²uDsê<à &hHGáF~&ÌXæçÎQàaÔDf\neÎÊ\0QGc*hQãcG(.bXødæÇìþ5ÃcòÂ9häâêx-C43&}ô4ä\rJ ÞRSµ+9Q0­Êt§Ü]¬w4ÃÏÊÁÐ4Æ'[NËbNñ\$,§Z#ª!FÖÃ8ø3üüÆªDÛL­\\Ëèr%/Ä1Ä<C/IB£ÄÃÀÄPaåÞÃðT@î.Û&R%Í¾¢+OEÁ\0004´M³q(SI\0";
      break;
    case "pt-br":
      $f = "%ÌÂ(ÃQÄ5Hào9ØjÓ±Ø 2Æ	ÈA\nN¦±¼\\\n*M¦q¢ma¨Ol(É 9H¤£äm4\r3x\\4Js!IÈ3@nB³3ÐË'Ìh5\rÇQXÞcaªchÇ>«#ðQØÂz4ÁFó¤Îi7Mjb©lµLQc	ÎNE2Pc ¢I¸ç>4°1¦ªe¶·ú©Ê!',Î¢A+O_cfÍçkNC\rZÖhÏbL[I9OvÍqÅ¸Þn¡±ØÆDé,¹\\Ã(¾ÇµGM7k]3c/_4IÈå`¢©Ï&U7ÍÆó¹º094ÃN\"7¬Sî¦í³`: Ò9A9ëàÈà@35ÐÐêËªV7º¦«2Æk(êRRbÎ³é:]\r©ò õ@®j\\9.ÓÈô ¢\0¯«Ð¤2(#¯Ú¾È\" Ò¶òhÌÀ(h7#ë\$/.Ó<¶H\"|¦ìÓë12K´µ PÄ@Â ¢+¡ 3B`Þ¿¿Éê~#*° \0ÜD|ýÁáÁÆ·¢/@@0Ä(Å<ê\n\\:Ó(t'\nC X\0ÐÑÁèD4 à9Ax^;Øt÷G'Árø3èÈ_	\"5`ÈJ8|´NðÜ3/ZçxÂ8¯`Æ¼°(óy>Bj ò:³ÉT²6*Ô\0@¢HMçzÝ­-áy	Ãz%´\"ô+££t£ @1*°áølcxØ:°)D®½c1Ë¡2/L¨ÆÑdC¢ Õb¡kÓ×·¥îÁ²tÔÚ\\n{&#cObô`Ó¡h( Ów&0I 6,ì'Õ¨Ë'&äí²ÞèIÂbh ÉÉJV¦Îê¬<eT^}hb.ë¸nèæ1¥Ãäì!B¢&LSÂ>Î[& Þó©;ÄmCtqâ<ÛjO*rc+ ¼ÖsñÜd=]ZH4bÿ#m0É£Ç^Írèâ=º1Ps¼ß	q\\\r°üXÚMéx¿Ée?\0#Í»o¤þ@æc6ºýx¾|xlÀÇ=LóÚ|hÿ&Àî¿L5áÄèÈçr|ªÆW£4ÔµhëºÃ0f\r\0«c2Jy/%å`ê¦Í°sÌÔ5EÕyä3Äø¢U0RqC	7<Ð ÒÞdMá'\n´üu°GúQ9*öbeV&±Z+ep®â¾XÝa(Ø:É\rÀ¼·Á>¦ôô=åÂDL)Se=^ÐÚ#ÐYÔ@~Ë³)¡4Æ¯UT³P±f«eUº¹Wjõ_¬Ö0rY)øÄ´­Ñ½çÒC¡.aÌ©âÖwJ\n_Pê<èSt_\rò 2ñ¥Êk×qÔ5¡Ô\"oAÝSQ\\*Vå%7G,80hì d^>%K&Mûv%Gu®¤Ó Gcä æp4#9üÎyNP\"S­@\$ëç0((à¤TfdC*Pw²£ÃÑæà'PmXr6çJ)öäa'>\n\$4¥¶ðÛÈQÄÆÃQ.V©ÁHH·­i¢µ¨èR<sÃCÄ/Êâ`I¨Pü\n¾OàÂFZÔlPÑÒLñ:*a3VHl#¡DéÍuÎwI2&ºä¼¡BO¥Ó:ÐÒ\$¤zG'ÇDy5H¡ ²FdÙÀ6æ©T6il;óDÿ6¨\$ÉÁBA\0Â¢¡:éä¿4öB:@\rÉ4©r#æ%³â>ÿQRSõÑåJ¸sÐgiÔb·§ø[qºjîÕËJßb ëÜ\0'qØ;Õyä¬wDU1ZEQ1¥%22VbÜ¢äá	á8P T­^ò@-ê ÉIPæ6ÇX\n9O3¬1E&+f%¹«+TYg«1fLÉªé`JAMU¼%uú£\r!á%&fû\\ézQÂJP Ä.;Ã`ÞTý«<=Ý\";v®-<bË.A_µqÁÁ¦AvëG\$TÏ;ÇîvB±>_\$(£§ÒoR)ePMó;'qGJ\nFbMr¶b/,ÁäíÔ)hkè°ØþnIÌ¨¦vh\nLQxÉ@ôösî®¿\"\\2s¨x[®zf3hzÑl2rÊg(1NäaÛ6TâûqDxê}RÖñ =K¸À§#ªðN¨ÆÀ!ÝsºçW`n»Ë^è\0ÖA\\¸eÒåº\\0ÖJèg±È´luÒ'À((æÎ¢`©Ùç\$¶MêJ\rçEüR*tÚ]ì&öH¢Â T!\$¤_PSþ®ÊYLQxQ¶	z\$(ÊË4LÓ16ÄÓ9¦Ù?Y2§ DeUüHÑñ@mÀ%ìñÜº9×7òSJ¦/)OÚqÜÎßR@0ÜÄv)ùq\$_´Þd¨\0å1Ëô~8ÍN¹·s]3tèÊmúÊmÇõÞB [4ÁÜÉ)Pu		Md·0ëÛGÜIÜ®>ÒÒôüRR7I6ÅÊqD<pÊ¼ìÇ¸²tIvá]¼øÆ¶þyWUÚ#ÁÍ)6>ÂBgÁÅõæ2iàáÖ¡Æ'ÙîlÁÞ/O&Óf¯í¸¥L÷FôéÀ2å¥}«äÏ\rlc¾f4¨AÔÆ{`E£ßKêg­CçÛúzï|ÒÜ(õùOG	ÜVTf²CT4FÝ	@äÒOQ¦º:f\rvAï2ð,bïÔ¾¯ý\0æ/Oâ/.	ãnÔ­\\AHn%OÀqNvcÂ¿mZÁLöcDD2À:ëJ/OÔQlTÇ¬rÅÌ`,düLpqä¼üÃD?drÆpQ,ZûæN#aPe.H\n<ö§zså@üÐ\\T÷ð4÷P{	°sÐ ûãü/|×¢T2`¤Ýc\0åîægÀæ¯SîP÷®WíØèn½Hø#nç/z|¥^Ø4Øp£°^ÅQ\r~»M¨Éí|©	xýì@0)\\}Mb?ÇF*­jO\"*-£Àðdrbj-f>UäðDD!a[b0Oéuî1¬DÖö9¢ãnh'Â2\\k/Ãº*dÎ3)ý°ô»q^OºµäöÂC\"¢ÇÁMv»æ8H/¤\nÍ@30q±¾øPý	ÅÌ§0}\rè¸§	ÍÞï}ÑçÐ\n1Ù	¥Õ­P:ÜQ&@sû Ã(fz@&0¥!LÎnR#Ì6ÄÖpë=±Ñr;\r±Âü2E\rÃï·B<b¡mT^Ì,ä&ò\\£1!f¬¦R/D­g.Ïä¿g/¦úDÔ\$BÒ\rÑþÅ2LaU)ËüÐ¦üR§)²\$°1rX®ò*±'gm,R©*Äö²PÌ±îúf?%O|Öåì(B,Í°cë¬ØÃYÃ#\$HÿBzBòúÙÒþ6óy±àÍ1[1@Ë0FºÒó\r.ÍØÏ~\n¢Úr¡ã3ý3Ò½C3¥!+Ñ.²Ã4M´3M¬×s\\ÚbP4ó ­à÷íìN\"ó3òà\$ïk7dµ8ÊLJ5P|÷À1 ÐRPc§²í¶Rj3F)'¬ûåêQÎTâÊ&#bùÓ»02f¨\\¦aÑreC®0pxFlÛ	PÓ;æ(l,÷\$-°èÇéü<Øk\r&¤ ÞEª!RCO\0Ë£¬ ZjO!çÐxªü1jL\n ¨ÀZbf#\"ØNÎLûï^íéo<x`Ì]?ëN3b!\"\$.2ìjgÊØülìH2¢ ó:\0¢&£Ü>øÇþÜ#ò:£%#4xß Fðs#}AëªPKxZô:ìÆÐÔJ=  &%H!Eè&:åÌÌ>R,m 6ñF3ÌF8oJ)Tðüm¢Ú&¤Ø1¨d|ä³9ÈÆàÔïHCOBÚû½OíDn£oQ&?ÀÞ(\r50Ôò9±Ôÿ\0Cô·\"ÔòÂOÞÔPJÄLVLóÐ:Õs¤\$hBl@ì4TBÊS@êDF¯T,Ü2l¢/±­6cì3DB#53ú2eÔÃ\"´åÙ'l.Ûf2Ãàî/¬ü²Ç-Mª±7Y<¤ó<ï´H(9< ";
      break;
    case "ro":
      $f = "%ÌÂ(uM¢Ôé0ÕÆãr1DcK!2i2¦Èa	!;HEÀ¢4v?!\r¦Á¦a2M'1\0´@%9ådætË¤!ºeÑâÒ±`(`1ÆQ°Üp9Î¦ã¡4Á\r&s©ÈQÁFsy°o9ZÍ&ã\rÙ7FÔhÉ&2l´ØAÎÇH:LFSaVE2l¸H°(n9ÈL¹ÄÄÎf;ÌÓ+,á¦o^NÆ© :n§N,èhð2YYYNû)ÒXyú3ÔXA´ÍöKÙ×¬eÌäNZ>³¡ÕAõãó#\r¦÷¡ñôyÛ³qÈLYN[àQ2lÁBz2B¨Ü5ÃxîÀ¤#ðS\$0´!\0È7·ðJÇð¤ æ;¯\"V#.£xæ­ÆÃ/qpä6¢ÎÂñ²¡ ²´JÒ DêR`*	Øèë0ãPÂ ñ¢.B,Ô´²»?JD¼ÂÉ229#õ\nHàÁ/q¸),ÄÛÈ#£xÚ2h2¡²ãJ£`ÒÂ¸+ÊÃ#£jÂ\$-4ç.ÏäþÅ/\0P¡®£!03Å@üêÎxÏ¤ÃBÃ*ÈÎ)ÊöÂÐÂy\r <9)\n9ÅoªBL*;³CCe\\¡H9`@#CCÆ3¡Ð:æáxïoÉÂuTArð3õP_×XÈJ\0|6¯	2ö3/	ªíxÂ9¬ Ë1:dÁ#½*:E1-´°7³(â*ô¼cÆ=UOÉâ#N&å2cbF/íX¥¤ÐK¼Òñ¸Ü½è(J2~:9k,ãH!Å#ÜÎÏüåÆãHë¼hôÆ(täÅ3¢ Ê3j#b;/£¬)àX#¢#,c{#;ÇQ¼yú,Tºò±°ë?¸°í[ÆÕÆ´üêÆ3¦Í¤c®Ñ³-	²\r#\"1G-^°\"`@8ëÃòo#ºn£\r8¬Á°´cuVÑP´°ø½ª	ÚIËxu*Ó=õôtÞ¼&|\$µ¾£Ç¦`¯O&ÊJ|4v SBÊìO@ &Cªg7ÆLS\0ó~ßé]¡yhr8¶< |OÉâ1H	@²LùÂ:/RÈùÚªEgl9³üQÐ#Z@(ØHÍC0fOÊ¤ÌGÛhT\ræáT\\h¨cWaÌ3Ø¨zÎN°¸0ª4c\0¢ã!Á0RPÙè \$`¤3O1+\nÑÅðS30\nµqvé)@-&®µVºÙ[kuouÂ«\"æ?k¤7ô._Üu@ú;¦`ÿ¢V&&4|¯ÊpKF)g àÎA°v:ÑfL*%a5\n¥»Q*:zIhðçÑVh[in-åÀ¸zå\\ñÑtyãÒòyÀÊ§2Èÿ 2NØ×² Tí	°\nËÑÊu¡Å`æuâ¥/¼²Å'¸BSi;`b9©IWNlÐ0i<c¢¼¤¸C±9ÀîEÃÀÆCI}¦AE-É(Æ,ÚÐÄo³/e¤ÜA{5wa\0Ñr©*úc1`ÒnÐ4yÐYõräPT¥QL	Få  K¥!Â5BÉ3jæh©§w¸xâQ<#AÍX`N:ö\rÄ5,©BÎC¢&E­kÎyô¡%a) p|È0)Y%2lN¤ØÍiÔÔQ\$DRÈ&5©è¡æ_[É1_?	M'Qx1&M\r,ÑZÝ1NA<<nr´Bâä±«æ1Ãt*qÎJ!7Òl£ÔLBO\naR¶,áãÁP¼¸ÚZIi!¬õT¬NzE)¤é^Á`l5¼+4xç	)GôÝÞpá¹8Wô>G72RQÉ»#A*gâSÍCñ(¤kNIÃ)\\¬ýî\"ûP!Ú§ÌôíªE,Òôc©}aÔ~ómjÅ8²ÐUK>I´Ò]dêâYðLpn\0¸ÆªP	¡ëX&i.cöZ\\rG±ÙD0Ã¹xÅ¶¸%EÉ4Ñâ)ÂB,YHiÄ± `Ë®SPÒf[K)ýHÄi3beVi&Iä4¶\\Åî ©¨<  (ìPI/iÔÅ^lý¢ûÍAÕ1½VËF\r&I(60ÑlCPï²Cî~I	üT\$àðÆE=+z°Ic8¦ï±{;WIµ8^KêÕ¦ì2tÖmû,Ä*iÂìgÌ8x-¬ùØ±ísgÊÓ,%¶k]nÃ8b§kUZÔq¡év@¾Ó:Þàc\rdÛäÄ½¼CZP`tø7å Üw>3:ÕÌ3á8aíkBßÊãöu23¬Ò¥½ªmEIÀQC&ÓJdm²¶rsóE%,¾_f-Í6OÔâ\r¨C	mÎ\nR,ê¶CôÈ#´Ñ2hÃf5ÈVÏ>6hÁN56	3i`[ÑKgÁEÎÂâ ÀÛtYÌë¬2sDL·j\0¸HzðxìG±þ®j»2\\Â[ÍÚc&Áéán¿»¡+g]3¸÷3Û|_]¼Ãö½ÕÙî^!PWB¹d©}Ã°Ù!bdcN M@©çëÂmÔ!:0%\nì>EMîTKÑîscÞ(vkoòØ §À¡îÁ»Z±Fÿ3£GQËÅgå´s:f+®e8.¶´*EEYhL°åí½HnüÌ*þ§Ï`PMDíÇùèÃâ|ì)Þ\$.ÖM&0Âì¹\0ÆC6ýnÐÂoNð\$Ð&Ã`êÃ§zæDdF|J­ÂÎKÒÃÂöºbúj¤Lâ.M\"§Òl\\ÍôÃH©fg©gÅ&LæÄã<Û\r¤/\$rj0|îöíNüOb/m¢Å\r/c@FD>§\nRÄÊDRìÂÍïØýÏàÉ%4¹Ð¾Ë @<cÍ{ÎSj²>0ÛÅ0Ñ	&=\0§¶v¥\r¶FïàÖç¸&pÿ1\r±	ÐNú¨ TÃ*: O*î ããÀa®Æîî¨ìñí0ö× °Ñï¢Í®Aìên²5Ì¯QSÆÀª~ÂBhcÓ1ýêÞãõQ	Eï÷\rÀñðéì@¦¦I¨--º!*Ü:ÄÂ`@Â¡>JÌäP¼CÇ©¬;â¢1g^Q¦5ÀÜ¡F:Ã°êÚ\"JÃëÒU*|±àÑq~À¯\r\rÜ4­d*;MÂÚ¬nà-Ó\0ÞhC:Mi=0%\r²\$bò(pÑxý£GÏ÷# ÊÙF4jå1~w¬b²4ÙfÚåETp¢ÉÑ#ÏÞ2îW&BÑeð'&Xc2ipmåF7ã¿'.ZzªGRNâ(ª¨, ÌaDH÷fn#<Òä àø^t¦ÚãÌÜä\$Á°Ã#ïá-W-r,ïù.7.ROï÷!I°ä¢ÐaÒ=1U.çm%CÒ¢(»®a2Òû	\"H}bÛBògÁcBnV,àÂAw-J2ó33a73³?Ì4s0`sM5ApÛ/Ó4\\UO¼s=1sk5³576\"ÓvL³!4·&óx8Ì6àë),&àdÍ90D6æ}\rä¤(åTBÐVTÄLÀ\rs°'';búLs¿:ÓÄ6ÈÖkYd@I<väNâòà¤8Òl k-l3(q\"è¥>óÿ'Ñ3ï@Þ¯ÅA6L§z¢,\nO¥P4-³\n´0æ72é).¦å »U`B¸ÔÒí-C:\nN®°ö<@Mô\\mgS±JËuFhðêULºhnU#¢dªÐ2\"HfdïHY'².:ÎÈï2ÔJÔGHÑ\\ï#^gäê\rVµ¦=<O~7!êTÂÈ\$E~|H\$r\r§Ì)&©à@\n ¨ÀZ;I0ïfä°&h­pñî­\r5äUî, !\"¾£ÊÉ0d°v'>M0GÀ<4n0IMÉ´ÊÂ9DPh<¶ô4\$¨8\"\0A2ñ8AvÒ%ê¹ÌX&`RHzM \$dª&dî¥ÐÓ\$u1FG ¦´|èO»4ðÙÑ¥[\"O)
®±5ÄÃé(d5ÃSË²\n2¢x6m<£ÜØS¿ZÄsrY\$f2ºF~j*6Ç<L¢fJµ)§a¹aå	Ðr#âÂD0tùJÜQ&KåÎH`	àá_f0:'òN¢êÒÀ/fÒÒ\$Fíß1ÓrÇéGÜkGVÌ°½ghÖ|@ÞÀ@î/,Ó2ðe4ÀË7rÐmBþ3Ô\"I[f2@	\0t	 @¦\n`";
      break;
    case "ru":
      $f = "%ÌÂ) h-D\rAhÐX4móEÑFxAfÑ@C#mÃE¡#«i{ a2ÊfAÕÔZHÐ^GWqõ¢h.ahêÞhµh¢)-I¥ÓhyL®%0q )Ì9h(§HôR»DÖèLÆÑDÌâè)¬ CÈf4ãÌ%GÃfÕ\nbÖ¬Á÷{ÜR\r%¡mú5!s,kP¨tv_¥h¡nø]ò#ªÉPÖ'[ß\$´ÅôÖ!&c¢ÒhìÚK'FA¡IE\$e6jl°läÑ¬Ý2\"²º\\í©mËK×V7Å¥s6õýÕÐP¢h¾NC¢h@©ª®zP<£¸¨lì:\nË,¸c¶;ðjA0ÍÀÈÑpï9m³#)©Ä¥ï~ZÄc(º1^ªåÓ¤0é7Ï8ÉÅª«ÀG£H©µEÒ ´*8õC«`Ù*­c¯	µ±ü.ùÄ.£®ð80´	ôÏ9\"\\ÇÒ«ZöÅHÚû8M²ð\"ò¼?>jRÊ´ñvÈºåkÂôæKòL´îÂd¹ Ä£ÛEQc* \$|zÎ2ÑqR¸Î*JC²êÄ<hñªþä|â¨5úÕËJ~Ío\"Ø¡Ï(ãÝS·ÏZ9ÔªÙ#A	»Ê ÄÅY*Wzi8ÏøË(vI>ãÎ6\r.º¨ÔÂ×¨í¶OOJ/=N9w#Ð4ò·# Ú4Ã(äÙÙÓB5'ókÆÖ¢«ÚD_£EÓRÕÞÍs/C\rS,[ÙÓÌG\0x0@ä2ÁèD4 à9Ax^;èpÃb\\7C8^2Áxà0cï¦xD²ÙMÌF*+ª~£%Hª ¼2~\\xÂ-|%H&ÞâËRöÅ©Dà®é	p¤v,Q×z¸Ö³å9Ã@°Ëõ¯7&I¦K÷.üQiñP¾/UÁ(É®	0×v\nKõÙõú÷b¾=ÌÕÈ-OzhKbÊ?I¥ïx­ÝNÅp¨&RÓ3yRRóFr¼¤húqÌ·\$J+ðdÉ)#å;¥R\$Iz{µ>u¡æ£õYfãþjÝÕÐ[¡9æ¼±ÒÅJ(ñn¶d<JK\\¢ùJ\$P+ël­\rÊR6¦øÑ`­ÒQ[01æò#Yd+çµ]\"ãpvØAL(ÀZu\\IÄ\"ÄÂzHDZK'È)9Uä[Aæ©/ÆJ8pLßDæøhMð¼st¨rÍmö4½jSi¿LKæ-7g\r3èY ¦vü 0­IÙ\0IE.^×l\$@)ÖæyYed¨UmBædZâß&L¸ÐrÉlm­¼³9BBAÈN(ÂUâÈÎô·-#@XôdÑ?7'zKåpsÏ¤Q'aU T©0OÃQèÔë	¬Ã`tIÒÈeôÑ!6âU×é\$F®e+½bJ©ÅpQ2)®q1%ç²)ÈhÒÙõ)Ò¥É4;ä-é¸¦Xí\nÙó Aà·gdÅnO3ÄCg¼YK0ûÀ\$Y9Mø. E²TÓé>¡DÁô#öVYÌ*ÒÿQtFgp¼t·OZ@qçÄt¤kæ~Òz\0,)Tv8´µöÓÖLgé¡zÑ\nuDÌÜ3¨%u;?a®µYEf¡Ç0Ð8<eÌ0ÖÄC1ID\\G`68±pâ¡sÚßHË3f¬Ý³¶zÏÚCh­¿´¦Ó/¼7èm#Zk©ÝKGõjº]dµ?ðB:´øgNr­«Q!Dr\n ºf5ªÇ>J^¦!øCÒå@ºÞZ	Ü±]d¦ø ´S}¸4ªÉ3fpÎã>h\r	¢vÒ,Ki­=¨Pðsµ\rE­6ºoL´/'å:«k<ù£öCÒX-VYrE&úA4¢ED|s)ðÿ©ºæØá_±ÆAöÏÄMÑCÔÔËM\nmÂ;53Ù \"É¿âíEÊú)duë-YÁªv!Ë*ntbdÆÆ«|¥59Å|%DKAÓ<v\r°¨´Diã\"@,xzÎ|ñ¤-{*(§31Nä=D×l#UJ\0CñÑÄoÇÉýAdÂÁ2½dú¤1ÐèæM¸m1H¿\"â?(êÀ¦D°ùÆ%É^zaKµ@Î;¬¼S¨8ÆM4Õ©Ýù¬·\"ª2¢%`%E|ËhêAl #ÔñÏZW-b9bf\0!0¤m%KGÆ¨ËÈsvA]lÕ¼f\$T9ÄÙm]¿ºÁ®å½m	â¬Â9±&}d.ý[©²ùBØã}Ë´-c¶Fß?Uy¦4©>rZnã2®\\«Yõ>ìmÐÒÓÃ~Á~i6º¡ÔãAÔ.Ö½¹¢jÆdo°%sðH (ð¦!ú÷5E»¶3UâFå¤ü¥PQTç|r_%2M HµcÀ­ k#ó8­õ3&¡l5ëñåcÆLnSÂÆ¤Äè[Ëè]QM¯¶_!ðÑ%EÀÁ0@ PU'Uh};¢ zq;ø,ËfAnÉ¢P<ÍÆzÖlU1qzN¦Õ§¹qïÆ1í»<®aV}Ü~Îcò=	^çTdoPcË×,	´¯µuÛbø­)vØFt¼#HíMZQùz¬¯¯â QúdÙÇr\rÀS?XNQÍà¹\"¢õé\\íyÜ9Î§Î,¿GÄÿ>Ä&Gé¿;¥ýd&öè¬ýdÆ®)¬o\"FÔ·dw¯®Ug(W(D£:ÂÄ_c(¹©h	nÈ 	\0 Ú)Ê¬ÎGiÞ,©¸V®|·Æò@tåþkÊfF¿´Ó¯<ÁÈLq¯FÅ?g20j+Hìi nGÚG\$ûèäüD{Â\"\"DRÌ|n j\$(à`Æfëâ\r\0Þ2Í.EíØüâì¦Ðº¨Gãæ¿DÐ®Æzð7ÇOoSïJUpörðúÏ¢-P\\B^uhKÇ\"p°zq\n\$Õ±G\"ÁçÐqå¸Åªñ Q\$TÏÓûfòÅÞo2çþ\$äj±c~×ñ\\[ÂzpbÒyBÚp3ÑDwD^k>g÷@ï@@,\"ìg>ÒÇ°\$ÿ\"ÛiNáJ¨:*ç±Pôp,8Enô~)ì'ìÿí|P¥TÈ°É&-8MhkâFsÍFÀ\n¨ 	~â¤\$9hâÑdÍ¼ÍïÄbNÒivÊ¦¼o)r, ÊGhãvúV\" wF¶I\$:[b\r\n®c®:ü¢¢éz¸pë\$t.j â«~,Q\"²VòRZ*^Í.®Òw2lÁq'2w o|*R&>L©â§V£²)-cjdÁhd­HÍû*'2j#²y,IÁ,ÆñN*-,ã­)ìÒÆG2ª¬lpl#Û.2-è=Ì].ìbõ-òú}ÖÂ9òBV2N#°¬!¢8ùnôRCÒòòS'\n¦ý2Í -â+óC²ò\"¹\"SRf?ó8ü¯R.øRiÎI\"dGñ^ÆÈrÎ÷4¡v_çqhõ¤QR\"xìzÄ«\"²ö\$ë§²Y&\0ï¤PlL.ÎCñðq:\"á#3çD9fø¨ïæJBÑ'rnôêfqÄ·<Ò°\"á(,+ãÕ<q+ó_¢:§4®Óò.E(ü,bªãu@=RbÆ3ÿç1Õ@ªi°\0öOr}#èÎExHGAMÇ¼Þø;\r>|×bWJOÀ(D0%Ê3! 7¦ôÁ4rF\$Ït±,÷D.DñæZ¯]æ\\êäoÑ-Q) &LK@Lø1'ÈN`Þ\rê\r Ü £Z(\\IgIOÚoDÀdÉòÃ³õ@ôqBeèKñ©Nt0=RöUoGG==ÞýOà©Té@ÈCuò®ÕéV)¿rBaB6)&.oå?ÕÿôCéÎCµ3\rÊíB\$uBpI.æ@á#AóVE%4Cg\"c.SÏ>Õ'Nµ-KH]'5gRê*õm7-gëõy*Ò(,1C	éUÈ\"VÅYjíY±ÃZaW²þ:È;U\"u¯*rñ10ò¬¯Lô|ÓNÈ_%Ï(G2øèäÏuëXr(ó1øóæõëQuB(¬ìeíBÃ.P/|F\$ÑcD¼gýôä`åV¿ðñ\"îÄg¶é2Ö5(ÃH»Rlt¾+R&Ä!òÂðèüÑ4â6Y¥15ÈTÔC¡`lrqîÏ,÷fV\$E¡d3dÈÍDô+A¨Oè¹ëïÒ·1êõÃ<ÞU¨µ±]µµ±\0qòõ­^ÖÇm0ÿl3û>Owl³\rCV]ÕAmhtÖÙn/×mÑ0ÓÕn!|Âª\\©\rY×jõ³qqbÃ¥v5ßaO×r­(s1CWpvûQQwBr	`îSL Çëbf×X#§UÑc³ý'¯ªJñAT¤ml+YÇO\\UÒ¦>T;xsYäx´!]ÎÒxUwBõisNuC¨¡30Ì~c°W·\$\\tëèÇtp¢~T}W+T3@7©}pQä/©0/-\"UMcExAj¤áPõhQ:O+cÁE'S·q7\nfsøU=eo­Å ØXÂsîzÆµ<8Ä\rÔ³BU|v°%.Æx3\rO@#~6ý	E­Pã\rxo³D¥ynV÷TrÄ?#Wéz1ç}Ucxe\"X!z6Ô¤\"VUµQ_ôýwµWæZxÂaEQ5tñ¸ä-£)_öÏñrXñw6÷7\$Ãk1É~G-y\\û}Wph¥Ñxa7Ù2¯8M¸ù]AÙ7+±Q9éBÐ²ß<`=¸¿x5Â÷ê¥Cé9	R2³Xï§C)ò]ZWÌ·rq7u*ª!ëKj/5Õfý¥÷¦ÂÄ@-]DPñFö9Sv-¨(uÓÍ)ô^u¹lôZR=­xÇ,*õ©y«¶ÑõËWã|;sÆ	!@@Øa 1:WÒZlbªëEzÌÀÌ8ñ¶Í Ói8×eÓÄëC[L@. ª\n ¢©\nñ0ì&8cÒ<ª\n.\\ZdÅH±Ó\"[bI':xñ¦º\$¦ðØÜ\\Å3;Gf8PIDqÈ¤@í¬±ù±EÏ7\rÓ©3ÃÙH.Bw*©FlMv¸¶òHòFz\$.Â/Yd/ÄaÍ2¢Fúm¼*t÷vu2B\$fòU÷}@v«Ø(Á×ð·ùd6à¥OAv§E7Ã+;VN4¢ÑÛ>;BYÏ [Jþ´)@[T'ÉÝ+K{c«HãCÉxò{O@;tOÃ7êu2ñj@aµepóWVÅnÐ|	¬ÆDP8èêx8«0.bé\rR/¬ÇÚèâ¨ðÂ«f\$@F«äþ-Xû#ï±Ó%]ä:S4c;µ-¥YÇnxÜ{&ê´q¼V%ÂNÄk;+~P¦ê]ûZÞÚÔ&¥í±n¼ÏPÈpÂ­gg¥	¬äOã}\"eÁXDËxgxc¤=4z®Z\0öT÷¾ET0|ÊA";
      break;
    case "sk":
      $f = "%ÌÂ(¦Ã]ç(!@n2\ræC	ÈÒl7ÃÌ&¥¦Á¤ÚÃP\rÐèØÞl2¥±¾5Îqø\$\"r:\rFQ\0æBÁá0¸yË%9´90cA¨Øn8ÆyèÂj)AèÉBÍ&sLÊR\nb¯M&}èa1fæ³Ì«k01ðQZ0Å_bÔ·Õò  _0qN¡:Q\rö¹AÚ n4Ñ%b	®¤a6OR¦¡5#7ü\n\n*ãò8Î	¿!Ö\"F¸ëo;G³A#vÚ8.D8íÜ1û*­àÍÉÌÂ\n-L6la+æy5ãO&(î3:=.Ï@1ØÂx¶¡È\$2\"J\r(æ\$\"ä<ãjhý£B¡«z=	ÈÜ1º\rHÖ¢jJ|¦)ãJ¢©©	F<ð»Þ\"%\n<9Ã\n\n)¨ûæ1 Pº¥à)µ,`2ãhÊ:3. óº-\nn9fRÈà< ÃÊ£3\r¨4B@P 7²ù[0¦Åð\$BÈÀÁe\nÍ;\"Ã@ØnCÜ\n£EÌëXÓEQR# ÚºÄ*lÇRØVÆãR\"(ô¢C,Q\nÇÁ`@!ÈàÊ3¡Ð:æáxïcËå>Ar3ó ^8HÃîxD£ÌPÂ1¨Ì³í«Îã|ÓTzéUÞ-\rïãæä<²àê×!\ná-5	â\$4&ëåÿM'ã« ¢NÖ÷È« 0µxJ2Î:8Þ;997.:Ðà2tÔ	rÊb:J4àêÝLV93Ù½¥Éí\n\"cpÞ¿¿CÞÆÃ¨Ü5¶²h3è;h½¿X!|¾:@PÖ2©Ên¿7¤¸Ô='Jà¸	0Ò:Pyk!Í\n{t;S¦¹\rfB'©Õ40[\n\"eLóâcÒâ:%È³:¼ã:uk\0¶á³¸õQÐ\$áÝ¼Âä}QR6á¢etCÛ6	í!BÓSOx6Â;À<x¯×]Í:xS\rÛ½ãµÝTl*0AÚ*5¸Ãnh\n¤Ö\rÍ`ä7EÔ×à7Ã2æ:èP}\\?*MjPdÚ6£ Éº#Caèòvj\n\$&ýÊJÃjl\rq Ì\$!<2)B£Lb<æÄòDúKxk¡Ìè2¥¡0sSA[&BTKñ@\\bÇ[; ¸Â¡[.`°C@í\r2QpÃÔÊ.¢hM(èÄRÈ¢Ji:(E&¿!5ê&-Ã¨!¡ú#g2ÄhÐÐtp:ÃµM*#1]°Éaâ5¤:#\$ò/5 N¬´BU²¸B^+å°\"ÆY)P,Õ´CX/Áà¶òl¼ÜWìF Ã¤¸5,£_-gØm#ª#=Ê\nÔ=:v=aÑ¥³¦¢I2.\n(¼Ì=`¹ïJ5v¯UúÁXkcu§¥jÎK@10^ßÈ\rËin<0àLR²ý- ÈPJ2¶0äåM².FIZ\\Ra¾c:!Øâ{pÌ½±ÐòÀ ;FLÄ ¸\"4Þ²*bf²óÊÒôÐS\$ÈVC0t	Zeª~,Î¬·Ðèi:¢è¥>HKÌ2ð.Ø¦ÂtBrÍTÒ#e\"(Td-!z)ü<ÕXp@\n\n0)C\$X8 ªÔ±&²Ì*rBÍÒ®èLúÚ*Láth\$f.Õj]MG&63Jq9P¹DBih2*ò:­q&dZaIÎ¤§8º§AÐUfA¬\0ÂFãîQrvlMB|9 FÖÑzöÉ!&%ª#à3E¹Õ¡yØÀ½_%µÍT=S¸k/dÜG¦o\rÎ1l¹&NY±²r|,É\\z!ð¨ÁBÔâZf.(S\0P	áL*/@Á\rÇßábþk B¼7¤öPQSòïÐ0ÝwÛZüOtR\ndûP\0Sr\$<÷¶0í(¾Kf¨ÛRCz,(!*I+'¸DæfÈC¶vM²nB.¹j¬ÒÉ¤ªQ­5æÄÙjTEJtçÍrÄ[a©Qe¡À1ð\$a44·tÞØ¤Á6f(xOlª­9µ\"È^ghThwBKØm¼^Çù;ãLCÊA)Ñ¨j(B¯ÑÀ¤ ÓÒ³õ¨Ý¬wJÖr5N¼ÈAÍD!LÌ¬ÄÔB©)FOá&\" 2uT­u^\\Ñnq¬';L£\0 £#nNèo2;üÌ¤i¾ÙÆÀ§uiç/æ0'ìK¯8·0dÖx·ÀÞ_-¥Ð7WbÐ(ovØMÌ ìCBp\$ÐÙ_n{`¡;ö¼Á¦ñ'76@ð0ÑÈ.¼»D6PC5\$Ý°'£cËfÎåé0.s4~	p¦ÇZæNB*é±©æÑ\"Ú=æ`ÏÖ¨PD'ÈÓñkDÜ©J¤eÈTFID«õ6R(F\npH]åu\nQ%äÊbkê\r¥y8 Aa É°æB 2ÌyÖÄs\nÇ¤³/ê¼2 ^Ua­a=/¢'.'9Y¬\\0l:4ÍJ;r]91Ü,6ì@ÛÕ?®-r#Øû2åí}½ú÷:Õ3ûÂÛ,Mø'Ï×È#><(:Â!0¶\"<EùúèU*_\\­r´PÕÓi.?×?¾a~4|âeÈ¿_¸Ò&Dþàôú/ôoæÿ©­þð¦àÅÙ\0¦Ân¥>L3FT,b8ÌÂz)eýÌ;(Hj°kÈ¥+2E\$âFb{CVèä=DèÎ\rî`èQÐ\\>/ã`B,@o.I¥è^Í 'Òó?dØ	lgfK¥Úµò8®9\0ì<¤¶'.JbBÖ	Î>BÌ.Ä2%ëâoÄÜLÖ6Jú0Ïµ\rc¾¾*Û\rà7®Vù\"ÖD¸Öî\$.SPÔ%é(ûÐÍ'1Cç,îPÈjyÂpM;È\ræ´¤GÄÄÒà.çÄ`- ¤,¦È\nà&Úq\$ØÏñ\$â'î¨OæidØgÎhù,å\rl\nÔ.\\a§ÞêÚá åq#e×DlàPþÀñ PZÐÖøf\ríVUÑ=qs1³E\rq÷QvÎ2vqÕ±GÜãQ¨zÑ§©úh¦ÂCNäû\"Í ìVÍ`±¹ïPÈ0ø ±ø11\r!1}!ØÂ÷Ï£ D t#²+!Q!PÑ#Mj¬ïÁ#¨0ü®rèjÜèÌÀ¯Q¯%bBËK;&l¾ém!qú¬Òw&N'ã&ññÒ>l\ràÔaäÎ!Æª&ò¤½¬öIFQ¢>BxÝþ¥ZaëM àVÂVcªÒÈ¨ôeÌú¨¸­Í'È~HÈÎ«æb9.Ð>júLòÂò¶¤¤j*,¤\$c'²èîçDúaè£&ç­Ôëeá-æ^f\$îäæN÷ó=&ðüÀÒtä³G!rÓ2éç^Èó<8îÞ£§\rsKîÃa6ùñ7Nð'è×Ò%\r\"xËsvÎû5àA9SsÌZ¢ö7Còf¨ f #Á} Xä¥X'àßêÜ,Äbæñ\$ç\$qã6ñ±3Õ\$RI\rr8Îã\$2)8qó>Îb'ÌjGa³úµ\$7?F×­?Ô\0006´ïÔ	\"t ì#¦þþ²ÌN@Ö&¤V¤³mÓMËCCk3Vóì\ntB&4G7²7LuaE47A³T3EClfEU!sÝ4ñGoå?a{ï9/5ÇMg;§=´=7íI7é7ÂCJQ?SE.JnªëÎÁ4=éTÄT24%!M\"5S5Ï2ÆMÓI&ÖëäBDvHÓe9hROôOÙGË4äîðöADû&óëHnKQUÒN4MRú\r<3>óÖÐ¤îh¦Ü¤Ë#ÿSESÉ!Æ8¼­ÿ\"^©E÷<kÿÕ[@¤-\0RSV`ÝUpVîäÔ®#ò1DIØ'\$â\nµU:U°Ð8æÊù\"2:\n'¬Ä	'd¸\rV=@Ò\rcPDcV»«V¢r´t-Î@Dt#äeR·.P:Êif:*x\0ª\n pþ/dÊ\0äÂ÷²\"/ô©VDSa/Æ5`#Âx_%¦9aOÉyâTB&\"ª.FsÔ`2`¬J#dØ³çæ%Cn¦RÄ¦M(ì;/äQfB;3,cßEàM\$)70fhÍò°¥ãÞ\nrs2¢\$³\"ÞNrq/ê¼6´6CswÂÜÖ°ñqÓw>ÖÔ7Ò3&ªÓNU±ÉG±!n1+##Gcäãö4ò¶ü0äF¾3<^;Ç³GMæ'yi×.')½u¤E>BÃt@¤ô÷\nÐvßápCa\rÃ¢ÞÎ¥JsSV\rófÂw¶mÅÛlÂß2ä,ÒÍ=<'ógYÒ1kn­Øl/n£ÜÞÔeDi´Ö(*&0:\rÂH¢\n\0";
      break;
    case "sl":
      $f = "%ÌÂ(eMç#)´@n0\rìUñ¤èi'CyÐÊk2 ÆQØÊÄF\"	1°Òk7ÎÜv?5B§25åfèA¼Å2dB\0PÀb2£a¸àr\n)ÇepÓ(0#ðUpÂz7ÁP³IÓ6A£C	ÊlaCH(­H;_IÑ±Êdi1È&ó¨ÐaCÍõ³§l2Ì§1p@u8F«GCA§9t1f\$E3AÊÃ}Ök¬B|<Ã6¦¡ë?§&ÚÆ·_´7K08üÊ±·ÁDÑ*ÅPßIFSÔ¼U8Bî·Ò©¸×i;òL§#.}ºNp!¿7ôÌàùcº2\$BÚ9#hXÏ¿´2¨:V7Ì(¦°@½èâ	¨ë¢T¥<Ë R~:¨sj° ¬ºKxÂ9,@P\"È2ãhÊ:IDrð<CÄì\rkÒ86\r2<â+1á|±\rnü%\r2c' T~9¢Q¢ÏÀÃJTÀ ¨×\rH)52H2\r«{×>ëKòý¡iÂ1lÜ7áVÑ>/@;¢ÃCA+p9Xxµ£(ÌC@è:tã½T6óÊ9ÈXÎÂ{þ9À0^'aò926£cHÞ7xÂ\$N¢ìÊ«p¸èBÞ¡\"Àô3µÃ Äµ\njâ#ÎÌÅÅ!6mBîµ	Ã|5qO+Æë°¨ÄMú_ãRé_Ø\0Ó`ä¬#`à2`P©'B\\.,âÉ¼)2P2Ë\nsSÃê6&yHØ:Ì6sÖ¾Ã«Ì1ÍS´2C­³ºnÂP¾iÈä%ÊÅ4Î+n\rù8&A(´R\"\rãe³9(*FXàåyÈØ63««0k¾¡7Â¢&-c\"¼n4sp¡b1W×r Bµ\n©Sho<;¥|Ã:+¾;\rñ©ÌF|HéÅ®k­ß\\}\r.®n'hHêÝzEÅäÆípódÙid9Â3-
£ªÞÐÛ8ÚÃSMµ'LâBz}hÊ<#Ãtcm[¸O Va1lln¢Ã2\\¢\$BxÉ5£,7²v Ü<³þðê¡0feàç¼Ò#ü(HÚ¼Ä*«©Ì S*É[¯bg` ¨cÏ1XAÈÏ¨uÕqëAaðC	Ù!}Pª5J©ÕJ«U©éX+%hyõ\"]c«°}*yr´Jl)@ÐYá-!Ø´¦Ôæýñ\"	§ÈBEn®TÃÔ	@½çb§! TJS*TÕbxÄ9+5jöÞëß\nñàÚÂì!ë¤#Hëù\$ÚãÖþâB4Þ³çÕû\"Äa ðÎÕDkÕÎE8rTg`!(F¨aÑ?õ\n® h¦ÕHC;+aH@1¶8HáF0ßSDH1FBa\nìCHhBÓ]yIØ¨ ìò0Á¹\räáâ+Nf\\ÌYº ^YOÊ)³ðï?%©¬¢G*HX³XECþ¡NDÇ!¼8A*®ºæ1Î\$òÕ³£S1:ÊºkdpÂFËXì,B%»/Fàì Ípt¡\$v8i\"HZxÅ8	¹'Ð2('T@ñ^,¯\"N#XÑY¤<Â3Âm\rÈTÒQBY£°Ç´ByFHc\"J:ºÃþf µ0%ÄÁhÎD4XP	áL*Fà´þQØ­5PÕ§7Äå\"+³BËDóæ¾XrM6ôT1Ð@Ü` x¬À°'IaB¨\$þ2-n«âmDX1cvFY¹­V Ê	^Z!ÍÈK*Ð:Ï<á^	B	]\r´éÌ³þk¤q®EMu¯¢DÒ¹1\"÷ÌËRºM\röI9NÇ3<ä`úäL±T1È¦rD=è2v@ÂÕ÷àtÝUâ,î!Å#vÿÊÃãFP7Ëößn§ÁÙj°_YÝ¤ì=uÅc&²%vBsÎvµ`¹«G\$L{Âxs\$Ëd«jlXTtF`¬:Âð¤<L!L»±B<ºBÑHÌ&óo?Û3Ä9ÌÐÊÉ ØLÈ''±#fö=gCºÕË ¾å¢ís9¦ÁÁ2ÞÈC[ösd,:¤S}\\ðÓv.º%uÃ=3§Hfµ,·ËñTÁ}köÕ²ÂÕå@BÞN¢B³QBè2Ã#z*ô|å8dÚëZkLJ´\rIK	Ø*@Â@ ¯Üû¾ºâ~Å¡ÿ\"X´]I&[ÅõòÀÌ)m¬3vyÁ7¼#h­ó?ü^ðh2ÁÄ¾·ÔRAaÏJÕbWø	1(­n·¤õ[°lPÅÞOÆµî1ªÏÔº!´+ÁL@iÜ7ò~\"£¸â¼·ñÃÆøêÃ\\Ør%Îù7â¦')Å§RÃj,Uú¯Lç\$¹öá9r'>g¡uB	n,K¦}ÉO,¥\"/wæFvëV×÷ÞÌïÕ¡µø{àÉ	ðÝ»x²\"düw>Y½h¾µòLÏhwF>]lD<G×wcÓ`×ØV;ì´#}8ªW%íl ò'¤BC(;Ü#Ò^cr>iÃØ3ÞØ]aF6ÛÔ¾­e°EH\$Ê2dCz³í²¼¶hæRø >HÒ;°ºÇ2Cu^¤Î'¸}nòþR9ÖÄðì0Ì¶ãâÃäÃÂbÐì.ºæIUM\0JsänãÅeÜËÌXì2Í\n+C\nÂÒü= àyBÈZÃ\\FB%Å¢ÑPW¢,\$ËÚÅïdÂ\nÂ÷kö	-\$Ó'êñ.,H<IãÓ	Ó¦ºE§¼L¾¢F; ìLÌð\r.LîÂ\\DÀbÃ,1\nî¬Ûð-\nÐ%¥ÙãXrÃ!}ðÚXÂuð(PØpÐèðÔBÎúÂ\\º%ÆÚ9(¶¦ÐþÂ8b`Öé¼ãÎÂä.ääq!»q#ñÃfï\$àÎt1+®Ã1\rñ°ÖéBónéÏ#¢Ãê¥QUc®ÀäbàH<ÔíRºa\rìÔÃÑ7%±Õ¥ê¬óg0G06¦x71>b&&b<ChÑ²0ë´(¸IÀàÀD\"\"ñÂ5Ð9£l>ìÒbFxCj(Â%Qà Ô2hf=d HËHÛ±aÓ±î%p2E,`oÍH7óí\\ìá#%ÑÌ;/ã#Ò7±¨ÿOt£#EÒk²#òA\0òD'ÉLBa#%]&Ï_g­BÆ,&­1%,LBÒ^K,Òdâc/bb¯þg&Ãi'!\rc\ræôú«õ2{\0±&m©r¿pý%¬Ú²o\$Ò%&pgdÔpN¼´ÔK'=%Râ\rfy rZSü5Îæ]¦0ÌÖ\$ÀÜm²cLS1\$ £}°ä#ó2KÑ°\"q?2Ó\"Äî~³=1bÔbðOÃ%M°Í-Í+Rÿ6\0Ö>IffF&@Xb^1ã8.É¶\"(ÊÞsg7Ók8,î°-bÌâD&`OjK7ÍBÖí%ï` »FÖÍ~K'3º+­<'].­{<£½/2ë³ØK!äÛxKMÅ;B¶ëâòKsÍ33Å?óó,°í-²FÍ\"`/d-RÓ,æ\"ú~@ìÐ§ÊcNä­.î£ÚîwCÀÏÎjð4:3q(XôCt8Å£7tACIÃ)»D«¸ÂEeºí@\nr°?ãx_²C\"I(1yFC(ýdçrÞëé=DtgIéIPETIJGInfQ}Hïæ^IèHØk£;	á«ôÇdK°\$@ªB\r°ì¥Cj\n ¨ÀZ©4øQòsHÎÆïý\$Nh%qBç/Pq\\»ÅU¿ÉKBuî¥ñâ!®²:Åm-L+î/Ì \nBîPdÔm¯M\"ù?\\¶¢µM&ÉÂLÃÁbßV#:!B|çF®ÍÈ!cè>~6ã¢bdÊ£n8DRbîVÙ­¦ÑÌËâ©H³L?Z×QÖÀª¬¢¸éb`\0U;ZÐàéUÐ381ã#8CB.O-7Æ£%ÌÑ&Oå§Õ9T Ñ#08\nÐr&`´¦aö±tÎÒãM¤C,X½\"\nÅ¤Z@êu4týK/ .Hj%¬,Ã+°³!CHlXÞæ©Na§ýVlG&5\\Í@¬Àäã*#Ó2äÂ/MCB4ø`@FÁH";
      break;
    case "sr":
      $f = "%ÌÂ) ¡h.Úi µ4¶	 ¾ÃÚ¨|EzÐ\\4SÖ\r¢h/ãP¥ðºHÖPön¯vÎ0GÖÖ h¡ä\r\nâ)E¨ÑÈ:%9¥Í¥>/©ÍéÙM}H×á`(`1ÆQ°Üp9WhtuÀO`¿J\r¢±®ðeþ;±¯ ÑF\rgK¡B`ÉÒÞýX42¸]nG<^PdeCRµÇ×¼íûFÏt ¢É¼ê 4NÆQ¸Þ 8'cI°Êg2ÄN9Ôàd08CA§¤t0¹Õ¸D1%ÝCo-'Ñ3õDo¶8eAº¾á¶íÒZ½£ÎA½)ä¿@{b0*;p&Ð\0¦á\r#pÎ4í\rY¡¨Éã] Ès(¤>ÍXª7\rn0î7(ä9\rã\\\";/Â9¸ Þè¸£xè:Ãk!Øæ;Æ£\"¶N\"ëã\\£:C¤*üÁí	z§E¢<E-à¦êÂ¶½-Ð½¨©ª\"#JÒ+d´¯*{Ð^@éë£5è1DKùÚ0j²F9A²hÒuPÚ¬XDªû*±*LÐü¢Ìèü@2¼Ü^@-8­R6U4ªù5Èz'QÆT8Ð§ÝV¡½ôòG3RæDÇ=O¤çi1ï l+ôãHc#Æ1º#*3Ý·,r1Gn î4»ô0¹T9`@`@ä2ÁèD4 à9Ax^;ãpÃpÜq´j3ã(ÜÉ#&á°\r±«\nÑ¨Úì#xÜã|Ó2\rGYA,Â¯*77Ò°eÃ½M:	+YJ\"oVË¡MÆ¦Û¢ZSï¡: 'OÚx®0Cuæ£\"\"Ñ½H¨¸îiÑ»îMô¨=5²(T2_ðMz´0è1 Ã*jSO1aÆ=b&0£d;#`ê2Ãîý 6fHô#ôI¤KCDÀj?ú3÷Nò¬%ªÑ°ib\"Ë¢p OÔI£úõ2k+Cf\$L4#L[/_bE-F¦óu2ñÈÙ£×¤6DëÏ*¬­zk0Z«¯´¢&JS/k¶*¢n°\nT÷¶F«yY9¥ ³¨£j)æÄ)I©\$/¬Lè*/¨#Oî ÐëhMéõ`Àk!p·t¤ð²^cÌk½&j Ç¶vÔ B ÐB-S²]:E`ÐêÒel,:VÚ»QlA<³tK¢sÁ\r`Òâ°eÀøDÄ+C7;ÇçG\0@Qn&GCÁÚ\rËÉG~\0\nsÁÈÞåÚÂã¸fÁ±q±<Dû\n¼ã³0ÜA\0uaÕw¯ÌçA\0l\rá\n6 !0ÆøÞÞn§h0RVà(%FÁØ&O.\n&b¥\nòBÀpKdÁ\r!r0ÂØkb,M±v2Ææ³Lô}£5d`Nöh£Y[ycE5HlKeW\$ÐBrji?àkJ\$øÔvv¦%dìàXZN`ì&p0æ Ä£cÝ1ÀÜsd,@H)	<ÙXI\r¡ÀçÖD#\\v§Ëõ¹HC[/IÊOSôòVidSiDÂblZ\nËiuÚh°!ó(4EÝ<Y° _a°6\0Äq2ôù¹ÍF\$¥dÒ¶Ê©XÙÜ:u|!§á¥ÐÈemZëCª.ÍjËz_ÔFL,ÿ	@\$d¡RÄ\0 ¬Çeá99inÖÌèÈ©xc3N£sNxenI9C¤w\nÿ­¼;Ü\"¶L\n2gRÆÁÚs!\rZÍdFL¼é0\0æW çqàá.Ø&`mÈ;ÐÆ\"lgb¸íØ@ÆçåÏ#WI ÀÂF)¢bªkµbªµ!\0ð\$­àxFE ª_*Ðý¾#¯M	\n6MÍ= k#uÊù@(ÊæÑ4!¢íð*J\$(Á¨uùWÁ\$&ÝÉ ¢Ó¤ÂÃ£\03#@Û5)rä¼À1Ê¤y`îBI:UJT¤GèP	áL*YÈcÅÖ1J¨ÛºJÛìÁ¯;5y,âq&ÍÕ-(&¸DÔÀn/	côE\"Q#ùº\r¨2`Ê,p%Æôßßt`u¥S^¡3\0¦ýòeÀ1`©innA¦®Åù2vCG\rÂ*õqâ&¥]<(^¨²ÎHÜð¨P*[WBÊ^4M\nm0*L½gö! +c\0B`EÙ»>íûTd¡Üm¨n²\nX²Ðd°±¼ba¼4\r!õN-òöÂÕæ5òa¸Y\n`ê¤k-w:TÝBc/f]¼n¯xÚÄífÍèl¡¹FJûAjLfèÏs¥MéäÇrÊmNo,=»7«<ýOgÞOË\r-MÏùËÎ°¬^G¶uõbán âÊWÄÆ4òOÎºhO¨í\n\rIp2ÒZlOíVê±-CòJ!:Ä\"ý¬a=µÓÛÖÐëaØñ;>~ ()ôé#%;n)Sn®3ÝE!0ØÝÂ9¿PçbC+\"a¡!D<.^ÕÊ\\>JròSwE¨Å=¹ÀÊÖTÁBÑÃx¢B)x¸yA,þÐ^çË{ýmç41H0º»j8Iö×\rDë²L!ÀPC¬¡5PÂÎÉÄ½áöwåO\nJWÞÄÅ0w ý¬èÂFÌ¼Y\$0öPèH2)â à'ûç|!ö-Oµ:|ÞäðÀnn1(VÙ¨ 	\0@É\$d\r\$m];Dzö«ºdéÍnè&ðà@fäjø6Ë0fÊ0tJ*¢LBh¢.Wh«b`Æðj³(?ÀZ%D³c\r\n	20¨ÏÉù\nèÛ/ÂÏQ£úÅcK0Æ)îPÎ,°ÒRÖØF¢Ã0±1	)üöPê+ðî2.XD-Ã¯h*¾æØ­²¢]+i°ý	ñ\r±	\r¬W=	neþqI\nqM\nÐðU1>mÍQa\nA}qi5ï:@Õ^Fî ®¡)Ê¢âÂX©.Ñez'Jª&£Uðü%?ñ MªXÈÍâhVqÎZ¤Ø*¼M#¤b#Ab¬VªâçÐ5CæÌñ¤|N.\"Hv[c*Ëçôxe\r¦xæïN@Èò¤ÐxÁ0QG²åïÅL ¤<C'Äo\0xÍ>âfî1ï6éãè<Mð·?ò^ÖÒPMèîNÞÎ¢¶ñ'N(ñ  æÍÖ³#vZÈz4òY&£UÏ(*&qòäOÇ*FÆå­â3òøî¶AA)X4%`xéw®ãÒÞ¢mK\0hà1rÐhrÀjÂ/îá \$c^ÎÀxÁ%.ÌÑï/ï¦îîg)ãýR~påy¢¦oÒ\rçD\r© Áhà¸X³ëeåPÌë))°×(ÄQ)µ4±.éNU%BÈÜ®E-LâåàYO÷6g»®){7ci7®`U&#¢¶A&rY5]8Á¡:I'Ñ§ù+S±825&òò2áÄ¢]Æ¢\$ÅR>q&f¯Ð¿./=S»=43âpäé/#7sñ=2Ú)°ý=­>ß>LNlÒy>´	r`wÈJ^×¥(°>KÔ.ücæü´9' 6l´C8¯ÉCoÏ*Å);çH\0Qê/Î\n(\rÞm,Ó\0Nfx\rödì0¼UB`ÇÆûN4}Î>,T~r¦ò«æú- 4X/tªÅOAGé,÷J1Ìyã³ô4æ<4qIEt2×Mx Ï²\$4t{´¿(O5\$d=G ­¦pgî.kC³ÅE¿/Õ	>tMCÒDøt`keEP6ö°;®ø¸øP(ÿæ´?Fn2õ>>5BãT;)µ&#U*'u=Ç+cCS\nÄõMWEåL6D°@\$ÎÌ\r 4È³Ê³Hh\"Çi³ú[US:':Ð=Î9tOUu¹Zô8Tc+5hùãòX|N#1þùË0jÌJêi0j/s¹\"UØ@[IgË_.8øê47ôlN/½\$Þq¶õ\r%µ¶¿V%Ì%`ufö/%c!*RûµèmWP9W£ed¥UV'[NqRÔõc+]5çHâRO£`Ð¥Jó¶pqÔþÖ8v¾D\\²ê®0gh/Ùh¤­öt*0¹ÏÒýv86õ,¨þd%ÀsÅÈþoê	.ÌñeµGRÖÈÿÄbvAUÊ)oÿfv	RÅV¯ûlÏÿV5\0VónD ÓkU°'XÖMöRVÑQõYqrq¶Ý<vÔJ·Üöéca °£n£#ZÕ½4.=BtÆìUby<ð Â¾êbOfü¶gv\rv\rØlñ\\@K_vÍÊêBiw1Cvl7B¸=ánµi·\rFâ{u²4m3ÀÁ´ÓäÊ7B©wP¹>Ðñ¹&,Á>³@¢.¶É`\rV ÷4ãVAòâ\"ª¼,TOP\"\\ÍDÌ­ëÄF\0ª\n p)²È-±QdÆæ07Á§wð²ùñvM¯BÎB\".Ò0fYÖøÅ(ÆÔîî	 ÞÈ\0Ì/ìÐÌC×òä#r4>p\næq/®g	(O~f»~¥SVó&2Ì b0Í##ø5Òq+#u6}û\"¹-h~4+*h|\$çH8ÕÄ¬xÄ~·ÀÚµ|s&øÄ¤28\"ç8×0¸Û:vÓR³¢t1³ÞSÍA9(Ç)×ÃSd<ÂÕz§··¾UrÌZ¤ 6Æ´M,ç²@¶ãSdæ'±45JÓ.ÝÞFhqKó±p ¬]Àê Ûu4#q.¬ìH >¤ì9/Õy\$/hz¢yÙ%UD°Apiª¨Jè5.éørRÁï\ríhã;V9(wÃêÅÌþ5îyâ½ìüÂçÑ`";
      break;
    case "sv":
      $f = "%ÌÂ(e:ì5)È@i7¢	È 6ELÔàp&Ã)¸\\\n\$0ÖÆsÒ8t!CtrZo9I\rb%9¤äiC7áñ,X\nFC1 Ôl7AL4\$8ÈuOMfSüt7ASI a6&ã<¼Âb2\$)9HÊd¶Ù7#qßuÂ]D(­ND°0è(àr4¨¶ë\$U0!1ãn%(Æì:]x½Idå3O´Û\ræ3Dpt9ÏtQNÊÿÆ·Þö!Å§²Ý¾×r#-ÿ+/5&ã´ôÜdÍ~hIóÐÝÌ':4¶Td5gb(Ä«è7'\"N+<Ãc7\"#Ì¨Ãì£¦E#Î¼¾j(\n\$CrÅ¯ã\nL	Ã¨Ú6¬3C7Mà@=è9<Ë«°!\"\rhé8C²Èðã*Ò3	#cè<H¦<¥£*Ô)¬ó°ñ¼²C&£p&?É,5ï¾Ã±H(,lD¡(Ù4\rÌ«Ä2\r¨:/Iô¦8LD9ª]¦!Ó>JU\r?¥³ÿ\0Á\0x\r0ÌCCD8aÐ^õ(\\Ï#sázJ¶£æ;À!xD ÂlþÃSr`7Áà^0Ðz6\rMKâ\nÃHæFc¨Ö:®Â¸óµ°ò­ÂìØ.\"pò/­-²¬¢ãò7`Aw\"H(7Ð²ë³&W¼O8]B\r´´6rvÒF ×:®R\\ó²c\$²95Ve5B0ê7ZcM#8Îã.µü¼)¥O\nU+.dv)·ì3ÄX¨2äo0çîÈÊ<èôd(è§ÏF4½Eëºò6c\\E9BkLÜóêÙ,¦®ómn[ÖTÊ¯Ì\0ÎÁ ä¤ÛêM®Ú4mó%9íÓtÖÌ3I#8ì	#há¹\"(ñÁÉY§¸Ûúz!9[Ë,ÙZ6©m,0×5©A( äÆ6ÒõÀ`\$C=ª2ôÁô0¥\"f:F¨¤ QMààv}ê÷âÏDÙKörÐìeÎÃ 'ÍÐÌ3@ÕRN&Må\r³á\0Ú.äóY\rÈÇ©5J\\çgXÏÃ['p;t6Ã½R¡p	ÏñWÊør}O°§\$èºCóÆ}û¿ÖKý¹CâX\rh u ½ÔvCë~áôP)á=AA4\$,lÕVgM		RàLU6§Tú¡Tj;ªxhªâ¬UÁ¸EÁV¶ÑZ(blÃ*²cÈ Éâd-£Î?§ÞPB\$á :`ê#:b\"\"5#a\"q#©àè¨¤TÊ¢è¤«Ùù)1[+	\"()1Ø¼RAoÕ@£>G\rQ	Iªõ7fi\rü+ 9Æ3;R77/RvÑÍiªA\reÂÈÕ(ÌaJÆE ÒúÈ«îåÑñõÈÈä~T9BÜ9'.m¬¼zÈIAÆ¤\0læ-82}°d)@  ¨kH.ä0sÆ¡3<Q\r0Ë6Líq¸BöDç0¥\"AÐòÜËlÙ\rÑ½MÒ?7ç	uÒ#!©õ*ca#,d%© ìnQ\$ñæQâFL) C\naH#OÞXÉ0 qa¦håzP!ð^9¢X¡àäÅ¡dÊ!x&)tÿ'Ù+51*TªBPäÕ¥OKüÔ\\º©&l+ÈÐRT9aD3U&tàPAOMI`>ÈHÅL(e©Da@'0¨Tb3 <õ*HQñ§ÓX-«¸s´¤ã1Ô}G ýRd=â?`I¹\nmX Ù\rÃIÁP(\"Óà%«=¶\"Y\$¬Â3	N@²2vÍ1dÅ;`\0U\n@\"¨BI	Áf^p@xR\nP p|¯¢÷_(x0¯ PD¼[R,¡è.vL½?±cGL1Òh­ðpÃË¾°\"´¼ÎíF¤ÑF#æ|9dBñ¦ãRÆ­'¦g[= ¡@9¹h¢Ì2w\$1¶È+¼­±<X/gtðXd Hß4g°mÊ¹t&¢8ZWêø#7Ì0Ó´¸sÏ'@)°`×8l=î\$¤ÎcH¼¥¸¤¦ðq(s\r½âªB[ÃtMÄ&È\\uël+-¼Þãª\r2!¨êÇ# (&Ââµõ)ÃÑä´Ößhneä6èúÈ(C8ÚX6çÖx²3fL2¥ùÏ­JÙS@ã&´Û+HKB<!T:+æ¨Â¾AICCPQñnÇ)ÆBô³û!\$\\S?`È`g'û»BPËÍ5âÂ¢%ÖEÁx àæ,¯.c!ëà~¼Ãjó^\\CÄ­|NÂqYÈ8×1¤:^qóÈgMkLÕ`JSÖýh/à|ÝñxÃ\0W;ß;ïòòNÌþtg\$¤Î!ó1·÷3Io*­H:É´>sv_%ÕJÇ)*KìõL¤êÉ©Ì{ÍYëÌÜ÷Póa/Ý7qvÖj¦ç«>àÃV}·Á¼hÌaÀ6kÞcKTm|(\\oâ>i=\$yaè®?qgÔ%Ä¼ta8ÉìA19\n1Á	((2a39j÷©+ÏKc±ðýücbÍ\$MEç<¡æH¯ÇWÈ+Bç£¯ |qqÿó»Ä{`£M\\~tkHyâë¢Þ·ÆHýåþçü?¦ú_òr-\$Süü<ñ#~6g\$óðó §/HþN`	oÜ`þÂjKÄã(4ãZGî0(v¬i×N6ån:®\\ãÖæj\"mt}nÆóðc0\\»b,oRÏ¢ó.À¶×-w§~õp\"Õ@Z¹ÇÄÕÉâiÃbA	p?\$äÜlÃBIæ¥Âùì Ø¼\$L.9¤@=Ãã	¡J*XÅðp'°Õ\\5çÖÖl5EôÓ¯ ÿÏ3hðð\nüÅ\\oTÈþÇæä[MÎÝ\"¯Êô\"NNÝ1o0æô#ËMÙØÏÀ¦P¤v\\àä?,è\r\"ð5Ji4\"ê£mÆÜ°,ó°lÇ1w±2Ö/
31{q@[­bf8dÍH5.ÈÂ¾;#Ìcf:XJnQ¢bþZ¢¶hÄªAjJIlDJÖÝ\"ÑÑP	Á-oLÑ5é©g\n\nð§çÒÑ~·\$<ÿñBEåùÑÌ×­~ñÃæ^`ÊQl#âþ\$í|¨T2O#\$.O#²)\$-\$bm\"HN&\\3­< Ù äÙcür\0Ñ&í¦jRwñæ'-¨\"ró2ÚR(MÒ'M\nÒ=8ÝD°ÝòÌ¡ÂñOÈy¶KRº!Ò¾3Bú2bHïi)R¼@ÒÔ¯±à>c\$=;\"b&dH¦¤áqº8ëXÿ.ÁlÅdµÈý0Åó NfÂ-\\­%ØÙr0eäYò--äóÈW-Ë1ÓA,FÿÃU#Äf\rV\rdª¬l\"kH/ãjàê§\nb&§íL½ ¨© p¸­Â4\"Oâ.ÆÒºóéâ9Ó 4ïþ	hx¥\0rÔq°£îô/(°\08ÆÔfRÊ<äA=,Ú%´(Ór8o¢é!¶ê\$P+ÅÌ«{®0ij%¢Q<@¦\\ÒÄvõP^sBP@ìk\$1¯C\nÆ°ÄÿAªÆ²\n!ï4µ´:ðÑ+ô8óÞ30OYBÃ;Å´IÆS\nk|ÕbzCâûL\$B`ñGÄ¾ïÐ°ÜnVaÂz#cÓEö0lÕcÆ. fBü:ÓÕB#~0`ÃåÖ¤ÀÈLFÄÓD\rfø­nÇíIÇ\$yCY¤A`ET\r@";
      break;
    case "ta":
      $f = "%ÌÂ)À®J¸è¸:ªÂ:º¬¢ðu>8â@#\"°ñ\0 êp6Ì&ALQ\\! êøò¹_ FK£hÌâµ¯ã3XÒ½.B!PÅt9_¦Ð`ê\$RT¡êmq?5MN%ÕurÎ¹@W DS\nÂâ4ûª;¢Ô(´pP°0cA¨Øn8ÒU©Ò_\\ÈdjåõÂÄ?¤Ú&Jèí¦GFM§¡äSI²XrJëÎ¢_Ç'ìõÅJuCÇ^íêêÊ½p i4ä=¼ïxSúâÃ¶»î/Q*AdÞu'c(ÜoF±¤Øe3Nb§Nd0;§CA§Öt0¼û¼lî,WêKúÉ¨NCR,H\0µkí7êS§*R¸Þ¢jÂ¶MY`³¸,ù#esÿ·ªÕÂrÊ¢±µñ\rBî¢ãÁÐÔàB¶4Ã;2¡)(³|\nD¡¬à@\0Pª7\rnøî7(ä9\rã\">/ÈÂ9»£ Þõ;Ãxè\$ãË9Xæ;Ì£#w¤I´@´¥Ìk6Gô\"I îuW(R0,d­ðù\rÃÒ7Éj*+­]¦!1ã%Ðn,L·k\n.©uHY¦«3Vå7drÚ±Äª¹\\)êKz«0\\W+ê ÎÕÒq1ezwµvæ«J)Ó®dB¦æÊH=ªÍ¶\nÑÑÒZÌ«ÊÑkF¼¤¢8Ê7£-ÂÓ8l¸ª2=u@Þ)uï¢L³WbDh:a	¬;@ÛÁ@¦<oÛrR\náh®)­R_Ûó¸9dµM ìªËtFa@«6f\nMÕãiülÆªl\"Ö«\næ@á÷ÛaÛ·ÕJ*4I+¬qj8J¶Ú¦#A5kE£y# Û\"LAÝ8;óç:Îá\0Â1oU=\"îÛtÒ1Mnèî4¾¼æ0¼3Ôø9`@q@ä2ÁèD4 à9Ax^;ôrWµÓ]2áxÊ7óÈç=Ï¡xD¶ÃlÊîÈ4Ê6¾4øÜã}?éMV½¥Ö=ìÔ*bêZývµú¹Â±ÕSXÍUU±+³©cÑ0Í_¯YÈä«F­@Ø2xÐ2y·Ûò­.l²P*úVÏ-	VdôÒW!È7\$@ÎAA(dEÍv#Þ¸®zi4¶ôW[ñUÍ5å£\"£jÇWÔ`­ÙËÂ=÷sþÍKÖDaÊêv!°:WÆµ \"î8/8W¨uæ¨naÑßT4ÖÌ`  pÎäÃ;½TåAÇúAÕð®e+½`:Åi-qlR¢¤H®'oÕÆµå\r ë]â°Èt]R}KU\$³xë{Ïl9bÅ\0Øaù<ä1ÜBL%Ðz3YHãFðF\"HúÐsP2ÆÄßB*GPÞ¬÷Þ+\"N~ÐÀâøZØÔCêò_FygæFÉ:cÁ\0Årqµ	Gød L+æY0¤3Õ|.cCWí)¢Ûxðu±ÏHÞt«+¬ã©40§í¥2©¦VP(	&;²Hi3!ªÑ¡ÆtA¹O-_«Ð¹RM¦×\\,A!éÓ4LtÅ3V#å¯z£HLÐêÈ\\xavL:XÝA<¼R´j3ÏO-FÅ¬õ¤J¨Ï´;ªe2¦ÝVª  «ªd2©·V§\0s7pøÊb´¾\nÈc±ÎèoÁ66Ãvf úùD«@@Þxàn (·6ðÞ4?¤3¤@æãv5Z«AwHO(`¦¿É'÷,¤[rn(¦Ð¢m6·ÚÒµiÎ	H\ni.·t¬c{à´ºDÄãi\r!¶¹²äÜ«s.mÎ¹÷BèÛbfu©Ö&ê¿²×yÞ¤Z¤\$·fÍ,3b4×¥¬\0ÌÙ°âµE@Ë-\$+ö54ã²ÕUÍUûW!\nyH	©¬ù\\\\ìbaÀ4¹4ýNn{rÎaÍ9Ç<è»¢mWeÓ'RêÝUfS®ùÙ;JJ\r«J°°h w­Ü'Åbqè¶\"ºõ´ekÅqeVy&¨\n;B¥zsv÷a\"·A î·{ÀîÅoØ!§Këì²YÁCÙ a` 1áldbj3%j[Ô\n+*gDW(ÄbCÕz:Fà  .ÉKq²ÎÌ¤,ä¶iðQ \n©a¹ÒÅ8{â<ôX\$tª§­¸·i.Ã¾¶?¢Á5èrØ(ú­2>¾\rG59¹Õ6í1;Öâyo «Gp q.½ÅÁ î}ChÇU9Ìf3ç\$í7¥ !0¤q°U9@äxfç\$¶kÊñ/U).QôÊ¿¢»,Fhã\$p{	WNÈI¸&¢yAéq5KÈËñúÚÅ§:²ãÔª{Føíxiµ{ñ²¤é+9Zõä*N\\éµÈøµ)*³_6Z}û¾¢ÉÉ-QZ¤<³e Hy:íºå§äêñÁõ=nL8ÄZp m¸ÒÜ\r¼)MÜû8ÒnÑ<áF!UH%ÎØO\naQä¼VË8ÕA¯ÏJJ4lgg~üÐÈ½ðe~k	ÌhÙëR²hEÉ\rÕ6§Ô<¿×®;FeÚwÃ:DiÕëru0rRJÛèb©À)ÊASdÙåq*i=M×;q9ö\0As¦Ðr¾Ü(õ*Å§ªÃz%Å£¢SU¿WO	À*\0B E_@/éHùØB=a<nRÑØ§(vpb©Ðabê\nÏFLM\"ÿ¢fÿïæ¥pö¦Êëþ_/¢øÒ§àáÄ|knxGD*X/^[C>Ú!Za©.)j°RjnJ^0:èãçÉhñj	É¾«fØÈ®òÂpxðéæ½yÌ¢bÂéêiHt­Äc	%o4<ÅwÇÔ°lè¦Gï\0°içÒ\n<ÓëéhdÊÍ)\nb´ßÎ(ä¸¢Z+KÞµ/´g ¬m\0?CÔdï£Vj¢¡\$\ny<?\nz´`éäø t¬#èLêíÔÒí2Ñ*ÎwÊ¨`¦fÈÉ¹%ãbØÃ9NV(\"J\nG(r`æ\rHûp'pÑé ,ñ	è ¦\r ôÊ4k@À¦£Îmm)º¤@%KÖè²¥:@æÏ Ê¬àÐNJL ¥\0jVÅ\nþëæïîyÂô,J\rÈ¯¨\$\nmÒ4@Â´ÁlÐ\nû(\$< Êè)qP7iÄ5gê¤ÐáLû-\$ \rË[#.Ð©\"ÃmrBõ¸ú¨dIÁ'°)\"ð ®i%òZé)0Zí#j70ÓJ^`ËÆ\rb\ng&>#» 2òbáÿ1ñÉ 7²¦á¯ë'ïð@D¥âjä\nRf´á`ÉÊ3èbúc7+g\nÈD)!AFm\r%ófrÇv>&HQÄ9*©ßÒÈÊ1(ûbñÙð¿	r+Q`P,am\0PD£j:FÅ#2nOÐ\n`ÊL@ÒN\nðëÄè\rp>DüÐhßñ:O1ê\" B¡2Ã¨\$xaÑé\$áÈ%01	ß0\0úð»i1ý1Ë\$nçÐµ/IL¿'óRáGØ|ÉöÐ¶z°Ê- \\-Óº_¡³¤äÐ9:°EÉ;Í;sd¤<9è2Ð£Ó¯SóÌ:0Äà²ôUÄRB\\2Ï>¢rÊ2( SÔnÉÏ,Õ3¸Óå-6Hí\rà-þ°oBÎs?4¿3ÓD3ØólÐI#³ÞçôPçT å@îOEìÔsDóîäzQ#	p@LîÄ\$Vi¤æ¤J(¨÷'MöÑÁ\$cJRÔE\nO©z}Ï`\$´¸%`R´o%-S*¥\\bÈß´\$ã7\r)k\$ÓÜÒS.UÔ+7k`3dZåþ1ÔúÎUNHô¿n\\£bvÀÒâ®Ä`À\\¡ç÷Nu&É-Lç¥SD\nÀ°SË>e¼½«ýL.fU0|Sºµb<)HÌ\"`PcÎfHâ&ÇB\$À®y\$6¤EG\0\$+åjrå®KH	KA\$¿IP4Z.RKSN5©C5®7s2ªRàÆX¡FDÔ¨¥îiVTHôåx¤ÕÈ£tu<uÓEÕì·5ðcEð~«ùW_Ì§`a`nw;¶\rYuµû>ÕÿAÞèwV	9Ò¨OøèPÂÁç¢VårÐé&ï(xÈ|XãuG\nFLàª0ARÊ+UÉuöÔ6q18ôD*vBÈl\r,n¨_J¥]@P\0,3iòä°©%JËöSÅbÇ½\\ïv·;Ò¾£ij.5y#òB\0¨,Ì4êÛµ¡u3tIéq0«\nõÅ^«c5Ëpl¢®És-U¤wp3.É	a6Í:w`3Ñr©*ôYo)bPï3v+^v/^¶W*×6AYav1t±qÝ4Üòuu·uÓÏwiÑw¶­wäu\\é¶¤u·?u÷lw}03luHçåUwucd (	0#bál+!{ôyöÏNqNñí??ó¿{zNö6íPta=S/|JÝZ7ËkÈì,QZP³q,FÔítÓáV´CtÇvs@·N¢¦ÇPxW&O2y%Ò½9ÖsÖÑ`KaK8,úØ#~]s³å÷C7²ÓÓðí3I¹R÷oÖ;?Cw&¨TÐª]586º&aOöµ\$¸[lDYnt:Ñ\\4o¸cókr3h|ÍªMlý¯Ð-\"¸+IÖ¤	Ï^Tÿ'ÖâÏb¿_*o.äHC>u~v¦P2]íOÍ=´U?Ò5mµT8»tpã~5¸@©jc¯Ï4è  ­\"ÑØËéõEØöJúµ3aÓ³cÖ#P÷N:8jÙØ,,PG^O'\"D\r\"M9m'·z7OçÉëY	ÕùµNvqä}~×QW9W7¹°ÓèCr8÷UÙpêFMdóP£¸\rÀèÒ9£¿ö50®K0òNh¹áLe%MÇY¬ÕG¹Ý \n YèÒ7£ó9^XKy«m:'¡¯°°OA¢ëÔµIÆ£òãWE¢YG0°ãzCQDFÕ]md-Iõ¦ã§5¡NIiÐEæmWG·xPÕVû^·ÈÀ.·a)3ª\n3/¹£Z»sú«	Ú'b¥:3`H OOò\\×0à)¨Ê5<É®-\\TÏyº+YTô¦w%1¥øw¦È¯h8ìV³»n	kÇ¯7¯oº¡ úßy²È3³yÑ±¹½«7szXÿ¯³¬w¹µYw´/]w¶gí´[m°z)ImòR4¶ïz±³_*BUy¸Ë¥Ö'¦ÕÛ;xÛ%'¹¶í¹úI¯Û'·»I°²²;\$ÖÝÇVmÖxÌÓ_¼»Î;CÖÌÖlØMU)\0×½ç.á)³\\)\0Û*\0×*U_äõ÷ªû§£d}+téq®Ë{c«|'Á(EÂ×±»QxÃ¶?Á\\A±[o®¼Q¼:÷·ÚÙ¦9ÖõmZ¤ú<\$³sØFI&½2Æ³ZÛÄºÐkÅâ­d­³*ÿû»F¼V#>ÁZ<ÊuÉ×º[OÊ÷^àMl¸¹Y:Ñ«ñ©J%jû\\PshfÈX´eJäurY¹ú¬gÿGÛ©Â5Ë8ìâ÷|Ù\$7Ð ¶ýpÔÃtð\$#vÀàU×WÅA¹úB<õM(/¹bÒs£}\r0\\8TC­z[·k¬E²çWó½ÖyGÖ³t·8cu¨®}Fè\\ Ønâ\r;Ï°G¨Öt\ràÈ\r Ì±g)\0Öt¦äæÌ¸L\0ª\n p«ËÛ\$)e·L{\\mÎp>¡,^¡­ÿ±Ì×»Ñ!;ý ÑéY±ò<ùZGH¢jÀÝýAY]ZÕõÅÇ\na\rná©Ï³çóCæ¬	½±Û@ó[7´ê¸g1|\rÄS¨ÉÍÐwPôäRóÞí[³Ê·lqÄ|B\${fäAV÷±säTä±Iå¤)¾÷lrfü?\0éýÜH×=«Lä·ñ¥ZÀl©æp^Éc«âgÕçï!³x¡\$¥Â);5Ò	\"zíPU1ÈElß×¥í¸R2ÖU4ÉÕÅR(¸GÊH'Éz5ð^\$6Z;íûª·J´=c´;ÔL\0Þ4!W½Tkð±¨ï.êaJS7acÇïÓò±Ò¯ê)(ÓdÙßÙâH¦Ø?Ð]+Z7fX¶ä¬¸Gù(vM @Ç&ÞµRDq`ê Û[8V\nBM	ÿDmq¢f;f)\0þe\\X¯Ok[(© Ùf«I\$©×®\0¨'º%8d5myÈÞ»d01ÔâÛ½^ö¸G0½øî{âæ`\$Î°¥(ØD`oxÃÆ&å¤k­¼!\\KÜNwÉ.9íC>m¨Nqj\\á|}À4	 :À";
      break;
    case "th":
      $f = "%ÌÂáOZAS0U/Z\$CDAUPÈ´qp£¥ ªØ*Æ\n  ª¸*\nÅW	ùlM1ÄÑ\"èâT¸®!«R4\\K3uÄmp¹¡ãPUÄåq\\-c8UR\n%bh9\\êÇEY*uq2[ÈÄS\ny8\\E×1ÌBñH¥#'\0PÀb2£a¸às=UW	8»{³®#+µ&Õ\\K#ð[á[=æ-¶¸O5Õ,§¶%Ê&Ý¶\\&¤°TÔJ}Õ'·[®A«CÝó\\¶Öðßk%Ä'T¡ßL¯WÈ½g+!è'òMbãCãÐù ¢É¼ê 4NÆQ¸Þ 8'cI°Ê3£@:>ã¨à2#£è:\rL:#ü»·-Ú ¥³·EÂMªðËï³ÅÁa9­³~¥NsL©é¬^\\.-R\\Î\"¶ÓC²¬CEÃÎ©MÃRé:³¸½()E¸Ï<·äØ)¾CHÜ3§©srñR7Ë!p´ÅËbLB¨Ü5¾Ã¸Ü£ä7IàÂ#æú|úã @9ÀÃñCðæ;Ï\$(Î¸ì(¶34ÐÜ#mSAºJs¯±Øª,»pòA\0b)±Ý>Öªm«/:¬\$ÓJËRç\n;ªÓ~À&ËuUÉÈ* Ì9lô\\SÂ,?#ÆNÃDôN\\ºM¼ÙGR®\\ÌìÆº6Ê\nH#Ê\nò÷jß&4ÝèÅµÌ{8éúRõ!*¥µ¾éL1	pNYË52´-SR¸ñ<+/ÖÁ®\\Üf)iêÓ_H.!¹ØÏÞ8Å©ØP '·V Å¶eJ¨)7¶z)Özù¸xã4«/ ôcºW¢ÇzF7¸²óÈ¢¦R°2\r²ÔêP4íCQ9PÃÆ1À#´3É>Sóè;0cÝ¶¿u 9`@nÃ@ä2ÁèD4 à9Ax^;ópÃ°lSÌð3ã(ÜÑ£Háè\r³Ãé-ÓÀÛ#xÜã|å:RóJò3ºÈîè+|Î©ÉX\\§é¶TKSÕ{a2õ¾Ið½£êÕ7=nz¯fûLï¸ò¼÷Ãn³ùWì³º\\Ñ;`P®0Cvæ9A(ÈA\rú!ì#ÈùÝFâ1åH%	ëô#Êé#.²IGgu/4âÓÌ*Ïdò!óhOÊ	ÝÄ GC¨lPÁØ0ÀêW³¾]>[iLdêô¾&ÇÔhéÇ4b¢h#2I²¥êÇN9Zd¶AÒ¬Íê0WEá£v|(ëMJ~D×¼PÎëÙ(gtÈJX£'¯)§2@©\nñI(#CØÐþPá0ã A`ñND0¢ß¬m£ôD¤ËÖGoe¾dd[Òk_©á±Ç_;ãf^S\"Q\"Ã@evdõè¢ãÕ.%ÐaJá\\»5`xÞì©-Ï	<YxBÀ¸B\"útQS¥ò;C¹håóÝ}*Øõ\$^áhBsa1Øtáát´:5â[Õü·Tæþa.áÝÉðÁðCvÁ4yøh >X@-,\$ô¤IôIá:ê3Dèäò¡áæäíî(oÀG¨daîn®,ðÌcbBàUüSC¦ô*óîìÃpyÖVÞÜC41°7t´Ü0tU(0pÃEh«þih:  PÁM;tø)BP~/\$ÊÅ´R)á'Pø¦ôìCKnIl5öÂ«t 4FÆâq®=È¹7*åÜË°éÔ9÷BèÔ\rÕÑ }h]¢[¢\$ð)·H¬ Ft¬¯W>¬+p¸RÄr?MjÑªI)sÃÉëÎ7i¤Ôü¬¥tî:à\\Zpî&É8ç ä£sÝÍ9Ëç tN:*Oi]XI\r¡Àÿ×D(¾Íùÿ IæC[¯QÖ¤_u«]YÒa0<Ì©·ºä­®vÕî\\·PÇhÝ° o²C#è­¾Ïü0k ¡êsptÕFCÕJ¬Ûâ@xT9ìÙ¾á¦ÒðvÈ)ÔC³m*j¨È±Ù#§\\¿+<eÕêTvd°@@P\0 º`V%N´ð2ìyeQ¿(¤ìP Ýb0»s@gÜü³úÃ+ÿmaÑ åÛpàoùå\nLÆ~lÒ;\$HÆ lzëâuuè	»5Ü(¢sA¼7\nÊà]3áÝÌJÆÜ ÃØÌúHÉê]än8èÖã=ÛcJÁ\0C\naH#@\rj´[@3¦ÑKr§¥Z.º0¥\\L­Nö¹INæF [±Ñ@¯[,ª[\nB¤KÅ¦Bd«69#; -Ãê·ja\$MDîEA\$d,nPîþ ÔâÃ7À3'pÛao3cÓiÌ1ÕDÿóò@<DeÞDÁ;\$Üf=£\nñEÙÂà°-\rÝ4\n°2\$Kæ(Äú´\0ÞªÜëÔdò¹A«kPÜr÷Dãt¹sq§¾¥¬Ì¹±'´÷ÈÄæª£nºªÉY/Ã3¸ ?Ø#Lµ#_øi¾j.(nøzGÍ<Èý¢©Êa2¸E~{ ±ÅU±&°Âp \n¡@\"¨|?&_YAª<@/§ÚQ¨³ºuÅ<Ð/xÞ#á6hYl\"W~ØìÉòx¢ãdd'ª×v»ÉñlC±YÎg-?è047ZZü¾l¡±#ªúìóÊä½÷´j^)ÏëmZþ²ñùÏí<lô¦Oò'ßfR¯zÌöFh¸&6Û/|öeÌ5)jEjÑiTDÇú@MäGa2NaäwîDÛ\"~ö(¶Êì²XI¾4h*È´æc'Âè7Â~4MÆÍïð\0ðFD/°ðÅÊô§¦\n`ÒHp¡Kæ¬¨pµ£üÂìòK£x@\rç6Ï#ðÌ^æÇ Ê½ÀÐP \niØ×ÍJMGM*+A\$êÇsÇþ?ÊãPæOtïÈ¾ô\"\")`|IF^údCæO§Wâé÷-èü%ÖåÿäJIHDÎèü÷Mî&H´ïÜïgGeêir`ÖqpÈÇB\rbz+¨ü3¯V&È2ÞÂ,@côä\0¦dTØ\"¢JHæoæÙg,ÃÅÐxÜh3ªqbæ:Íº÷ÎiQ¢ZæHø£ÊeeEE4êñÀ¨ 	\0@á0ÊPgð>ÎÅÔQmîÎÚ7ìÐØm§þã¬DHÑpßf¬;¥ÄeDd&Qîgý¦V.gÿÄ :()\0é¢>9O¸FNÚíµ\"ØèÂø/4a2dD¼eaJ'õÒøÃ;#Hý#ÝËlZd)%ÞF	Ôo @NàîÐCñ®~/ã`õ±¬Fr %1JdY&í¬Ñ§åe|gd(§ã:F>ÃA&J\nàÊNäîÀPV(<bÄA-R¨ZOZ4G²2G*â±/oäÃºÖ@:JxG-´yB¤ÃpôÓ®S#!{ÈV:¥ì&IDùïY2DR9é¢W£p-bdùò¯¤dýc`HÒ}HÁ3Ët{³b¥£'óp'¥GÔ¶ÈIa6+\"S6°s8åS8Jõ5\$&B~sc2³¡:ïîùÂÙ;3s¾ Ä(dóFsÈ91'¼¥ÞÿO²üÉU8çîüpîâ{3	À)°T2â~yHÐÈ,É+ôô):óÚ<¿BAaAfp£b\"ü5Q\"{iOf^D=%©>.`èé°è\"Ú\rèl\rªLîÃ@äÐ= ôSBæs>Ïù²Áï¨}DC½DÝ=Rþ&w1)TXB{ÉQHQþ³ÌÈ­=Tô'>´£¦}K¿E ¦9rq:TÖµÜÒ0Ñ´¯<´ætßNò×iEIËP«J¦\0ÉªXÌÑÉElh@D:Jç6LQnc\$R,; Ôü*É®Òz¥¤)\nîï&\"Z:ÙJÕI¢õÑ2Y/]#P{ÇL´öÕc+îô+nù&ÓhÛQ»OUD]&ÒPæëlDÐïnnùô2õÈF	;VSü_eJPÿEô¥MC¤yUb{Ã:®d_3ÜÑòEE¿IoÙZÐ]¸EóÄ]xpÕ®\
\ÈY1ÿCZaETuVUUkO].GL·;[;Qg?`óJóÇ´õ£@TAVö!ðóPvFKÓ½6óÀ{ÖAS´W¶XdUO'×W³cKcïeeÊp\0002¿Xµ[fÈ+g²<8ö)D6Jµqe%¥:(ý{9%mi6]fËhqLVMWÑÖ«*ö2¶Âöè	?dFüù:Ãp^Mºb±gÄp^Óe(ü¢Ø/æÖÖÛBØSà\nFþlTá,4åP×¬·S¶diÖOP¶UL\"´kq´kW5j3ÁrµWV¾à@ ÜÎ'mL6WT? Þn\0È¡öÍ<WV©\\ÓÃKðN÷mwxab\na¢±Z±|§õõÃJ/;	+[Z>]hu\rzhoz·@JE\"77MËz1r/vO¡Ow´tWËUq]|÷-u1WXJ÷Ç{wßfeñ^ôÖa×zêãY6Fi7ø±U×J3³REwc·w8ó2a£%,ÓZf1@±D>üÕPªN`É@,@RLÛ8H@L<tFêM%CcãNÇLAXX8h°4Ý@\no7ºý8|*÷æÇ|Ö»v4M}u\rÊ³¸k3W'É~·UÑpN¸,ù AQr^Ò\\§hÖ­i±Hò5+,ìÓY7MDwé}VQ!YF)ÔÆ¯4ÁwóËx;8?J4ö	ü\r aÈ´ù®ÜÍºÝ\"`ïþÄ¼TySf·*à{× J7àWrH\\jïÓ ²Ú7g 	F%xê_yRs\"3:)Õ'C¥>YJ~X\r'-r«@Øn*\r8F÷%òóDQ;5õyb'ì)èámV¢ ª\n p£«à·&R.Z59âÝSõæSñ\$-´äÏJ\$Y9]J{lV2¢dÍ`	 Þà\0Ì4t#Ãº)È<:²Q°Z8#bØê7:{±|4Dv*_uCT/7w8¼ùâÔÎYby	n´uªhA!	PÄ¸WèÌL¢hî}ÈÏìÓI`ô:oy|H Ì¶«WyÓ|-ø'Ù\naÈ=òI=éËJ>íª[¨»+O`¨£\$>#çØd\ràà´fÃ\rÖ·^&5gGÁDêWâeÂªÖ]òÇZ¯Ç\\3ðÔvö<÷ñ{	 ÇÉ0[Å@¹£®ù\$VP3YÔö@¬mÀê ÛböÞT²^WaÉ¦{æ(a=im´<«gZyÆ4%«-­ /C:câóaÖéa}èÈ|LÚ³Hdd<Úù¥yp¦-Yb\rîèãôAWñ|[­¦B­lLÌ{ÙU³#T3¨<4B\0	\0t	 @¦\n`";
      break;
    case "tr":
      $f = "%ÌÂ(o9L\";\rln2NFaÚi<ÎBàS`z4hPË\"2B!B¼òu:`Eºhr§2r	L§cÀAb'âÁ\0(`1ÆQ°Üp9bò(¹ÎBi=ÁRÖ*|4¤&`(¨a1\râÉ®|Ã^¤ñZÉ®øK0fK¡¾ì\n!L¾x7Ì¦È­Ö 4°Ôò¡èk¯°¸|æ\"titò3-ñz7eL§lDìa63Ú®I7F¸Óº¿AE=éÉF¹qH7PuÊMÀ¢©¸Ön7äQ#j|aÅ'=©¼Êsx03©ÀáÂ=g3¼hÈ'a\0ê=;C¢h6)Âj2;I`Ò¸Á\0ÖìA	²j%H\\:\$á¢&ãÁ0@ä·A#HÐÖ Úí:£ÐÎå#Í\0Ø4B\nã(Þ¡S\n;I ÆÀäÈBÒ9Ãk:Ãª!»0´XB7Á\0P¬{ÕGxÒ²±	;4=	¼Â Ú\$£½Ïä>¯¸äÂ#\$9«ÃpÎ!pcÒõµ£¸Ò:\rz¿T#ú9`@!c@ä2ÁèD4 à9Ax^;ÕpÃ:¤(\\ázNÒ¯ãüIØ|6£MjB3#Qxè4¸!à^0ÈäÆ +4#àDym(\"Qâ92ÀÚ¼(ã*5ö<O31©¥,·U©7BLî!PTL£\"XÀMû-@08+tj#C¬¤'ûÐ4²âþÌPHç{D°mßnvNÎÖ¯#-ÔÜL,åbÃ43%8Å³´Ã)¦ëfd À¨&\r(»P&³Ìà&ª:X,¬¬PÜ½+@Å¸qxæ9b	Gúh0Úc[ÔYî=B×-ÏxÁàQO\"ØæÝx];çº\0ëx±>Ó«®)b,»®·.#Écn£ÌÔÈ zÂ,°JsÊr×bç¿p8!n[¤^=6²60×Ôúb9\$Plá6USðàÃHÏÀ½Ð|>âvIìc[\0£MÐÝçyèåê£Ä87+Îæhä\raê&6Á>qp°ô}	 ×÷|ä;Ãôë³aÊ Í\"õÊy^.Y¾Ñbþ°æpNW¡¬á½r#É¡#AÀÀ3ÔæPPy]hG¢vzèM,ÀâM3Fq8ÖÒQº\"\$LÑ)À@§¢TT*¥XÕr°mZ+`ÜÉKÓXç^ãÕ\rÅeaÁ­È^*á¹ãB8nCJ=çÀÈTÐl/Ð3!Ô9IMQ-t.^)AObEEâQÖ;à@`bF')­jg\"4HT*RªuRªÕj¯JÌ9+Unö^ÛÝêô9çVs|wJ¯	\$FÙ¡)¹\"+qs£>g\$ál&8´ö¢EÑm¦6£s.ÍÃ`g\$ÄNØN'ÜÐ±D,7Æø)àkö1r>´f¸<\"­ÀS\$åPy\n¨7 g]H1ïäV  È &>A;1³u Ê)]å-æP±/Äæ/ô+?1hºJc),pOR.Ffj=rAÈ¯)©5tÙa¤@ÐDðÇèÉD¤n\\©uüÙ cEd\\3ªBXkj ¤´2Êu<iá`¢) DÃQ8!ÖK2Päë ÔzädìÇM	-zOñÿ@}#ØãQtÀð@#Ü}\$H¤²JPLþAA¸11\nP *Ä9zCH|C(G<<Ãh2caåþ­Ì~§õ/ì1ò^¸P	áL*Â&WÕ5#ÿËsÊ9@&@ÛòD8áÔ<¼2DIÄhíLÈpG0T¡5òî@z/-ø¼ìà)µäðP,Uñ9&Òl3~}©EAÓÖÐ­6¼>åôÕ\"	_m9XÆ:¼I»§	á8P T´@\$åAÎ9¥0`\".fðº°Qì +\râÀ.\"Ä3b.Ç\"NFç\\fÁËµÉvð¤>¢2\\ûJ°:³È<ò[¦\$ ØYrÒÔÊ³Âadræce}és!#Ì±ZMÊp¦\"JÚéc+Ùç¬äÈÚÉË©dn@8w@º\\Î¤ÄX#u¸W¨oá¬½­Ð0Wáð^ÐtòÒ ô'-OÔqÉ6o¨ñM¢'! b%Õÿ2ãysZÃÆ»³'<¾ÚY/5ÂÈaÜvG´&õ^sÆ³iî:Æ§É8sR&¢³*{\"©	§ ÂÁ´:ßIKtm8(.!½§6ßÞÌ·uÐ¼+ÓÊØm#ÛDÁÇh0á¯BGl5?¹+ÊÁQ¤×ÇhQÊiÃÒô\n×­\"Æ\"O¥ ²Âû	è\"q¸\"J¯\$ÕmÿD\"|BC	VÉ0è'¦çå«@ê\"9èfLÖÙ#`{<Âuîúrÿb\0ªC®×ÔÔ+û®/^³1±'Ï '°t^Å8!Oeýt{ÚòíØ¬Ïw!QÏ|³WpÖØL!u@ú÷i¿8g ìÒf0÷Ýyc`[5~Uá÷1Þ¼W~ÖYÕôzxrÓ¼±íG£~JíFötHem{¯iÜå\r*%nw¡>`Z»Ë Èelù·ÝfPHê\\ì²¸6QôIÌ\"558R¼Éjâ'¬ä]6qÂ.ÿÎPT\\Q¨×*o;~{¡Þ#!¨Ö2C(îOøÈÔ¬úÞF¬ÿOÖæßÉè\"sÏ¸ç£°°.e^kb'cF8B¦î\"GÆÈG,L¸Êj\\!ÈôÓ¢øPX\\GÇm£sd¸Å¢ \rößî£p\"ÙJýÂ\"ÊIBðâ¦ü\r­ª@l¦¢iÄèÎÅ Ð¢:\nÆÔp¦Ø>\"ð o)ë-	É6ÊCêéæpÐÆÆt¦ùohÐèÐÛÅÍþMPæõ¨ÀDî^SpóNö).Ò&\0í®¼ÿÏ çä7.Íþ'Eý#ð¢Mnó¬á¦±,øñFNáÐãPa?±]HK8à+Ä7êà¦ÀÉ\$LQ\0òm¬äI)\"2\"Õd^ó5¥AãDÎ 9ç%°J'#~àíäÎJ0(ÉóZrÎ	æªÞtáNÉä%¼ÝÍ×ðÊñ\rÚÝåÅö¬ÍÔq÷Àå\rý\0Ó°*vlhæùnf01íO!àó!1o('0\"1\0Âj&dì¶Ä§®EÄ`-ä^£p!o i:K-0ìñfQp&ò- qpdL!%J±ìZ¢,s#J;ÆQ2>2J2zÍÅ âgÍ8F#&§\${ ½\"0æf2-Å#ïä2h RÉ-í9(²í*®\"®&ê#à@ÊC È<£ì9Â9/rúNÿ0'j|ª?î*ãRü«ä:ÚàÊÜ 3zêµ/6äÀ-ÎA#pånA.~ã³Hn3TbªRZ;r(çKrâÿ|çS[ñs7ac7Q´×¨ZÉrn^i9C@Çèìc]*8õS¢è\"ð#	:*¼ÏVãR\ràà!DÂb-%úé³®õî®È'\0BíWÓç`g+)M\$]C,C6\$C^²ãZd\rVÂüctöâ\n ¨ÀZÀ&,»JB&ðq\$À¥ ÀéÃ;3ë;A;ªv¹+D­g%¢Ù&^9â\r@§iän,®âDèÎþÔo¤É7´âQFÃd_â¤çàrrè&NâÎKô/¤¾J:\$NDgúu-èà®e¦×é,À|¯A11l,ÓÉLÈg<ÓÐ¼¬ëNSsÂå\$:»¼ê\"*!7ÔØ#~µõÅ»NN ÎKÂ@£¤pl¤60¤æßPÃ õ\n0¦1\0ÞFÒÿ_`¬8L ýãZ£PÅ¨ø}Pdþ#pgaT2|²AGTªÏðÐ&¢MÌÖEÌ`óÎgÂÎ¦ î\"d9+ÇÆ-àÖÆFð¸°RPË¦fcä";
      break;
    case "uk":
      $f = "%ÌÂ) h-ZÆù ¶h.ÚÊ h-Ú¬m ½h £ÑÄ& h¡#Ëº.(.<»h£#ñvÒÐ_´Ps94R\\ÊøÒñ¢h %¨ä²p	Nm¹ ¤ÄcØL¢¡4PÒá\0(`1ÆQ°Üp9\$¤ñÕü&;dHÃø5õ}QÄ\$¥öÑCÆäË©üZB¡	D8±ÄÚ(iÍyA~Gt(êÂy¢g£²Yã1~ÍÒ(ùëBd×¯Km®JI±\r.(²§èV­¼V1>#ãë\$:-ÀÇ÷r%CÎÇ´)/½ÕÐtép­^Ö\rðâ>[73'ÎòÑ6ªSP5dZ¤{îh>/Ñ ú¤êz0è)28Ë?Êvï(P|\"ùÀo¼¦­KBÚ\"i{* Äô Ä5Ï²¿:ã¹úÐ²¼H£ÈÓ8Þ£\"JB¸®Zè£(F)µÊZY(Â\$×&Y¦¬£ç6,«X\\¹NÛzÀ#¼æÑDZ²9«Ëª±)éÄµ+Å;DLh1(É3Ïë É(1@Ý·¬£lhQñÉ MHª>Kò X Äü!¨Ð°q Q&«ëß1ód3WÁH³\\Cº%PÑnTx®H«Ý\$´D-ü©h³äUÍ^5¬O²RÒ\" Ò\"9#:èôhÙÆGQ8mn#àNÝÃOåéÃ*# Ú4Ã(å&¿ÑÔ¤ç!r¬®Þ°õÛX_Ü¥ò0Ð\\kÜUsÉý;(ê~ìÌáàÂ\rÊ3¡Ð:æáxïÃ\rý`ApÞ9áxÊ7ãÂ9c¾v2á´Fn¹=,®@Mn;GBáà^0ÎjXÆî/Qâ ÃÑ¬+\rbe^V§8<\nÙÂvûxZnz	\n¶O[DÒ_q¼¤N¨õlÛiúûR¡Iò!7`PJ2biZBòüÊ«\rsÆ5ÏÕîhZ²Ú~²H(5hö¤|¿\$õ`K ÄDUDÈÎöÁ§':)Y:«%Ú<NÖÓÄÞ3u^ÜÞj\"µSe#YÖjê/ÜÂpfÉ<¼®ÊÇr\r°AS\0ZÞ¶|×)zãPËê8ÅÏFFV¢Túz\$ìÎ,bdÃV.5'EÚJ,4ôôVø@PS\n!1è1ÓhAñ>&I±¤F_¢:­Ð¢ÅnÅÉB\\«1%·ÖêßÎ!D¦áBI³UÄ¡Ä35b<NXðà+x[*S]HêaM2°[\nø×*oê¹\$+\$-hÑìjëì\"(ÙÜ(kíû¢0fÒ° dM- %JZPì&=­5Â] ÃL!q1§§ð¶M¬Éü´×2¸0II0ÇãÔEÍb!RQªôÂáÊ{\$©õæÉÉNÄ¤úÏqiÀèÁÛ%&AÙ@§ã)ÕYü1'FÞ[TÎ0uG\$ÿe±Bë ¥ÂÄ9¤ýøLDÑÝTEaI¹2V\\ÄÜ2È­Cà4&T'³5ÏM	øNÈQ«6&\$õ½6C@\npDj\r9zqCÓ¨èNÃ#;³DÔöCYí>¬ÌwõóÍ5	½mÐ©÷7ç\r!ÓwiÒ¨²ÐcsÀD9çGf4Á¥PO4àÏbu ¤]&WI¼&E¨äÎ4DI<MILØ°£Q\ný_ì9&yKÎ<æ8Ó2&HÉC*e¹3&hÍ«9glõðÈÃpa¦À4Elòh¯,Eð¬a)¿<FDIT2#+4åL	J+aUÕéÑ%D¨Jä/RÓlO+Nåh©a ½:íb?¹ª|Ä°;6ÔAe8+s%dì¥²Ö^ÌYwf¬Þ²3¦xÏ\0eÑ;ÐTIC¢PU5²jÚªéºê½Y¦iÖlÄ0KsçÑ.ØjBìiù_'Q¸x%UnèìD¤xµP²Íøiæ¨mó¾©'võ'fÁPOsªùRÙ»²ÂsÚ´ìQI²·ÉÈõC<Ê<;¾å¯y,Ì²BF,@*PP	@ã¡,Ý¡9\0 ­ÒO\nUì §1)\n ¬Ï\0ÕÔ_L^éA\\¯Eà;Î@'ù)ãÀ§Ñ|<]ö1,Jh©ÜyûéÆèá9ÁÈÁaê7È0£¥¹ue[r·Æ\04É¦¯T©Iª¨\06ï8É-CãDGg×#h2JSà!0¤Kgv6IÏ¼4·í.JmWGU¢öÈÍP1èdÞ>y¤kL&¦mo§F,\\R1¼ÇXÅý×	¿¦\$#uûuÌ¥PWÆÔ»Á¾rÄ¾B´ÒFÄ¥V&qYlZ¼GRDÉíÉÝS=ô}Ò2:B¨êÕsy¨ñxO\naQùÅuÑCaIÝW%bF ôÎÜ¯FImÓ£Èò­`ë\$¦ÞML´¶Â ÂúD¼ ¶¤9ÉÑ-ô¯²\$uiON\$Rf`BIÚ°F\nBhÁ£½äÿÎõîb4ápUBá´-u!TxeQTÝàÛê[LªL\"ùÎ7¾ô&àÐwBn3#mØª¾àÎð¾5ue­p)æEW#Eà[ÊF)? yü~÷	EY~¯­ÈD±ËÙ¾MÅÆúÔ3ëð±¾âÝì\$>»û%!«ß1²b³ÕIzuï}Þ\ny^qð½ÅLAÇLG\$Q&®7­jþÇµBZDßfÃ½Ê#¦×þ6;fà¥Fz>_¤ñ u-¶BÈKDäâ´)RÕ\nBª¤þðd¢EM®îyNViNCÊRÂa\" Fl(PÈäFÃFm0Oz hlAí\0I(àrmc¤Æ|¨~6ÀæfLº Ð\ràè6êP¢Ùè©ÄEÆÔþc»Ã#D&gÎ;\"z÷°Sù=ÇÔâÈ'ÎlÄOÖnÄ*ð¸nGóîD\$1¡ ÍÌ\\\n@¶0ñ/NC°/îÚÁ&2Ì[OXBDr14ý¢:D:qñÈÐ!DGÎT¥6\$^Ì¦&±*((««ìÞMÞc]\ríæÆw¾ZÉ\0DD§BÍ\n¦CF5ª9È(ÖÈºñ0YÏµ¯º/j¶WI.\"U\0\n¨ 	æßGníäJÙdªÊé\\Ë,Ävk*³ËNm-öt§,s\0^-ª4m¦ö'átt#u¥5aEQºùÌ z§ø(bhÊ& ;­ÉJ\0Cêy¦7Ê>1GW1ðóGCÂ	ã2©éï 	 ÌôYêÃ1ò!±÷!Ñýp¨ÚcÎÆ#	ôï!%Ç;\noç#RÂ²:ÏÑ&KÝ&&Ä«!Mn;©í&2P6TÁòÒ'6!©&¨Êbm°\$<r\0QÞ¤Vé\"F½Î¢Îwv²¾xâ¦,/ÈLîZÌô<!j=ê\nT®Ò{bPRÄÍáäf&èZÉ (¥ì*Ä>æ&°ÀÂdÂ¡ån0ÌR[ð.}md\\iRìD¤åq1ñ°HZH©§î*dÅ&Äpæ»|A)0ö%ï	vùÖùÊ!î#bÁBåQør6ÉË6nê¾Ïk]§à³u8ç6¤?90®po59³ª)¦ü<0\nÈã9g8o4M¯¸¶óZ³º=®æm¢Ý>Aç¬ÒzJ!@zpÌÿáx÷¢¯4ãø+C»p2´~/c\rðÖÄVò<òLqAB¯>\$Í\\Âwh2ñ>ÂÁ7A7ð(v½býC&ä`Þ\rê\r Ü ²\"ÏBFnò4d\\HrüÎs¬Ó¥3l\\\$úôrc4:G¯Z84øó-GåHY6sÄZÔ~YÐ	Gr;3¢èµto¡D£Kód\n<sKËULô=KApøM,\$\\4£:²â/\rî¥ähLòqEMÔ¶ólí¼ADÇ ÔôoÌ0ôúÔþ§x©«NôßPÓ£QîHôóNOd(g·PE(ïÒ:Ô´CÔ?*í´M [ìÏPÅGÔº¤a5dîkUs±ST@:µvíÅÛVpúõ	Jf«J±v@B<­´nòÀ¬@©Õez'Å ø¥ªãÊÙ¤ÜÒSDòµ¤¬õ©çáãþoÔ0±H4BÑH\\ÂËsÂªNÞÞq²5Ñ¨x­ZsÐe\n-4(®×cXõ}ÐÝ3P==G7°çCR^ÕÎ\"Ñ4\nFô'µmX1ûN1-d!iVÔz¨«p¦ouM4©W1wMHo_\n3)ÁeDSW@lÖydv-J¶´HµÇu]&ýfó!if¨Ýì>_Q3u²E©;&¯cçP1\$>o0ØÌl,·q¹.§>gÊ]õßR1fÄÃelh6ãTfãhZÏ·o-OTéS'ioÖs\n{èÐ}'\nv]Äµ7ö±\r[	qeqÑ	f×\$üöõ=\\V¬¾d^ãó­%ÌvaÓn¶erlÐAv«·gppñ®a­¶Gk`®IL6I7ÖrÓñ:üÁysö«y×z^wet´EUvìïuâf71iykkz2p×äI\"ªsih[Ý~QµUà#q¯1~Mw}77g]Å¡gÂ)LwÃqXW1a.*Tµt«}3¸%.ûwªpC QDÉ¶VGR« [1Ï_Âydy+DnZöx7fUW!J?\$÷	d?o÷'nUIFQ\$ó'U2¸cLóÓ?Q3v¶ÔOrRÙrf£øµ80½\$'E/ÐxÉpÓ¢H»U+~vPÖPóZ6ÒbC\r¾\",Ñ'¸Ø¿TíQByy,÷RP8ßU3d¢/P3pQr(+á¿Íö_2Ji~\rVî\n´Èq3ãB½D!P¸°Vëál-V·§hÓ®Vm?\$,\n ¨ÀZa\"2FlõI	\nLãSrEU/N¼!Âm¯),²¡%+GMn¾~ù³LÓb £¨ùÇµ3ç	ðæ:¡d^cx#QBDU5ÉTéÖ6¢)ÎÎæ×Ë×B­ï'*ò_XéRÀõM	0\"=,°[¡Ñ	4nFF*þ+ô×DH¼\r^±U;&|5l0ð>õX-9SÕZÔÃ	h_V§¦vT,¢ÎDu=:Y:Åo{ìÁ7K§z<n!Â6±ùú1©ZUÕéZÐe>ÜìOíòØV4],J³Ü0:â®O\\°zÜâõáD¸ð64x&è(ï­1ÜýZD#¦5÷DªHi¤Ù÷3÷ñ8tyM·±Të¤G`Fg*sü©¸©±ºnM¯êj=	´°<yFÚ|P¡££RÇõ2rGW²Øs3A=Ngþ/;S[V´þ";
      break;
    case "uz":
      $f = "%ÌÂ(a<\rÆäêk6LB¼Nl6Lp(a5Í1`äu<Ì'Aèi6Ì&á%4MFØ`æBÁá\"ÉØÔu2Kc'8è0cA¨Øn8'3A¼Üc4MÆsIè@k7Ï#ø**'®'3`(;M6,q&¤å¸ðÆ}Ä£+7áÌ7ÓÍþ:B:\rWÔ.3²b\r­ë4 êq×ß/Â|\0(¦a8è¶Ûò :`ð¹*æ{Vv´ËNü-Ço¹¹è÷æ³)æÅdgx¼i£wÐ7MçX('°bî%IÞyÕÄawu¤Ã:D°Ò5£¨ûðñÄ0K82cz(²ö­ì¼À£\n2ø#ÐØ¼CX³«:\$VL[<&¯{âºê\nn¢*2MÄ4¾7csXß¯#è%ct\$nÛü5Läñ P2­)s\n</sà½&c¨ìô¸«\$ÂÊ+*FÛ£í6ËÑÝ1LrÒ4BºÀCcò7Ã+ì¤@Sæ»Â Þ#ÊÎõ;ÂÈ\rÓhAPsS@t\rAID3Ð.P¬Å\"
0Ú7c2ÁèD49cæáxï_ÈÕ)6ÌÎÄ¡|9c»\"2á|-8áL4äýxÂ@/½·O2ló%:2ê=&KZ£TîÐÈ³\nùò,=&Cw;Ë¸È¼Áá÷~ßë²Ð!(Ø2±,'ãxÐùa8»&ªL<N:Dò\0ª9=Ô9+CÖ,#°è7¬è´ë9×;WÈìÐ¸Í:CFÌ`3C*d:\$Rà((Ý'#j2ÍFRën9L×\r!(:Ëº!* 7Ò@è]m;ü²f	\$£	\r6Ë7¯H&ÇJ¸ð¦( ê<¤ã8O<\rïJâ:û?Ð9%±!;'¶ÃÆ!`Á=ùÏÒFÏì§/SüÚ	Ñ)sÀ6¥ì¨õ%£##©}.ÌâK.u¸<!uHÈs·Qci©iíÄì\0004Û¶üI°@àæEÿÒôµa¦9ÃßÝ´íÁñ|ëÐ°Þã:üí*ÿÔDÌ¯reYñcG þ(Ù`S,¬3òY©#­±¾¼ R\rbÕ1ÌMò\\jM39Â¸_#5lø8¢fA±c{ÖBÒL}0NnD+,ÐùCúRa}Æ¢¿¨k\r\$DzÄxRJÏð ='ëR:¤},¤Â¢´bÏ2å%bhØC@V\nÉ¦«Un®UÚ½WáÝ`©2Ä±2È\rÀ¼2âVJòÑÇñÆ4z»W{òt¤\$3PNþHèS«2KÒjÉze&J¼@Ñ;8*³ÜÔTº{TÆS*Èà \n§EÅÏñ\\4Ùþ¾0êÃ©þ3ÊÄÖÇÕl®¢ºWù`,)(rXë\$2èIZO±²Y,Á×~RQµX©©BÊ°èÀsÆL#g\"ïmïÎI¡?dÁm9L!¼¦Ð¢\$Æ¶¢#sîðêvª)àbÈ#¡-(k©´jhIT*¤/	\r4(  (i.r|\nMhe,\$Â£tR]L+NáF¾øÂðÔ\n¢(Ð®*\"NJIYM¤ÀÇ\":}Y'qÕ©ÕHÎå\0êa>ß2QïláÉ#2J9-ó\$°Â@ÚvÃu_PZ±ÀÂF«DÅwL¦NH¥{\$4:Y@(^g*µE\\Wî_Ep43¨) 	(|Ëbøíñm,É¸¨q'Û~7ë-@kÙKaéÌðÑ]!\r\$ØÏPá-b!%rÌ(ð¦(k°{@¸:õÈÓdd¹ý?òzøË@nÔÍ3¹ì³n%åäì¹ÛIeÂ]Ó5Äbç\0F\nÜÈÃXÂC¸¦x9¦ÔÞ£q#ª^¾FRÍ)«ùb0'Erôchè,ÂHLÄÁ@y8C	\$)bX±p\ndì¤'bxLKO;ã ôÝø\n\nÏhÀêHuT¤ZÒ0£^di!\"Ç¥«§¦P¨®\\yA<ÿ3×M`°|VUÆæT\n\\5ØÓ·Ke\"IÒÚçêp	º»ÙÁPg÷#ËwlUtÕ2	ÃÌ\rOÓtÆXR\0HAÄ­ú`ùìHÔÍÖÀ*MN¢À¸|ÛA:/N¡'À)åtÃ~#],	it@PH]Á¼1¦I0ÌY[¢bhdÑ (96:\nêGÍU6;7FuÉQËÆíqÃÅpÔ\$ÁÜ]Þí¼¾/§LÂI\rÈ\$T1t®aÖÆb¦Ê§x*nùãg\r\$<<Ñèuè(géµ qgÆËï\n,%ÅE(ÅNNAA:G³ÜôàPS-@2KÛ¦uÊîi9ï7¤\0×Ot¯¼Yå¨2¶êd;Ä!K¤û¦g ¦½é¨;\$²0^S5faÉÄdRW·^Bj)\n£\n3f<cÙ}sËúR ÚºdEíÑ#¸Îäö»¯w{ÙwµÿÝL¹:ñÊà8²Nt¡c&xÛ¨¯ò]Ó¿2?:Ü\ríñÑw2a£yF?Æ.RXqº(Oû¤o\rî[3ºÀ¦ÄÜQ^Jª§ürû£³÷fy*&S}ÞPX2¦°ÎeÒAûû?¾ÚO/-ÃÐxoh\$Ý4íW1Â0Æè\"°ö\"gÂï¨wú±\$3íl&lôÿ¯î­/ ÝFÎïÏMxp/ÿb;çÈ\"_0(È~`l²âê\nftsé.&H`ÞÌ¸A@ô'ÚFøF\"eÀÜP\\ `ÚHj?ÃôÇÄðjH¦XÞMè¶°\"ô;BBL¤Ðd? Ü ¦êIL>¬~ÐlêÕ/õÌæç¬úÐ¤ ²l|>Lõ\nÐºvN0ÈPP¼t®Gçï\rÀ¦vQÏ^C°æupè6'&|Ë®fÁ®llÅÏöñNÎïMîu´.dP½±\nòñ±ï´J,8^>ÿ0ãn\r\rðØßE¬<áXðÐ&d7ÃX#-ü#CHeì{J¡bJÍÇ1	L²%¥D¯j:ÀDqhßñnÇø}\"ÄÞpH  ×CZG-66I:ê\"\\J¶¢(æ=êûÊ\rÎß±oæÒq2tv4æzÊFÜÅìÜ±Oø^­ÐlÐ³­ÎÉìõ qôèâÓB6%P\rÒçQ÷Qû\"2 Ä;\"ÇIrç2/ÂãÌÐ=`êiçýÅ0Íh\$rÙÊ2«r£½îipàÏõ&#÷2tæ±%#hÉìÞÐÛ!(À\rÒh Ühä¹¥é'òm*f{%%Â2«NIòjH§RÀ%ÒÅ(R»,ÒÂ?ò&ñò+ÀÃ,ä\n?ò.Ré-2@áÍ,qú\nÿ-S¼í(ç0²°=nR.\$û	'¬õ2.)+!1¥I0ò=n2ä2³>RÓ(C®<ãE-0G0ÒÞu3O4;ÌHÍ#ÄºçèàL-.~ÒpC³pNN3ù3~Lç7s0Ï@°f4ÙÄó2K «rBmÃ\"Âdnæ!¼8,ûÐ.ô¢;m6Oº83ÂC \r@×d4'®\0\n¿=²©ìÿóJÎÊ%³îÿ11Æ.ò(CJ5Âh`è0àV\"£²äl(`ª\n pf­t+ÝàM±Îì.ò§zwó°9?q6L%D·DÐ&xÏâ]\nôBB2#n3£¼	£N%þG248T\n4t0å\r6\$HÜÎF¶Üå®<k²P(Á->D¨<£n,\"l@Þo0ÇÈå¤»\rÑ]Ç6LÃ¤;MËjN`æ?ÅNôçC´ðï@àDô4RáOÍÖ6C§%ðH}1|»SXgÆ¬y@Ð:aÎyâú'\"vvÍÝ	5@Çðk½R§æ7>OØ] P¢GPåÓ2Ó\\0cü\$±¶=cî'Târ^EgU+¾sEBËU12>OÒéfwG,²¿-¬e)4#@äµº¥ â";
      break;
    case "vi":
      $f = "%ÌÂ(ha­\rÆqÐÐá] á®ÒÓ]¡Îc\rTnAjÓ¢hc,\"	³b5HÅØq 	Nd)	R!/5Â!PÃ¤A&n®&°0cA¨Øn8QE\r ÆÃY\$±Eyt9D0°Q(£¤íVh<&b°-Ñ[¹ºnoâ\nÓ(©U`Ô+½~Âda¬®æH¾8iDåµ\\¤PnÐÌpâu<Ä4k{¸C3	2Rum£´Þ]/ãtUÚ[­]á7;qöqwñN(¦a;m{\rB\n'îÙ»í_ÖÁ2[aTÜk7Îô)Èäo9HH¡Ä0c+Ô7£67 ê8Ä8@îüê@à¢¨» \\®ãj LÁ+@ÞÆ»Él7)vOIvL®ãÂ:IÈæ§èÚfakÂÃjcÐ]/ÄP!\0ÎÌdè!ª K P k¼<ËM\0ÎÃ\rêà@Äh4 A³N!c3(7\$ÈXÐb,(¤ëRÙ-2jÆ]ì2<¤!iJ NÃÆA1¨¡[¨(¡RÜf1B\"ÖË\rÜA¯°áZ8B< Ë&u=SI#qtI>Ê(¼0ÀP2\r®ÓëÀ°<9Áphå#ÇnÒãý\0@C¸Ò\rã­B%\n\0xÊ3¡Ð:æáxï{Ã\ra?/ÀÎ£p_pÜcÈIÐ|6¿´3?kø4Äáà^0É°2ºTû.ÌBED¯\",Ú9eÒÌÁ9)ª:Õ&Y^å\"ô·­;¢\nãä7ZH(J2/CÈè2S)Äc£s2©RÌ©¤éJÃVJ\"!7ÍØ\"]q¸ÙÃØ: V6ÅqJÄeJZ7k,2 J®ûGV\n¤5¸û½°HÆ3Q7tW£cÃ©VIë~;U²6Ãì¦4J4È¸íYfëæBÎF¶\n#©TÄ®é@¤-ÑRØÊ|[46'ýh¦(PñCcnÃ\r®¨¸)+çuÅß¢\$¸ã\n)C¸4{Úau!¹FTz~¨Øµ%ÛWôµhðUHÝ*]RûT¬ùÚsà/Ø5ðuëxÏ %ÐêÐJçaK¨:V~ä>Ï91C>h3Ï,Èè¤t~J6A±@Óð	þ²ÀÐÊ8nZ(£öëC)\r\$´\0 ²·`Ò\rá3*ÜIèh;¢§²\\Fµ BnEÊP.Ù)´èè]ú,\\¤ Q¤Òz^Qfi¡®.Hn2¡ªT¸·ò	3ÅxÐ3@­ @¯ÖWK1Ä@Òç¥u®ÕÞ¼Wõ^áÝ|¯¹4ó\0`A±4NÂô´\rÌLÂ¢B&Âé~KQúACü:G4óÄY3oäÄ¤EÒíK¸©uK\$t.¥Ø»ò^Ù|/©2}er_ìDØ	&ôñBIfmOd9SÌýCÙNa¡\"Èü[È¹{Hq1B@óÍX- ØÒÖIÐÊÑfhòMd,å ­ \r½'´C:FÔ±.©@¨Ûf¥Ô¡¤*.Ê]S¨Å×¦8½Êû¥uÂè[  'p&i¸	wÒD	%|Dé\"x®èÙw8 Òva g¤ë hb± Å>TÆy·ÄcHÆú°äMCYð´¤æÞ\\E	°j:è(Áë¾ÓÄÐ	!KiÀtA5zaH#X)f´±è1&³4Ð«±eÀ òªác©6i\0(É;(¡v(ÐMsrÐ§Ósíj-DÈ6úØBIÃ>è¦«mR­Èu\$\$0ó\$¯74B]×Q§@¨\$3s¥&å9+ÔÍ\0Õ¤+ú2¬|¦Ú Mø/\n<)HØw¦¨»s\$LôpjÄ\\âXKÝª´:M+úC3\$À¡Ð<RÑÕë3ñ~£Gr\"RH¶(Â!p*ÉÎ¶3dçÄÔÒEç«ª2tiÃ-¨²ÚÎb^f,Ü+N ä^EåGèB\$yé©Qêæ+²Â Äj½gF\$éX\rE¦)w8çôI)ã>²12Y;¹öË'ûìzzd³qv\"Ám(Y¸hDû âÓ±^h?ñ#±¬£­taL_(#/L\0Ó0 *:G04Uz³âøÙm(bþz´É\rPûLÜs¤ÙwYÞê\nÂ^#\$ø³7ÑZùÐ-ª-´õõ\\Ãg¦¤95Õ=âÓN\np%ÂÞÛÀ#6þPÒÙHz®gd2sÌ\r1Wµ<¶%B \"jQÅef¹·ñþBqV}(à*CHÍ^ùoñk-|ºÔ c	MupÖÞyÐ\nÉ§tj<kè§|&l|àç4+x¾¢iË,	ÉÖMÝ­(#¦ûJFUèDÊªÜkÉvjWÁP*#gX¬ù²Pu|w¥%Ó#ÃÃ\"xGÐ^\0T\r2I1Üµp,In5í¦F.VpIA*EåÝ4|ÕPY¢ÂxeòþeCJÿá^0jRÆR£±vä¯8²Öý£Á7!¥` îÈ+àÈ'èÅ{Çs¸¸?êp©]Î¨Åá´ý(ë´FoÂKÊ¨FíúOêKÿ´S£­ãð1DF\"¬»Ê//\nçLóOÃ\$°7\$ä ­èÍÎ`)'\n~Â*ëD¸ÕI\n¸ÌÂ6Í	².îd¬v6)¼ÔÍQÐFô#°zãÔ¢f¹ÁFÅ¸3ÆúÎ\rÎL So~â89Ð2§2ÃÔëÐéN-NÞ\0>â6'@~èBB¼t^\"b-qbÅMpÍä5\n°¾Ë¦®UÐFAÞ!vä\\8ÐKoE¬¤0jæïÛ\rÂ åFlàÚNY	2ÏGKD¦ÔpJü/8;Ð>H0\\ÛLàÓ±ÆídÔq&!Ð[	·P(H-T|g½ðo%çî\"íú|JU­äK/,÷ïØ>å¼XEËP{B.õ\"õCcÉÀNQÕq~æ±q0h8ã\\\$\"¸ÐãçÇÂ¡¤fº\"æ9£|pñ¶qN\\é/þQé¶¶ÅÊEd:ªj«.NÅÆ4ÊGñÆÉF<ÊÌ\$ìèmê·0(ìnHÑ£	­0cn\\Îqrz.ÐÀÎBã	¦fBQ¬¬e± J¦bfqQ\"2;\"±Nx|*ÿ\"Dé\";¬¤ìÆGÑ32cçRT9e'\$fÒy'ÐnÒvkrñväô¡N7¬8\$JâÌR!w\0ìÖn¢:T\\ È~­_ñhìri!b/,e'HF.Å-2{'¶øCzòÌWê8PíÒå(Ö÷má\r7íQb*9£TÌ<ÎÒ?	PòÂ%C ¶ípZ-I.³\"I¬í/Å3l¤Îð1ÑìäÙ-GÅ4Ëw'æ\\¼ñîí=(DØsî\riF@\nB`#Õ\n\nFB³hçSn@ÀÄ³tlÊVðD©6³7\"¼ç¦7Àle{®òÑ?N+(ä°Òsµ%ó-sºÕ3¿óÆésc<â¸yWNfìò:Ñ¤îàÏE;fZíïDQñÓe?®ãËFmêÃ©ÛQÆáÌ¢64çâ5cÝÙBezª6:w\"{\$£Öÿ:ÀÂÉëÀUtÿAwDO\"hÔ©-KàHÀCJ)\0-\n@ÉG­®IðÓº\"S*Gá;.ÌTÃ÷cHJ¥,)\0ª\n qp8¸\0JÏO(ÊG#cFW\$xç/Kó`æÞ¨2nS+1Ì/É¢kHqõ.´00Ô3bn6ÔcªÐ^dJWd+Xµ\"Øm*K ¨vÇpÏ.¡*Øë0a1mP#´FA/Ð`\$53\"ÒNFÔLv`àÔZÜõ5µDÌ«\$ã& @6õ^/ÉF_\$eþp4ÎTØðäPÜÌÀJl£ð7j¾<ÂElJôLOP¬YêZ\"V%¥NEEU35ÒÄêoRLnÔm\\ëÁro·V£vMKLµ\0ä0°»\nÔjðPa^0 ÛÊ*®:J§âU0ºpzÊE<íuÀ4IþÒ\$²:õN¼ \ràì> î­Æ1\$-N´\"ÚáAjí¬3¥q?ÅvÒfÜkªCD(`";
      break;
    case "zh":
      $f = "%ÌÂ:\$\nr.®ör/d²È»[8Ð S8r©NT*Ð®\\9ÓHH¤Z1!S¹VøJè@%9£QÉl]m	F¹U©*qQ;CÈf4ãÌu¨s¨UÎUt w¯à§:¥t\nr£îU:.:²PÇ.\r7d^%äu)c©xU`æF«©j»árs'PnÊAÌZEúfªº]£EúvitîUÊÙÎ»SëÕ®{Íîû¤ÓPõg5ÿ	EÂPýNå1	VÚ\n¢èW«]\n!z¿s¥Ôâ©ÎRºR¿ÄV×I:(¯s#.UzÎ @Ò:w'_²T\$ùpV¸LùÌD')bJ¬\$ÒpÅ¢©ñÊ[MZó\n.Á¨ñ>så±ÒKAZKåaLHAtF3ÙÊDË!zHµäâÐCé*reñÊ^K#´s¹ÎXg)<·åv×¬hòE')2í¿òAnrjÐú¾ä\n:ô1'+Ö²2izJ¸¯sÍ²à hÒ7£Ò]	9Hö½óN_Äes¸Kèû?	RY4=DÂF@4C(ÌC@è:tã½T3Ì÷>Ãxä3ã(Üæ9õÈIÀ|èÇ1B:LÎ\$=0!òtIÌE'5(ñÎRMy&sÄ#SEÍCHõÎ÷]Kª:KC%Ùum0ìKVÇ)\"EA(ÈCGAFpÜ&ÉèéfTY¸ÐCG)\0DõGSýW)\0^c¶­®TÇeíñwa D#¤8s*.]Ä\"h^§9zW#¤s\0]c¢¢9äa D©j<VÖí¾]2gâÅCû\$CCmî8)\"eüntI¥ãªÜ4÷}Ó|=3Q'¹'1Q,äg^ÛÖùnKÖsÄÐSLÌ&ÊÌeãÈ¤:Ï¿·³C3ß4ª;½íº.Ml`#@6£èS6::£Ü=Â9CòZð\\u£|!ãpÌ4ýËÞZ)¿M=ùÝùÏÆ?þpA\$Ó_ê§¹KzÐ<©O66¬¬Òt_Ñ¤No-6MÑùHX¤:+3¥.~É»Fbt\"Ö\$rÍ!b¼J±ÕP~®T]±'Ç@°Gôì¤ðàrâ¼[\0 Ç@\$Õ\$C1v+ÊSª}Pª5J©ÕJ«U°¡X+%h­xdx¡:°ñ2Â°_:X±%£¤PQÒ'Ea/1ir\"&¦B\rB±oÔMå ¤ÔÈ~²pO¸r§ò TJS*TÕb®*ÅY«UnCÀtVÁÎ'+uðÛâABV2>,ÞE\$è\$W1Ì,Àä\"rÁ,ùEø4H=³µãIî>ÈÅÑH9kdÉvdü\$?ãMÀ#§¦9#ø Å9t,Ç¨Ù4ËzZÊù) @@Pzè\"MQ-DV':¢HIAÃG\nTf,8o&Hs±_;0hDM#ÒbÊ¹{mRÍî NAr@¢àïaÌ(ä3³¸WÆT&(8¯LZ(Ò(ÄÑ-I\0PP£\n\"\0C\naH#.)(Û#gî8/IÜ\$V¡&.£FÁRî:²ÈâE=É9)%hhs\náj¾Ù;?c±a@ó+\\pÆ Z¢èÐ»ÐW*RdÈ±Q¸4G4åô\\Ñ¤\0Â£×RAOX¡cäÈH¸U÷át¬¬¢*³½ÑÒ!Yë?lôþ 	ÞEÏL#@ CÈ &	8E¶@OIy­\"â9+1d²Ï£¢R	cÄÐ(úYâäð¨P*[sÂ E	ê¢a/WEª`LS¸A/ÄÇD¨¿MB¹sÂFôÀQB.QwÜEß¢È)ÅÙÍ0tÞË7gÊÌ2þÅµ¦èÖÎ\rnîaÀá#/ÄØç-ó/)Ü&ÙdFð@&g»@DCOA³Ì^Ê|Ôè\n|J¼çðêIÅaÌås4GEÊ¹nÙ3+çÈn<­ArV\n9Á07ªÐÊÀÃl¡=ãÀæÔì\r¼:1Ñ;>åt/ÒvJåÖ»Nx§]ç¯&h(jp4 \\ç|òGEÐ,mE\nÞè·I¶pÜa(Ðh¡5ãÀ§b`eIiLâ¿DxKGo\n¥¨5[-l]Á4:D¾\nAÌäê&k4âpHÃ=')À.o¸IÊê@Â@æ<@Q½¦+óÃ<Be°0^6è Dwk)±6àÐQK°-Ä(DdîéXY7'£m# Q66wHÝÆD¸îXó1Ø²ßóhÅð½àÇL«W¾fyäD'ÖÀoÁVôÓÚ1btR±1UoRÆ£A\\2.QoÅE¸ØØÎ	ævÛh[Q[k6b¨­åÁ¶Ï)hË°G¢ÑÌ.%/è¾5ÑÒ õ&9g6÷ÌÃ]ø93]wB`qe}dÈ?°u¬¤h{6{^Ã¶¡Q±{¡47<ÌÇTdL×ÎRì&[O+\\¸ñZµ0FZ§r]9ø^Tâ9ç«ñÏ-ÐÝb¶áÐ4ÂAEîÙKàîë=âö¢O³j_wÜEþ|÷ý×Âà¼Ôß}ïws+ÝÐ|ëæÏÍçèX3ÿ®µâjüzýcµ»XÚãÛÓ{ß´mÇøóôèÿ±o_'Q%44%#yËf\$|Q©Á V²ÏDIå.(¢°ÿ-a\"¯ü&pQª6;ÆjÏ#v!!(Ï)\0ëd7D.ì¸íÐ¡so -6¶OøöO Íäüã|ýïÊùG4ÎLà/PatØ2ùï^\r~'Ðt^Ð#é	%Ó	æ±Áu\nÐmÂØJv\\a:J¡^oÁ\\ÝH.L\"é4¡Î­¹N¶Áb×l6ûOÖ^âû&	­Z°ä×¯¦Ð¤'*eN)j¦×ùqemJq3:a=\"Ý\rÏÐhOµ0ý0ÿ£Ë'-KqHíÃõQ\0Ï)%íí\"Ò`ÖSet\0Ä¥l\0È@Þ\0àÊçm)ÇM1v'ÐO±Q ÑñvlÀ­2~1M6Ó ÖöPÔêÔpê]1ÄÔ1Èø­íR4FñU¡NÕTçFD°±ÑRØáo¬ûW î	tÒ@Èûæi	øÅ#\$-tìÝÃ>F-xãÃ^Å.¸FÐ§øÉ,w#\"J8íMäïÈÞâî>,¡gÒ\rv©~æpá 3ïF)´ú âÐ\$P(C´\n ¨ÀZhÄÊàOÂÞ¥C¢!¬dã ä¥åªúH.!î¦6Ã¬¢°6¢øÖ\rd°féüdgÊ³Ð¦T3\n²ÂÑ.ÒðÊäª°Ì#¡ä\\JÖöN¢ÊÔÂêìÒîôï1À6Ó-L3o2ÍÓ\"©êþðp-4*2fLï/únþ²ÁÒ½+Ö,2ó³f½At½,ÄFE®&ö%´ÎÀ¬ Æ ê\r¨Ä!@¡,#¡G1Àñ%ÈÐLö\$\$Å1³9á{2%A\nç©J÷sÁ<K÷;®9áL>Á7:qLCo»6°>(+±ìD\\À";
      break;
    case "zh-tw":
      $f = "%
ÌÂ:\$\ns¡.eUÈ¸E9PK72©(æP¢h)Ê@º:i	Æaè§Je åR)Ü«{º	Nd(ÜvQDCÑ®UjaÊTOABÀPÀb2£a¸àr\nr/TuéÊ®M9Rèçzñ?T×Èò9>åS¢ÁNeIÌDºhw2Y2èPÒcº¡Ð¼WÜÒË*=sºÝï7»íýB¥9J¥Úñ\"X¹Qê÷2±æM­/«J2å@\"ïWËör¡TDÄ{u¼©ëãtsápøÎîÁÕãSÐô\\=\0çV¡«ôïp­\"RÕ )ÐªOHêýÎT\\Ó§:}JéF+üêJVÏ*rEZs!Z¥y®éVê½¯yPê¤A.ÈyZë6YÌIÁ)\ns	ÎZÈæÌ¢ÊÊ[¹Ê2Ì¥ÂK®d¹J»ç12A\$±&¤ºY+;ZY+\$j[GAnæ%ò²J½sàt)ÒPÇ)<¹?Ëô\0Uåw*Áx].ê2ø¥Áft+<KdÊ×À(A2]£å*æX!rBô\n# Ú4Ã(ätE\rl	Tr¤{:ÉOpbJBOó:ÊF@4C(ÌC@è:tã½4)KÃxä3ã(Üæ9öPÈIÐ|t(¡B1Ö¯åBã|¥\$	qóø/ç9H]DäÌ¸»et\\¢¥ÂK6×íÿ?à®LQÙ\\ó¥1ÊH@PJ2ò:¡@æ®ea	&ÞsÅ2ÙÐSo1Qd­Û0±×3M¶eÛwd:<C§)xGÞdÙrBæHäreÙÌBòiÎ^áç1I@\"Z¡ÅP@fg1pMä	j^°B Ê<ÒJðLf*É8¬3(Ú°:Ñsú ¦(&^ê)ÌDr\näæ·1(\\Õ´	È_ßÜ¾Àß7ß&]ß>Tt7ç34·ÕøÒ¥r<üÑç/hCH÷HsôÛû,øõo7|Ûu+wxbÃ@6£ãXÛU¨è:Cp@0úÃä9#ÍÑuÀW 9ÂÞ7ÃHÏê¾ø}¤Uðå1	~1KTÿðNúÏåôFQ8)HTK°PØs3c¤M#& ºÅ)F¨Gp ÙøÀXa*%Í)F«´G4«ÇB',a\naÒ\$\n0¾¤CÕ¸)È%â)ÿ(çpfD °N´*R¡Ê:a\0àÄ(®&ÉÀD	`\$U³VªÝ\\«µz¯Ö\nÃX±Id,¥³xd|á:Î¶ðæ«èVâÊ\"@¢XCt ¦	e/¸¥0Ê´ã³ÖEâÊ#Èª¸áé«ª¥õd¢pOØÂ­²¸WJñ_,Ö\"Æk%e¬ÕÛØànrEl-¨b (±¯¹üf\0CG@®e}Á¨!]°²>]¾w/Òð U¿Ô\$fEh¤Câ5½1QÌäB±'!!Î&Ñ°9sa\"ÄÔCQ>-Ì0d\$ÀTD2(2xDµvÔû¬\0 \rô\$BGòu\"¤ê#äÑFÖ*D°M?!PD*¹ê\$G@­&dÍtl!E*«U)õ\r\$8lG9iÂ^*õÈhÐ£L\nòf#@A\n/Ï8F[(¦4ZjI\0C\naH#\0èÃZÎ'²þ)ã£Eá­\$&ra\\E²^0´\nG0®°\\CÂk=à&Èµ×4t§X EP,S!Ì'áBbàS*rñ?ôQXáÊ¼W©~9ã¤KÄ\"G4á¤N\"xS\nVEFLIëfdÎ;è&³/ÁU³ÀÎià¨ª\r@û50T\n¬;&ÉÜ8L%ÔDÞG*sªòpÁ,9ô6¢Ô¹BLÒpO	À*\0B EX@.	d,^1 #G9##Â=Á£V0Ósn¢é»³¶ba:Xì¢VAEÙX/ODÉå Òê»äÎqòhTñãØ»Á®íÞE\0*Ï0£H ±P\"Úì`³\$Ó£R\"Ä\0æ¥Ê\0RÁ]ÅaeP\0ÖøYEh½D4n¢¨A¢ÞjÎÕÔÕq£ê]ÿ	9ÓâbLÚ!\"¦¢¹J%ÜE<¡07¬PÊÆl¡J0ÃµYÁÌ4ðèB+-gU\rÐ:×EH©,£bæ*¦`ádæ\n9Å ¢hIaÍ¹Ñs°6¼Øäs¡ Br\$.X\\â0)/½ù7ì0ÐCkÓÁ\rZÇ@ÊßÜ'Sñ|S¦L6[·w§5¢T]&_'^#h?RÅãBhg,¦N¨»ÚôÊ»§J\"}ÐÆJyÑ,C	û-Ë|`øà¥ãÕà÷óð~Qåbà,Æ(	ôñà@ÂZ¯åtÑ!qz,GHædÈÚq\"ãØ¤ªúxÃGËxfâ5<|ÜuQ¼ÉrÜÓ/òTD/.·%%´9'!è@É¿}EñÓ½âq9\r%\0½ÉÃ¾oÌ\"3·Læ´³ñuÉÑðîV\"<·×¢öñçäý#N LNÀ¹½L!1|TðOa¢ºI÷)ºx'OF Õ¶;ëýõF(O{öfþ²îò:|¯«â0Ø«ÿçSm÷ÕpkáÛ°ÐøfÊhT\nÖs¤O\"*ç£ßFÉì¿»ù~ì?÷ñk#ßIÎ/ýØh8Ø-KäÂþ'ÌÓ@ÚEænì#ÏºÃGVÈopÈÏÏql>uotÉnOúg<È!Wp&Nx°6ÞPNÕÂâ÷P@s\rXuXh.ì à²FlñC.çN`2ðzçîèoùr¾Ëð¿AkÏúrðÛK÷.\n0­ÙÈ¡\$9\r¢^BÁÊ0>ã\$éâ«Äÿ\0NUéÐø\r¤É´¨Áp²J\0M8\"ÂLU°íÂÛÛkùPÀÐ¬¸÷¬1ÃJÂp^`&6ÐHÒQ4×Q9íåív6îw<×6×\$ÒNjm	Íî\nQb0p_NAñxrBà.GB¢ÎÁ^hÐ+áB^£~(¯ö7pKN\rEþ(!Ë.±²Êæº°!ÊhÚ0%âQzËÌhqÛ#yÂL\"'\rÏã£ÈË\$-\nèÐ àÒQÇÞR0ñDáòM!ñâþäÛÍÀ\refZF2¥\0È@Þ\0àÒçÃ#MÃ#§R@{(RÒPÛ­Õ#Ñ#Ò]\$-B­ÌÂ2bMÑ&²¦VÝÁ2à\$ç G0ÞÞý(íëÐ_*RÞÐ9­)Câò\$HqyLr¤¶K²,\r,Ò#ä#,¡\n)@ZM¾.aÌãLwAD<^ë¢²Hi¨	+N³Ï*?¡H^!F¢¡bêä:!k1,åd:øÏhä.3!¡¡zà\0Ø:R:Ã¨ã^6&rIjäY-\0ª\n p9'PO°6vêê¦B!§g¥¸J0bÚæ¶¡ÐôR9d²Ã1\rôu°7CÏÅ\n÷\\^SÈ¨\r ä²Ht:ÓØqìÂ/B´¢lÅ-/ù!Ð¡Æö#§Níálî3\"÷Oã2aTa`bûGQAuaÌi#Íü./ÌE¯þÙTNÄJ*ñá, îÈæª&Ê¨þ\nÀÂ`ê ÚSÚ++ÍBmnNBý>ÙíRô³òw3û?ìn¥D0J,j2#ÌÁ&rrBÒðN#ÂÁâÁ+ÄÁL";
      break;
  }
  $Ni = array();
  foreach (explode("\n", lzw_decompress($f)) as $X) $Ni[] = (strpos($X, "\t") ? explode("\t", $X) : $X);
  return $Ni;
}
abstract
class
SqlDb
{
  static $he;
  var $extension;
  var $flavor = '';
  var $server_info;
  var $affected_rows = 0;
  var $info = '';
  var $errno = 0;
  var $error = '';
  protected $multi;
  abstract
  function
  attach($N, $V, $F);
  abstract
  function
  quote($Q);
  abstract
  function
  select_db($Nb);
  abstract
  function
  query($H, $Xi = false);
  function
  multi_query($H)
  {
    return $this->multi = $this->query($H);
  }
  function
  store_result()
  {
    return $this->multi;
  }
  function
  next_result()
  {
    return
      false;
  }
}
if (extension_loaded('pdo')) {
  abstract
  class
  PdoDb
  extends
  SqlDb
  {
    protected $pdo;
    function
    dsn($mc, $V, $F, array $Pf = array())
    {
      $Pf[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_SILENT;
      $Pf[\PDO::ATTR_STATEMENT_CLASS] = array('Adminer\PdoResult');
      try {
        $this->pdo = new
          \PDO($mc, $V, $F, $Pf);
      } catch (\Exception $Hc) {
        return $Hc->getMessage();
      }
      $this->server_info = @$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);
      return '';
    }
    function
    quote($Q)
    {
      return $this->pdo->quote($Q);
    }
    function
    query($H, $Xi = false)
    {
      $I = $this->pdo->query($H);
      $this->error = "";
      if (!$I) {
        list(, $this->errno, $this->error) = $this->pdo->errorInfo();
        if (!$this->error) $this->error = lang(21);
        return
          false;
      }
      $this->store_result($I);
      return $I;
    }
    function
    store_result($I = null)
    {
      if (!$I) {
        $I = $this->multi;
        if (!$I) return
          false;
      }
      if ($I->columnCount()) {
        $I->num_rows = $I->rowCount();
        return $I;
      }
      $this->affected_rows = $I->rowCount();
      return
        true;
    }
    function
    next_result()
    {
      $I = $this->multi;
      if (!is_object($I)) return
        false;
      $I->_offset = 0;
      return @$I->nextRowset();
    }
  }
  class
  PdoResult
  extends
  \PDOStatement
  {
    var $_offset = 0, $num_rows;
    function
    fetch_assoc()
    {
      return $this->fetch_array(\PDO::FETCH_ASSOC);
    }
    function
    fetch_row()
    {
      return $this->fetch_array(\PDO::FETCH_NUM);
    }
    private
    function
    fetch_array($jf)
    {
      $J = $this->fetch($jf);
      return ($J ? array_map(array($this, 'unresource'), $J) : $J);
    }
    private
    function
    unresource($X)
    {
      return (is_resource($X) ? stream_get_contents($X) : $X);
    }
    function
    fetch_field()
    {
      $K = (object)$this->getColumnMeta($this->_offset++);
      $U = $K->pdo_type;
      $K->type = ($U == \PDO::PARAM_INT ? 0 : 15);
      $K->charsetnr = ($U == \PDO::PARAM_LOB || (isset($K->flags) && in_array("blob", (array)$K->flags)) ? 63 : 0);
      return $K;
    }
    function
    seek($D)
    {
      for ($t = 0; $t < $D; $t++) $this->fetch();
    }
  }
}
function
add_driver($u, $C)
{
  SqlDriver::$gc[$u] = $C;
}
function
get_driver($u)
{
  return
    SqlDriver::$gc[$u];
}
abstract
class
SqlDriver
{
  static $he;
  static $gc = array();
  static $Pc = array();
  static $re;
  protected $conn;
  protected $types = array();
  var $insertFunctions = array();
  var $editFunctions = array();
  var $unsigned = array();
  var $operators = array();
  var $functions = array();
  var $grouping = array();
  var $onActions = "RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";
  var $inout = "IN|OUT|INOUT";
  var $enumLength = "'(?:''|[^'\\\\]|\\\\.)*'";
  var $generated = array();
  static
  function
  connect($N, $V, $F)
  {
    $g = new
      Db;
    return ($g->attach($N, $V, $F) ?: $g);
  }
  function
  __construct(Db $g)
  {
    $this->conn = $g;
  }
  function
  types()
  {
    return
      call_user_func_array('array_merge', array_values($this->types));
  }
  function
  structuredTypes()
  {
    return
      array_map('array_keys', $this->types);
  }
  function
  enumLength(array $n) {}
  function
  unconvertFunction(array $n) {}
  function
  select($R, array $M, array $Z, array $vd, array $Rf = array(), $_ = 1, $E = 0, $Gg = false)
  {
    $me = (count($vd) < count($M));
    $H = adminer()->selectQueryBuild($M, $Z, $vd, $Rf, $_, $E);
    if (!$H) $H = "SELECT" . limit(($_GET["page"] != "last" && $_ && $vd && $me && JUSH == "sql" ? "SQL_CALC_FOUND_ROWS " : "") . implode(", ", $M) . "\nFROM " . table($R), ($Z ? "\nWHERE " . implode(" AND ", $Z) : "") . ($vd && $me ? "\nGROUP BY " . implode(", ", $vd) : "") . ($Rf ? "\nORDER BY " . implode(", ", $Rf) : ""), $_, ($E ? $_ * $E : 0), "\n");
    $Xh = microtime(true);
    $J = $this->conn->query($H);
    if ($Gg) echo
    adminer()->selectQuery($H, $Xh, !$J);
    return $J;
  }
  function
  delete($R, $Pg, $_ = 0)
  {
    $H = "FROM " . table($R);
    return
      queries("DELETE" . ($_ ? limit1($R, $H, $Pg) : " $H$Pg"));
  }
  function
  update($R, array $O, $Pg, $_ = 0, $Ah = "\n")
  {
    $qj = array();
    foreach (
      $O
      as $y => $X
    ) $qj[] = "$y = $X";
    $H = table($R) . " SET$Ah" . implode(",$Ah", $qj);
    return
      queries("UPDATE" . ($_ ? limit1($R, $H, $Pg, $Ah) : " $H$Pg"));
  }
  function
  insert($R, array $O)
  {
    return
      queries("INSERT INTO " . table($R) . ($O ? " (" . implode(", ", array_keys($O)) . ")\nVALUES (" . implode(", ", $O) . ")" : " DEFAULT VALUES") . $this->insertReturning($R));
  }
  function
  insertReturning($R)
  {
    return "";
  }
  function
  insertUpdate($R, array $L, array $G)
  {
    return
      false;
  }
  function
  begin()
  {
    return
      queries("BEGIN");
  }
  function
  commit()
  {
    return
      queries("COMMIT");
  }
  function
  rollback()
  {
    return
      queries("ROLLBACK");
  }
  function
  slowQuery($H, $_i) {}
  function
  convertSearch($v, array $X, array $n)
  {
    return $v;
  }
  function
  convertOperator($Lf)
  {
    return $Lf;
  }
  function
  value($X, array $n)
  {
    return (method_exists($this->conn, 'value') ? $this->conn->value($X, $n) : $X);
  }
  function
  quoteBinary($oh)
  {
    return
      q($oh);
  }
  function
  warnings() {}
  function
  tableHelp($C, $pe = false) {}
  function
  hasCStyleEscapes()
  {
    return
      false;
  }
  function
  engines()
  {
    return
      array();
  }
  function
  supportsIndex(array $S)
  {
    return !is_view($S);
  }
  function
  checkConstraints($R)
  {
    return
      get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME
WHERE c.CONSTRAINT_SCHEMA = " . q($_GET["ns"] != "" ? $_GET["ns"] : DB) . "
AND t.TABLE_NAME = " . q($R) . "
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'", $this->conn);
  }
  function
  allFields()
  {
    $J = array();
    foreach (
      get_rows("SELECT TABLE_NAME AS tab, COLUMN_NAME AS field, IS_NULLABLE AS nullable, DATA_TYPE AS type, CHARACTER_MAXIMUM_LENGTH AS length" . (JUSH == 'sql' ? ", COLUMN_KEY = 'PRI' AS `primary`" : "") . "
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = " . q($_GET["ns"] != "" ? $_GET["ns"] : DB) . "
ORDER BY TABLE_NAME, ORDINAL_POSITION", $this->conn) as $K
    ) {
      $K["null"] = ($K["nullable"] == "YES");
      $J[$K["tab"]][] = $K;
    }
    return $J;
  }
}
add_driver("sqlite", "SQLite");
if (isset($_GET["sqlite"])) {
  define('Adminer\DRIVER', "sqlite");
  if (class_exists("SQLite3") && $_GET["ext"] != "pdo") {
    abstract
    class
    SqliteDb
    extends
    SqlDb
    {
      var $extension = "SQLite3";
      private $link;
      function
      attach($p, $V, $F)
      {
        $this->link = new
          \SQLite3($p);
        $tj = $this->link->version();
        $this->server_info = $tj["versionString"];
        return '';
      }
      function
      query($H, $Xi = false)
      {
        $I = @$this->link->query($H);
        $this->error = "";
        if (!$I) {
          $this->errno = $this->link->lastErrorCode();
          $this->error = $this->link->lastErrorMsg();
          return
            false;
        } elseif ($I->numColumns()) return
          new
          Result($I);
        $this->affected_rows = $this->link->changes();
        return
          true;
      }
      function
      quote($Q)
      {
        return (is_utf8($Q) ? "'" . $this->link->escapeString($Q) . "'" : "x'" . first(unpack('H*', $Q)) . "'");
      }
    }
    class
    Result
    {
      var $num_rows;
      private $result, $offset = 0;
      function
      __construct($I)
      {
        $this->result = $I;
      }
      function
      fetch_assoc()
      {
        return $this->result->fetchArray(SQLITE3_ASSOC);
      }
      function
      fetch_row()
      {
        return $this->result->fetchArray(SQLITE3_NUM);
      }
      function
      fetch_field()
      {
        $d = $this->offset++;
        $U = $this->result->columnType($d);
        return (object)array("name" => $this->result->columnName($d), "type" => ($U == SQLITE3_TEXT ? 15 : 0), "charsetnr" => ($U == SQLITE3_BLOB ? 63 : 0),);
      }
      function
      __destruct()
      {
        $this->result->finalize();
      }
    }
  } elseif (extension_loaded("pdo_sqlite")) {
    abstract
    class
    SqliteDb
    extends
    PdoDb
    {
      var $extension = "PDO_SQLite";
      function
      attach($p, $V, $F)
      {
        $this->dsn(DRIVER . ":$p", "", "");
        $this->query("PRAGMA foreign_keys = 1");
        $this->query("PRAGMA busy_timeout = 500");
        return '';
      }
    }
  }
  if (class_exists('Adminer\SqliteDb')) {
    class
    Db
    extends
    SqliteDb
    {
      function
      attach($p, $V, $F)
      {
        parent::attach($p, $V, $F);
        $this->query("PRAGMA foreign_keys = 1");
        $this->query("PRAGMA busy_timeout = 500");
        return '';
      }
      function
      select_db($p)
      {
        if (is_readable($p) && $this->query("ATTACH " . $this->quote(preg_match("~(^[/\\\\]|:)~", $p) ? $p : dirname($_SERVER["SCRIPT_FILENAME"]) . "/$p") . " AS a")) return !self::attach($p, '', '');
        return
          false;
      }
    }
  }
  class
  Driver
  extends
  SqlDriver
  {
    static $Pc = array("SQLite3", "PDO_SQLite");
    static $re = "sqlite";
    protected $types = array(array("integer" => 0, "real" => 0, "numeric" => 0, "text" => 0, "blob" => 0));
    var $insertFunctions = array();
    var $editFunctions = array("integer|real|numeric" => "+/-", "text" => "||",);
    var $operators = array("=", "<", ">", "<=", ">=", "!=", "LIKE", "LIKE %%", "IN", "IS NULL", "NOT LIKE", "NOT IN", "IS NOT NULL", "SQL");
    var $functions = array("hex", "length", "lower", "round", "unixepoch", "upper");
    var $grouping = array("avg", "count", "count distinct", "group_concat", "max", "min", "sum");
    static
    function
    connect($N, $V, $F)
    {
      if ($F != "") return
        lang(22);
      return
        parent::connect(":memory:", "", "");
    }
    function
    __construct(Db $g)
    {
      parent::__construct($g);
      if (min_version(3.31, 0, $g)) $this->generated = array("STORED", "VIRTUAL");
    }
    function
    structuredTypes()
    {
      return
        array_keys($this->types[0]);
    }
    function
    insertUpdate($R, array $L, array $G)
    {
      $qj = array();
      foreach (
        $L
        as $O
      ) $qj[] = "(" . implode(", ", $O) . ")";
      return
        queries("REPLACE INTO " . table($R) . " (" . implode(", ", array_keys(reset($L))) . ") VALUES\n" . implode(",\n", $qj));
    }
    function
    tableHelp($C, $pe = false)
    {
      if ($C == "sqlite_sequence") return "fileformat2.html#seqtab";
      if ($C == "sqlite_master") return "fileformat2.html#$C";
    }
    function
    checkConstraints($R)
    {
      preg_match_all('~ CHECK *(\( *(((?>[^()]*[^() ])|(?1))*) *\))~', get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = " . q($R), 0, $this->conn), $Re);
      return
        array_combine($Re[2], $Re[2]);
    }
    function
    allFields()
    {
      $J = array();
      foreach (tables_list() as $R => $U) {
        foreach (fields($R) as $n) $J[$R][] = $n;
      }
      return $J;
    }
  }
  function
  idf_escape($v)
  {
    return '"' . str_replace('"', '""', $v) . '"';
  }
  function
  table($v)
  {
    return
      idf_escape($v);
  }
  function
  get_databases($gd)
  {
    return
      array();
  }
  function
  limit($H, $Z, $_, $D = 0, $Ah = " ")
  {
    return " $H$Z" . ($_ ? $Ah . "LIMIT $_" . ($D ? " OFFSET $D" : "") : "");
  }
  function
  limit1($R, $H, $Z, $Ah = "\n")
  {
    return (preg_match('~^INTO~', $H) || get_val("SELECT sqlite_compileoption_used('ENABLE_UPDATE_DELETE_LIMIT')") ? limit($H, $Z, 1, 0, $Ah) : " $H WHERE rowid = (SELECT rowid FROM " . table($R) . $Z . $Ah . "LIMIT 1)");
  }
  function
  db_collation($k, $jb)
  {
    return
      get_val("PRAGMA encoding");
  }
  function
  logged_user()
  {
    return
      get_current_user();
  }
  function
  tables_list()
  {
    return
      get_key_vals("SELECT name, type FROM sqlite_master WHERE type IN ('table', 'view') ORDER BY (name = 'sqlite_sequence'), name");
  }
  function
  count_tables($j)
  {
    return
      array();
  }
  function
  table_status($C = "")
  {
    $J = array();
    foreach (get_rows("SELECT name AS Name, type AS Engine, 'rowid' AS Oid, '' AS Auto_increment FROM sqlite_master WHERE type IN ('table', 'view') " . ($C != "" ? "AND name = " . q($C) : "ORDER BY name")) as $K) {
      $K["Rows"] = get_val("SELECT COUNT(*) FROM " . idf_escape($K["Name"]));
      $J[$K["Name"]] = $K;
    }
    foreach (get_rows("SELECT * FROM sqlite_sequence" . ($C != "" ? " WHERE name = " . q($C) : ""), null, "") as $K) $J[$K["name"]]["Auto_increment"] = $K["seq"];
    return $J;
  }
  function
  is_view($S)
  {
    return $S["Engine"] == "view";
  }
  function
  fk_support($S)
  {
    return !get_val("SELECT sqlite_compileoption_used('OMIT_FOREIGN_KEY')");
  }
  function
  fields($R)
  {
    $J = array();
    $G = "";
    foreach (get_rows("PRAGMA table_" . (min_version(3.31) ? "x" : "") . "info(" . table($R) . ")") as $K) {
      $C = $K["name"];
      $U = strtolower($K["type"]);
      $l = $K["dflt_value"];
      $J[$C] = array("field" => $C, "type" => (preg_match('~int~i', $U) ? "integer" : (preg_match('~char|clob|text~i', $U) ? "text" : (preg_match('~blob~i', $U) ? "blob" : (preg_match('~real|floa|doub~i', $U) ? "real" : "numeric")))), "full_type" => $U, "default" => (preg_match("~^'(.*)'$~", $l, $B) ? str_replace("''", "'", $B[1]) : ($l == "NULL" ? null : $l)), "null" => !$K["notnull"], "privileges" => array("select" => 1, "insert" => 1, "update" => 1, "where" => 1, "order" => 1), "primary" => $K["pk"],);
      if ($K["pk"]) {
        if ($G != "") $J[$G]["auto_increment"] = false;
        elseif (preg_match('~^integer$~i', $U)) $J[$C]["auto_increment"] = true;
        $G = $C;
      }
    }
    $Rh = get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = " . q($R));
    $v = '(("[^"]*+")+|[a-z0-9_]+)';
    preg_match_all('~' . $v . '\s+text\s+COLLATE\s+(\'[^\']+\'|\S+)~i', $Rh, $Re, PREG_SET_ORDER);
    foreach (
      $Re
      as $B
    ) {
      $C = str_replace('""', '"', preg_replace('~^"|"$~', '', $B[1]));
      if ($J[$C]) $J[$C]["collation"] = trim($B[3], "'");
    }
    preg_match_all('~' . $v . '\s.*GENERATED ALWAYS AS \((.+)\) (STORED|VIRTUAL)~i', $Rh, $Re, PREG_SET_ORDER);
    foreach (
      $Re
      as $B
    ) {
      $C = str_replace('""', '"', preg_replace('~^"|"$~', '', $B[1]));
      $J[$C]["default"] = $B[3];
      $J[$C]["generated"] = strtoupper($B[4]);
    }
    return $J;
  }
  function
  indexes($R, $h = null)
  {
    $h = connection($h);
    $J = array();
    $Rh = get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = " . q($R), 0, $h);
    if (preg_match('~\bPRIMARY\s+KEY\s*\((([^)"]+|"[^"]*"|`[^`]*`)++)~i', $Rh, $B)) {
      $J[""] = array("type" => "PRIMARY", "columns" => array(), "lengths" => array(), "descs" => array());
      preg_match_all('~((("[^"]*+")+|(?:`[^`]*+`)+)|(\S+))(\s+(ASC|DESC))?(,\s*|$)~i', $B[1], $Re, PREG_SET_ORDER);
      foreach (
        $Re
        as $B
      ) {
        $J[""]["columns"][] = idf_unescape($B[2]) . $B[4];
        $J[""]["descs"][] = (preg_match('~DESC~i', $B[5]) ? '1' : null);
      }
    }
    if (!$J) {
      foreach (fields($R) as $C => $n) {
        if ($n["primary"]) $J[""] = array("type" => "PRIMARY", "columns" => array($C), "lengths" => array(), "descs" => array(null));
      }
    }
    $Vh = get_key_vals("SELECT name, sql FROM sqlite_master WHERE type = 'index' AND tbl_name = " . q($R), $h);
    foreach (get_rows("PRAGMA index_list(" . table($R) . ")", $h) as $K) {
      $C = $K["name"];
      $w = array("type" => ($K["unique"] ? "UNIQUE" : "INDEX"));
      $w["lengths"] = array();
      $w["descs"] = array();
      foreach (get_rows("PRAGMA index_info(" . idf_escape($C) . ")", $h) as $nh) {
        $w["columns"][] = $nh["name"];
        $w["descs"][] = null;
      }
      if (preg_match('~^CREATE( UNIQUE)? INDEX ' . preg_quote(idf_escape($C) . ' ON ' . idf_escape($R), '~') . ' \((.*)\)$~i', $Vh[$C], $ah)) {
        preg_match_all('/("[^"]*+")+( DESC)?/', $ah[2], $Re);
        foreach ($Re[2] as $y => $X) {
          if ($X) $w["descs"][$y] = '1';
        }
      }
      if (!$J[""] || $w["type"] != "UNIQUE" || $w["columns"] != $J[""]["columns"] || $w["descs"] != $J[""]["descs"] || !preg_match("~^sqlite_~", $C)) $J[$C] = $w;
    }
    return $J;
  }
  function
  foreign_keys($R)
  {
    $J = array();
    foreach (get_rows("PRAGMA foreign_key_list(" . table($R) . ")") as $K) {
      $q = &$J[$K["id"]];
      if (!$q) $q = $K;
      $q["source"][] = $K["from"];
      $q["target"][] = $K["to"];
    }
    return $J;
  }
  function
  view($C)
  {
    return
      array("select" => preg_replace('~^(?:[^`"[]+|`[^`]*`|"[^"]*")* AS\s+~iU', '', get_val("SELECT sql FROM sqlite_master WHERE type = 'view' AND name = " . q($C))));
  }
  function
  collations()
  {
    return (isset($_GET["create"]) ? get_vals("PRAGMA collation_list", 1) : array());
  }
  function
  information_schema($k)
  {
    return
      false;
  }
  function
  error()
  {
    return
      h(connection()->error);
  }
  function
  check_sqlite_name($C)
  {
    $Pc = "db|sdb|sqlite";
    if (!preg_match("~^[^\\0]*\\.($Pc)\$~", $C)) {
      connection()->error = lang(23, str_replace("|", ", ", $Pc));
      return
        false;
    }
    return
      true;
  }
  function
  create_database($k, $c)
  {
    if (file_exists($k)) {
      connection()->error = lang(24);
      return
        false;
    }
    if (!check_sqlite_name($k)) return
      false;
    try {
      $A = new
        Db();
      $A->attach($k, '', '');
    } catch (\Exception $Hc) {
      connection()->error = $Hc->getMessage();
      return
        false;
    }
    $A->query('PRAGMA encoding = "UTF-8"');
    $A->query('CREATE TABLE adminer (i)');
    $A->query('DROP TABLE adminer');
    return
      true;
  }
  function
  drop_databases($j)
  {
    connection()->attach(":memory:", '', '');
    foreach (
      $j
      as $k
    ) {
      if (!@unlink($k)) {
        connection()->error = lang(24);
        return
          false;
      }
    }
    return
      true;
  }
  function
  rename_database($C, $c)
  {
    if (!check_sqlite_name($C)) return
      false;
    connection()->attach(":memory:", '', '');
    connection()->error = lang(24);
    return @rename(DB, $C);
  }
  function
  auto_increment()
  {
    return " PRIMARY KEY AUTOINCREMENT";
  }
  function
  alter_table($R, $C, $o, $id, $ob, $xc, $c, $Ba, $og)
  {
    $jj = ($R == "" || $id);
    foreach (
      $o
      as $n
    ) {
      if ($n[0] != "" || !$n[1] || $n[2]) {
        $jj = true;
        break;
      }
    }
    $b = array();
    $cg = array();
    foreach (
      $o
      as $n
    ) {
      if ($n[1]) {
        $b[] = ($jj ? $n[1] : "ADD " . implode($n[1]));
        if ($n[0] != "") $cg[$n[0]] = $n[1][0];
      }
    }
    if (!$jj) {
      foreach (
        $b
        as $X
      ) {
        if (!queries("ALTER TABLE " . table($R) . " $X")) return
          false;
      }
      if ($R != $C && !queries("ALTER TABLE " . table($R) . " RENAME TO " . table($C))) return
        false;
    } elseif (!recreate_table($R, $C, $b, $cg, $id, $Ba)) return
      false;
    if ($Ba) {
      queries("BEGIN");
      queries("UPDATE sqlite_sequence SET seq = $Ba WHERE name = " . q($C));
      if (!connection()->affected_rows) queries("INSERT INTO sqlite_sequence (name, seq) VALUES (" . q($C) . ", $Ba)");
      queries("COMMIT");
    }
    return
      true;
  }
  function
  recreate_table($R, $C, array $o, array $cg, array $id, $Ba = "", $x = array(), $ic = "", $la = "")
  {
    if ($R != "") {
      if (!$o) {
        foreach (fields($R) as $y => $n) {
          if ($x) $n["auto_increment"] = 0;
          $o[] = process_field($n, $n);
          $cg[$y] = idf_escape($y);
        }
      }
      $Fg = false;
      foreach (
        $o
        as $n
      ) {
        if ($n[6]) $Fg = true;
      }
      $kc = array();
      foreach (
        $x
        as $y => $X
      ) {
        if ($X[2] == "DROP") {
          $kc[$X[1]] = true;
          unset($x[$y]);
        }
      }
      foreach (indexes($R) as $te => $w) {
        $e = array();
        foreach ($w["columns"] as $y => $d) {
          if (!$cg[$d]) continue
            2;
          $e[] = $cg[$d] . ($w["descs"][$y] ? " DESC" : "");
        }
        if (!$kc[$te]) {
          if ($w["type"] != "PRIMARY" || !$Fg) $x[] = array($w["type"], $te, $e);
        }
      }
      foreach (
        $x
        as $y => $X
      ) {
        if ($X[0] == "PRIMARY") {
          unset($x[$y]);
          $id[] = "  PRIMARY KEY (" . implode(", ", $X[2]) . ")";
        }
      }
      foreach (foreign_keys($R) as $te => $q) {
        foreach ($q["source"] as $y => $d) {
          if (!$cg[$d]) continue
            2;
          $q["source"][$y] = idf_unescape($cg[$d]);
        }
        if (!isset($id[" $te"])) $id[] = " " . format_foreign_key($q);
      }
      queries("BEGIN");
    }
    $Va = array();
    foreach (
      $o
      as $n
    ) {
      if (preg_match('~GENERATED~', $n[3])) unset($cg[array_search($n[0], $cg)]);
      $Va[] = "  " . implode($n);
    }
    $Va = array_merge($Va, array_filter($id));
    foreach (driver()->checkConstraints($R) as $Xa) {
      if ($Xa != $ic) $Va[] = "  CHECK ($Xa)";
    }
    if ($la) $Va[] = "  CHECK ($la)";
    $ui = ($R == $C ? "adminer_$C" : $C);
    if (!queries("CREATE TABLE " . table($ui) . " (\n" . implode(",\n", $Va) . "\n)")) return
      false;
    if ($R != "") {
      if ($cg && !queries("INSERT INTO " . table($ui) . " (" . implode(", ", $cg) . ") SELECT " . implode(", ", array_map('Adminer\idf_escape', array_keys($cg))) . " FROM " . table($R))) return
        false;
      $Ti = array();
      foreach (triggers($R) as $Ri => $Ai) {
        $Qi = trigger($Ri, $R);
        $Ti[] = "CREATE TRIGGER " . idf_escape($Ri) . " " . implode(" ", $Ai) . " ON " . table($C) . "\n$Qi[Statement]";
      }
      $Ba = $Ba ? "" : get_val("SELECT seq FROM sqlite_sequence WHERE name = " . q($R));
      if (!queries("DROP TABLE " . table($R)) || ($R == $C && !queries("ALTER TABLE " . table($ui) . " RENAME TO " . table($C))) || !alter_indexes($C, $x)) return
        false;
      if ($Ba) queries("UPDATE sqlite_sequence SET seq = $Ba WHERE name = " . q($C));
      foreach (
        $Ti
        as $Qi
      ) {
        if (!queries($Qi)) return
          false;
      }
      queries("COMMIT");
    }
    return
      true;
  }
  function
  index_sql($R, $U, $C, $e)
  {
    return "CREATE $U " . ($U != "INDEX" ? "INDEX " : "") . idf_escape($C != "" ? $C : uniqid($R . "_")) . " ON " . table($R) . " $e";
  }
  function
  alter_indexes($R, $b)
  {
    foreach (
      $b
      as $G
    ) {
      if ($G[0] == "PRIMARY") return
        recreate_table($R, $R, array(), array(), array(), "", $b);
    }
    foreach (array_reverse($b) as $X) {
      if (!queries($X[2] == "DROP" ? "DROP INDEX " . idf_escape($X[1]) : index_sql($R, $X[0], $X[1], "(" . implode(", ", $X[2]) . ")"))) return
        false;
    }
    return
      true;
  }
  function
  truncate_tables($T)
  {
    return
      apply_queries("DELETE FROM", $T);
  }
  function
  drop_views($vj)
  {
    return
      apply_queries("DROP VIEW", $vj);
  }
  function
  drop_tables($T)
  {
    return
      apply_queries("DROP TABLE", $T);
  }
  function
  move_tables($T, $vj, $si)
  {
    return
      false;
  }
  function
  trigger($C, $R)
  {
    if ($C == "") return
      array("Statement" => "BEGIN\n\t;\nEND");
    $v = '(?:[^`"\s]+|`[^`]*`|"[^"]*")+';
    $Si = trigger_options();
    preg_match("~^CREATE\\s+TRIGGER\\s*$v\\s*(" . implode("|", $Si["Timing"]) . ")\\s+([a-z]+)(?:\\s+OF\\s+($v))?\\s+ON\\s*$v\\s*(?:FOR\\s+EACH\\s+ROW\\s)?(.*)~is", get_val("SELECT sql FROM sqlite_master WHERE type = 'trigger' AND name = " . q($C)), $B);
    $_f = $B[3];
    return
      array("Timing" => strtoupper($B[1]), "Event" => strtoupper($B[2]) . ($_f ? " OF" : ""), "Of" => idf_unescape($_f), "Trigger" => $C, "Statement" => $B[4],);
  }
  function
  triggers($R)
  {
    $J = array();
    $Si = trigger_options();
    foreach (get_rows("SELECT * FROM sqlite_master WHERE type = 'trigger' AND tbl_name = " . q($R)) as $K) {
      preg_match('~^CREATE\s+TRIGGER\s*(?:[^`"\s]+|`[^`]*`|"[^"]*")+\s*(' . implode("|", $Si["Timing"]) . ')\s*(.*?)\s+ON\b~i', $K["sql"], $B);
      $J[$K["name"]] = array($B[1], $B[2]);
    }
    return $J;
  }
  function
  trigger_options()
  {
    return
      array("Timing" => array("BEFORE", "AFTER", "INSTEAD OF"), "Event" => array("INSERT", "UPDATE", "UPDATE OF", "DELETE"), "Type" => array("FOR EACH ROW"),);
  }
  function
  begin()
  {
    return
      queries("BEGIN");
  }
  function
  last_id($I)
  {
    return
      get_val("SELECT LAST_INSERT_ROWID()");
  }
  function
  explain($g, $H)
  {
    return $g->query("EXPLAIN QUERY PLAN $H");
  }
  function
  found_rows($S, $Z) {}
  function
  types()
  {
    return
      array();
  }
  function
  create_sql($R, $Ba, $bi)
  {
    $J = get_val("SELECT sql FROM sqlite_master WHERE type IN ('table', 'view') AND name = " . q($R));
    foreach (indexes($R) as $C => $w) {
      if ($C == '') continue;
      $J
        .= ";\n\n" . index_sql($R, $w['type'], $C, "(" . implode(", ", array_map('Adminer\idf_escape', $w['columns'])) . ")");
    }
    return $J;
  }
  function
  truncate_sql($R)
  {
    return "DELETE FROM " . table($R);
  }
  function
  use_sql($Nb) {}
  function
  trigger_sql($R)
  {
    return
      implode(get_vals("SELECT sql || ';;\n' FROM sqlite_master WHERE type = 'trigger' AND tbl_name = " . q($R)));
  }
  function
  show_variables()
  {
    $J = array();
    foreach (get_rows("PRAGMA pragma_list") as $K) {
      $C = $K["name"];
      if ($C != "pragma_list" && $C != "compile_options") {
        $J[$C] = array($C, '');
        foreach (get_rows("PRAGMA $C") as $K) $J[$C][1] .= implode(", ", $K) . "\n";
      }
    }
    return $J;
  }
  function
  show_status()
  {
    $J = array();
    foreach (get_vals("PRAGMA compile_options") as $Of) $J[] = explode("=", $Of, 2) + array('', '');
    return $J;
  }
  function
  convert_field($n) {}
  function
  unconvert_field($n, $J)
  {
    return $J;
  }
  function
  support($Uc)
  {
    return
      preg_match('~^(check|columns|database|drop_col|dump|indexes|descidx|move_col|sql|status|table|trigger|variables|view|view_trigger)$~', $Uc);
  }
}
add_driver("pgsql", "PostgreSQL");
if (isset($_GET["pgsql"])) {
  define('Adminer\DRIVER', "pgsql");
  if (extension_loaded("pgsql") && $_GET["ext"] != "pdo") {
    class
    PgsqlDb
    extends
    SqlDb
    {
      var $extension = "PgSQL";
      var $timeout = 0;
      private $link, $string, $database = true;
      function
      _error($Cc, $m)
      {
        if (ini_bool("html_errors")) $m = html_entity_decode(strip_tags($m));
        $m = preg_replace('~^[^:]*: ~', '', $m);
        $this->error = $m;
      }
      function
      attach($N, $V, $F)
      {
        $k = adminer()->database();
        set_error_handler(array($this, '_error'));
        $this->string = "host='" . str_replace(":", "' port='", addcslashes($N, "'\\")) . "' user='" . addcslashes($V, "'\\") . "' password='" . addcslashes($F, "'\\") . "'";
        $Wh = adminer()->connectSsl();
        if (isset($Wh["mode"])) $this->string
          .= " sslmode='" . $Wh["mode"] . "'";
        $this->link = @pg_connect("$this->string dbname='" . ($k != "" ? addcslashes($k, "'\\") : "postgres") . "'", PGSQL_CONNECT_FORCE_NEW);
        if (!$this->link && $k != "") {
          $this->database = false;
          $this->link = @pg_connect("$this->string dbname='postgres'", PGSQL_CONNECT_FORCE_NEW);
        }
        restore_error_handler();
        if ($this->link) pg_set_client_encoding($this->link, "UTF8");
        return ($this->link ? '' : $this->error);
      }
      function
      quote($Q)
      {
        return (function_exists('pg_escape_literal') ? pg_escape_literal($this->link, $Q) : "'" . pg_escape_string($this->link, $Q) . "'");
      }
      function
      value($X, array $n)
      {
        return ($n["type"] == "bytea" && $X !== null ? pg_unescape_bytea($X) : $X);
      }
      function
      select_db($Nb)
      {
        if ($Nb == adminer()->database()) return $this->database;
        $J = @pg_connect("$this->string dbname='" . addcslashes($Nb, "'\\") . "'", PGSQL_CONNECT_FORCE_NEW);
        if ($J) $this->link = $J;
        return $J;
      }
      function
      close()
      {
        $this->link = @pg_connect("$this->string dbname='postgres'");
      }
      function
      query($H, $Xi = false)
      {
        $I = @pg_query($this->link, $H);
        $this->error = "";
        if (!$I) {
          $this->error = pg_last_error($this->link);
          $J = false;
        } elseif (!pg_num_fields($I)) {
          $this->affected_rows = pg_affected_rows($I);
          $J = true;
        } else $J = new
          Result($I);
        if ($this->timeout) {
          $this->timeout = 0;
          $this->query("RESET statement_timeout");
        }
        return $J;
      }
      function
      warnings()
      {
        return
          h(pg_last_notice($this->link));
      }
      function
      copyFrom($R, array $L)
      {
        $this->error = '';
        set_error_handler(function ($Cc, $m) {
          $this->error = (ini_bool('html_errors') ? html_entity_decode($m) : $m);
          return
            true;
        });
        $J = pg_copy_from($this->link, $R, $L);
        restore_error_handler();
        return $J;
      }
    }
    class
    Result
    {
      var $num_rows;
      private $result, $offset = 0;
      function
      __construct($I)
      {
        $this->result = $I;
        $this->num_rows = pg_num_rows($I);
      }
      function
      fetch_assoc()
      {
        return
          pg_fetch_assoc($this->result);
      }
      function
      fetch_row()
      {
        return
          pg_fetch_row($this->result);
      }
      function
      fetch_field()
      {
        $d = $this->offset++;
        $J = new
          \stdClass;
        $J->orgtable = pg_field_table($this->result, $d);
        $J->name = pg_field_name($this->result, $d);
        $J->type = pg_field_type($this->result, $d);
        $J->charsetnr = ($J->type == "bytea" ? 63 : 0);
        return $J;
      }
      function
      __destruct()
      {
        pg_free_result($this->result);
      }
    }
  } elseif (extension_loaded("pdo_pgsql")) {
    class
    PgsqlDb
    extends
    PdoDb
    {
      var $extension = "PDO_PgSQL";
      var $timeout = 0;
      function
      attach($N, $V, $F)
      {
        $k = adminer()->database();
        $mc = "pgsql:host='" . str_replace(":", "' port='", addcslashes($N, "'\\")) . "' client_encoding=utf8 dbname='" . ($k != "" ? addcslashes($k, "'\\") : "postgres") . "'";
        $Wh = adminer()->connectSsl();
        if (isset($Wh["mode"])) $mc
          .= " sslmode='" . $Wh["mode"] . "'";
        return $this->dsn($mc, $V, $F);
      }
      function
      select_db($Nb)
      {
        return (adminer()->database() == $Nb);
      }
      function
      query($H, $Xi = false)
      {
        $J = parent::query($H, $Xi);
        if ($this->timeout) {
          $this->timeout = 0;
          parent::query("RESET statement_timeout");
        }
        return $J;
      }
      function
      warnings() {}
      function
      copyFrom($R, array $L)
      {
        $J = $this->pdo->pgsqlCopyFromArray($R, $L);
        $this->error = idx($this->pdo->errorInfo(), 2) ?: '';
        return $J;
      }
      function
      close() {}
    }
  }
  if (class_exists('Adminer\PgsqlDb')) {
    class
    Db
    extends
    PgsqlDb
    {
      function
      multi_query($H)
      {
        if (preg_match('~\bCOPY\s+(.+?)\s+FROM\s+stdin;\n?(.*)\n\\\\\.$~is', str_replace("\r\n", "\n", $H), $B)) {
          $L = explode("\n", $B[2]);
          $this->affected_rows = count($L);
          return $this->copyFrom($B[1], $L);
        }
        return
          parent::multi_query($H);
      }
    }
  }
  class
  Driver
  extends
  SqlDriver
  {
    static $Pc = array("PgSQL", "PDO_PgSQL");
    static $re = "pgsql";
    var $operators = array("=", "<", ">", "<=", ">=", "!=", "~", "!~", "LIKE", "LIKE %%", "ILIKE", "ILIKE %%", "IN", "IS NULL", "NOT LIKE", "NOT IN", "IS NOT NULL");
    var $functions = array("char_length", "lower", "round", "to_hex", "to_timestamp", "upper");
    var $grouping = array("avg", "count", "count distinct", "max", "min", "sum");
    static
    function
    connect($N, $V, $F)
    {
      $g = parent::connect($N, $V, $F);
      if (is_string($g)) return $g;
      $tj = get_val("SELECT version()", 0, $g);
      $g->flavor = (preg_match('~CockroachDB~', $tj) ? 'cockroach' : '');
      $g->server_info = preg_replace('~^\D*([\d.]+[-\w]*).*~', '\1', $tj);
      if (min_version(9, 0, $g)) $g->query("SET application_name = 'Adminer'");
      if ($g->flavor == 'cockroach') add_driver(DRIVER, "CockroachDB");
      return $g;
    }
    function
    __construct(Db $g)
    {
      parent::__construct($g);
      $this->types = array(lang(25) => array("smallint" => 5, "integer" => 10, "bigint" => 19, "boolean" => 1, "numeric" => 0, "real" => 7, "double precision" => 16, "money" => 20), lang(26) => array("date" => 13, "time" => 17, "timestamp" => 20, "timestamptz" => 21, "interval" => 0), lang(27) => array("character" => 0, "character varying" => 0, "text" => 0, "tsquery" => 0, "tsvector" => 0, "uuid" => 0, "xml" => 0), lang(28) => array("bit" => 0, "bit varying" => 0, "bytea" => 0), lang(29) => array("cidr" => 43, "inet" => 43, "macaddr" => 17, "macaddr8" => 23, "txid_snapshot" => 0), lang(30) => array("box" => 0, "circle" => 0, "line" => 0, "lseg" => 0, "path" => 0, "point" => 0, "polygon" => 0),);
      if (min_version(9.2, 0, $g)) {
        $this->types[lang(27)]["json"] = 4294967295;
        if (min_version(9.4, 0, $g)) $this->types[lang(27)]["jsonb"] = 4294967295;
      }
      $this->insertFunctions = array("char" => "md5", "date|time" => "now",);
      $this->editFunctions = array(number_type() => "+/-", "date|time" => "+ interval/- interval", "char|text" => "||",);
      if (min_version(12, 0, $g)) $this->generated = array("STORED");
    }
    function
    enumLength(array $n)
    {
      $zc = $this->types[lang(31)][$n["type"]];
      return ($zc ? type_values($zc) : "");
    }
    function
    setUserTypes($Wi)
    {
      $this->types[lang(31)] = array_flip($Wi);
    }
    function
    insertReturning($R)
    {
      $Ba = array_filter(fields($R), function ($n) {
        return $n['auto_increment'];
      });
      return (count($Ba) == 1 ? " RETURNING " . idf_escape(key($Ba)) : "");
    }
    function
    insertUpdate($R, array $L, array $G)
    {
      foreach (
        $L
        as $O
      ) {
        $fj = array();
        $Z = array();
        foreach (
          $O
          as $y => $X
        ) {
          $fj[] = "$y = $X";
          if (isset($G[idf_unescape($y)])) $Z[] = "$y = $X";
        }
        if (!(($Z && queries("UPDATE " . table($R) . " SET " . implode(", ", $fj) . " WHERE " . implode(" AND ", $Z)) && connection()->affected_rows) || queries("INSERT INTO " . table($R) . " (" . implode(", ", array_keys($O)) . ") VALUES (" . implode(", ", $O) . ")"))) return
          false;
      }
      return
        true;
    }
    function
    slowQuery($H, $_i)
    {
      $this->conn->query("SET statement_timeout = " . (1000 * $_i));
      $this->conn->timeout = 1000 * $_i;
      return $H;
    }
    function
    convertSearch($v, array $X, array $n)
    {
      $xi = "char|text";
      if (strpos($X["op"], "LIKE") === false) $xi
        .= "|date|time(stamp)?|boolean|uuid|inet|cidr|macaddr|" . number_type();
      return (preg_match("~$xi~", $n["type"]) ? $v : "CAST($v AS text)");
    }
    function
    quoteBinary($oh)
    {
      return "'\\x" . bin2hex($oh) . "'";
    }
    function
    warnings()
    {
      return $this->conn->warnings();
    }
    function
    tableHelp($C, $pe = false)
    {
      $Je = array("information_schema" => "infoschema", "pg_catalog" => ($pe ? "view" : "catalog"),);
      $A = $Je[$_GET["ns"]];
      if ($A) return "$A-" . str_replace("_", "-", $C) . ".html";
    }
    function
    supportsIndex(array $S)
    {
      return $S["Engine"] != "view";
    }
    function
    hasCStyleEscapes()
    {
      static $Ra;
      if ($Ra === null) $Ra = (get_val("SHOW standard_conforming_strings", 0, $this->conn) == "off");
      return $Ra;
    }
  }
  function
  idf_escape($v)
  {
    return '"' . str_replace('"', '""', $v) . '"';
  }
  function
  table($v)
  {
    return
      idf_escape($v);
  }
  function
  get_databases($gd)
  {
    return
      get_vals("SELECT datname FROM pg_database
WHERE datallowconn = TRUE AND has_database_privilege(datname, 'CONNECT')
ORDER BY datname");
  }
  function
  limit($H, $Z, $_, $D = 0, $Ah = " ")
  {
    return " $H$Z" . ($_ ? $Ah . "LIMIT $_" . ($D ? " OFFSET $D" : "") : "");
  }
  function
  limit1($R, $H, $Z, $Ah = "\n")
  {
    return (preg_match('~^INTO~', $H) ? limit($H, $Z, 1, 0, $Ah) : " $H" . (is_view(table_status1($R)) ? $Z : $Ah . "WHERE ctid = (SELECT ctid FROM " . table($R) . $Z . $Ah . "LIMIT 1)"));
  }
  function
  db_collation($k, $jb)
  {
    return
      get_val("SELECT datcollate FROM pg_database WHERE datname = " . q($k));
  }
  function
  logged_user()
  {
    return
      get_val("SELECT user");
  }
  function
  tables_list()
  {
    $H = "SELECT table_name, table_type FROM information_schema.tables WHERE table_schema = current_schema()";
    if (support("materializedview")) $H
      .= "
UNION ALL
SELECT matviewname, 'MATERIALIZED VIEW'
FROM pg_matviews
WHERE schemaname = current_schema()";
    $H
      .= "
ORDER BY 1";
    return
      get_key_vals($H);
  }
  function
  count_tables($j)
  {
    $J = array();
    foreach (
      $j
      as $k
    ) {
      if (connection()->select_db($k)) $J[$k] = count(tables_list());
    }
    return $J;
  }
  function
  table_status($C = "")
  {
    static $Ed;
    if ($Ed === null) $Ed = get_val("SELECT 'pg_table_size'::regproc");
    $J = array();
    foreach (
      get_rows("SELECT
	c.relname AS \"Name\",
	CASE c.relkind WHEN 'r' THEN 'table' WHEN 'm' THEN 'materialized view' ELSE 'view' END AS \"Engine\"" . ($Ed ? ",
	pg_table_size(c.oid) AS \"Data_length\",
	pg_indexes_size(c.oid) AS \"Index_length\"" : "") . ",
	obj_description(c.oid, 'pg_class') AS \"Comment\",
	" . (min_version(12) ? "''" : "CASE WHEN c.relhasoids THEN 'oid' ELSE '' END") . " AS \"Oid\",
	c.reltuples as \"Rows\",
	n.nspname
FROM pg_class c
JOIN pg_namespace n ON(n.nspname = current_schema() AND n.oid = c.relnamespace)
WHERE relkind IN ('r', 'm', 'v', 'f', 'p')
" . ($C != "" ? "AND relname = " . q($C) : "ORDER BY relname")) as $K
    ) $J[$K["Name"]] = $K;
    return $J;
  }
  function
  is_view($S)
  {
    return
      in_array($S["Engine"], array("view", "materialized view"));
  }
  function
  fk_support($S)
  {
    return
      true;
  }
  function
  fields($R)
  {
    $J = array();
    $ta = array('timestamp without time zone' => 'timestamp', 'timestamp with time zone' => 'timestamptz',);
    foreach (
      get_rows("SELECT
	a.attname AS field,
	format_type(a.atttypid, a.atttypmod) AS full_type,
	pg_get_expr(d.adbin, d.adrelid) AS default,
	a.attnotnull::int,
	col_description(c.oid, a.attnum) AS comment" . (min_version(10) ? ",
	a.attidentity" . (min_version(12) ? ",
	a.attgenerated" : "") : "") . "
FROM pg_class c
JOIN pg_namespace n ON c.relnamespace = n.oid
JOIN pg_attribute a ON c.oid = a.attrelid
LEFT JOIN pg_attrdef d ON c.oid = d.adrelid AND a.attnum = d.adnum
WHERE c.relname = " . q($R) . "
AND n.nspname = current_schema()
AND NOT a.attisdropped
AND a.attnum > 0
ORDER BY a.attnum") as $K
    ) {
      preg_match('~([^([]+)(\((.*)\))?([a-z ]+)?((\[[0-9]*])*)$~', $K["full_type"], $B);
      list(, $U, $z, $K["length"], $ma, $xa) = $B;
      $K["length"] .= $xa;
      $Za = $U . $ma;
      if (isset($ta[$Za])) {
        $K["type"] = $ta[$Za];
        $K["full_type"] = $K["type"] . $z . $xa;
      } else {
        $K["type"] = $U;
        $K["full_type"] = $K["type"] . $z . $ma . $xa;
      }
      if (in_array($K['attidentity'], array('a', 'd'))) $K['default'] = 'GENERATED ' . ($K['attidentity'] == 'd' ? 'BY DEFAULT' : 'ALWAYS') . ' AS IDENTITY';
      $K["generated"] = ($K["attgenerated"] == "s" ? "STORED" : "");
      $K["null"] = !$K["attnotnull"];
      $K["auto_increment"] = $K['attidentity'] || preg_match('~^nextval\(~i', $K["default"]) || preg_match('~^unique_rowid\(~', $K["default"]);
      $K["privileges"] = array("insert" => 1, "select" => 1, "update" => 1, "where" => 1, "order" => 1);
      if (preg_match('~(.+)::[^,)]+(.*)~', $K["default"], $B)) $K["default"] = ($B[1] == "NULL" ? null : idf_unescape($B[1]) . $B[2]);
      $J[$K["field"]] = $K;
    }
    return $J;
  }
  function
  indexes($R, $h = null)
  {
    $h = connection($h);
    $J = array();
    $ki = get_val("SELECT oid FROM pg_class WHERE relnamespace = (SELECT oid FROM pg_namespace WHERE nspname = current_schema()) AND relname = " . q($R), 0, $h);
    $e = get_key_vals("SELECT attnum, attname FROM pg_attribute WHERE attrelid = $ki AND attnum > 0", $h);
    foreach (
      get_rows("SELECT relname, indisunique::int, indisprimary::int, indkey, indoption, (indpred IS NOT NULL)::int as indispartial
FROM pg_index i, pg_class ci
WHERE i.indrelid = $ki AND ci.oid = i.indexrelid
ORDER BY indisprimary DESC, indisunique DESC", $h) as $K
    ) {
      $bh = $K["relname"];
      $J[$bh]["type"] = ($K["indispartial"] ? "INDEX" : ($K["indisprimary"] ? "PRIMARY" : ($K["indisunique"] ? "UNIQUE" : "INDEX")));
      $J[$bh]["columns"] = array();
      $J[$bh]["descs"] = array();
      if ($K["indkey"]) {
        foreach (explode(" ", $K["indkey"]) as $Zd) $J[$bh]["columns"][] = $e[$Zd];
        foreach (explode(" ", $K["indoption"]) as $ae) $J[$bh]["descs"][] = (intval($ae) & 1 ? '1' : null);
      }
      $J[$bh]["lengths"] = array();
    }
    return $J;
  }
  function
  foreign_keys($R)
  {
    $J = array();
    foreach (
      get_rows("SELECT conname, condeferrable::int AS deferrable, pg_get_constraintdef(oid) AS definition
FROM pg_constraint
WHERE conrelid = (SELECT pc.oid FROM pg_class AS pc INNER JOIN pg_namespace AS pn ON (pn.oid = pc.relnamespace) WHERE pc.relname = " . q($R) . " AND pn.nspname = current_schema())
AND contype = 'f'::char
ORDER BY conkey, conname") as $K
    ) {
      if (preg_match('~FOREIGN KEY\s*\((.+)\)\s*REFERENCES (.+)\((.+)\)(.*)$~iA', $K['definition'], $B)) {
        $K['source'] = array_map('Adminer\idf_unescape', array_map('trim', explode(',', $B[1])));
        if (preg_match('~^(("([^"]|"")+"|[^"]+)\.)?"?("([^"]|"")+"|[^"]+)$~', $B[2], $Pe)) {
          $K['ns'] = idf_unescape($Pe[2]);
          $K['table'] = idf_unescape($Pe[4]);
        }
        $K['target'] = array_map('Adminer\idf_unescape', array_map('trim', explode(',', $B[3])));
        $K['on_delete'] = (preg_match("~ON DELETE (" . driver()->onActions . ")~", $B[4], $Pe) ? $Pe[1] : 'NO ACTION');
        $K['on_update'] = (preg_match("~ON UPDATE (" . driver()->onActions . ")~", $B[4], $Pe) ? $Pe[1] : 'NO ACTION');
        $J[$K['conname']] = $K;
      }
    }
    return $J;
  }
  function
  view($C)
  {
    return
      array("select" => trim(get_val("SELECT pg_get_viewdef(" . get_val("SELECT oid FROM pg_class WHERE relnamespace = (SELECT oid FROM pg_namespace WHERE nspname = current_schema()) AND relname = " . q($C)) . ")")));
  }
  function
  collations()
  {
    return
      array();
  }
  function
  information_schema($k)
  {
    return
      get_schema() == "information_schema";
  }
  function
  error()
  {
    $J = h(connection()->error);
    if (preg_match('~^(.*\n)?([^\n]*)\n( *)\^(\n.*)?$~s', $J, $B)) $J = $B[1] . preg_replace('~((?:[^&]|&[^;]*;){' . strlen($B[3]) . '})(.*)~', '\1<b>\2</b>', $B[2]) . $B[4];
    return
      nl_br($J);
  }
  function
  create_database($k, $c)
  {
    return
      queries("CREATE DATABASE " . idf_escape($k) . ($c ? " ENCODING " . idf_escape($c) : ""));
  }
  function
  drop_databases($j)
  {
    connection()->close();
    return
      apply_queries("DROP DATABASE", $j, 'Adminer\idf_escape');
  }
  function
  rename_database($C, $c)
  {
    connection()->close();
    return
      queries("ALTER DATABASE " . idf_escape(DB) . " RENAME TO " . idf_escape($C));
  }
  function
  auto_increment()
  {
    return "";
  }
  function
  alter_table($R, $C, $o, $id, $ob, $xc, $c, $Ba, $og)
  {
    $b = array();
    $Og = array();
    if ($R != "" && $R != $C) $Og[] = "ALTER TABLE " . table($R) . " RENAME TO " . table($C);
    $Bh = "";
    foreach (
      $o
      as $n
    ) {
      $d = idf_escape($n[0]);
      $X = $n[1];
      if (!$X) $b[] = "DROP $d";
      else {
        $pj = $X[5];
        unset($X[5]);
        if ($n[0] == "") {
          if (isset($X[6])) $X[1] = ($X[1] == " bigint" ? " big" : ($X[1] == " smallint" ? " small" : " ")) . "serial";
          $b[] = ($R != "" ? "ADD " : "  ") . implode($X);
          if (isset($X[6])) $b[] = ($R != "" ? "ADD" : " ") . " PRIMARY KEY ($X[0])";
        } else {
          if ($d != $X[0]) $Og[] = "ALTER TABLE " . table($C) . " RENAME $d TO $X[0]";
          $b[] = "ALTER $d TYPE$X[1]";
          $Ch = $R . "_" . idf_unescape($X[0]) . "_seq";
          $b[] = "ALTER $d " . ($X[3] ? "SET" . preg_replace('~GENERATED ALWAYS(.*) STORED~', 'EXPRESSION\1', $X[3]) : (isset($X[6]) ? "SET DEFAULT nextval(" . q($Ch) . ")" : "DROP DEFAULT"));
          if (isset($X[6])) $Bh = "CREATE SEQUENCE IF NOT EXISTS " . idf_escape($Ch) . " OWNED BY " . idf_escape($R) . ".$X[0]";
          $b[] = "ALTER $d " . ($X[2] == " NULL" ? "DROP NOT" : "SET") . $X[2];
        }
        if ($n[0] != "" || $pj != "") $Og[] = "COMMENT ON COLUMN " . table($C) . ".$X[0] IS " . ($pj != "" ? substr($pj, 9) : "''");
      }
    }
    $b = array_merge($b, $id);
    if ($R == "") array_unshift($Og, "CREATE TABLE " . table($C) . " (\n" . implode(",\n", $b) . "\n)");
    elseif ($b) array_unshift($Og, "ALTER TABLE " . table($R) . "\n" . implode(",\n", $b));
    if ($Bh) array_unshift($Og, $Bh);
    if ($ob !== null) $Og[] = "COMMENT ON TABLE " . table($C) . " IS " . q($ob);
    foreach (
      $Og
      as $H
    ) {
      if (!queries($H)) return
        false;
    }
    return
      true;
  }
  function
  alter_indexes($R, $b)
  {
    $i = array();
    $hc = array();
    $Og = array();
    foreach (
      $b
      as $X
    ) {
      if ($X[0] != "INDEX") $i[] = ($X[2] == "DROP" ? "\nDROP CONSTRAINT " . idf_escape($X[1]) : "\nADD" . ($X[1] != "" ? " CONSTRAINT " . idf_escape($X[1]) : "") . " $X[0] " . ($X[0] == "PRIMARY" ? "KEY " : "") . "(" . implode(", ", $X[2]) . ")");
      elseif ($X[2] == "DROP") $hc[] = idf_escape($X[1]);
      else $Og[] = "CREATE INDEX " . idf_escape($X[1] != "" ? $X[1] : uniqid($R . "_")) . " ON " . table($R) . " (" . implode(", ", $X[2]) . ")";
    }
    if ($i) array_unshift($Og, "ALTER TABLE " . table($R) . implode(",", $i));
    if ($hc) array_unshift($Og, "DROP INDEX " . implode(", ", $hc));
    foreach (
      $Og
      as $H
    ) {
      if (!queries($H)) return
        false;
    }
    return
      true;
  }
  function
  truncate_tables($T)
  {
    return
      queries("TRUNCATE " . implode(", ", array_map('Adminer\table', $T)));
  }
  function
  drop_views($vj)
  {
    return
      drop_tables($vj);
  }
  function
  drop_tables($T)
  {
    foreach (
      $T
      as $R
    ) {
      $P = table_status1($R);
      if (!queries("DROP " . strtoupper($P["Engine"]) . " " . table($R))) return
        false;
    }
    return
      true;
  }
  function
  move_tables($T, $vj, $si)
  {
    foreach (array_merge($T, $vj) as $R) {
      $P = table_status1($R);
      if (!queries("ALTER " . strtoupper($P["Engine"]) . " " . table($R) . " SET SCHEMA " . idf_escape($si))) return
        false;
    }
    return
      true;
  }
  function
  trigger($C, $R)
  {
    if ($C == "") return
      array("Statement" => "EXECUTE PROCEDURE ()");
    $e = array();
    $Z = "WHERE trigger_schema = current_schema() AND event_object_table = " . q($R) . " AND trigger_name = " . q($C);
    foreach (get_rows("SELECT * FROM information_schema.triggered_update_columns $Z") as $K) $e[] = $K["event_object_column"];
    $J = array();
    foreach (
      get_rows('SELECT trigger_name AS "Trigger", action_timing AS "Timing", event_manipulation AS "Event", \'FOR EACH \' || action_orientation AS "Type", action_statement AS "Statement"
FROM information_schema.triggers' . "
$Z
ORDER BY event_manipulation DESC") as $K
    ) {
      if ($e && $K["Event"] == "UPDATE") $K["Event"] .= " OF";
      $K["Of"] = implode(", ", $e);
      if ($J) $K["Event"] .= " OR $J[Event]";
      $J = $K;
    }
    return $J;
  }
  function
  triggers($R)
  {
    $J = array();
    foreach (get_rows("SELECT * FROM information_schema.triggers WHERE trigger_schema = current_schema() AND event_object_table = " . q($R)) as $K) {
      $Qi = trigger($K["trigger_name"], $R);
      $J[$Qi["Trigger"]] = array($Qi["Timing"], $Qi["Event"]);
    }
    return $J;
  }
  function
  trigger_options()
  {
    return
      array("Timing" => array("BEFORE", "AFTER"), "Event" => array("INSERT", "UPDATE", "UPDATE OF", "DELETE", "INSERT OR UPDATE", "INSERT OR UPDATE OF", "DELETE OR INSERT", "DELETE OR UPDATE", "DELETE OR UPDATE OF", "DELETE OR INSERT OR UPDATE", "DELETE OR INSERT OR UPDATE OF"), "Type" => array("FOR EACH ROW", "FOR EACH STATEMENT"),);
  }
  function
  routine($C, $U)
  {
    $L = get_rows('SELECT routine_definition AS definition, LOWER(external_language) AS language, *
FROM information_schema.routines
WHERE routine_schema = current_schema() AND specific_name = ' . q($C));
    $J = idx($L, 0, array());
    $J["returns"] = array("type" => $J["type_udt_name"]);
    $J["fields"] = get_rows('SELECT parameter_name AS field, data_type AS type, character_maximum_length AS length, parameter_mode AS inout
FROM information_schema.parameters
WHERE specific_schema = current_schema() AND specific_name = ' . q($C) . '
ORDER BY ordinal_position');
    return $J;
  }
  function
  routines()
  {
    return
      get_rows('SELECT specific_name AS "SPECIFIC_NAME", routine_type AS "ROUTINE_TYPE", routine_name AS "ROUTINE_NAME", type_udt_name AS "DTD_IDENTIFIER"
FROM information_schema.routines
WHERE routine_schema = current_schema()
ORDER BY SPECIFIC_NAME');
  }
  function
  routine_languages()
  {
    return
      get_vals("SELECT LOWER(lanname) FROM pg_catalog.pg_language");
  }
  function
  routine_id($C, $K)
  {
    $J = array();
    foreach ($K["fields"] as $n) {
      $z = $n["length"];
      $J[] = $n["type"] . ($z ? "($z)" : "");
    }
    return
      idf_escape($C) . "(" . implode(", ", $J) . ")";
  }
  function
  last_id($I)
  {
    $K = (is_object($I) ? $I->fetch_row() : array());
    return ($K ? $K[0] : 0);
  }
  function
  explain($g, $H)
  {
    return $g->query("EXPLAIN $H");
  }
  function
  found_rows($S, $Z)
  {
    if (preg_match("~ rows=([0-9]+)~", get_val("EXPLAIN SELECT * FROM " . idf_escape($S["Name"]) . ($Z ? " WHERE " . implode(" AND ", $Z) : "")), $ah)) return $ah[1];
  }
  function
  types()
  {
    return
      get_key_vals("SELECT oid, typname
FROM pg_type
WHERE typnamespace = (SELECT oid FROM pg_namespace WHERE nspname = current_schema())
AND typtype IN ('b','d','e')
AND typelem = 0");
  }
  function
  type_values($u)
  {
    $Bc = get_vals("SELECT enumlabel FROM pg_enum WHERE enumtypid = $u ORDER BY enumsortorder");
    return ($Bc ? "'" . implode("', '", array_map('addslashes', $Bc)) . "'" : "");
  }
  function
  schemas()
  {
    return
      get_vals("SELECT nspname FROM pg_namespace ORDER BY nspname");
  }
  function
  get_schema()
  {
    return
      get_val("SELECT current_schema()");
  }
  function
  set_schema($qh, $h = null)
  {
    if (!$h) $h = connection();
    $J = $h->query("SET search_path TO " . idf_escape($qh));
    driver()->setUserTypes(types());
    return $J;
  }
  function
  foreign_keys_sql($R)
  {
    $J = "";
    $P = table_status1($R);
    $ed = foreign_keys($R);
    ksort($ed);
    foreach (
      $ed
      as $dd => $cd
    ) $J
      .= "ALTER TABLE ONLY " . idf_escape($P['nspname']) . "." . idf_escape($P['Name']) . " ADD CONSTRAINT " . idf_escape($dd) . " $cd[definition] " . ($cd['deferrable'] ? 'DEFERRABLE' : 'NOT DEFERRABLE') . ";\n";
    return ($J ? "$J\n" : $J);
  }
  function
  create_sql($R, $Ba, $bi)
  {
    $gh = array();
    $Dh = array();
    $P = table_status1($R);
    if (is_view($P)) {
      $uj = view($R);
      return
        rtrim("CREATE VIEW " . idf_escape($R) . " AS $uj[select]", ";");
    }
    $o = fields($R);
    if (count($P) < 2 || empty($o)) return
      false;
    $J = "CREATE TABLE " . idf_escape($P['nspname']) . "." . idf_escape($P['Name']) . " (\n    ";
    foreach (
      $o
      as $n
    ) {
      $lg = idf_escape($n['field']) . ' ' . $n['full_type'] . default_value($n) . ($n['null'] ? "" : " NOT NULL");
      $gh[] = $lg;
      if (preg_match('~nextval\(\'([^\']+)\'\)~', $n['default'], $Re)) {
        $Ch = $Re[1];
        $Qh = first(get_rows((min_version(10) ? "SELECT *, cache_size AS cache_value FROM pg_sequences WHERE schemaname = current_schema() AND sequencename = " . q(idf_unescape($Ch)) : "SELECT * FROM $Ch"), null, "-- "));
        $Dh[] = ($bi == "DROP+CREATE" ? "DROP SEQUENCE IF EXISTS $Ch;\n" : "") . "CREATE SEQUENCE $Ch INCREMENT $Qh[increment_by] MINVALUE $Qh[min_value] MAXVALUE $Qh[max_value]" . ($Ba && $Qh['last_value'] ? " START " . ($Qh["last_value"] + 1) : "") . " CACHE $Qh[cache_value];";
      }
    }
    if (!empty($Dh)) $J = implode("\n\n", $Dh) . "\n\n$J";
    $G = "";
    foreach (indexes($R) as $Xd => $w) {
      if ($w['type'] == 'PRIMARY') {
        $G = $Xd;
        $gh[] = "CONSTRAINT " . idf_escape($Xd) . " PRIMARY KEY (" . implode(', ', array_map('Adminer\idf_escape', $w['columns'])) . ")";
      }
    }
    foreach (driver()->checkConstraints($R) as $ub => $wb) $gh[] = "CONSTRAINT " . idf_escape($ub) . " CHECK $wb";
    $J
      .= implode(",\n    ", $gh) . "\n) WITH (oids = " . ($P['Oid'] ? 'true' : 'false') . ");";
    if ($P['Comment']) $J
      .= "\n\nCOMMENT ON TABLE " . idf_escape($P['nspname']) . "." . idf_escape($P['Name']) . " IS " . q($P['Comment']) . ";";
    foreach (
      $o
      as $Wc => $n
    ) {
      if ($n['comment']) $J
        .= "\n\nCOMMENT ON COLUMN " . idf_escape($P['nspname']) . "." . idf_escape($P['Name']) . "." . idf_escape($Wc) . " IS " . q($n['comment']) . ";";
    }
    foreach (get_rows("SELECT indexdef FROM pg_catalog.pg_indexes WHERE schemaname = current_schema() AND tablename = " . q($R) . ($G ? " AND indexname != " . q($G) : ""), null, "-- ") as $K) $J
      .= "\n\n$K[indexdef];";
    return
      rtrim($J, ';');
  }
  function
  truncate_sql($R)
  {
    return "TRUNCATE " . table($R);
  }
  function
  trigger_sql($R)
  {
    $P = table_status1($R);
    $J = "";
    foreach (triggers($R) as $Pi => $Oi) {
      $Qi = trigger($Pi, $P['Name']);
      $J
        .= "\nCREATE TRIGGER " . idf_escape($Qi['Trigger']) . " $Qi[Timing] $Qi[Event] ON " . idf_escape($P["nspname"]) . "." . idf_escape($P['Name']) . " $Qi[Type] $Qi[Statement];;\n";
    }
    return $J;
  }
  function
  use_sql($Nb)
  {
    return "\connect " . idf_escape($Nb);
  }
  function
  show_variables()
  {
    return
      get_rows("SHOW ALL");
  }
  function
  process_list()
  {
    return
      get_rows("SELECT * FROM pg_stat_activity ORDER BY " . (min_version(9.2) ? "pid" : "procpid"));
  }
  function
  convert_field($n) {}
  function
  unconvert_field($n, $J)
  {
    return $J;
  }
  function
  support($Uc)
  {
    return
      preg_match('~^(check|database|table|columns|sql|indexes|descidx|comment|view|' . (min_version(9.3) ? 'materializedview|' : '') . 'scheme|' . (min_version(11) ? 'procedure|' : '') . 'routine|sequence|trigger|type|variables|drop_col' . (connection()->flavor == 'cockroach' ? '' : '|processlist') . '|kill|dump)$~', $Uc);
  }
  function
  kill_process($X)
  {
    return
      queries("SELECT pg_terminate_backend(" . number($X) . ")");
  }
  function
  connection_id()
  {
    return "SELECT pg_backend_pid()";
  }
  function
  max_connections()
  {
    return
      get_val("SHOW max_connections");
  }
}
add_driver("oracle", "Oracle (beta)");
if (isset($_GET["oracle"])) {
  define('Adminer\DRIVER', "oracle");
  if (extension_loaded("oci8") && $_GET["ext"] != "pdo") {
    class
    Db
    extends
    SqlDb
    {
      var $extension = "oci8";
      var $_current_db;
      private $link;
      function
      _error($Cc, $m)
      {
        if (ini_bool("html_errors")) $m = html_entity_decode(strip_tags($m));
        $m = preg_replace('~^[^:]*: ~', '', $m);
        $this->error = $m;
      }
      function
      attach($N, $V, $F)
      {
        $this->link = @oci_new_connect($V, $F, $N, "AL32UTF8");
        if ($this->link) {
          $this->server_info = oci_server_version($this->link);
          return '';
        }
        $m = oci_error();
        return $m["message"];
      }
      function
      quote($Q)
      {
        return "'" . str_replace("'", "''", $Q) . "'";
      }
      function
      select_db($Nb)
      {
        $this->_current_db = $Nb;
        return
          true;
      }
      function
      query($H, $Xi = false)
      {
        $I = oci_parse($this->link, $H);
        $this->error = "";
        if (!$I) {
          $m = oci_error($this->link);
          $this->errno = $m["code"];
          $this->error = $m["message"];
          return
            false;
        }
        set_error_handler(array($this, '_error'));
        $J = @oci_execute($I);
        restore_error_handler();
        if ($J) {
          if (oci_num_fields($I)) return
            new
            Result($I);
          $this->affected_rows = oci_num_rows($I);
          oci_free_statement($I);
        }
        return $J;
      }
    }
    class
    Result
    {
      var $num_rows;
      private $result, $offset = 1;
      function
      __construct($I)
      {
        $this->result = $I;
      }
      private
      function
      convert($K)
      {
        foreach (
          (array)$K
          as $y => $X
        ) {
          if (is_a($X, 'OCILob') || is_a($X, 'OCI-Lob')) $K[$y] = $X->load();
        }
        return $K;
      }
      function
      fetch_assoc()
      {
        return $this->convert(oci_fetch_assoc($this->result));
      }
      function
      fetch_row()
      {
        return $this->convert(oci_fetch_row($this->result));
      }
      function
      fetch_field()
      {
        $d = $this->offset++;
        $J = new
          \stdClass;
        $J->name = oci_field_name($this->result, $d);
        $J->type = oci_field_type($this->result, $d);
        $J->charsetnr = (preg_match("~raw|blob|bfile~", $J->type) ? 63 : 0);
        return $J;
      }
      function
      __destruct()
      {
        oci_free_statement($this->result);
      }
    }
  } elseif (extension_loaded("pdo_oci")) {
    class
    Db
    extends
    PdoDb
    {
      var $extension = "PDO_OCI";
      var $_current_db;
      function
      attach($N, $V, $F)
      {
        return $this->dsn("oci:dbname=//$N;charset=AL32UTF8", $V, $F);
      }
      function
      select_db($Nb)
      {
        $this->_current_db = $Nb;
        return
          true;
      }
    }
  }
  class
  Driver
  extends
  SqlDriver
  {
    static $Pc = array("OCI8", "PDO_OCI");
    static $re = "oracle";
    var $insertFunctions = array("date" => "current_date", "timestamp" => "current_timestamp",);
    var $editFunctions = array("number|float|double" => "+/-", "date|timestamp" => "+ interval/- interval", "char|clob" => "||",);
    var $operators = array("=", "<", ">", "<=", ">=", "!=", "LIKE", "LIKE %%", "IN", "IS NULL", "NOT LIKE", "NOT IN", "IS NOT NULL", "SQL");
    var $functions = array("length", "lower", "round", "upper");
    var $grouping = array("avg", "count", "count distinct", "max", "min", "sum");
    function
    __construct(Db $g)
    {
      parent::__construct($g);
      $this->types = array(lang(25) => array("number" => 38, "binary_float" => 12, "binary_double" => 21), lang(26) => array("date" => 10, "timestamp" => 29, "interval year" => 12, "interval day" => 28), lang(27) => array("char" => 2000, "varchar2" => 4000, "nchar" => 2000, "nvarchar2" => 4000, "clob" => 4294967295, "nclob" => 4294967295), lang(28) => array("raw" => 2000, "long raw" => 2147483648, "blob" => 4294967295, "bfile" => 4294967296),);
    }
    function
    begin()
    {
      return
        true;
    }
    function
    insertUpdate($R, array $L, array $G)
    {
      foreach (
        $L
        as $O
      ) {
        $fj = array();
        $Z = array();
        foreach (
          $O
          as $y => $X
        ) {
          $fj[] = "$y = $X";
          if (isset($G[idf_unescape($y)])) $Z[] = "$y = $X";
        }
        if (!(($Z && queries("UPDATE " . table($R) . " SET " . implode(", ", $fj) . " WHERE " . implode(" AND ", $Z)) && connection()->affected_rows) || queries("INSERT INTO " . table($R) . " (" . implode(", ", array_keys($O)) . ") VALUES (" . implode(", ", $O) . ")"))) return
          false;
      }
      return
        true;
    }
    function
    hasCStyleEscapes()
    {
      return
        true;
    }
  }
  function
  idf_escape($v)
  {
    return '"' . str_replace('"', '""', $v) . '"';
  }
  function
  table($v)
  {
    return
      idf_escape($v);
  }
  function
  get_databases($gd)
  {
    return
      get_vals("SELECT DISTINCT tablespace_name FROM (
SELECT tablespace_name FROM user_tablespaces
UNION SELECT tablespace_name FROM all_tables WHERE tablespace_name IS NOT NULL
)
ORDER BY 1");
  }
  function
  limit($H, $Z, $_, $D = 0, $Ah = " ")
  {
    return ($D ? " * FROM (SELECT t.*, rownum AS rnum FROM (SELECT $H$Z) t WHERE rownum <= " . ($_ + $D) . ") WHERE rnum > $D" : ($_ ? " * FROM (SELECT $H$Z) WHERE rownum <= " . ($_ + $D) : " $H$Z"));
  }
  function
  limit1($R, $H, $Z, $Ah = "\n")
  {
    return " $H$Z";
  }
  function
  db_collation($k, $jb)
  {
    return
      get_val("SELECT value FROM nls_database_parameters WHERE parameter = 'NLS_CHARACTERSET'");
  }
  function
  logged_user()
  {
    return
      get_val("SELECT USER FROM DUAL");
  }
  function
  get_current_db()
  {
    $k = connection()->_current_db ?: DB;
    unset(connection()->_current_db);
    return $k;
  }
  function
  where_owner($Dg, $fg = "owner")
  {
    if (!$_GET["ns"]) return '';
    return "$Dg$fg = sys_context('USERENV', 'CURRENT_SCHEMA')";
  }
  function
  views_table($e)
  {
    $fg = where_owner('');
    return "(SELECT $e FROM all_views WHERE " . ($fg ?: "rownum < 0") . ")";
  }
  function
  tables_list()
  {
    $uj = views_table("view_name");
    $fg = where_owner(" AND ");
    return
      get_key_vals("SELECT table_name, 'table' FROM all_tables WHERE tablespace_name = " . q(DB) . "$fg
UNION SELECT view_name, 'view' FROM $uj
ORDER BY 1");
  }
  function
  count_tables($j)
  {
    $J = array();
    foreach (
      $j
      as $k
    ) $J[$k] = get_val("SELECT COUNT(*) FROM all_tables WHERE tablespace_name = " . q($k));
    return $J;
  }
  function
  table_status($C = "")
  {
    $J = array();
    $uh = q($C);
    $k = get_current_db();
    $uj = views_table("view_name");
    $fg = where_owner(" AND ");
    foreach (
      get_rows('SELECT table_name "Name", \'table\' "Engine", avg_row_len * num_rows "Data_length", num_rows "Rows" FROM all_tables WHERE tablespace_name = ' . q($k) . $fg . ($C != "" ? " AND table_name = $uh" : "") . "
UNION SELECT view_name, 'view', 0, 0 FROM $uj" . ($C != "" ? " WHERE view_name = $uh" : "") . "
ORDER BY 1") as $K
    ) $J[$K["Name"]] = $K;
    return $J;
  }
  function
  is_view($S)
  {
    return $S["Engine"] == "view";
  }
  function
  fk_support($S)
  {
    return
      true;
  }
  function
  fields($R)
  {
    $J = array();
    $fg = where_owner(" AND ");
    foreach (get_rows("SELECT * FROM all_tab_columns WHERE table_name = " . q($R) . "$fg ORDER BY column_id") as $K) {
      $U = $K["DATA_TYPE"];
      $z = "$K[DATA_PRECISION],$K[DATA_SCALE]";
      if ($z == ",") $z = $K["CHAR_COL_DECL_LENGTH"];
      $J[$K["COLUMN_NAME"]] = array("field" => $K["COLUMN_NAME"], "full_type" => $U . ($z ? "($z)" : ""), "type" => strtolower($U), "length" => $z, "default" => $K["DATA_DEFAULT"], "null" => ($K["NULLABLE"] == "Y"), "privileges" => array("insert" => 1, "select" => 1, "update" => 1, "where" => 1, "order" => 1),);
    }
    return $J;
  }
  function
  indexes($R, $h = null)
  {
    $J = array();
    $fg = where_owner(" AND ", "aic.table_owner");
    foreach (
      get_rows("SELECT aic.*, ac.constraint_type, atc.data_default
FROM all_ind_columns aic
LEFT JOIN all_constraints ac ON aic.index_name = ac.constraint_name AND aic.table_name = ac.table_name AND aic.index_owner = ac.owner
LEFT JOIN all_tab_cols atc ON aic.column_name = atc.column_name AND aic.table_name = atc.table_name AND aic.index_owner = atc.owner
WHERE aic.table_name = " . q($R) . "$fg
ORDER BY ac.constraint_type, aic.column_position", $h) as $K
    ) {
      $Xd = $K["INDEX_NAME"];
      $lb = $K["DATA_DEFAULT"];
      $lb = ($lb ? trim($lb, '"') : $K["COLUMN_NAME"]);
      $J[$Xd]["type"] = ($K["CONSTRAINT_TYPE"] == "P" ? "PRIMARY" : ($K["CONSTRAINT_TYPE"] == "U" ? "UNIQUE" : "INDEX"));
      $J[$Xd]["columns"][] = $lb;
      $J[$Xd]["lengths"][] = ($K["CHAR_LENGTH"] && $K["CHAR_LENGTH"] != $K["COLUMN_LENGTH"] ? $K["CHAR_LENGTH"] : null);
      $J[$Xd]["descs"][] = ($K["DESCEND"] && $K["DESCEND"] == "DESC" ? '1' : null);
    }
    return $J;
  }
  function
  view($C)
  {
    $uj = views_table("view_name, text");
    $L = get_rows('SELECT text "select" FROM ' . $uj . ' WHERE view_name = ' . q($C));
    return
      reset($L);
  }
  function
  collations()
  {
    return
      array();
  }
  function
  information_schema($k)
  {
    return
      get_schema() == "INFORMATION_SCHEMA";
  }
  function
  error()
  {
    return
      h(connection()->error);
  }
  function
  explain($g, $H)
  {
    $g->query("EXPLAIN PLAN FOR $H");
    return $g->query("SELECT * FROM plan_table");
  }
  function
  found_rows($S, $Z) {}
  function
  auto_increment()
  {
    return "";
  }
  function
  alter_table($R, $C, $o, $id, $ob, $xc, $c, $Ba, $og)
  {
    $b = $hc = array();
    $Yf = ($R ? fields($R) : array());
    foreach (
      $o
      as $n
    ) {
      $X = $n[1];
      if ($X && $n[0] != "" && idf_escape($n[0]) != $X[0]) queries("ALTER TABLE " . table($R) . " RENAME COLUMN " . idf_escape($n[0]) . " TO $X[0]");
      $Xf = $Yf[$n[0]];
      if ($X && $Xf) {
        $Bf = process_field($Xf, $Xf);
        if ($X[2] == $Bf[2]) $X[2] = "";
      }
      if ($X) $b[] = ($R != "" ? ($n[0] != "" ? "MODIFY (" : "ADD (") : "  ") . implode($X) . ($R != "" ? ")" : "");
      else $hc[] = idf_escape($n[0]);
    }
    if ($R == "") return
      queries("CREATE TABLE " . table($C) . " (\n" . implode(",\n", $b) . "\n)");
    return (!$b || queries("ALTER TABLE " . table($R) . "\n" . implode("\n", $b))) && (!$hc || queries("ALTER TABLE " . table($R) . " DROP (" . implode(", ", $hc) . ")")) && ($R == $C || queries("ALTER TABLE " . table($R) . " RENAME TO " . table($C)));
  }
  function
  alter_indexes($R, $b)
  {
    $hc = array();
    $Og = array();
    foreach (
      $b
      as $X
    ) {
      if ($X[0] != "INDEX") {
        $X[2] = preg_replace('~ DESC$~', '', $X[2]);
        $i = ($X[2] == "DROP" ? "\nDROP CONSTRAINT " . idf_escape($X[1]) : "\nADD" . ($X[1] != "" ? " CONSTRAINT " . idf_escape($X[1]) : "") . " $X[0] " . ($X[0] == "PRIMARY" ? "KEY " : "") . "(" . implode(", ", $X[2]) . ")");
        array_unshift($Og, "ALTER TABLE " . table($R) . $i);
      } elseif ($X[2] == "DROP") $hc[] = idf_escape($X[1]);
      else $Og[] = "CREATE INDEX " . idf_escape($X[1] != "" ? $X[1] : uniqid($R . "_")) . " ON " . table($R) . " (" . implode(", ", $X[2]) . ")";
    }
    if ($hc) array_unshift($Og, "DROP INDEX " . implode(", ", $hc));
    foreach (
      $Og
      as $H
    ) {
      if (!queries($H)) return
        false;
    }
    return
      true;
  }
  function
  foreign_keys($R)
  {
    $J = array();
    $H = "SELECT c_list.CONSTRAINT_NAME as NAME,
c_src.COLUMN_NAME as SRC_COLUMN,
c_dest.OWNER as DEST_DB,
c_dest.TABLE_NAME as DEST_TABLE,
c_dest.COLUMN_NAME as DEST_COLUMN,
c_list.DELETE_RULE as ON_DELETE
FROM ALL_CONSTRAINTS c_list, ALL_CONS_COLUMNS c_src, ALL_CONS_COLUMNS c_dest
WHERE c_list.CONSTRAINT_NAME = c_src.CONSTRAINT_NAME
AND c_list.R_CONSTRAINT_NAME = c_dest.CONSTRAINT_NAME
AND c_list.CONSTRAINT_TYPE = 'R'
AND c_src.TABLE_NAME = " . q($R);
    foreach (get_rows($H) as $K) $J[$K['NAME']] = array("db" => $K['DEST_DB'], "table" => $K['DEST_TABLE'], "source" => array($K['SRC_COLUMN']), "target" => array($K['DEST_COLUMN']), "on_delete" => $K['ON_DELETE'], "on_update" => null,);
    return $J;
  }
  function
  truncate_tables($T)
  {
    return
      apply_queries("TRUNCATE TABLE", $T);
  }
  function
  drop_views($vj)
  {
    return
      apply_queries("DROP VIEW", $vj);
  }
  function
  drop_tables($T)
  {
    return
      apply_queries("DROP TABLE", $T);
  }
  function
  last_id($I)
  {
    return
      0;
  }
  function
  schemas()
  {
    $J = get_vals("SELECT DISTINCT owner FROM dba_segments WHERE owner IN (SELECT username FROM dba_users WHERE default_tablespace NOT IN ('SYSTEM','SYSAUX')) ORDER BY 1");
    return ($J ?: get_vals("SELECT DISTINCT owner FROM all_tables WHERE tablespace_name = " . q(DB) . " ORDER BY 1"));
  }
  function
  get_schema()
  {
    return
      get_val("SELECT sys_context('USERENV', 'SESSION_USER') FROM dual");
  }
  function
  set_schema($sh, $h = null)
  {
    if (!$h) $h = connection();
    return $h->query("ALTER SESSION SET CURRENT_SCHEMA = " . idf_escape($sh));
  }
  function
  show_variables()
  {
    return
      get_rows('SELECT name, display_value FROM v$parameter');
  }
  function
  show_status()
  {
    $J = array();
    $L = get_rows('SELECT * FROM v$instance');
    foreach (reset($L) as $y => $X) $J[] = array($y, $X);
    return $J;
  }
  function
  process_list()
  {
    return
      get_rows('SELECT
	sess.process AS "process",
	sess.username AS "user",
	sess.schemaname AS "schema",
	sess.status AS "status",
	sess.wait_class AS "wait_class",
	sess.seconds_in_wait AS "seconds_in_wait",
	sql.sql_text AS "sql_text",
	sess.machine AS "machine",
	sess.port AS "port"
FROM v$session sess LEFT OUTER JOIN v$sql sql
ON sql.sql_id = sess.sql_id
WHERE sess.type = \'USER\'
ORDER BY PROCESS
');
  }
  function
  convert_field($n) {}
  function
  unconvert_field($n, $J)
  {
    return $J;
  }
  function
  support($Uc)
  {
    return
      preg_match('~^(columns|database|drop_col|indexes|descidx|processlist|scheme|sql|status|table|variables|view)$~', $Uc);
  }
}
add_driver("mssql", "MS SQL");
if (isset($_GET["mssql"])) {
  define('Adminer\DRIVER', "mssql");
  if (extension_loaded("sqlsrv") && $_GET["ext"] != "pdo") {
    class
    Db
    extends
    SqlDb
    {
      var $extension = "sqlsrv";
      private $link, $result;
      private
      function
      get_error()
      {
        $this->error = "";
        foreach (sqlsrv_errors() as $m) {
          $this->errno = $m["code"];
          $this->error
            .= "$m[message]\n";
        }
        $this->error = rtrim($this->error);
      }
      function
      attach($N, $V, $F)
      {
        $vb = array("UID" => $V, "PWD" => $F, "CharacterSet" => "UTF-8");
        $Wh = adminer()->connectSsl();
        if (isset($Wh["Encrypt"])) $vb["Encrypt"] = $Wh["Encrypt"];
        if (isset($Wh["TrustServerCertificate"])) $vb["TrustServerCertificate"] = $Wh["TrustServerCertificate"];
        $k = adminer()->database();
        if ($k != "") $vb["Database"] = $k;
        $this->link = @sqlsrv_connect(preg_replace('~:~', ',', $N), $vb);
        if ($this->link) {
          $be = sqlsrv_server_info($this->link);
          $this->server_info = $be['SQLServerVersion'];
        } else $this->get_error();
        return ($this->link ? '' : $this->error);
      }
      function
      quote($Q)
      {
        $Yi = strlen($Q) != strlen(utf8_decode($Q));
        return ($Yi ? "N" : "") . "'" . str_replace("'", "''", $Q) . "'";
      }
      function
      select_db($Nb)
      {
        return $this->query(use_sql($Nb));
      }
      function
      query($H, $Xi = false)
      {
        $I = sqlsrv_query($this->link, $H);
        $this->error = "";
        if (!$I) {
          $this->get_error();
          return
            false;
        }
        return $this->store_result($I);
      }
      function
      multi_query($H)
      {
        $this->result = sqlsrv_query($this->link, $H);
        $this->error = "";
        if (!$this->result) {
          $this->get_error();
          return
            false;
        }
        return
          true;
      }
      function
      store_result($I = null)
      {
        if (!$I) $I = $this->result;
        if (!$I) return
          false;
        if (sqlsrv_field_metadata($I)) return
          new
          Result($I);
        $this->affected_rows = sqlsrv_rows_affected($I);
        return
          true;
      }
      function
      next_result()
      {
        return $this->result ? !!sqlsrv_next_result($this->result) : false;
      }
    }
    class
    Result
    {
      var $num_rows;
      private $result, $offset = 0, $fields;
      function
      __construct($I)
      {
        $this->result = $I;
      }
      private
      function
      convert($K)
      {
        foreach (
          (array)$K
          as $y => $X
        ) {
          if (is_a($X, 'DateTime')) $K[$y] = $X->format("Y-m-d H:i:s");
        }
        return $K;
      }
      function
      fetch_assoc()
      {
        return $this->convert(sqlsrv_fetch_array($this->result, SQLSRV_FETCH_ASSOC));
      }
      function
      fetch_row()
      {
        return $this->convert(sqlsrv_fetch_array($this->result, SQLSRV_FETCH_NUMERIC));
      }
      function
      fetch_field()
      {
        if (!$this->fields) $this->fields = sqlsrv_field_metadata($this->result);
        $n = $this->fields[$this->offset++];
        $J = new
          \stdClass;
        $J->name = $n["Name"];
        $J->type = ($n["Type"] == 1 ? 254 : 15);
        $J->charsetnr = 0;
        return $J;
      }
      function
      seek($D)
      {
        for ($t = 0; $t < $D; $t++) sqlsrv_fetch($this->result);
      }
      function
      __destruct()
      {
        sqlsrv_free_stmt($this->result);
      }
    }
    function
    last_id($I)
    {
      return
        get_val("SELECT SCOPE_IDENTITY()");
    }
    function
    explain($g, $H)
    {
      $g->query("SET SHOWPLAN_ALL ON");
      $J = $g->query($H);
      $g->query("SET SHOWPLAN_ALL OFF");
      return $J;
    }
  } else {
    abstract
    class
    MssqlDb
    extends
    PdoDb
    {
      function
      select_db($Nb)
      {
        return $this->query(use_sql($Nb));
      }
      function
      lastInsertId()
      {
        return $this->pdo->lastInsertId();
      }
    }
    function
    last_id($I)
    {
      return
        connection()->lastInsertId();
    }
    function
    explain($g, $H) {}
    if (extension_loaded("pdo_sqlsrv")) {
      class
      Db
      extends
      MssqlDb
      {
        var $extension = "PDO_SQLSRV";
        function
        attach($N, $V, $F)
        {
          return $this->dsn("sqlsrv:Server=" . str_replace(":", ",", $N), $V, $F);
        }
      }
    } elseif (extension_loaded("pdo_dblib")) {
      class
      Db
      extends
      MssqlDb
      {
        var $extension = "PDO_DBLIB";
        function
        attach($N, $V, $F)
        {
          return $this->dsn("dblib:charset=utf8;host=" . str_replace(":", ";unix_socket=", preg_replace('~:(\d)~', ';port=\1', $N)), $V, $F);
        }
      }
    }
  }
  class
  Driver
  extends
  SqlDriver
  {
    static $Pc = array("SQLSRV", "PDO_SQLSRV", "PDO_DBLIB");
    static $re = "mssql";
    var $insertFunctions = array("date|time" => "getdate");
    var $editFunctions = array("int|decimal|real|float|money|datetime" => "+/-", "char|text" => "+",);
    var $operators = array("=", "<", ">", "<=", ">=", "!=", "LIKE", "LIKE %%", "IN", "IS NULL", "NOT LIKE", "NOT IN", "IS NOT NULL");
    var $functions = array("len", "lower", "round", "upper");
    var $grouping = array("avg", "count", "count distinct", "max", "min", "sum");
    var $generated = array("PERSISTED", "VIRTUAL");
    var $onActions = "NO ACTION|CASCADE|SET NULL|SET DEFAULT";
    static
    function
    connect($N, $V, $F)
    {
      if ($N == "") $N = "localhost:1433";
      return
        parent::connect($N, $V, $F);
    }
    function
    __construct(Db $g)
    {
      parent::__construct($g);
      $this->types = array(lang(25) => array("tinyint" => 3, "smallint" => 5, "int" => 10, "bigint" => 20, "bit" => 1, "decimal" => 0, "real" => 12, "float" => 53, "smallmoney" => 10, "money" => 20), lang(26) => array("date" => 10, "smalldatetime" => 19, "datetime" => 19, "datetime2" => 19, "time" => 8, "datetimeoffset" => 10), lang(27) => array("char" => 8000, "varchar" => 8000, "text" => 2147483647, "nchar" => 4000, "nvarchar" => 4000, "ntext" => 1073741823), lang(28) => array("binary" => 8000, "varbinary" => 8000, "image" => 2147483647),);
    }
    function
    insertUpdate($R, array $L, array $G)
    {
      $o = fields($R);
      $fj = array();
      $Z = array();
      $O = reset($L);
      $e = "c" . implode(", c", range(1, count($O)));
      $Qa = 0;
      $fe = array();
      foreach (
        $O
        as $y => $X
      ) {
        $Qa++;
        $C = idf_unescape($y);
        if (!$o[$C]["auto_increment"]) $fe[$y] = "c$Qa";
        if (isset($G[$C])) $Z[] = "$y = c$Qa";
        else $fj[] = "$y = c$Qa";
      }
      $qj = array();
      foreach (
        $L
        as $O
      ) $qj[] = "(" . implode(", ", $O) . ")";
      if ($Z) {
        $Qd = queries("SET IDENTITY_INSERT " . table($R) . " ON");
        $J = queries("MERGE " . table($R) . " USING (VALUES\n\t" . implode(",\n\t", $qj) . "\n) AS source ($e) ON " . implode(" AND ", $Z) . ($fj ? "\nWHEN MATCHED THEN UPDATE SET " . implode(", ", $fj) : "") . "\nWHEN NOT MATCHED THEN INSERT (" . implode(", ", array_keys($Qd ? $O : $fe)) . ") VALUES (" . ($Qd ? $e : implode(", ", $fe)) . ");");
        if ($Qd) queries("SET IDENTITY_INSERT " . table($R) . " OFF");
      } else $J = queries("INSERT INTO " . table($R) . " (" . implode(", ", array_keys($O)) . ") VALUES\n" . implode(",\n", $qj));
      return $J;
    }
    function
    begin()
    {
      return
        queries("BEGIN TRANSACTION");
    }
    function
    tableHelp($C, $pe = false)
    {
      $Je = array("sys" => "catalog-views/sys-", "INFORMATION_SCHEMA" => "information-schema-views/",);
      $A = $Je[get_schema()];
      if ($A) return "relational-databases/system-$A" . preg_replace('~_~', '-', strtolower($C)) . "-transact-sql";
    }
  }
  function
  idf_escape($v)
  {
    return "[" . str_replace("]", "]]", $v) . "]";
  }
  function
  table($v)
  {
    return ($_GET["ns"] != "" ? idf_escape($_GET["ns"]) . "." : "") . idf_escape($v);
  }
  function
  get_databases($gd)
  {
    return
      get_vals("SELECT name FROM sys.databases WHERE name NOT IN ('master', 'tempdb', 'model', 'msdb')");
  }
  function
  limit($H, $Z, $_, $D = 0, $Ah = " ")
  {
    return ($_ ? " TOP (" . ($_ + $D) . ")" : "") . " $H$Z";
  }
  function
  limit1($R, $H, $Z, $Ah = "\n")
  {
    return
      limit($H, $Z, 1, 0, $Ah);
  }
  function
  db_collation($k, $jb)
  {
    return
      get_val("SELECT collation_name FROM sys.databases WHERE name = " . q($k));
  }
  function
  logged_user()
  {
    return
      get_val("SELECT SUSER_NAME()");
  }
  function
  tables_list()
  {
    return
      get_key_vals("SELECT name, type_desc FROM sys.all_objects WHERE schema_id = SCHEMA_ID(" . q(get_schema()) . ") AND type IN ('S', 'U', 'V') ORDER BY name");
  }
  function
  count_tables($j)
  {
    $J = array();
    foreach (
      $j
      as $k
    ) {
      connection()->select_db($k);
      $J[$k] = get_val("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES");
    }
    return $J;
  }
  function
  table_status($C = "")
  {
    $J = array();
    foreach (
      get_rows("SELECT ao.name AS Name, ao.type_desc AS Engine, (SELECT value FROM fn_listextendedproperty(default, 'SCHEMA', schema_name(schema_id), 'TABLE', ao.name, null, null)) AS Comment
FROM sys.all_objects AS ao
WHERE schema_id = SCHEMA_ID(" . q(get_schema()) . ") AND type IN ('S', 'U', 'V') " . ($C != "" ? "AND name = " . q($C) : "ORDER BY name")) as $K
    ) $J[$K["Name"]] = $K;
    return $J;
  }
  function
  is_view($S)
  {
    return $S["Engine"] == "VIEW";
  }
  function
  fk_support($S)
  {
    return
      true;
  }
  function
  fields($R)
  {
    $qb = get_key_vals("SELECT objname, cast(value as varchar(max)) FROM fn_listextendedproperty('MS_DESCRIPTION', 'schema', " . q(get_schema()) . ", 'table', " . q($R) . ", 'column', NULL)");
    $J = array();
    $ii = get_val("SELECT object_id FROM sys.all_objects WHERE schema_id = SCHEMA_ID(" . q(get_schema()) . ") AND type IN ('S', 'U', 'V') AND name = " . q($R));
    foreach (
      get_rows("SELECT c.max_length, c.precision, c.scale, c.name, c.is_nullable, c.is_identity, c.collation_name, t.name type, d.definition [default], d.name default_constraint, i.is_primary_key
FROM sys.all_columns c
JOIN sys.types t ON c.user_type_id = t.user_type_id
LEFT JOIN sys.default_constraints d ON c.default_object_id = d.object_id
LEFT JOIN sys.index_columns ic ON c.object_id = ic.object_id AND c.column_id = ic.column_id
LEFT JOIN sys.indexes i ON ic.object_id = i.object_id AND ic.index_id = i.index_id
WHERE c.object_id = " . q($ii)) as $K
    ) {
      $U = $K["type"];
      $z = (preg_match("~char|binary~", $U) ? intval($K["max_length"]) / ($U[0] == 'n' ? 2 : 1) : ($U == "decimal" ? "$K[precision],$K[scale]" : ""));
      $J[$K["name"]] = array("field" => $K["name"], "full_type" => $U . ($z ? "($z)" : ""), "type" => $U, "length" => $z, "default" => (preg_match("~^\('(.*)'\)$~", $K["default"], $B) ? str_replace("''", "'", $B[1]) : $K["default"]), "default_constraint" => $K["default_constraint"], "null" => $K["is_nullable"], "auto_increment" => $K["is_identity"], "collation" => $K["collation_name"], "privileges" => array("insert" => 1, "select" => 1, "update" => 1, "where" => 1, "order" => 1), "primary" => $K["is_primary_key"], "comment" => $qb[$K["name"]],);
    }
    foreach (get_rows("SELECT * FROM sys.computed_columns WHERE object_id = " . q($ii)) as $K) {
      $J[$K["name"]]["generated"] = ($K["is_persisted"] ? "PERSISTED" : "VIRTUAL");
      $J[$K["name"]]["default"] = $K["definition"];
    }
    return $J;
  }
  function
  indexes($R, $h = null)
  {
    $J = array();
    foreach (
      get_rows("SELECT i.name, key_ordinal, is_unique, is_primary_key, c.name AS column_name, is_descending_key
FROM sys.indexes i
INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
WHERE OBJECT_NAME(i.object_id) = " . q($R), $h) as $K
    ) {
      $C = $K["name"];
      $J[$C]["type"] = ($K["is_primary_key"] ? "PRIMARY" : ($K["is_unique"] ? "UNIQUE" : "INDEX"));
      $J[$C]["lengths"] = array();
      $J[$C]["columns"][$K["key_ordinal"]] = $K["column_name"];
      $J[$C]["descs"][$K["key_ordinal"]] = ($K["is_descending_key"] ? '1' : null);
    }
    return $J;
  }
  function
  view($C)
  {
    return
      array("select" => preg_replace('~^(?:[^[]|\[[^]]*])*\s+AS\s+~isU', '', get_val("SELECT VIEW_DEFINITION FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA = SCHEMA_NAME() AND TABLE_NAME = " . q($C))));
  }
  function
  collations()
  {
    $J = array();
    foreach (get_vals("SELECT name FROM fn_helpcollations()") as $c) $J[preg_replace('~_.*~', '', $c)][] = $c;
    return $J;
  }
  function
  information_schema($k)
  {
    return
      get_schema() == "INFORMATION_SCHEMA";
  }
  function
  error()
  {
    return
      nl_br(h(preg_replace('~^(\[[^]]*])+~m', '', connection()->error)));
  }
  function
  create_database($k, $c)
  {
    return
      queries("CREATE DATABASE " . idf_escape($k) . (preg_match('~^[a-z0-9_]+$~i', $c) ? " COLLATE $c" : ""));
  }
  function
  drop_databases($j)
  {
    return
      queries("DROP DATABASE " . implode(", ", array_map('Adminer\idf_escape', $j)));
  }
  function
  rename_database($C, $c)
  {
    if (preg_match('~^[a-z0-9_]+$~i', $c)) queries("ALTER DATABASE " . idf_escape(DB) . " COLLATE $c");
    queries("ALTER DATABASE " . idf_escape(DB) . " MODIFY NAME = " . idf_escape($C));
    return
      true;
  }
  function
  auto_increment()
  {
    return " IDENTITY" . ($_POST["Auto_increment"] != "" ? "(" . number($_POST["Auto_increment"]) . ",1)" : "") . " PRIMARY KEY";
  }
  function
  alter_table($R, $C, $o, $id, $ob, $xc, $c, $Ba, $og)
  {
    $b = array();
    $qb = array();
    $Yf = fields($R);
    foreach (
      $o
      as $n
    ) {
      $d = idf_escape($n[0]);
      $X = $n[1];
      if (!$X) $b["DROP"][] = " COLUMN $d";
      else {
        $X[1] = preg_replace("~( COLLATE )'(\\w+)'~", '\1\2', $X[1]);
        $qb[$n[0]] = $X[5];
        unset($X[5]);
        if (preg_match('~ AS ~', $X[3])) unset($X[1], $X[2]);
        if ($n[0] == "") $b["ADD"][] = "\n  " . implode("", $X) . ($R == "" ? substr($id[$X[0]], 16 + strlen($X[0])) : "");
        else {
          $l = $X[3];
          unset($X[3]);
          unset($X[6]);
          if ($d != $X[0]) queries("EXEC sp_rename " . q(table($R) . ".$d") . ", " . q(idf_unescape($X[0])) . ", 'COLUMN'");
          $b["ALTER COLUMN " . implode("", $X)][] = "";
          $Xf = $Yf[$n[0]];
          if (default_value($Xf) != $l) {
            if ($Xf["default"] !== null) $b["DROP"][] = " " . idf_escape($Xf["default_constraint"]);
            if ($l) $b["ADD"][] = "\n $l FOR $d";
          }
        }
      }
    }
    if ($R == "") return
      queries("CREATE TABLE " . table($C) . " (" . implode(",", (array)$b["ADD"]) . "\n)");
    if ($R != $C) queries("EXEC sp_rename " . q(table($R)) . ", " . q($C));
    if ($id) $b[""] = $id;
    foreach (
      $b
      as $y => $X
    ) {
      if (!queries("ALTER TABLE " . table($C) . " $y" . implode(",", $X))) return
        false;
    }
    foreach (
      $qb
      as $y => $X
    ) {
      $ob = substr($X, 9);
      queries("EXEC sp_dropextendedproperty @name = N'MS_Description', @level0type = N'Schema', @level0name = " . q(get_schema()) . ", @level1type = N'Table', @level1name = " . q($C) . ", @level2type = N'Column', @level2name = " . q($y));
      queries("EXEC sp_addextendedproperty
@name = N'MS_Description',
@value = $ob,
@level0type = N'Schema',
@level0name = " . q(get_schema()) . ",
@level1type = N'Table',
@level1name = " . q($C) . ",
@level2type = N'Column',
@level2name = " . q($y));
    }
    return
      true;
  }
  function
  alter_indexes($R, $b)
  {
    $w = array();
    $hc = array();
    foreach (
      $b
      as $X
    ) {
      if ($X[2] == "DROP") {
        if ($X[0] == "PRIMARY") $hc[] = idf_escape($X[1]);
        else $w[] = idf_escape($X[1]) . " ON " . table($R);
      } elseif (!queries(($X[0] != "PRIMARY" ? "CREATE $X[0] " . ($X[0] != "INDEX" ? "INDEX " : "") . idf_escape($X[1] != "" ? $X[1] : uniqid($R . "_")) . " ON " . table($R) : "ALTER TABLE " . table($R) . " ADD PRIMARY KEY") . " (" . implode(", ", $X[2]) . ")")) return
        false;
    }
    return (!$w || queries("DROP INDEX " . implode(", ", $w))) && (!$hc || queries("ALTER TABLE " . table($R) . " DROP " . implode(", ", $hc)));
  }
  function
  found_rows($S, $Z) {}
  function
  foreign_keys($R)
  {
    $J = array();
    $If = array("CASCADE", "NO ACTION", "SET NULL", "SET DEFAULT");
    foreach (get_rows("EXEC sp_fkeys @fktable_name = " . q($R) . ", @fktable_owner = " . q(get_schema())) as $K) {
      $q = &$J[$K["FK_NAME"]];
      $q["db"] = $K["PKTABLE_QUALIFIER"];
      $q["ns"] = $K["PKTABLE_OWNER"];
      $q["table"] = $K["PKTABLE_NAME"];
      $q["on_update"] = $If[$K["UPDATE_RULE"]];
      $q["on_delete"] = $If[$K["DELETE_RULE"]];
      $q["source"][] = $K["FKCOLUMN_NAME"];
      $q["target"][] = $K["PKCOLUMN_NAME"];
    }
    return $J;
  }
  function
  truncate_tables($T)
  {
    return
      apply_queries("TRUNCATE TABLE", $T);
  }
  function
  drop_views($vj)
  {
    return
      queries("DROP VIEW " . implode(", ", array_map('Adminer\table', $vj)));
  }
  function
  drop_tables($T)
  {
    return
      queries("DROP TABLE " . implode(", ", array_map('Adminer\table', $T)));
  }
  function
  move_tables($T, $vj, $si)
  {
    return
      apply_queries("ALTER SCHEMA " . idf_escape($si) . " TRANSFER", array_merge($T, $vj));
  }
  function
  trigger($C, $R)
  {
    if ($C == "") return
      array();
    $L = get_rows("SELECT s.name [Trigger],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT' WHEN OBJECTPROPERTY(s.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE' WHEN OBJECTPROPERTY(s.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing],
c.text
FROM sysobjects s
JOIN syscomments c ON s.id = c.id
WHERE s.xtype = 'TR' AND s.name = " . q($C));
    $J = reset($L);
    if ($J) $J["Statement"] = preg_replace('~^.+\s+AS\s+~isU', '', $J["text"]);
    return $J;
  }
  function
  triggers($R)
  {
    $J = array();
    foreach (
      get_rows("SELECT sys1.name,
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT' WHEN OBJECTPROPERTY(sys1.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE' WHEN OBJECTPROPERTY(sys1.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing]
FROM sysobjects sys1
JOIN sysobjects sys2 ON sys1.parent_obj = sys2.id
WHERE sys1.xtype = 'TR' AND sys2.name = " . q($R)) as $K
    ) $J[$K["name"]] = array($K["Timing"], $K["Event"]);
    return $J;
  }
  function
  trigger_options()
  {
    return
      array("Timing" => array("AFTER", "INSTEAD OF"), "Event" => array("INSERT", "UPDATE", "DELETE"), "Type" => array("AS"),);
  }
  function
  schemas()
  {
    return
      get_vals("SELECT name FROM sys.schemas");
  }
  function
  get_schema()
  {
    if ($_GET["ns"] != "") return $_GET["ns"];
    return
      get_val("SELECT SCHEMA_NAME()");
  }
  function
  set_schema($qh)
  {
    $_GET["ns"] = $qh;
    return
      true;
  }
  function
  create_sql($R, $Ba, $bi)
  {
    if (is_view(table_status1($R))) {
      $uj = view($R);
      return "CREATE VIEW " . table($R) . " AS $uj[select]";
    }
    $o = array();
    $G = false;
    foreach (fields($R) as $C => $n) {
      $X = process_field($n, $n);
      if ($X[6]) $G = true;
      $o[] = implode("", $X);
    }
    foreach (indexes($R) as $C => $w) {
      if (!$G || $w["type"] != "PRIMARY") {
        $e = array();
        foreach ($w["columns"] as $y => $X) $e[] = idf_escape($X) . ($w["descs"][$y] ? " DESC" : "");
        $C = idf_escape($C);
        $o[] = ($w["type"] == "INDEX" ? "INDEX $C" : "CONSTRAINT $C " . ($w["type"] == "UNIQUE" ? "UNIQUE" : "PRIMARY KEY")) . " (" . implode(", ", $e) . ")";
      }
    }
    foreach (driver()->checkConstraints($R) as $C => $Xa) $o[] = "CONSTRAINT " . idf_escape($C) . " CHECK ($Xa)";
    return "CREATE TABLE " . table($R) . " (\n\t" . implode(",\n\t", $o) . "\n)";
  }
  function
  foreign_keys_sql($R)
  {
    $o = array();
    foreach (foreign_keys($R) as $id) $o[] = ltrim(format_foreign_key($id));
    return ($o ? "ALTER TABLE " . table($R) . " ADD\n\t" . implode(",\n\t", $o) . ";\n\n" : "");
  }
  function
  truncate_sql($R)
  {
    return "TRUNCATE TABLE " . table($R);
  }
  function
  use_sql($Nb)
  {
    return "USE " . idf_escape($Nb);
  }
  function
  trigger_sql($R)
  {
    $J = "";
    foreach (triggers($R) as $C => $Qi) $J
      .= create_trigger(" ON " . table($R), trigger($C, $R)) . ";";
    return $J;
  }
  function
  convert_field($n) {}
  function
  unconvert_field($n, $J)
  {
    return $J;
  }
  function
  support($Uc)
  {
    return
      preg_match('~^(check|comment|columns|database|drop_col|dump|indexes|descidx|scheme|sql|table|trigger|view|view_trigger)$~', $Uc);
  }
}
class
Adminer
{
  static $he;
  var $error = '';
  function
  name()
  {
    return "<a href='https://www.adminer.org/'" . target_blank() . " id='h1'><img src='" . h(preg_replace("~\\?.*~", "", ME) . "?file=logo.png&version=5.2.1") . "' width='24' height='24' alt='' id='logo'>Adminer</a>";
  }
  function
  credentials()
  {
    return
      array(SERVER, $_GET["username"], get_password());
  }
  function
  connectSsl() {}
  function
  permanentLogin($i = false)
  {
    return
      password_file($i);
  }
  function
  bruteForceKey()
  {
    return $_SERVER["REMOTE_ADDR"];
  }
  function
  serverName($N)
  {
    return
      h($N);
  }
  function
  database()
  {
    return
      DB;
  }
  function
  databases($gd = true)
  {
    return
      get_databases($gd);
  }
  function
  pluginsLinks() {}
  function
  operators()
  {
    return
      driver()->operators;
  }
  function
  schemas()
  {
    return
      schemas();
  }
  function
  queryTimeout()
  {
    return
      2;
  }
  function
  headers() {}
  function
  csp(array $Gb)
  {
    return $Gb;
  }
  function
  head($Kb = null)
  {
    return
      true;
  }
  function
  css()
  {
    $J = array();
    foreach (array("", "-dark") as $jf) {
      $p = "adminer$jf.css";
      if (file_exists($p)) $J[] = "$p?v=" . crc32(file_get_contents($p));
    }
    return $J;
  }
  function
  loginForm()
  {
    echo "<table class='layout'>\n", adminer()->loginFormField('driver', '<tr><th>' . lang(32) . '<td>', html_select("auth[driver]", SqlDriver::$gc, DRIVER, "loginDriver(this);")), adminer()->loginFormField('server', '<tr><th>' . lang(33) . '<td>', '<input name="auth[server]" value="' . h(SERVER) . '" title="hostname[:port]" placeholder="localhost" autocapitalize="off">'), adminer()->loginFormField('username', '<tr><th>' . lang(34) . '<td>', '<input name="auth[username]" id="username" autofocus value="' . h($_GET["username"]) . '" autocomplete="username" autocapitalize="off">' . script("const authDriver = qs('#username').form['auth[driver]']; authDriver && authDriver.onchange();")), adminer()->loginFormField('password', '<tr><th>' . lang(35) . '<td>', '<input type="password" name="auth[password]" autocomplete="current-password">'), adminer()->loginFormField('db', '<tr><th>' . lang(36) . '<td>', '<input name="auth[db]" value="' . h($_GET["db"]) . '" autocapitalize="off">'), "</table>\n", "<p><input type='submit' value='" . lang(37) . "'>\n", checkbox("auth[permanent]", 1, $_COOKIE["adminer_permanent"], lang(38)) . "\n";
  }
  function
  loginFormField($C, $Gd, $Y)
  {
    return $Gd . $Y . "\n";
  }
  function
  login($Le, $F)
  {
    if ($F == "") return
      lang(39, target_blank());
    return
      true;
  }
  function
  tableName(array $hi)
  {
    return
      h($hi["Name"]);
  }
  function
  fieldName(array $n, $Rf = 0)
  {
    $U = $n["full_type"];
    $ob = $n["comment"];
    return '<span title="' . h($U . ($ob != "" ? ($U ? ": " : "") . $ob : '')) . '">' . h($n["field"]) . '</span>';
  }
  function
  selectLinks(array $hi, $O = "")
  {
    echo '<p class="links">';
    $Je = array("select" => lang(40));
    if (support("table") || support("indexes")) $Je["table"] = lang(41);
    $pe = false;
    if (support("table")) {
      $pe = is_view($hi);
      if ($pe) $Je["view"] = lang(42);
      else $Je["create"] = lang(43);
    }
    if ($O !== null) $Je["edit"] = lang(44);
    $C = $hi["Name"];
    foreach (
      $Je
      as $y => $X
    ) echo " <a href='" . h(ME) . "$y=" . urlencode($C) . ($y == "edit" ? $O : "") . "'" . bold(isset($_GET[$y])) . ">$X</a>";
    echo
    doc_link(array(JUSH => driver()->tableHelp($C, $pe)), "?"), "\n";
  }
  function
  foreignKeys($R)
  {
    return
      foreign_keys($R);
  }
  function
  backwardKeys($R, $gi)
  {
    return
      array();
  }
  function
  backwardKeysPrint(array $Fa, array $K) {}
  function
  selectQuery($H, $Xh, $Sc = false)
  {
    $J = "</p>\n";
    if (!$Sc && ($yj = driver()->warnings())) {
      $u = "warnings";
      $J = ", <a href='#$u'>" . lang(45) . "</a>" . script("qsl('a').onclick = partial(toggle, '$u');", "") . "$J<div id='$u' class='hidden'>\n$yj</div>\n";
    }
    return "<p><code class='jush-" . JUSH . "'>" . h(str_replace("\n", " ", $H)) . "</code> <span class='time'>(" . format_time($Xh) . ")</span>" . (support("sql") ? " <a href='" . h(ME) . "sql=" . urlencode($H) . "'>" . lang(10) . "</a>" : "") . $J;
  }
  function
  sqlCommandQuery($H)
  {
    return
      shorten_utf8(trim($H), 1000);
  }
  function
  sqlPrintAfter() {}
  function
  rowDescription($R)
  {
    return "";
  }
  function
  rowDescriptions(array $L, array $jd)
  {
    return $L;
  }
  function
  selectLink($X, array $n) {}
  function
  selectVal($X, $A, array $n, $bg)
  {
    $J = ($X === null ? "<i>NULL</i>" : (preg_match("~char|binary|boolean~", $n["type"]) && !preg_match("~var~", $n["type"]) ? "<code>$X</code>" : (preg_match('~json~', $n["type"]) ? "<code class='jush-js'>$X</code>" : $X)));
    if (preg_match('~blob|bytea|raw|file~', $n["type"]) && !is_utf8($X)) $J = "<i>" . lang(46, strlen($bg)) . "</i>";
    return ($A ? "<a href='" . h($A) . "'" . (is_url($A) ? target_blank() : "") . ">$J</a>" : $J);
  }
  function
  editVal($X, array $n)
  {
    return $X;
  }
  function
  config()
  {
    return
      array();
  }
  function
  tableStructurePrint(array $o, $hi = null)
  {
    echo "<div class='scrollable'>\n", "<table class='nowrap odds'>\n", "<thead><tr><th>" . lang(47) . "<td>" . lang(48) . (support("comment") ? "<td>" . lang(49) : "") . "</thead>\n";
    $ai = driver()->structuredTypes();
    foreach (
      $o
      as $n
    ) {
      echo "<tr><th>" . h($n["field"]);
      $U = h($n["full_type"]);
      $c = h($n["collation"]);
      echo "<td><span title='$c'>" . (in_array($U, (array)$ai[lang(31)]) ? "<a href='" . h(ME . 'type=' . urlencode($U)) . "'>$U</a>" : $U . ($c && isset($hi["Collation"]) && $c != $hi["Collation"] ? " $c" : "")) . "</span>", ($n["null"] ? " <i>NULL</i>" : ""), ($n["auto_increment"] ? " <i>" . lang(50) . "</i>" : "");
      $l = h($n["default"]);
      echo (isset($n["default"]) ? " <span title='" . lang(51) . "'>[<b>" . ($n["generated"] ? "<code class='jush-" . JUSH . "'>$l</code>" : $l) . "</b>]</span>" : ""), (support("comment") ? "<td>" . h($n["comment"]) : ""), "\n";
    }
    echo "</table>\n", "</div>\n";
  }
  function
  tableIndexesPrint(array $x)
  {
    echo "<table>\n";
    foreach (
      $x
      as $C => $w
    ) {
      ksort($w["columns"]);
      $Gg = array();
      foreach ($w["columns"] as $y => $X) $Gg[] = "<i>" . h($X) . "</i>" . ($w["lengths"][$y] ? "(" . $w["lengths"][$y] . ")" : "") . ($w["descs"][$y] ? " DESC" : "");
      echo "<tr title='" . h($C) . "'><th>$w[type]<td>" . implode(", ", $Gg) . "\n";
    }
    echo "</table>\n";
  }
  function
  selectColumnsPrint(array $M, array $e)
  {
    print_fieldset("select", lang(52), $M);
    $t = 0;
    $M[""] = array();
    foreach (
      $M
      as $y => $X
    ) {
      $X = idx($_GET["columns"], $y, array());
      $d = select_input(" name='columns[$t][col]'", $e, $X["col"], ($y !== "" ? "selectFieldChange" : "selectAddRow"));
      echo "<div>" . (driver()->functions || driver()->grouping ? html_select("columns[$t][fun]", array(-1 => "") + array_filter(array(lang(53) => driver()->functions, lang(54) => driver()->grouping)), $X["fun"]) . on_help("event.target.value && event.target.value.replace(/ |\$/, '(') + ')'", 1) . script("qsl('select').onchange = function () { helpClose();" . ($y !== "" ? "" : " qsl('select, input', this.parentNode).onchange();") . " };", "") . "($d)" : $d) . "</div>\n";
      $t++;
    }
    echo "</div></fieldset>\n";
  }
  function
  selectSearchPrint(array $Z, array $e, array $x)
  {
    print_fieldset("search", lang(55), $Z);
    foreach (
      $x
      as $t => $w
    ) {
      if ($w["type"] == "FULLTEXT") echo "<div>(<i>" . implode("</i>, <i>", array_map('Adminer\h', $w["columns"])) . "</i>) AGAINST", " <input type='search' name='fulltext[$t]' value='" . h($_GET["fulltext"][$t]) . "'>", script("qsl('input').oninput = selectFieldChange;", ""), checkbox("boolean[$t]", 1, isset($_GET["boolean"][$t]), "BOOL"), "</div>\n";
    }
    $Ua = "this.parentNode.firstChild.onchange();";
    foreach (array_merge((array)$_GET["where"], array(array())) as $t => $X) {
      if (!$X || ("$X[col]$X[val]" != "" && in_array($X["op"], adminer()->operators()))) echo "<div>" . select_input(" name='where[$t][col]'", $e, $X["col"], ($X ? "selectFieldChange" : "selectAddRow"), "(" . lang(56) . ")"), html_select("where[$t][op]", adminer()->operators(), $X["op"], $Ua), "<input type='search' name='where[$t][val]' value='" . h($X["val"]) . "'>", script("mixin(qsl('input'), {oninput: function () { $Ua }, onkeydown: selectSearchKeydown, onsearch: selectSearchSearch});", ""), "</div>\n";
    }
    echo "</div></fieldset>\n";
  }
  function
  selectOrderPrint(array $Rf, array $e, array $x)
  {
    print_fieldset("sort", lang(57), $Rf);
    $t = 0;
    foreach ((array)$_GET["order"] as $y => $X) {
      if ($X != "") {
        echo "<div>" . select_input(" name='order[$t]'", $e, $X, "selectFieldChange"), checkbox("desc[$t]", 1, isset($_GET["desc"][$y]), lang(58)) . "</div>\n";
        $t++;
      }
    }
    echo "<div>" . select_input(" name='order[$t]'", $e, "", "selectAddRow"), checkbox("desc[$t]", 1, false, lang(58)) . "</div>\n", "</div></fieldset>\n";
  }
  function
  selectLimitPrint($_)
  {
    echo "<fieldset><legend>" . lang(59) . "</legend><div>", "<input type='number' name='limit' class='size' value='" . intval($_) . "'>", script("qsl('input').oninput = selectFieldChange;", ""), "</div></fieldset>\n";
  }
  function
  selectLengthPrint($yi)
  {
    if ($yi !== null) echo "<fieldset><legend>" . lang(60) . "</legend><div>", "<input type='number' name='text_length' class='size' value='" . h($yi) . "'>", "</div></fieldset>\n";
  }
  function
  selectActionPrint(array $x)
  {
    echo "<fieldset><legend>" . lang(61) . "</legend><div>", "<input type='submit' value='" . lang(52) . "'>", " <span id='noindex' title='" . lang(62) . "'></span>", "<script" . nonce() . ">\n", "const indexColumns = ";
    $e = array();
    foreach (
      $x
      as $w
    ) {
      $Jb = reset($w["columns"]);
      if ($w["type"] != "FULLTEXT" && $Jb) $e[$Jb] = 1;
    }
    $e[""] = 1;
    foreach (
      $e
      as $y => $X
    ) json_row($y);
    echo ";\n", "selectFieldChange.call(qs('#form')['select']);\n", "</script>\n", "</div></fieldset>\n";
  }
  function
  selectCommandPrint()
  {
    return !information_schema(DB);
  }
  function
  selectImportPrint()
  {
    return !information_schema(DB);
  }
  function
  selectEmailPrint(array $uc, array $e) {}
  function
  selectColumnsProcess(array $e, array $x)
  {
    $M = array();
    $vd = array();
    foreach ((array)$_GET["columns"] as $y => $X) {
      if ($X["fun"] == "count" || ($X["col"] != "" && (!$X["fun"] || in_array($X["fun"], driver()->functions) || in_array($X["fun"], driver()->grouping)))) {
        $M[$y] = apply_sql_function($X["fun"], ($X["col"] != "" ? idf_escape($X["col"]) : "*"));
        if (!in_array($X["fun"], driver()->grouping)) $vd[] = $M[$y];
      }
    }
    return
      array($M, $vd);
  }
  function
  selectSearchProcess(array $o, array $x)
  {
    $J = array();
    foreach (
      $x
      as $t => $w
    ) {
      if ($w["type"] == "FULLTEXT" && $_GET["fulltext"][$t] != "") $J[] = "MATCH (" . implode(", ", array_map('Adminer\idf_escape', $w["columns"])) . ") AGAINST (" . q($_GET["fulltext"][$t]) . (isset($_GET["boolean"][$t]) ? " IN BOOLEAN MODE" : "") . ")";
    }
    foreach ((array)$_GET["where"] as $y => $X) {
      $hb = $X["col"];
      if ("$hb$X[val]" != "" && in_array($X["op"], adminer()->operators())) {
        $sb = array();
        foreach (($hb != "" ? array($hb => $o[$hb]) : $o) as $C => $n) {
          $Dg = "";
          $rb = " $X[op]";
          if (preg_match('~IN$~', $X["op"])) {
            $Ud = process_length($X["val"]);
            $rb
              .= " " . ($Ud != "" ? $Ud : "(NULL)");
          } elseif ($X["op"] == "SQL") $rb = " $X[val]";
          elseif (preg_match('~^(I?LIKE) %%$~', $X["op"], $B)) $rb = " $B[1] " . adminer()->processInput($n, "%$X[val]%");
          elseif ($X["op"] == "FIND_IN_SET") {
            $Dg = "$X[op](" . q($X["val"]) . ", ";
            $rb = ")";
          } elseif (!preg_match('~NULL$~', $X["op"])) $rb
            .= " " . adminer()->processInput($n, $X["val"]);
          if ($hb != "" || (isset($n["privileges"]["where"]) && (preg_match('~^[-\d.' . (preg_match('~IN$~', $X["op"]) ? ',' : '') . ']+$~', $X["val"]) || !preg_match('~' . number_type() . '|bit~', $n["type"])) && (!preg_match("~[\x80-\xFF]~", $X["val"]) || preg_match('~char|text|enum|set~', $n["type"])) && (!preg_match('~date|timestamp~', $n["type"]) || preg_match('~^\d+-\d+-\d+~', $X["val"])))) $sb[] = $Dg . driver()->convertSearch(idf_escape($C), $X, $n) . $rb;
        }
        $J[] = (count($sb) == 1 ? $sb[0] : ($sb ? "(" . implode(" OR ", $sb) . ")" : "1 = 0"));
      }
    }
    return $J;
  }
  function
  selectOrderProcess(array $o, array $x)
  {
    $J = array();
    foreach ((array)$_GET["order"] as $y => $X) {
      if ($X != "") $J[] = (preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~', $X) ? $X : idf_escape($X)) . (isset($_GET["desc"][$y]) ? " DESC" : "");
    }
    return $J;
  }
  function
  selectLimitProcess()
  {
    return (isset($_GET["limit"]) ? intval($_GET["limit"]) : 50);
  }
  function
  selectLengthProcess()
  {
    return (isset($_GET["text_length"]) ? "$_GET[text_length]" : "100");
  }
  function
  selectEmailProcess(array $Z, array $jd)
  {
    return
      false;
  }
  function
  selectQueryBuild(array $M, array $Z, array $vd, array $Rf, $_, $E)
  {
    return "";
  }
  function
  messageQuery($H, $zi, $Sc = false)
  {
    restart_session();
    $Id = &get_session("queries");
    if (!idx($Id, $_GET["db"])) $Id[$_GET["db"]] = array();
    if (strlen($H) > 1e6) $H = preg_replace('~[\x80-\xFF]+$~', '', substr($H, 0, 1e6)) . "\nâ¦";
    $Id[$_GET["db"]][] = array($H, time(), $zi);
    $Th = "sql-" . count($Id[$_GET["db"]]);
    $J = "<a href='#$Th' class='toggle'>" . lang(63) . "</a>\n";
    if (!$Sc && ($yj = driver()->warnings())) {
      $u = "warnings-" . count($Id[$_GET["db"]]);
      $J = "<a href='#$u' class='toggle'>" . lang(45) . "</a>, $J<div id='$u' class='hidden'>\n$yj</div>\n";
    }
    return " <span class='time'>" . @date("H:i:s") . "</span>" . " $J<div id='$Th' class='hidden'><pre><code class='jush-" . JUSH . "'>" . shorten_utf8($H, 1000) . "</code></pre>" . ($zi ? " <span class='time'>($zi)</span>" : '') . (support("sql") ? '<p><a href="' . h(str_replace("db=" . urlencode(DB), "db=" . urlencode($_GET["db"]), ME) . 'sql=&history=' . (count($Id[$_GET["db"]]) - 1)) . '">' . lang(10) . '</a>' : '') . '</div>';
  }
  function
  editRowPrint($R, array $o, $K, $fj) {}
  function
  editFunctions(array $n)
  {
    $J = ($n["null"] ? "NULL/" : "");
    $fj = isset($_GET["select"]) || where($_GET);
    foreach (array(driver()->insertFunctions, driver()->editFunctions) as $y => $qd) {
      if (!$y || (!isset($_GET["call"]) && $fj)) {
        foreach (
          $qd
          as $sg => $X
        ) {
          if (!$sg || preg_match("~$sg~", $n["type"])) $J
            .= "/$X";
        }
      }
      if ($y && $qd && !preg_match('~set|blob|bytea|raw|file|bool~', $n["type"])) $J
        .= "/SQL";
    }
    if ($n["auto_increment"] && !$fj) $J = lang(50);
    return
      explode("/", $J);
  }
  function
  editInput($R, array $n, $_a, $Y)
  {
    if ($n["type"] == "enum") return (isset($_GET["select"]) ? "<label><input type='radio'$_a value='-1' checked><i>" . lang(8) . "</i></label> " : "") . ($n["null"] ? "<label><input type='radio'$_a value=''" . ($Y !== null || isset($_GET["select"]) ? "" : " checked") . "><i>NULL</i></label> " : "") . enum_input("radio", $_a, $n, $Y, $Y === 0 ? 0 : null);
    return "";
  }
  function
  editHint($R, array $n, $Y)
  {
    return "";
  }
  function
  processInput(array $n, $Y, $s = "")
  {
    if ($s == "SQL") return $Y;
    $C = $n["field"];
    $J = q($Y);
    if (preg_match('~^(now|getdate|uuid)$~', $s)) $J = "$s()";
    elseif (preg_match('~^current_(date|timestamp)$~', $s)) $J = $s;
    elseif (preg_match('~^([+-]|\|\|)$~', $s)) $J = idf_escape($C) . " $s $J";
    elseif (preg_match('~^[+-] interval$~', $s)) $J = idf_escape($C) . " $s " . (preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i", $Y) ? $Y : $J);
    elseif (preg_match('~^(addtime|subtime|concat)$~', $s)) $J = "$s(" . idf_escape($C) . ", $J)";
    elseif (preg_match('~^(md5|sha1|password|encrypt)$~', $s)) $J = "$s($J)";
    return
      unconvert_field($n, $J);
  }
  function
  dumpOutput()
  {
    $J = array('text' => lang(64), 'file' => lang(65));
    if (function_exists('gzencode')) $J['gz'] = 'gzip';
    return $J;
  }
  function
  dumpFormat()
  {
    return (support("dump") ? array('sql' => 'SQL') : array()) + array('csv' => 'CSV,', 'csv;' => 'CSV;', 'tsv' => 'TSV');
  }
  function
  dumpDatabase($k) {}
  function
  dumpTable($R, $bi, $pe = 0)
  {
    if ($_POST["format"] != "sql") {
      echo "\xef\xbb\xbf";
      if ($bi) dump_csv(array_keys(fields($R)));
    } else {
      if ($pe == 2) {
        $o = array();
        foreach (fields($R) as $C => $n) $o[] = idf_escape($C) . " $n[full_type]";
        $i = "CREATE TABLE " . table($R) . " (" . implode(", ", $o) . ")";
      } else $i = create_sql($R, $_POST["auto_increment"], $bi);
      set_utf8mb4($i);
      if ($bi && $i) {
        if ($bi == "DROP+CREATE" || $pe == 1) echo "DROP " . ($pe == 2 ? "VIEW" : "TABLE") . " IF EXISTS " . table($R) . ";\n";
        if ($pe == 1) $i = remove_definer($i);
        echo "$i;\n\n";
      }
    }
  }
  function
  dumpData($R, $bi, $H)
  {
    if ($bi) {
      $Te = (JUSH == "sqlite" ? 0 : 1048576);
      $o = array();
      $Rd = false;
      if ($_POST["format"] == "sql") {
        if ($bi == "TRUNCATE+INSERT") echo
        truncate_sql($R) . ";\n";
        $o = fields($R);
        if (JUSH == "mssql") {
          foreach (
            $o
            as $n
          ) {
            if ($n["auto_increment"]) {
              echo "SET IDENTITY_INSERT " . table($R) . " ON;\n";
              $Rd = true;
              break;
            }
          }
        }
      }
      $I = connection()->query($H, 1);
      if ($I) {
        $fe = "";
        $Pa = "";
        $ue = array();
        $rd = array();
        $di = "";
        $Vc = ($R != '' ? 'fetch_assoc' : 'fetch_row');
        $Cb = 0;
        while ($K = $I->$Vc()) {
          if (!$ue) {
            $qj = array();
            foreach (
              $K
              as $X
            ) {
              $n = $I->fetch_field();
              if (idx($o[$n->name], 'generated')) {
                $rd[$n->name] = true;
                continue;
              }
              $ue[] = $n->name;
              $y = idf_escape($n->name);
              $qj[] = "$y = VALUES($y)";
            }
            $di = ($bi == "INSERT+UPDATE" ? "\nON DUPLICATE KEY UPDATE " . implode(", ", $qj) : "") . ";\n";
          }
          if ($_POST["format"] != "sql") {
            if ($bi == "table") {
              dump_csv($ue);
              $bi = "INSERT";
            }
            dump_csv($K);
          } else {
            if (!$fe) $fe = "INSERT INTO " . table($R) . " (" . implode(", ", array_map('Adminer\idf_escape', $ue)) . ") VALUES";
            foreach (
              $K
              as $y => $X
            ) {
              if ($rd[$y]) {
                unset($K[$y]);
                continue;
              }
              $n = $o[$y];
              $K[$y] = ($X !== null ? unconvert_field($n, preg_match(number_type(), $n["type"]) && !preg_match('~\[~', $n["full_type"]) && is_numeric($X) ? $X : q(($X === false ? 0 : $X))) : "NULL");
            }
            $oh = ($Te ? "\n" : " ") . "(" . implode(",\t", $K) . ")";
            if (!$Pa) $Pa = $fe . $oh;
            elseif (JUSH == 'mssql' ? $Cb % 1000 != 0 : strlen($Pa) + 4 + strlen($oh) + strlen($di) < $Te) $Pa
              .= ",$oh";
            else {
              echo $Pa . $di;
              $Pa = $fe . $oh;
            }
          }
          $Cb++;
        }
        if ($Pa) echo $Pa . $di;
      } elseif ($_POST["format"] == "sql") echo "-- " . str_replace("\n", " ", connection()->error) . "\n";
      if ($Rd) echo "SET IDENTITY_INSERT " . table($R) . " OFF;\n";
    }
  }
  function
  dumpFilename($Pd)
  {
    return
      friendly_url($Pd != "" ? $Pd : (SERVER != "" ? SERVER : "localhost"));
  }
  function
  dumpHeaders($Pd, $lf = false)
  {
    $eg = $_POST["output"];
    $Nc = (preg_match('~sql~', $_POST["format"]) ? "sql" : ($lf ? "tar" : "csv"));
    header("Content-Type: " . ($eg == "gz" ? "application/x-gzip" : ($Nc == "tar" ? "application/x-tar" : ($Nc == "sql" || $eg != "file" ? "text/plain" : "text/csv") . "; charset=utf-8")));
    if ($eg == "gz") {
      ob_start(function ($Q) {
        return
          gzencode($Q);
      }, 1e6);
    }
    return $Nc;
  }
  function
  dumpFooter()
  {
    if ($_POST["format"] == "sql") echo "-- " . gmdate("Y-m-d H:i:s e") . "\n";
  }
  function
  importServerPath()
  {
    return "adminer.sql";
  }
  function
  homepage()
  {
    echo '<p class="links">' . ($_GET["ns"] == "" && support("database") ? '<a href="' . h(ME) . 'database=">' . lang(66) . "</a>\n" : ""), (support("scheme") ? "<a href='" . h(ME) . "scheme='>" . ($_GET["ns"] != "" ? lang(67) : lang(68)) . "</a>\n" : ""), ($_GET["ns"] !== "" ? '<a href="' . h(ME) . 'schema=">' . lang(69) . "</a>\n" : ""), (support("privileges") ? "<a href='" . h(ME) . "privileges='>" . lang(70) . "</a>\n" : "");
    return
      true;
  }
  function
  navigation($if)
  {
    echo "<h1>" . adminer()->name() . " <span class='version'>" . VERSION;
    $tf = $_COOKIE["adminer_version"];
    echo " <a href='https://www.adminer.org/#download'" . target_blank() . " id='version'>" . (version_compare(VERSION, $tf) < 0 ? h($tf) : "") . "</a>", "</span></h1>\n";
    switch_lang();
    if ($if == "auth") {
      $eg = "";
      foreach ((array)$_SESSION["pwds"] as $sj => $Fh) {
        foreach (
          $Fh
          as $N => $nj
        ) {
          $C = h(get_setting("vendor-$sj-$N") ?: get_driver($sj));
          foreach (
            $nj
            as $V => $F
          ) {
            if ($F !== null) {
              $Qb = $_SESSION["db"][$sj][$N][$V];
              foreach (($Qb ? array_keys($Qb) : array("")) as $k) $eg
                .= "<li><a href='" . h(auth_url($sj, $N, $V, $k)) . "'>($C) " . h($V . ($N != "" ? "@" . adminer()->serverName($N) : "") . ($k != "" ? " - $k" : "")) . "</a>\n";
            }
          }
        }
      }
      if ($eg) echo "<ul id='logins'>\n$eg</ul>\n" . script("mixin(qs('#logins'), {onmouseover: menuOver, onmouseout: menuOut});");
    } else {
      $T = array();
      if ($_GET["ns"] !== "" && !$if && DB != "") {
        connection()->select_db(DB);
        $T = table_status('', true);
      }
      adminer()->syntaxHighlighting($T);
      adminer()->databasesPrint($if);
      $ka = array();
      if (DB == "" || !$if) {
        if (support("sql")) {
          $ka[] = "<a href='" . h(ME) . "sql='" . bold(isset($_GET["sql"]) && !isset($_GET["import"])) . ">" . lang(63) . "</a>";
          $ka[] = "<a href='" . h(ME) . "import='" . bold(isset($_GET["import"])) . ">" . lang(71) . "</a>";
        }
        $ka[] = "<a href='" . h(ME) . "dump=" . urlencode(isset($_GET["table"]) ? $_GET["table"] : $_GET["select"]) . "' id='dump'" . bold(isset($_GET["dump"])) . ">" . lang(72) . "</a>";
      }
      $Vd = $_GET["ns"] !== "" && !$if && DB != "";
      if ($Vd) $ka[] = '<a href="' . h(ME) . 'create="' . bold($_GET["create"] === "") . ">" . lang(73) . "</a>";
      echo ($ka ? "<p class='links'>\n" . implode("\n", $ka) . "\n" : "");
      if ($Vd) {
        if ($T) adminer()->tablesPrint($T);
        else
          echo "<p class='message'>" . lang(9) . "</p>\n";
      }
    }
  }
  function
  syntaxHighlighting(array $T)
  {
    echo
    script_src(preg_replace("~\\?.*~", "", ME) . "?file=jush.js&version=5.2.1", true);
    if (support("sql")) {
      echo "<script" . nonce() . ">\n";
      if ($T) {
        $Je = array();
        foreach (
          $T
          as $R => $U
        ) $Je[] = preg_quote($R, '/');
        echo "var jushLinks = { " . JUSH . ": [ '" . js_escape(ME) . (support("table") ? "table=" : "select=") . "\$&', /\\b(" . implode("|", $Je) . ")\\b/g ] };\n";
        foreach (array("bac", "bra", "sqlite_quo", "mssql_bra") as $X) echo "jushLinks.$X = jushLinks." . JUSH . ";\n";
        if (isset($_GET["sql"]) || isset($_GET["trigger"]) || isset($_GET["check"])) {
          $oi = array_fill_keys(array_keys($T), array());
          foreach (driver()->allFields() as $R => $o) {
            foreach (
              $o
              as $n
            ) $oi[$R][] = $n["field"];
          }
          echo "addEventListener('DOMContentLoaded', () => { autocompleter = jush.autocompleteSql('" . idf_escape("") . "', " . json_encode($oi) . "); });\n";
        }
      }
      echo "</script>\n";
    }
    echo
    script("syntaxHighlighting('" . preg_replace('~^(\d\.?\d).*~s', '\1', connection()->server_info) . "', '" . connection()->flavor . "');");
  }
  function
  databasesPrint($if)
  {
    $j = adminer()->databases();
    if (DB && $j && !in_array(DB, $j)) array_unshift($j, DB);
    echo "<form action=''>\n<p id='dbs'>\n";
    hidden_fields_get();
    $Ob = script("mixin(qsl('select'), {onmousedown: dbMouseDown, onchange: dbChange});");
    echo "<label title='" . lang(36) . "'>" . lang(74) . ": " . ($j ? html_select("db", array("" => "") + $j, DB) . $Ob : "<input name='db' value='" . h(DB) . "' autocapitalize='off' size='19'>\n") . "</label>", "<input type='submit' value='" . lang(20) . "'" . ($j ? " class='hidden'" : "") . ">\n";
    if (support("scheme")) {
      if ($if != "db" && DB != "" && connection()->select_db(DB)) {
        echo "<br><label>" . lang(75) . ": " . html_select("ns", array("" => "") + adminer()->schemas(), $_GET["ns"]) . "$Ob</label>";
        if ($_GET["ns"] != "") set_schema($_GET["ns"]);
      }
    }
    foreach (array("import", "sql", "schema", "dump", "privileges") as $X) {
      if (isset($_GET[$X])) {
        echo
        input_hidden($X);
        break;
      }
    }
    echo "</p></form>\n";
  }
  function
  tablesPrint(array $T)
  {
    echo "<ul id='tables'>" . script("mixin(qs('#tables'), {onmouseover: menuOver, onmouseout: menuOut});");
    foreach (
      $T
      as $R => $P
    ) {
      $R = "$R";
      $C = adminer()->tableName($P);
      if ($C != "") echo '<li><a href="' . h(ME) . 'select=' . urlencode($R) . '"' . bold($_GET["select"] == $R || $_GET["edit"] == $R, "select") . " title='" . lang(40) . "'>" . lang(76) . "</a> ", (support("table") || support("indexes") ? '<a href="' . h(ME) . 'table=' . urlencode($R) . '"' . bold(in_array($R, array($_GET["table"], $_GET["create"], $_GET["indexes"], $_GET["foreign"], $_GET["trigger"], $_GET["check"], $_GET["view"])), (is_view($P) ? "view" : "structure")) . " title='" . lang(41) . "'>$C</a>" : "<span>$C</span>") . "\n";
    }
    echo "</ul>\n";
  }
}
class
Plugins
{
  private
  static $va = array('dumpFormat' => true, 'dumpOutput' => true, 'editRowPrint' => true, 'editFunctions' => true, 'config' => true);
  var $plugins;
  var $error = '';
  private $hooks = array();
  function
  __construct($xg)
  {
    if ($xg === null) {
      $xg = array();
      $Ja = "adminer-plugins";
      if (is_dir($Ja)) {
        foreach (glob("$Ja/*.php") as $p) $Wd = include_once "./$p";
      }
      $Hd = " href='https://www.adminer.org/plugins/#use'" . target_blank();
      if (file_exists("$Ja.php")) {
        $Wd = include_once "./$Ja.php";
        if (is_array($Wd)) {
          foreach (
            $Wd
            as $wg
          ) $xg[get_class($wg)] = $wg;
        } else $this->error
          .= lang(77, "<b>$Ja.php</b>", $Hd) . "<br>";
      }
      foreach (get_declared_classes() as $eb) {
        if (!$xg[$eb] && preg_match('~^Adminer\w~i', $eb)) {
          $Yg = new
            \ReflectionClass($eb);
          $xb = $Yg->getConstructor();
          if ($xb && $xb->getNumberOfRequiredParameters()) $this->error
            .= lang(78, $Hd, "<b>$eb</b>", "<b>$Ja.php</b>") . "<br>";
          else $xg[$eb] = new $eb;
        }
      }
    }
    $this->plugins = $xg;
    $na = new
      Adminer;
    $xg[] = $na;
    $Yg = new
      \ReflectionObject($na);
    foreach ($Yg->getMethods() as $gf) {
      foreach (
        $xg
        as $wg
      ) {
        $C = $gf->getName();
        if (method_exists($wg, $C)) $this->hooks[$C][] = $wg;
      }
    }
  }
  function
  __call($C, array $jg)
  {
    $wa = array();
    foreach (
      $jg
      as $y => $X
    ) $wa[] = &$jg[$y];
    $J = null;
    foreach ($this->hooks[$C] as $wg) {
      $Y = call_user_func_array(array($wg, $C), $wa);
      if ($Y !== null) {
        if (!self::$va[$C]) return $Y;
        $J = $Y + (array)$J;
      }
    }
    return $J;
  }
}
abstract
class
Plugin
{
  protected $translations = array();
  function
  description()
  {
    return $this->lang('');
  }
  function
  screenshot()
  {
    return "";
  }
  protected
  function
  lang($v, $yf = null)
  {
    $wa = func_get_args();
    $wa[0] = idx($this->translations[LANG], $v) ?: $v;
    return
      call_user_func_array('Adminer\lang_format', $wa);
  }
}
Adminer::$he = (function_exists('adminer_object') ? adminer_object() : (is_dir("adminer-plugins") || file_exists("adminer-plugins.php") ? new
  Plugins(null) : new
  Adminer));
SqlDriver::$gc = array("server" => "MySQL / MariaDB") + SqlDriver::$gc;
if (!defined('Adminer\DRIVER')) {
  define('Adminer\DRIVER', "server");
  if (extension_loaded("mysqli") && $_GET["ext"] != "pdo") {
    class
    Db
    extends
    \MySQLi
    {
      static $he;
      var $extension = "MySQLi", $flavor = '';
      function
      __construct()
      {
        parent::init();
      }
      function
      attach($N, $V, $F)
      {
        mysqli_report(MYSQLI_REPORT_OFF);
        list($Ld, $yg) = explode(":", $N, 2);
        $Wh = adminer()->connectSsl();
        if ($Wh) $this->ssl_set($Wh['key'], $Wh['cert'], $Wh['ca'], '', '');
        $J = @$this->real_connect(($N != "" ? $Ld : ini_get("mysqli.default_host")), ($N . $V != "" ? $V : ini_get("mysqli.default_user")), ($N . $V . $F != "" ? $F : ini_get("mysqli.default_pw")), null, (is_numeric($yg) ? intval($yg) : ini_get("mysqli.default_port")), (is_numeric($yg) ? $yg : null), ($Wh ? ($Wh['verify'] !== false ? 2048 : 64) : 0));
        $this->options(MYSQLI_OPT_LOCAL_INFILE, false);
        return ($J ? '' : $this->error);
      }
      function
      set_charset($Wa)
      {
        if (parent::set_charset($Wa)) return
          true;
        parent::set_charset('utf8');
        return $this->query("SET NAMES $Wa");
      }
      function
      next_result()
      {
        return
          self::more_results() && parent::next_result();
      }
      function
      quote($Q)
      {
        return "'" . $this->escape_string($Q) . "'";
      }
    }
  } elseif (extension_loaded("mysql") && !((ini_bool("sql.safe_mode") || ini_bool("mysql.allow_local_infile")) && extension_loaded("pdo_mysql"))) {
    class
    Db
    extends
    SqlDb
    {
      private $link;
      function
      attach($N, $V, $F)
      {
        if (ini_bool("mysql.allow_local_infile")) return
          lang(79, "'mysql.allow_local_infile'", "MySQLi", "PDO_MySQL");
        $this->link = @mysql_connect(($N != "" ? $N : ini_get("mysql.default_host")), ("$N$V" != "" ? $V : ini_get("mysql.default_user")), ("$N$V$F" != "" ? $F : ini_get("mysql.default_password")), true, 131072);
        if (!$this->link) return
          mysql_error();
        $this->server_info = mysql_get_server_info($this->link);
        return '';
      }
      function
      set_charset($Wa)
      {
        if (function_exists('mysql_set_charset')) {
          if (mysql_set_charset($Wa, $this->link)) return
            true;
          mysql_set_charset('utf8', $this->link);
        }
        return $this->query("SET NAMES $Wa");
      }
      function
      quote($Q)
      {
        return "'" . mysql_real_escape_string($Q, $this->link) . "'";
      }
      function
      select_db($Nb)
      {
        return
          mysql_select_db($Nb, $this->link);
      }
      function
      query($H, $Xi = false)
      {
        $I = @($Xi ? mysql_unbuffered_query($H, $this->link) : mysql_query($H, $this->link));
        $this->error = "";
        if (!$I) {
          $this->errno = mysql_errno($this->link);
          $this->error = mysql_error($this->link);
          return
            false;
        }
        if ($I === true) {
          $this->affected_rows = mysql_affected_rows($this->link);
          $this->info = mysql_info($this->link);
          return
            true;
        }
        return
          new
          Result($I);
      }
    }
    class
    Result
    {
      var $num_rows;
      private $result;
      private $offset = 0;
      function
      __construct($I)
      {
        $this->result = $I;
        $this->num_rows = mysql_num_rows($I);
      }
      function
      fetch_assoc()
      {
        return
          mysql_fetch_assoc($this->result);
      }
      function
      fetch_row()
      {
        return
          mysql_fetch_row($this->result);
      }
      function
      fetch_field()
      {
        $J = mysql_fetch_field($this->result, $this->offset++);
        $J->orgtable = $J->table;
        $J->charsetnr = ($J->blob ? 63 : 0);
        return $J;
      }
      function
      __destruct()
      {
        mysql_free_result($this->result);
      }
    }
  } elseif (extension_loaded("pdo_mysql")) {
    class
    Db
    extends
    PdoDb
    {
      var $extension = "PDO_MySQL";
      function
      attach($N, $V, $F)
      {
        $Pf = array(\PDO::MYSQL_ATTR_LOCAL_INFILE => false);
        $Wh = adminer()->connectSsl();
        if ($Wh) {
          if ($Wh['key']) $Pf[\PDO::MYSQL_ATTR_SSL_KEY] = $Wh['key'];
          if ($Wh['cert']) $Pf[\PDO::MYSQL_ATTR_SSL_CERT] = $Wh['cert'];
          if ($Wh['ca']) $Pf[\PDO::MYSQL_ATTR_SSL_CA] = $Wh['ca'];
          if (isset($Wh['verify'])) $Pf[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = $Wh['verify'];
        }
        return $this->dsn("mysql:charset=utf8;host=" . str_replace(":", ";unix_socket=", preg_replace('~:(\d)~', ';port=\1', $N)), $V, $F, $Pf);
      }
      function
      set_charset($Wa)
      {
        return $this->query("SET NAMES $Wa");
      }
      function
      select_db($Nb)
      {
        return $this->query("USE " . idf_escape($Nb));
      }
      function
      query($H, $Xi = false)
      {
        $this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, !$Xi);
        return
          parent::query($H, $Xi);
      }
    }
  }
  class
  Driver
  extends
  SqlDriver
  {
    static $Pc = array("MySQLi", "MySQL", "PDO_MySQL");
    static $re = "sql";
    var $unsigned = array("unsigned", "zerofill", "unsigned zerofill");
    var $operators = array("=", "<", ">", "<=", ">=", "!=", "LIKE", "LIKE %%", "REGEXP", "IN", "FIND_IN_SET", "IS NULL", "NOT LIKE", "NOT REGEXP", "NOT IN", "IS NOT NULL", "SQL");
    var $functions = array("char_length", "date", "from_unixtime", "lower", "round", "floor", "ceil", "sec_to_time", "time_to_sec", "upper");
    var $grouping = array("avg", "count", "count distinct", "group_concat", "max", "min", "sum");
    static
    function
    connect($N, $V, $F)
    {
      $g = parent::connect($N, $V, $F);
      if (is_string($g)) {
        if (function_exists('iconv') && !is_utf8($g) && strlen($oh = iconv("windows-1250", "utf-8", $g)) > strlen($g)) $g = $oh;
        return $g;
      }
      $g->set_charset(charset($g));
      $g->query("SET sql_quote_show_create = 1, autocommit = 1");
      $g->flavor = (preg_match('~MariaDB~', $g->server_info) ? 'maria' : 'mysql');
      add_driver(DRIVER, ($g->flavor == 'maria' ? "MariaDB" : "MySQL"));
      return $g;
    }
    function
    __construct(Db $g)
    {
      parent::__construct($g);
      $this->types = array(lang(25) => array("tinyint" => 3, "smallint" => 5, "mediumint" => 8, "int" => 10, "bigint" => 20, "decimal" => 66, "float" => 12, "double" => 21), lang(26) => array("date" => 10, "datetime" => 19, "timestamp" => 19, "time" => 10, "year" => 4), lang(27) => array("char" => 255, "varchar" => 65535, "tinytext" => 255, "text" => 65535, "mediumtext" => 16777215, "longtext" => 4294967295), lang(80) => array("enum" => 65535, "set" => 64), lang(28) => array("bit" => 20, "binary" => 255, "varbinary" => 65535, "tinyblob" => 255, "blob" => 65535, "mediumblob" => 16777215, "longblob" => 4294967295), lang(30) => array("geometry" => 0, "point" => 0, "linestring" => 0, "polygon" => 0, "multipoint" => 0, "multilinestring" => 0, "multipolygon" => 0, "geometrycollection" => 0),);
      $this->insertFunctions = array("char" => "md5/sha1/password/encrypt/uuid", "binary" => "md5/sha1", "date|time" => "now",);
      $this->editFunctions = array(number_type() => "+/-", "date" => "+ interval/- interval", "time" => "addtime/subtime", "char|text" => "concat",);
      if (min_version('5.7.8', 10.2, $g)) $this->types[lang(27)]["json"] = 4294967295;
      if (min_version('', 10.7, $g)) {
        $this->types[lang(27)]["uuid"] = 128;
        $this->insertFunctions['uuid'] = 'uuid';
      }
      if (min_version(9, '', $g)) {
        $this->types[lang(25)]["vector"] = 16383;
        $this->insertFunctions['vector'] = 'string_to_vector';
      }
      if (min_version(5.7, 10.2, $g)) $this->generated = array("STORED", "VIRTUAL");
    }
    function
    unconvertFunction(array $n)
    {
      return (preg_match("~binary~", $n["type"]) ? "<code class='jush-sql'>UNHEX</code>" : ($n["type"] == "bit" ? doc_link(array('sql' => 'bit-value-literals.html'), "<code>b''</code>") : (preg_match("~geometry|point|linestring|polygon~", $n["type"]) ? "<code class='jush-sql'>GeomFromText</code>" : "")));
    }
    function
    insert($R, array $O)
    {
      return ($O ? parent::insert($R, $O) : queries("INSERT INTO " . table($R) . " ()\nVALUES ()"));
    }
    function
    insertUpdate($R, array $L, array $G)
    {
      $e = array_keys(reset($L));
      $Dg = "INSERT INTO " . table($R) . " (" . implode(", ", $e) . ") VALUES\n";
      $qj = array();
      foreach (
        $e
        as $y
      ) $qj[$y] = "$y = VALUES($y)";
      $di = "\nON DUPLICATE KEY UPDATE " . implode(", ", $qj);
      $qj = array();
      $z = 0;
      foreach (
        $L
        as $O
      ) {
        $Y = "(" . implode(", ", $O) . ")";
        if ($qj && (strlen($Dg) + $z + strlen($Y) + strlen($di) > 1e6)) {
          if (!queries($Dg . implode(",\n", $qj) . $di)) return
            false;
          $qj = array();
          $z = 0;
        }
        $qj[] = $Y;
        $z += strlen($Y) + 2;
      }
      return
        queries($Dg . implode(",\n", $qj) . $di);
    }
    function
    slowQuery($H, $_i)
    {
      if (min_version('5.7.8', '10.1.2')) {
        if ($this->conn->flavor == 'maria') return "SET STATEMENT max_statement_time=$_i FOR $H";
        elseif (preg_match('~^(SELECT\b)(.+)~is', $H, $B)) return "$B[1] /*+ MAX_EXECUTION_TIME(" . ($_i * 1000) . ") */ $B[2]";
      }
    }
    function
    convertSearch($v, array $X, array $n)
    {
      return (preg_match('~char|text|enum|set~', $n["type"]) && !preg_match("~^utf8~", $n["collation"]) && preg_match('~[\x80-\xFF]~', $X['val']) ? "CONVERT($v USING " . charset($this->conn) . ")" : $v);
    }
    function
    warnings()
    {
      $I = $this->conn->query("SHOW WARNINGS");
      if ($I && $I->num_rows) {
        ob_start();
        print_select_result($I);
        return
          ob_get_clean();
      }
    }
    function
    tableHelp($C, $pe = false)
    {
      $Ne = ($this->conn->flavor == 'maria');
      if (information_schema(DB)) return
        strtolower("information-schema-" . ($Ne ? "$C-table/" : str_replace("_", "-", $C) . "-table.html"));
      if (DB == "mysql") return ($Ne ? "mysql$C-table/" : "system-schema.html");
    }
    function
    hasCStyleEscapes()
    {
      static $Ra;
      if ($Ra === null) {
        $Uh = get_val("SHOW VARIABLES LIKE 'sql_mode'", 1, $this->conn);
        $Ra = (strpos($Uh, 'NO_BACKSLASH_ESCAPES') === false);
      }
      return $Ra;
    }
    function
    engines()
    {
      $J = array();
      foreach (get_rows("SHOW ENGINES") as $K) {
        if (preg_match("~YES|DEFAULT~", $K["Support"])) $J[] = $K["Engine"];
      }
      return $J;
    }
  }
  function
  idf_escape($v)
  {
    return "`" . str_replace("`", "``", $v) . "`";
  }
  function
  table($v)
  {
    return
      idf_escape($v);
  }
  function
  get_databases($gd)
  {
    $J = get_session("dbs");
    if ($J === null) {
      $H = "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";
      $J = ($gd ? slow_query($H) : get_vals($H));
      restart_session();
      set_session("dbs", $J);
      stop_session();
    }
    return $J;
  }
  function
  limit($H, $Z, $_, $D = 0, $Ah = " ")
  {
    return " $H$Z" . ($_ ? $Ah . "LIMIT $_" . ($D ? " OFFSET $D" : "") : "");
  }
  function
  limit1($R, $H, $Z, $Ah = "\n")
  {
    return
      limit($H, $Z, 1, 0, $Ah);
  }
  function
  db_collation($k, array $jb)
  {
    $J = null;
    $i = get_val("SHOW CREATE DATABASE " . idf_escape($k), 1);
    if (preg_match('~ COLLATE ([^ ]+)~', $i, $B)) $J = $B[1];
    elseif (preg_match('~ CHARACTER SET ([^ ]+)~', $i, $B)) $J = $jb[$B[1]][-1];
    return $J;
  }
  function
  logged_user()
  {
    return
      get_val("SELECT USER()");
  }
  function
  tables_list()
  {
    return
      get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");
  }
  function
  count_tables(array $j)
  {
    $J = array();
    foreach (
      $j
      as $k
    ) $J[$k] = count(get_vals("SHOW TABLES IN " . idf_escape($k)));
    return $J;
  }
  function
  table_status($C = "", $Tc = false)
  {
    $J = array();
    foreach (get_rows($Tc ? "SELECT TABLE_NAME AS Name, ENGINE AS Engine, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() " . ($C != "" ? "AND TABLE_NAME = " . q($C) : "ORDER BY Name") : "SHOW TABLE STATUS" . ($C != "" ? " LIKE " . q(addcslashes($C, "%_\\")) : "")) as $K) {
      if ($K["Engine"] == "InnoDB") $K["Comment"] = preg_replace('~(?:(.+); )?InnoDB free: .*~', '\1', $K["Comment"]);
      if (!isset($K["Engine"])) $K["Comment"] = "";
      if ($C != "") $K["Name"] = $C;
      $J[$K["Name"]] = $K;
    }
    return $J;
  }
  function
  is_view(array $S)
  {
    return $S["Engine"] === null;
  }
  function
  fk_support(array $S)
  {
    return
      preg_match('~InnoDB|IBMDB2I' . (min_version(5.6) ? '|NDB' : '') . '~i', $S["Engine"]);
  }
  function
  fields($R)
  {
    $Ne = (connection()->flavor == 'maria');
    $J = array();
    foreach (get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = " . q($R) . " ORDER BY ORDINAL_POSITION") as $K) {
      $n = $K["COLUMN_NAME"];
      $U = $K["COLUMN_TYPE"];
      $sd = $K["GENERATION_EXPRESSION"];
      $Qc = $K["EXTRA"];
      preg_match('~^(VIRTUAL|PERSISTENT|STORED)~', $Qc, $rd);
      preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~', $U, $Qe);
      $l = $K["COLUMN_DEFAULT"];
      if ($l != "") {
        $oe = preg_match('~text|json~', $Qe[1]);
        if (!$Ne && $oe) $l = preg_replace("~^(_\w+)?('.*')$~", '\2', stripslashes($l));
        if ($Ne || $oe) {
          $l = ($l == "NULL" ? null : preg_replace_callback("~^'(.*)'$~", function ($B) {
            return
              stripslashes(str_replace("''", "'", $B[1]));
          }, $l));
        }
        if (!$Ne && preg_match('~binary~', $Qe[1]) && preg_match('~^0x(\w*)$~', $l, $B)) $l = pack("H*", $B[1]);
      }
      $J[$n] = array("field" => $n, "full_type" => $U, "type" => $Qe[1], "length" => $Qe[2], "unsigned" => ltrim($Qe[3] . $Qe[4]), "default" => ($rd ? ($Ne ? $sd : stripslashes($sd)) : $l), "null" => ($K["IS_NULLABLE"] == "YES"), "auto_increment" => ($Qc == "auto_increment"), "on_update" => (preg_match('~\bon update (\w+)~i', $Qc, $B) ? $B[1] : ""), "collation" => $K["COLLATION_NAME"], "privileges" => array_flip(explode(",", "$K[PRIVILEGES],where,order")), "comment" => $K["COLUMN_COMMENT"], "primary" => ($K["COLUMN_KEY"] == "PRI"), "generated" => ($rd[1] == "PERSISTENT" ? "STORED" : $rd[1]),);
    }
    return $J;
  }
  function
  indexes($R, $h = null)
  {
    $J = array();
    foreach (get_rows("SHOW INDEX FROM " . table($R), $h) as $K) {
      $C = $K["Key_name"];
      $J[$C]["type"] = ($C == "PRIMARY" ? "PRIMARY" : ($K["Index_type"] == "FULLTEXT" ? "FULLTEXT" : ($K["Non_unique"] ? ($K["Index_type"] == "SPATIAL" ? "SPATIAL" : "INDEX") : "UNIQUE")));
      $J[$C]["columns"][] = $K["Column_name"];
      $J[$C]["lengths"][] = ($K["Index_type"] == "SPATIAL" ? null : $K["Sub_part"]);
      $J[$C]["descs"][] = null;
    }
    return $J;
  }
  function
  foreign_keys($R)
  {
    static $sg = '(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';
    $J = array();
    $Db = get_val("SHOW CREATE TABLE " . table($R), 1);
    if ($Db) {
      preg_match_all("~CONSTRAINT ($sg) FOREIGN KEY ?\\(((?:$sg,? ?)+)\\) REFERENCES ($sg)(?:\\.($sg))? \\(((?:$sg,? ?)+)\\)(?: ON DELETE (" . driver()->onActions . "))?(?: ON UPDATE (" . driver()->onActions . "))?~", $Db, $Re, PREG_SET_ORDER);
      foreach (
        $Re
        as $B
      ) {
        preg_match_all("~$sg~", $B[2], $Oh);
        preg_match_all("~$sg~", $B[5], $si);
        $J[idf_unescape($B[1])] = array("db" => idf_unescape($B[4] != "" ? $B[3] : $B[4]), "table" => idf_unescape($B[4] != "" ? $B[4] : $B[3]), "source" => array_map('Adminer\idf_unescape', $Oh[0]), "target" => array_map('Adminer\idf_unescape', $si[0]), "on_delete" => ($B[6] ?: "RESTRICT"), "on_update" => ($B[7] ?: "RESTRICT"),);
      }
    }
    return $J;
  }
  function
  view($C)
  {
    return
      array("select" => preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU', '', get_val("SHOW CREATE VIEW " . table($C), 1)));
  }
  function
  collations()
  {
    $J = array();
    foreach (get_rows("SHOW COLLATION") as $K) {
      if ($K["Default"]) $J[$K["Charset"]][-1] = $K["Collation"];
      else $J[$K["Charset"]][] = $K["Collation"];
    }
    ksort($J);
    foreach (
      $J
      as $y => $X
    ) sort($J[$y]);
    return $J;
  }
  function
  information_schema($k)
  {
    return ($k == "information_schema") || (min_version(5.5) && $k == "performance_schema");
  }
  function
  error()
  {
    return
      h(preg_replace('~^You have an error.*syntax to use~U', "Syntax error", connection()->error));
  }
  function
  create_database($k, $c)
  {
    return
      queries("CREATE DATABASE " . idf_escape($k) . ($c ? " COLLATE " . q($c) : ""));
  }
  function
  drop_databases(array $j)
  {
    $J = apply_queries("DROP DATABASE", $j, 'Adminer\idf_escape');
    restart_session();
    set_session("dbs", null);
    return $J;
  }
  function
  rename_database($C, $c)
  {
    $J = false;
    if (create_database($C, $c)) {
      $T = array();
      $vj = array();
      foreach (tables_list() as $R => $U) {
        if ($U == 'VIEW') $vj[] = $R;
        else $T[] = $R;
      }
      $J = (!$T && !$vj) || move_tables($T, $vj, $C);
      drop_databases($J ? array(DB) : array());
    }
    return $J;
  }
  function
  auto_increment()
  {
    $Ca = " PRIMARY KEY";
    if ($_GET["create"] != "" && $_POST["auto_increment_col"]) {
      foreach (indexes($_GET["create"]) as $w) {
        if (in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"], $w["columns"], true)) {
          $Ca = "";
          break;
        }
        if ($w["type"] == "PRIMARY") $Ca = " UNIQUE";
      }
    }
    return " AUTO_INCREMENT$Ca";
  }
  function
  alter_table($R, $C, array $o, array $id, $ob, $xc, $c, $Ba, $og)
  {
    $b = array();
    foreach (
      $o
      as $n
    ) {
      if ($n[1]) {
        $l = $n[1][3];
        if (preg_match('~ GENERATED~', $l)) {
          $n[1][3] = (connection()->flavor == 'maria' ? "" : $n[1][2]);
          $n[1][2] = $l;
        }
        $b[] = ($R != "" ? ($n[0] != "" ? "CHANGE " . idf_escape($n[0]) : "ADD") : " ") . " " . implode($n[1]) . ($R != "" ? $n[2] : "");
      } else $b[] = "DROP " . idf_escape($n[0]);
    }
    $b = array_merge($b, $id);
    $P = ($ob !== null ? " COMMENT=" . q($ob) : "") . ($xc ? " ENGINE=" . q($xc) : "") . ($c ? " COLLATE " . q($c) : "") . ($Ba != "" ? " AUTO_INCREMENT=$Ba" : "");
    if ($R == "") return
      queries("CREATE TABLE " . table($C) . " (\n" . implode(",\n", $b) . "\n)$P$og");
    if ($R != $C) $b[] = "RENAME TO " . table($C);
    if ($P) $b[] = ltrim($P);
    return ($b || $og ? queries("ALTER TABLE " . table($R) . "\n" . implode(",\n", $b) . $og) : true);
  }
  function
  alter_indexes($R, $b)
  {
    $Va = array();
    foreach (
      $b
      as $X
    ) $Va[] = ($X[2] == "DROP" ? "\nDROP INDEX " . idf_escape($X[1]) : "\nADD $X[0] " . ($X[0] == "PRIMARY" ? "KEY " : "") . ($X[1] != "" ? idf_escape($X[1]) . " " : "") . "(" . implode(", ", $X[2]) . ")");
    return
      queries("ALTER TABLE " . table($R) . implode(",", $Va));
  }
  function
  truncate_tables(array $T)
  {
    return
      apply_queries("TRUNCATE TABLE", $T);
  }
  function
  drop_views(array $vj)
  {
    return
      queries("DROP VIEW " . implode(", ", array_map('Adminer\table', $vj)));
  }
  function
  drop_tables(array $T)
  {
    return
      queries("DROP TABLE " . implode(", ", array_map('Adminer\table', $T)));
  }
  function
  move_tables(array $T, array $vj, $si)
  {
    $ch = array();
    foreach (
      $T
      as $R
    ) $ch[] = table($R) . " TO " . idf_escape($si) . "." . table($R);
    if (!$ch || queries("RENAME TABLE " . implode(", ", $ch))) {
      $Vb = array();
      foreach (
        $vj
        as $R
      ) $Vb[table($R)] = view($R);
      connection()->select_db($si);
      $k = idf_escape(DB);
      foreach (
        $Vb
        as $C => $uj
      ) {
        if (!queries("CREATE VIEW $C AS " . str_replace(" $k.", " ", $uj["select"])) || !queries("DROP VIEW $k.$C")) return
          false;
      }
      return
        true;
    }
    return
      false;
  }
  function
  copy_tables(array $T, array $vj, $si)
  {
    queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");
    foreach (
      $T
      as $R
    ) {
      $C = ($si == DB ? table("copy_$R") : idf_escape($si) . "." . table($R));
      if (($_POST["overwrite"] && !queries("\nDROP TABLE IF EXISTS $C")) || !queries("CREATE TABLE $C LIKE " . table($R)) || !queries("INSERT INTO $C SELECT * FROM " . table($R))) return
        false;
      foreach (get_rows("SHOW TRIGGERS LIKE " . q(addcslashes($R, "%_\\"))) as $K) {
        $Qi = $K["Trigger"];
        if (!queries("CREATE TRIGGER " . ($si == DB ? idf_escape("copy_$Qi") : idf_escape($si) . "." . idf_escape($Qi)) . " $K[Timing] $K[Event] ON $C FOR EACH ROW\n$K[Statement];")) return
          false;
      }
    }
    foreach (
      $vj
      as $R
    ) {
      $C = ($si == DB ? table("copy_$R") : idf_escape($si) . "." . table($R));
      $uj = view($R);
      if (($_POST["overwrite"] && !queries("DROP VIEW IF EXISTS $C")) || !queries("CREATE VIEW $C AS $uj[select]")) return
        false;
    }
    return
      true;
  }
  function
  trigger($C, $R)
  {
    if ($C == "") return
      array();
    $L = get_rows("SHOW TRIGGERS WHERE `Trigger` = " . q($C));
    return
      reset($L);
  }
  function
  triggers($R)
  {
    $J = array();
    foreach (get_rows("SHOW TRIGGERS LIKE " . q(addcslashes($R, "%_\\"))) as $K) $J[$K["Trigger"]] = array($K["Timing"], $K["Event"]);
    return $J;
  }
  function
  trigger_options()
  {
    return
      array("Timing" => array("BEFORE", "AFTER"), "Event" => array("INSERT", "UPDATE", "DELETE"), "Type" => array("FOR EACH ROW"),);
  }
  function
  routine($C, $U)
  {
    $ta = array("bool", "boolean", "integer", "double precision", "real", "dec", "numeric", "fixed", "national char", "national varchar");
    $Ph = "(?:\\s|/\\*[\s\S]*?\\*/|(?:#|-- )[^\n]*\n?|--\r?\n)";
    $zc = driver()->enumLength;
    $Vi = "((" . implode("|", array_merge(array_keys(driver()->types()), $ta)) . ")\\b(?:\\s*\\(((?:[^'\")]|$zc)++)\\))?" . "\\s*(zerofill\\s*)?(unsigned(?:\\s+zerofill)?)?)(?:\\s*(?:CHARSET|CHARACTER\\s+SET)\\s*['\"]?([^'\"\\s,]+)['\"]?)?";
    $sg = "$Ph*(" . ($U == "FUNCTION" ? "" : driver()->inout) . ")?\\s*(?:`((?:[^`]|``)*)`\\s*|\\b(\\S+)\\s+)$Vi";
    $i = get_val("SHOW CREATE $U " . idf_escape($C), 2);
    preg_match("~\\(((?:$sg\\s*,?)*)\\)\\s*" . ($U == "FUNCTION" ? "RETURNS\\s+$Vi\\s+" : "") . "(.*)~is", $i, $B);
    $o = array();
    preg_match_all("~$sg\\s*,?~is", $B[1], $Re, PREG_SET_ORDER);
    foreach (
      $Re
      as $ig
    ) $o[] = array("field" => str_replace("``", "`", $ig[2]) . $ig[3], "type" => strtolower($ig[5]), "length" => preg_replace_callback("~$zc~s", 'Adminer\normalize_enum', $ig[6]), "unsigned" => strtolower(preg_replace('~\s+~', ' ', trim("$ig[8] $ig[7]"))), "null" => true, "full_type" => $ig[4], "inout" => strtoupper($ig[1]), "collation" => strtolower($ig[9]),);
    return
      array("fields" => $o, "comment" => get_val("SELECT ROUTINE_COMMENT FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_NAME = " . q($C)),) + ($U != "FUNCTION" ? array("definition" => $B[11]) : array("returns" => array("type" => $B[12], "length" => $B[13], "unsigned" => $B[15], "collation" => $B[16]), "definition" => $B[17], "language" => "SQL",));
  }
  function
  routines()
  {
    return
      get_rows("SELECT ROUTINE_NAME AS SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");
  }
  function
  routine_languages()
  {
    return
      array();
  }
  function
  routine_id($C, array $K)
  {
    return
      idf_escape($C);
  }
  function
  last_id($I)
  {
    return
      get_val("SELECT LAST_INSERT_ID()");
  }
  function
  explain(Db $g, $H)
  {
    return $g->query("EXPLAIN " . (min_version(5.1) && !min_version(5.7) ? "PARTITIONS " : "") . $H);
  }
  function
  found_rows(array $S, array $Z)
  {
    return ($Z || $S["Engine"] != "InnoDB" ? null : $S["Rows"]);
  }
  function
  create_sql($R, $Ba, $bi)
  {
    $J = get_val("SHOW CREATE TABLE " . table($R), 1);
    if (!$Ba) $J = preg_replace('~ AUTO_INCREMENT=\d+~', '', $J);
    return $J;
  }
  function
  truncate_sql($R)
  {
    return "TRUNCATE " . table($R);
  }
  function
  use_sql($Nb)
  {
    return "USE " . idf_escape($Nb);
  }
  function
  trigger_sql($R)
  {
    $J = "";
    foreach (get_rows("SHOW TRIGGERS LIKE " . q(addcslashes($R, "%_\\")), null, "-- ") as $K) $J
      .= "\nCREATE TRIGGER " . idf_escape($K["Trigger"]) . " $K[Timing] $K[Event] ON " . table($K["Table"]) . " FOR EACH ROW\n$K[Statement];;\n";
    return $J;
  }
  function
  show_variables()
  {
    return
      get_rows("SHOW VARIABLES");
  }
  function
  show_status()
  {
    return
      get_rows("SHOW STATUS");
  }
  function
  process_list()
  {
    return
      get_rows("SHOW FULL PROCESSLIST");
  }
  function
  convert_field(array $n)
  {
    if (preg_match("~binary~", $n["type"])) return "HEX(" . idf_escape($n["field"]) . ")";
    if ($n["type"] == "bit") return "BIN(" . idf_escape($n["field"]) . " + 0)";
    if (preg_match("~geometry|point|linestring|polygon~", $n["type"])) return (min_version(8) ? "ST_" : "") . "AsWKT(" . idf_escape($n["field"]) . ")";
  }
  function
  unconvert_field(array $n, $J)
  {
    if (preg_match("~binary~", $n["type"])) $J = "UNHEX($J)";
    if ($n["type"] == "bit") $J = "CONVERT(b$J, UNSIGNED)";
    if (preg_match("~geometry|point|linestring|polygon~", $n["type"])) {
      $Dg = (min_version(8) ? "ST_" : "");
      $J = $Dg . "GeomFromText($J, $Dg" . "SRID($n[field]))";
    }
    return $J;
  }
  function
  support($Uc)
  {
    return !preg_match("~scheme|sequence|type|view_trigger|materializedview" . (min_version(8) ? "" : "|descidx" . (min_version(5.1) ? "" : "|event|partitioning")) . (min_version('8.0.16', '10.2.1') ? "" : "|check") . "~", $Uc);
  }
  function
  kill_process($X)
  {
    return
      queries("KILL " . number($X));
  }
  function
  connection_id()
  {
    return "SELECT CONNECTION_ID()";
  }
  function
  max_connections()
  {
    return
      get_val("SELECT @@max_connections");
  }
  function
  types()
  {
    return
      array();
  }
  function
  type_values($u)
  {
    return "";
  }
  function
  schemas()
  {
    return
      array();
  }
  function
  get_schema()
  {
    return "";
  }
  function
  set_schema($qh, $h = null)
  {
    return
      true;
  }
}
define('Adminer\JUSH', Driver::$re);
define('Adminer\SERVER', $_GET[DRIVER]);
define('Adminer\DB', $_GET["db"]);
define('Adminer\ME', preg_replace('~\?.*~', '', relative_uri()) . '?' . (sid() ? SID . '&' : '') . (SERVER !== null ? DRIVER . "=" . urlencode(SERVER) . '&' : '') . ($_GET["ext"] ? "ext=" . urlencode($_GET["ext"]) . '&' : '') . (isset($_GET["username"]) ? "username=" . urlencode($_GET["username"]) . '&' : '') . (DB != "" ? 'db=' . urlencode(DB) . '&' . (isset($_GET["ns"]) ? "ns=" . urlencode($_GET["ns"]) . "&" : "") : ''));
function
page_header($Bi, $m = "", $Oa = array(), $Ci = "")
{
  page_headers();
  if (is_ajax() && $m) {
    page_messages($m);
    exit;
  }
  if (!ob_get_level()) ob_start('ob_gzhandler', 4096);
  $Di = $Bi . ($Ci != "" ? ": $Ci" : "");
  $Ei = strip_tags($Di . (SERVER != "" && SERVER != "localhost" ? h(" - " . SERVER) : "") . " - " . adminer()->name());
  echo '<!DOCTYPE html>
<html lang="', LANG, '" dir="', lang(81), '">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>', $Ei, '</title>
<link rel="stylesheet" href="', h(preg_replace("~\\?.*~", "", ME) . "?file=default.css&version=5.2.1"), '">
';
  $Hb = adminer()->css();
  $Dd = false;
  $Bd = false;
  foreach (
    $Hb
    as $p
  ) {
    if (strpos($p, "adminer.css") !== false) $Dd = true;
    if (strpos($p, "adminer-dark.css") !== false) $Bd = true;
  }
  $Kb = ($Dd ? ($Bd ? null : false) : ($Bd ?: null));
  $Ze = " media='(prefers-color-scheme: dark)'";
  if ($Kb !== false) echo "<link rel='stylesheet'" . ($Kb ? "" : $Ze) . " href='" . h(preg_replace("~\\?.*~", "", ME) . "?file=dark.css&version=5.2.1") . "'>\n";
  echo "<meta name='color-scheme' content='" . ($Kb === null ? "light dark" : ($Kb ? "dark" : "light")) . "'>\n", script_src(preg_replace("~\\?.*~", "", ME) . "?file=functions.js&version=5.2.1");
  if (adminer()->head($Kb)) echo "<link rel='icon' href='data:image/gif;base64,R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n", "<link rel='apple-touch-icon' href='" . h(preg_replace("~\\?.*~", "", ME) . "?file=logo.png&version=5.2.1") . "'>\n";
  foreach (
    $Hb
    as $X
  ) echo "<link rel='stylesheet'" . (preg_match('~-dark\.~', $X) && !$Kb ? $Ze : "") . " href='" . h($X) . "'>\n";
  echo "\n<body class='" . lang(81) . " nojs'>\n";
  $p = get_temp_dir() . "/adminer.version";
  if (!$_COOKIE["adminer_version"] && function_exists('openssl_verify') && file_exists($p) && filemtime($p) + 86400 > time()) {
    $tj = unserialize(file_get_contents($p));
    $Mg = "-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwqWOVuF5uw7/+Z70djoK
RlHIZFZPO0uYRezq90+7Amk+FDNd7KkL5eDve+vHRJBLAszF/7XKXe11xwliIsFs
DFWQlsABVZB3oisKCBEuI71J4kPH8dKGEWR9jDHFw3cWmoH3PmqImX6FISWbG3B8
h7FIx3jEaw5ckVPVTeo5JRm/1DZzJxjyDenXvBQ/6o9DgZKeNDgxwKzH+sw9/YCO
jHnq1cFpOIISzARlrHMa/43YfeNRAm/tsBXjSxembBPo7aQZLAWHmaj5+K19H10B
nCpz9Y++cipkVEiKRGih4ZEvjoFysEOdRLj6WiD/uUNky4xGeA6LaJqh5XpkFkcQ
fQIDAQAB
-----END PUBLIC KEY-----
";
    if (openssl_verify($tj["version"], base64_decode($tj["signature"]), $Mg) == 1) $_COOKIE["adminer_version"] = $tj["version"];
  }
  echo
  script("mixin(document.body, {onkeydown: bodyKeydown, onclick: bodyClick" . (isset($_COOKIE["adminer_version"]) ? "" : ", onload: partial(verifyVersion, '" . VERSION . "', '" . js_escape(ME) . "', '" . get_token() . "')") . "});
document.body.classList.replace('nojs', 'js');
const offlineMessage = '" . js_escape(lang(82)) . "';
const thousandsSeparator = '" . js_escape(lang(4)) . "';"), "<div id='help' class='jush-" . JUSH . " jsonly hidden'></div>\n", script("mixin(qs('#help'), {onmouseover: () => { helpOpen = 1; }, onmouseout: helpMouseout});"), "<div id='content'>\n", "<span id='menuopen' class='jsonly'>" . icon("move", "", "menu", "") . "</span>" . script("qs('#menuopen').onclick = event => { qs('#foot').classList.toggle('foot'); event.stopPropagation(); }");
  if ($Oa !== null) {
    $A = substr(preg_replace('~\b(username|db|ns)=[^&]*&~', '', ME), 0, -1);
    echo '<p id="breadcrumb"><a href="' . h($A ?: ".") . '">' . get_driver(DRIVER) . '</a> Â» ';
    $A = substr(preg_replace('~\b(db|ns)=[^&]*&~', '', ME), 0, -1);
    $N = adminer()->serverName(SERVER);
    $N = ($N != "" ? $N : lang(33));
    if ($Oa === false) echo "$N\n";
    else {
      echo "<a href='" . h($A) . "' accesskey='1' title='Alt+Shift+1'>$N</a> Â» ";
      if ($_GET["ns"] != "" || (DB != "" && is_array($Oa))) echo '<a href="' . h($A . "&db=" . urlencode(DB) . (support("scheme") ? "&ns=" : "")) . '">' . h(DB) . '</a> Â» ';
      if (is_array($Oa)) {
        if ($_GET["ns"] != "") echo '<a href="' . h(substr(ME, 0, -1)) . '">' . h($_GET["ns"]) . '</a> Â» ';
        foreach (
          $Oa
          as $y => $X
        ) {
          $Xb = (is_array($X) ? $X[1] : h($X));
          if ($Xb != "") echo "<a href='" . h(ME . "$y=") . urlencode(is_array($X) ? $X[0] : $X) . "'>$Xb</a> Â» ";
        }
      }
      echo "$Bi\n";
    }
  }
  echo "<h2>$Di</h2>\n", "<div id='ajaxstatus' class='jsonly hidden'></div>\n";
  restart_session();
  page_messages($m);
  $j = &get_session("dbs");
  if (DB != "" && $j && !in_array(DB, $j, true)) $j = null;
  stop_session();
  define('Adminer\PAGE_HEADER', 1);
}
function
page_headers()
{
  header("Content-Type: text/html; charset=utf-8");
  header("Cache-Control: no-cache");
  header("X-Frame-Options: deny");
  header("X-XSS-Protection: 0");
  header("X-Content-Type-Options: nosniff");
  header("Referrer-Policy: origin-when-cross-origin");
  foreach (adminer()->csp(csp()) as $Gb) {
    $Fd = array();
    foreach (
      $Gb
      as $y => $X
    ) $Fd[] = "$y $X";
    header("Content-Security-Policy: " . implode("; ", $Fd));
  }
  adminer()->headers();
}
function
csp()
{
  return
    array(array("script-src" => "'self' 'unsafe-inline' 'nonce-" . get_nonce() . "' 'strict-dynamic'", "connect-src" => "'self'", "frame-src" => "https://www.adminer.org", "object-src" => "'none'", "base-uri" => "'none'", "form-action" => "'self'",),);
}
function
get_nonce()
{
  static $vf;
  if (!$vf) $vf = base64_encode(rand_string());
  return $vf;
}
function
page_messages($m)
{
  $gj = preg_replace('~^[^?]*~', '', $_SERVER["REQUEST_URI"]);
  $ff = idx($_SESSION["messages"], $gj);
  if ($ff) {
    echo "<div class='message'>" . implode("</div>\n<div class='message'>", $ff) . "</div>" . script("messagesPrint();");
    unset($_SESSION["messages"][$gj]);
  }
  if ($m) echo "<div class='error'>$m</div>\n";
  if (adminer()->error) echo "<div class='error'>" . adminer()->error . "</div>\n";
}
function
page_footer($if = "")
{
  echo "</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";
  adminer()->navigation($if);
  echo "</div>\n";
  if ($if != "auth") echo '<form action="" method="post">
<p class="logout">
<span>', h($_GET["username"]) . "\n", '</span>
<input type="submit" name="logout" value="', lang(83), '" id="logout">
', input_token(), '</form>
';
  echo "</div>\n\n", script("setupSubmitHighlight(document);");
}
function
int32($nf)
{
  while ($nf >= 2147483648) $nf -= 4294967296;
  while ($nf <= -2147483649) $nf += 4294967296;
  return (int)$nf;
}
function
long2str(array $W, $xj)
{
  $oh = '';
  foreach (
    $W
    as $X
  ) $oh
    .= pack('V', $X);
  if ($xj) return
    substr($oh, 0, end($W));
  return $oh;
}
function
str2long($oh, $xj)
{
  $W = array_values(unpack('V*', str_pad($oh, 4 * ceil(strlen($oh) / 4), "\0")));
  if ($xj) $W[] = strlen($oh);
  return $W;
}
function
xxtea_mx($Dj, $Cj, $ei, $se)
{
  return
    int32((($Dj >> 5 & 0x7FFFFFF) ^ $Cj << 2) + (($Cj >> 3 & 0x1FFFFFFF) ^ $Dj << 4)) ^ int32(($ei ^ $Cj) + ($se ^ $Dj));
}
function
encrypt_string($Zh, $y)
{
  if ($Zh == "") return "";
  $y = array_values(unpack("V*", pack("H*", md5($y))));
  $W = str2long($Zh, true);
  $nf = count($W) - 1;
  $Dj = $W[$nf];
  $Cj = $W[0];
  $Ng = floor(6 + 52 / ($nf + 1));
  $ei = 0;
  while ($Ng-- > 0) {
    $ei = int32($ei + 0x9E3779B9);
    $oc = $ei >> 2 & 3;
    for ($gg = 0; $gg < $nf; $gg++) {
      $Cj = $W[$gg + 1];
      $mf = xxtea_mx($Dj, $Cj, $ei, $y[$gg & 3 ^ $oc]);
      $Dj = int32($W[$gg] + $mf);
      $W[$gg] = $Dj;
    }
    $Cj = $W[0];
    $mf = xxtea_mx($Dj, $Cj, $ei, $y[$gg & 3 ^ $oc]);
    $Dj = int32($W[$nf] + $mf);
    $W[$nf] = $Dj;
  }
  return
    long2str($W, false);
}
function
decrypt_string($Zh, $y)
{
  if ($Zh == "") return "";
  if (!$y) return
    false;
  $y = array_values(unpack("V*", pack("H*", md5($y))));
  $W = str2long($Zh, false);
  $nf = count($W) - 1;
  $Dj = $W[$nf];
  $Cj = $W[0];
  $Ng = floor(6 + 52 / ($nf + 1));
  $ei = int32($Ng * 0x9E3779B9);
  while ($ei) {
    $oc = $ei >> 2 & 3;
    for ($gg = $nf; $gg > 0; $gg--) {
      $Dj = $W[$gg - 1];
      $mf = xxtea_mx($Dj, $Cj, $ei, $y[$gg & 3 ^ $oc]);
      $Cj = int32($W[$gg] - $mf);
      $W[$gg] = $Cj;
    }
    $Dj = $W[$nf];
    $mf = xxtea_mx($Dj, $Cj, $ei, $y[$gg & 3 ^ $oc]);
    $Cj = int32($W[0] - $mf);
    $W[0] = $Cj;
    $ei = int32($ei - 0x9E3779B9);
  }
  return
    long2str($W, true);
}
$ug = array();
if ($_COOKIE["adminer_permanent"]) {
  foreach (explode(" ", $_COOKIE["adminer_permanent"]) as $X) {
    list($y) = explode(":", $X);
    $ug[$y] = $X;
  }
}
function
add_invalid_login()
{
  $Ha = get_temp_dir() . "/adminer.invalid";
  foreach (glob("$Ha*") ?: array($Ha) as $p) {
    $r = file_open_lock($p);
    if ($r) break;
  }
  if (!$r) $r = file_open_lock("$Ha-" . rand_string());
  if (!$r) return;
  $ke = unserialize(stream_get_contents($r));
  $zi = time();
  if ($ke) {
    foreach (
      $ke
      as $le => $X
    ) {
      if ($X[0] < $zi) unset($ke[$le]);
    }
  }
  $je = &$ke[adminer()->bruteForceKey()];
  if (!$je) $je = array($zi + 30 * 60, 0);
  $je[1]++;
  file_write_unlock($r, serialize($ke));
}
function
check_invalid_login(array &$ug)
{
  $ke = array();
  foreach (glob(get_temp_dir() . "/adminer.invalid*") as $p) {
    $r = file_open_lock($p);
    if ($r) {
      $ke = unserialize(stream_get_contents($r));
      file_unlock($r);
      break;
    }
  }
  $je = idx($ke, adminer()->bruteForceKey(), array());
  $uf = ($je[1] > 29 ? $je[0] - time() : 0);
  if ($uf > 0) auth_error(lang(84, ceil($uf / 60)), $ug);
}
$Aa = $_POST["auth"];
if ($Aa) {
  session_regenerate_id();
  $sj = $Aa["driver"];
  $N = $Aa["server"];
  $V = $Aa["username"];
  $F = (string)$Aa["password"];
  $k = $Aa["db"];
  set_password($sj, $N, $V, $F);
  $_SESSION["db"][$sj][$N][$V][$k] = true;
  if ($Aa["permanent"]) {
    $y = implode("-", array_map('base64_encode', array($sj, $N, $V, $k)));
    $Hg = adminer()->permanentLogin(true);
    $ug[$y] = "$y:" . base64_encode($Hg ? encrypt_string($F, $Hg) : "");
    cookie("adminer_permanent", implode(" ", $ug));
  }
  if (count($_POST) == 1 || DRIVER != $sj || SERVER != $N || $_GET["username"] !== $V || DB != $k) redirect(auth_url($sj, $N, $V, $k));
} elseif ($_POST["logout"] && (!$_SESSION["token"] || verify_token())) {
  foreach (array("pwds", "db", "dbs", "queries") as $y) set_session($y, null);
  unset_permanent($ug);
  redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~', '', ME), 0, -1), lang(85) . ' ' . lang(86));
} elseif ($ug && !$_SESSION["pwds"]) {
  session_regenerate_id();
  $Hg = adminer()->permanentLogin();
  foreach (
    $ug
    as $y => $X
  ) {
    list(, $db) = explode(":", $X);
    list($sj, $N, $V, $k) = array_map('base64_decode', explode("-", $y));
    set_password($sj, $N, $V, decrypt_string(base64_decode($db), $Hg));
    $_SESSION["db"][$sj][$N][$V][$k] = true;
  }
}
function
unset_permanent(array &$ug)
{
  foreach (
    $ug
    as $y => $X
  ) {
    list($sj, $N, $V, $k) = array_map('base64_decode', explode("-", $y));
    if ($sj == DRIVER && $N == SERVER && $V == $_GET["username"] && $k == DB) unset($ug[$y]);
  }
  cookie("adminer_permanent", implode(" ", $ug));
}
function
auth_error($m, array &$ug)
{
  $Gh = session_name();
  if (isset($_GET["username"])) {
    header("HTTP/1.1 403 Forbidden");
    if (($_COOKIE[$Gh] || $_GET[$Gh]) && !$_SESSION["token"]) $m = lang(87);
    else {
      restart_session();
      add_invalid_login();
      $F = get_password();
      if ($F !== null) {
        if ($F === false) $m
          .= ($m ? '<br>' : '') . lang(88, target_blank(), '<code>permanentLogin()</code>');
        set_password(DRIVER, SERVER, $_GET["username"], null);
      }
      unset_permanent($ug);
    }
  }
  if (!$_COOKIE[$Gh] && $_GET[$Gh] && ini_bool("session.use_only_cookies")) $m = lang(89);
  $jg = session_get_cookie_params();
  cookie("adminer_key", ($_COOKIE["adminer_key"] ?: rand_string()), $jg["lifetime"]);
  if (!$_SESSION["token"]) $_SESSION["token"] = rand(1, 1e6);
  page_header(lang(37), $m, null);
  echo "<form action='' method='post'>\n", "<div>";
  if (hidden_fields($_POST, array("auth"))) echo "<p class='message'>" . lang(90) . "\n";
  echo "</div>\n";
  adminer()->loginForm();
  echo "</form>\n";
  page_footer("auth");
  exit;
}
if (isset($_GET["username"]) && !class_exists('Adminer\Db')) {
  unset($_SESSION["pwds"][DRIVER]);
  unset_permanent($ug);
  page_header(lang(91), lang(92, implode(", ", Driver::$Pc)), false);
  page_footer("auth");
  exit;
}
$g = '';
if (isset($_GET["username"]) && is_string(get_password())) {
  list($Ld, $yg) = explode(":", SERVER, 2);
  if (preg_match('~^\s*([-+]?\d+)~', $yg, $B) && ($B[1] < 1024 || $B[1] > 65535)) auth_error(lang(93), $ug);
  check_invalid_login($ug);
  $Fb = adminer()->credentials();
  $g = Driver::connect($Fb[0], $Fb[1], $Fb[2]);
  if (is_object($g)) {
    Db::$he = $g;
    Driver::$he = new
      Driver($g);
    if ($g->flavor) save_settings(array("vendor-" . DRIVER . "-" . SERVER => get_driver(DRIVER)));
  }
}
$Le = null;
if (!is_object($g) || ($Le = adminer()->login($_GET["username"], get_password())) !== true) {
  $m = (is_string($g) ? nl_br(h($g)) : (is_string($Le) ? $Le : lang(94))) . (preg_match('~^ | $~', get_password()) ? '<br>' . lang(95) : '');
  auth_error($m, $ug);
}
if ($_POST["logout"] && $_SESSION["token"] && !verify_token()) {
  page_header(lang(83), lang(96));
  page_footer("db");
  exit;
}
if (!$_SESSION["token"]) $_SESSION["token"] = rand(1, 1e6);
stop_session(true);
if ($Aa && $_POST["token"]) $_POST["token"] = get_token();
$m = '';
if ($_POST) {
  if (!verify_token()) {
    $ce = "max_input_vars";
    $Xe = ini_get($ce);
    if (extension_loaded("suhosin")) {
      foreach (array("suhosin.request.max_vars", "suhosin.post.max_vars") as $y) {
        $X = ini_get($y);
        if ($X && (!$Xe || $X < $Xe)) {
          $ce = $y;
          $Xe = $X;
        }
      }
    }
    $m = (!$_POST["token"] && $Xe ? lang(97, "'$ce'") : lang(96) . ' ' . lang(98));
  }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
  $m = lang(99, "'post_max_size'");
  if (isset($_GET["sql"])) $m
    .= ' ' . lang(100);
}
function
print_select_result($I, $h = null, array $Vf = array(), $_ = 0)
{
  $Je = array();
  $x = array();
  $e = array();
  $Ma = array();
  $Wi = array();
  $J = array();
  for ($t = 0; (!$_ || $t < $_) && ($K = $I->fetch_row()); $t++) {
    if (!$t) {
      echo "<div class='scrollable'>\n", "<table class='nowrap odds'>\n", "<thead><tr>";
      for ($qe = 0; $qe < count($K); $qe++) {
        $n = $I->fetch_field();
        $C = $n->name;
        $Uf = (isset($n->orgtable) ? $n->orgtable : "");
        $Tf = (isset($n->orgname) ? $n->orgname : $C);
        if ($Vf && JUSH == "sql") $Je[$qe] = ($C == "table" ? "table=" : ($C == "possible_keys" ? "indexes=" : null));
        elseif ($Uf != "") {
          if (isset($n->table)) $J[$n->table] = $Uf;
          if (!isset($x[$Uf])) {
            $x[$Uf] = array();
            foreach (indexes($Uf, $h) as $w) {
              if ($w["type"] == "PRIMARY") {
                $x[$Uf] = array_flip($w["columns"]);
                break;
              }
            }
            $e[$Uf] = $x[$Uf];
          }
          if (isset($e[$Uf][$Tf])) {
            unset($e[$Uf][$Tf]);
            $x[$Uf][$Tf] = $qe;
            $Je[$qe] = $Uf;
          }
        }
        if ($n->charsetnr == 63) $Ma[$qe] = true;
        $Wi[$qe] = $n->type;
        echo "<th" . ($Uf != "" || $n->name != $Tf ? " title='" . h(($Uf != "" ? "$Uf." : "") . $Tf) . "'" : "") . ">" . h($C) . ($Vf ? doc_link(array('sql' => "explain-output.html#explain_" . strtolower($C), 'mariadb' => "explain/#the-columns-in-explain-select",)) : "");
      }
      echo "</thead>\n";
    }
    echo "<tr>";
    foreach (
      $K
      as $y => $X
    ) {
      $A = "";
      if (isset($Je[$y]) && !$e[$Je[$y]]) {
        if ($Vf && JUSH == "sql") {
          $R = $K[array_search("table=", $Je)];
          $A = ME . $Je[$y] . urlencode($Vf[$R] != "" ? $Vf[$R] : $R);
        } else {
          $A = ME . "edit=" . urlencode($Je[$y]);
          foreach ($x[$Je[$y]] as $hb => $qe) $A
            .= "&where" . urlencode("[" . bracket_escape($hb) . "]") . "=" . urlencode($K[$qe]);
        }
      } elseif (is_url($X)) $A = $X;
      if ($X === null) $X = "<i>NULL</i>";
      elseif ($Ma[$y] && !is_utf8($X)) $X = "<i>" . lang(46, strlen($X)) . "</i>";
      else {
        $X = h($X);
        if ($Wi[$y] == 254) $X = "<code>$X</code>";
      }
      if ($A) $X = "<a href='" . h($A) . "'" . (is_url($A) ? target_blank() : '') . ">$X</a>";
      echo "<td" . ($Wi[$y] <= 9 || $Wi[$y] == 246 ? " class='number'" : "") . ">$X";
    }
  }
  echo ($t ? "</table>\n</div>" : "<p class='message'>" . lang(12)) . "\n";
  return $J;
}
function
referencable_primary($zh)
{
  $J = array();
  foreach (table_status('', true) as $ji => $R) {
    if ($ji != $zh && fk_support($R)) {
      foreach (fields($ji) as $n) {
        if ($n["primary"]) {
          if ($J[$ji]) {
            unset($J[$ji]);
            break;
          }
          $J[$ji] = $n;
        }
      }
    }
  }
  return $J;
}
function
textarea($C, $Y, $L = 10, $kb = 80)
{
  echo "<textarea name='" . h($C) . "' rows='$L' cols='$kb' class='sqlarea jush-" . JUSH . "' spellcheck='false' wrap='off'>";
  if (is_array($Y)) {
    foreach (
      $Y
      as $X
    ) echo
    h($X[0]) . "\n\n\n";
  } else
    echo
    h($Y);
  echo "</textarea>";
}
function
select_input($_a, array $Pf, $Y = "", $Jf = "", $vg = "")
{
  $ri = ($Pf ? "select" : "input");
  return "<$ri$_a" . ($Pf ? "><option value=''>$vg" . optionlist($Pf, $Y, true) . "</select>" : " size='10' value='" . h($Y) . "' placeholder='$vg'>") . ($Jf ? script("qsl('$ri').onchange = $Jf;", "") : "");
}
function
json_row($y, $X = null)
{
  static $ad = true;
  if ($ad) echo "{";
  if ($y != "") {
    echo ($ad ? "" : ",") . "\n\t\"" . addcslashes($y, "\r\n\t\"\\/") . '": ' . ($X !== null ? '"' . addcslashes($X, "\r\n\"\\/") . '"' : 'null');
    $ad = false;
  } else {
    echo "\n}\n";
    $ad = true;
  }
}
function
edit_type($y, array $n, array $jb, array $kd = array(), array $Rc = array())
{
  $U = $n["type"];
  echo "<td><select name='" . h($y) . "[type]' class='type' aria-labelledby='label-type'>";
  if ($U && !array_key_exists($U, driver()->types()) && !isset($kd[$U]) && !in_array($U, $Rc)) $Rc[] = $U;
  $ai = driver()->structuredTypes();
  if ($kd) $ai[lang(101)] = $kd;
  echo
  optionlist(array_merge($Rc, $ai), $U), "</select><td>", "<input name='" . h($y) . "[length]' value='" . h($n["length"]) . "' size='3'" . (!$n["length"] && preg_match('~var(char|binary)$~', $U) ? " class='required'" : "") . " aria-labelledby='label-length'>", "<td class='options'>", ($jb ? "<input list='collations' name='" . h($y) . "[collation]'" . (preg_match('~(char|text|enum|set)$~', $U) ? "" : " class='hidden'") . " value='" . h($n["collation"]) . "' placeholder='(" . lang(102) . ")'>" : ''), (driver()->unsigned ? "<select name='" . h($y) . "[unsigned]'" . (!$U || preg_match(number_type(), $U) ? "" : " class='hidden'") . '><option>' . optionlist(driver()->unsigned, $n["unsigned"]) . '</select>' : ''), (isset($n['on_update']) ? "<select name='" . h($y) . "[on_update]'" . (preg_match('~timestamp|datetime~', $U) ? "" : " class='hidden'") . '>' . optionlist(array("" => "(" . lang(103) . ")", "CURRENT_TIMESTAMP"), (preg_match('~^CURRENT_TIMESTAMP~i', $n["on_update"]) ? "CURRENT_TIMESTAMP" : $n["on_update"])) . '</select>' : ''), ($kd ? "<select name='" . h($y) . "[on_delete]'" . (preg_match("~`~", $U) ? "" : " class='hidden'") . "><option value=''>(" . lang(104) . ")" . optionlist(explode("|", driver()->onActions), $n["on_delete"]) . "</select> " : " ");
}
function
get_partitions_info($R)
{
  $od = "FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = " . q(DB) . " AND TABLE_NAME = " . q($R);
  $I = connection()->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $od ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");
  $J = array();
  list($J["partition_by"], $J["partition"], $J["partitions"]) = $I->fetch_row();
  $pg = get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $od AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");
  $J["partition_names"] = array_keys($pg);
  $J["partition_values"] = array_values($pg);
  return $J;
}
function
process_length($z)
{
  $Ac = driver()->enumLength;
  return (preg_match("~^\\s*\\(?\\s*$Ac(?:\\s*,\\s*$Ac)*+\\s*\\)?\\s*\$~", $z) && preg_match_all("~$Ac~", $z, $Re) ? "(" . implode(",", $Re[0]) . ")" : preg_replace('~^[0-9].*~', '(\0)', preg_replace('~[^-0-9,+()[\]]~', '', $z)));
}
function
process_type(array $n, $ib = "COLLATE")
{
  return " $n[type]" . process_length($n["length"]) . (preg_match(number_type(), $n["type"]) && in_array($n["unsigned"], driver()->unsigned) ? " $n[unsigned]" : "") . (preg_match('~char|text|enum|set~', $n["type"]) && $n["collation"] ? " $ib " . (JUSH == "mssql" ? $n["collation"] : q($n["collation"])) : "");
}
function
process_field(array $n, array $Ui)
{
  if ($n["on_update"]) $n["on_update"] = str_ireplace("current_timestamp()", "CURRENT_TIMESTAMP", $n["on_update"]);
  return
    array(idf_escape(trim($n["field"])), process_type($Ui), ($n["null"] ? " NULL" : " NOT NULL"), default_value($n), (preg_match('~timestamp|datetime~', $n["type"]) && $n["on_update"] ? " ON UPDATE $n[on_update]" : ""), (support("comment") && $n["comment"] != "" ? " COMMENT " . q($n["comment"]) : ""), ($n["auto_increment"] ? auto_increment() : null),);
}
function
default_value(array $n)
{
  $l = $n["default"];
  $rd = $n["generated"];
  return ($l === null ? "" : (in_array($rd, driver()->generated) ? (JUSH == "mssql" ? " AS ($l)" . ($rd == "VIRTUAL" ? "" : " $rd") . "" : " GENERATED ALWAYS AS ($l) $rd") : " DEFAULT " . (!preg_match('~^GENERATED ~i', $l) && (preg_match('~char|binary|text|json|enum|set~', $n["type"]) || preg_match('~^(?![a-z])~i', $l)) ? (JUSH == "sql" && preg_match('~text|json~', $n["type"]) ? "(" . q($l) . ")" : q($l)) : str_ireplace("current_timestamp()", "CURRENT_TIMESTAMP", (JUSH == "sqlite" ? "($l)" : $l)))));
}
function
type_class($U)
{
  foreach (array('char' => 'text', 'date' => 'time|year', 'binary' => 'blob', 'enum' => 'set',) as $y => $X) {
    if (preg_match("~$y|$X~", $U)) return " class='$y'";
  }
}
function
edit_fields(array $o, array $jb, $U = "TABLE", array $kd = array())
{
  $o = array_values($o);
  $Sb = (($_POST ? $_POST["defaults"] : get_setting("defaults")) ? "" : " class='hidden'");
  $pb = (($_POST ? $_POST["comments"] : get_setting("comments")) ? "" : " class='hidden'");
  echo "<thead><tr>\n", ($U == "PROCEDURE" ? "<td>" : ""), "<th id='label-name'>" . ($U == "TABLE" ? lang(105) : lang(106)), "<td id='label-type'>" . lang(48) . "<textarea id='enum-edit' rows='4' cols='12' wrap='off' style='display: none;'></textarea>" . script("qs('#enum-edit').onblur = editingLengthBlur;"), "<td id='label-length'>" . lang(107), "<td>" . lang(108);
  if ($U == "TABLE") echo "<td id='label-null'>NULL\n", "<td><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='" . lang(50) . "'>AI</abbr>", doc_link(array('sql' => "example-auto-increment.html", 'mariadb' => "auto_increment/", 'sqlite' => "autoinc.html", 'pgsql' => "datatype-numeric.html#DATATYPE-SERIAL", 'mssql' => "t-sql/statements/create-table-transact-sql-identity-property",)), "<td id='label-default'$Sb>" . lang(51), (support("comment") ? "<td id='label-comment'$pb>" . lang(49) : "");
  echo "<td>" . icon("plus", "add[" . (support("move_col") ? 0 : count($o)) . "]", "+", lang(109)), "</thead>\n<tbody>\n", script("mixin(qsl('tbody'), {onclick: editingClick, onkeydown: editingKeydown, oninput: editingInput});");
  foreach (
    $o
    as $t => $n
  ) {
    $t++;
    $Wf = $n[($_POST ? "orig" : "field")];
    $dc = (isset($_POST["add"][$t - 1]) || (isset($n["field"]) && !idx($_POST["drop_col"], $t))) && (support("drop_col") || $Wf == "");
    echo "<tr" . ($dc ? "" : " style='display: none;'") . ">\n", ($U == "PROCEDURE" ? "<td>" . html_select("fields[$t][inout]", explode("|", driver()->inout), $n["inout"]) : "") . "<th>";
    if ($dc) echo "<input name='fields[$t][field]' value='" . h($n["field"]) . "' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'>";
    echo
    input_hidden("fields[$t][orig]", $Wf);
    edit_type("fields[$t]", $n, $jb, $kd);
    if ($U == "TABLE") echo "<td>" . checkbox("fields[$t][null]", 1, $n["null"], "", "", "block", "label-null"), "<td><label class='block'><input type='radio' name='auto_increment_col' value='$t'" . ($n["auto_increment"] ? " checked" : "") . " aria-labelledby='label-ai'></label>", "<td$Sb>" . (driver()->generated ? html_select("fields[$t][generated]", array_merge(array("", "DEFAULT"), driver()->generated), $n["generated"]) . " " : checkbox("fields[$t][generated]", 1, $n["generated"], "", "", "", "label-default")), "<input name='fields[$t][default]' value='" . h($n["default"]) . "' aria-labelledby='label-default'>", (support("comment") ? "<td$pb><input name='fields[$t][comment]' value='" . h($n["comment"]) . "' data-maxlength='" . (min_version(5.5) ? 1024 : 255) . "' aria-labelledby='label-comment'>" : "");
    echo "<td>", (support("move_col") ? icon("plus", "add[$t]", "+", lang(109)) . " " . icon("up", "up[$t]", "â", lang(110)) . " " . icon("down", "down[$t]", "â", lang(111)) . " " : ""), ($Wf == "" || support("drop_col") ? icon("cross", "drop_col[$t]", "x", lang(112)) : "");
  }
}
function
process_fields(array &$o)
{
  $D = 0;
  if ($_POST["up"]) {
    $Ae = 0;
    foreach (
      $o
      as $y => $n
    ) {
      if (key($_POST["up"]) == $y) {
        unset($o[$y]);
        array_splice($o, $Ae, 0, array($n));
        break;
      }
      if (isset($n["field"])) $Ae = $D;
      $D++;
    }
  } elseif ($_POST["down"]) {
    $md = false;
    foreach (
      $o
      as $y => $n
    ) {
      if (isset($n["field"]) && $md) {
        unset($o[key($_POST["down"])]);
        array_splice($o, $D, 0, array($md));
        break;
      }
      if (key($_POST["down"]) == $y) $md = $n;
      $D++;
    }
  } elseif ($_POST["add"]) {
    $o = array_values($o);
    array_splice($o, key($_POST["add"]), 0, array(array()));
  } elseif (!$_POST["drop_col"]) return
    false;
  return
    true;
}
function
normalize_enum(array $B)
{
  $X = $B[0];
  return "'" . str_replace("'", "''", addcslashes(stripcslashes(str_replace($X[0] . $X[0], $X[0], substr($X, 1, -1))), '\\')) . "'";
}
function
grant($td, array $Jg, $e, $Gf)
{
  if (!$Jg) return
    true;
  if ($Jg == array("ALL PRIVILEGES", "GRANT OPTION")) return ($td == "GRANT" ? queries("$td ALL PRIVILEGES$Gf WITH GRANT OPTION") : queries("$td ALL PRIVILEGES$Gf") && queries("$td GRANT OPTION$Gf"));
  return
    queries("$td " . preg_replace('~(GRANT OPTION)\([^)]*\)~', '\1', implode("$e, ", $Jg) . $e) . $Gf);
}
function
drop_create($hc, $i, $jc, $vi, $lc, $Ke, $ef, $cf, $df, $Df, $rf)
{
  if ($_POST["drop"]) query_redirect($hc, $Ke, $ef);
  elseif ($Df == "") query_redirect($i, $Ke, $df);
  elseif ($Df != $rf) {
    $Eb = queries($i);
    queries_redirect($Ke, $cf, $Eb && queries($hc));
    if ($Eb) queries($jc);
  } else
    queries_redirect($Ke, $cf, queries($vi) && queries($lc) && queries($hc) && queries($i));
}
function
create_trigger($Gf, array $K)
{
  $Ai = " $K[Timing] $K[Event]" . (preg_match('~ OF~', $K["Event"]) ? " $K[Of]" : "");
  return "CREATE TRIGGER " . idf_escape($K["Trigger"]) . (JUSH == "mssql" ? $Gf . $Ai : $Ai . $Gf) . rtrim(" $K[Type]\n$K[Statement]", ";") . ";";
}
function
create_routine($kh, array $K)
{
  $O = array();
  $o = (array)$K["fields"];
  ksort($o);
  foreach (
    $o
    as $n
  ) {
    if ($n["field"] != "") $O[] = (preg_match("~^(" . driver()->inout . ")\$~", $n["inout"]) ? "$n[inout] " : "") . idf_escape($n["field"]) . process_type($n, "CHARACTER SET");
  }
  $Ub = rtrim($K["definition"], ";");
  return "CREATE $kh " . idf_escape(trim($K["name"])) . " (" . implode(", ", $O) . ")" . ($kh == "FUNCTION" ? " RETURNS" . process_type($K["returns"], "CHARACTER SET") : "") . ($K["language"] ? " LANGUAGE $K[language]" : "") . (JUSH == "pgsql" ? " AS " . q($Ub) : "\n$Ub;");
}
function
remove_definer($H)
{
  return
    preg_replace('~^([A-Z =]+) DEFINER=`' . preg_replace('~@(.*)~', '`@`(%|\1)', logged_user()) . '`~', '\1', $H);
}
function
format_foreign_key(array $q)
{
  $k = $q["db"];
  $wf = $q["ns"];
  return " FOREIGN KEY (" . implode(", ", array_map('Adminer\idf_escape', $q["source"])) . ") REFERENCES " . ($k != "" && $k != $_GET["db"] ? idf_escape($k) . "." : "") . ($wf != "" && $wf != $_GET["ns"] ? idf_escape($wf) . "." : "") . idf_escape($q["table"]) . " (" . implode(", ", array_map('Adminer\idf_escape', $q["target"])) . ")" . (preg_match("~^(" . driver()->onActions . ")\$~", $q["on_delete"]) ? " ON DELETE $q[on_delete]" : "") . (preg_match("~^(" . driver()->onActions . ")\$~", $q["on_update"]) ? " ON UPDATE $q[on_update]" : "");
}
function
tar_file($p, $Fi)
{
  $J = pack("a100a8a8a8a12a12", $p, 644, 0, 0, decoct($Fi->size), decoct(time()));
  $cb = 8 * 32;
  for ($t = 0; $t < strlen($J); $t++) $cb += ord($J[$t]);
  $J
    .= sprintf("%06o", $cb) . "\0 ";
  echo $J, str_repeat("\0", 512 - strlen($J));
  $Fi->send();
  echo
  str_repeat("\0", 511 - ($Fi->size + 511) % 512);
}
function
ini_bytes($ce)
{
  $X = ini_get($ce);
  switch (strtolower(substr($X, -1))) {
    case 'g':
      $X = (int)$X * 1024;
    case 'm':
      $X = (int)$X * 1024;
    case 'k':
      $X = (int)$X * 1024;
  }
  return $X;
}
function
doc_link(array $rg, $wi = "<sup>?</sup>")
{
  $Eh = connection()->server_info;
  $tj = preg_replace('~^(\d\.?\d).*~s', '\1', $Eh);
  $ij = array('sql' => "https://dev.mysql.com/doc/refman/$tj/en/", 'sqlite' => "https://www.sqlite.org/", 'pgsql' => "https://www.postgresql.org/docs/" . (connection()->flavor == 'cockroach' ? "current" : $tj) . "/", 'mssql' => "https://learn.microsoft.com/en-us/sql/", 'oracle' => "https://www.oracle.com/pls/topic/lookup?ctx=db" . preg_replace('~^.* (\d+)\.(\d+)\.\d+\.\d+\.\d+.*~s', '\1\2', $Eh) . "&id=",);
  if (connection()->flavor == 'maria') {
    $ij['sql'] = "https://mariadb.com/kb/en/";
    $rg['sql'] = (isset($rg['mariadb']) ? $rg['mariadb'] : str_replace(".html", "/", $rg['sql']));
  }
  return ($rg[JUSH] ? "<a href='" . h($ij[JUSH] . $rg[JUSH] . (JUSH == 'mssql' ? "?view=sql-server-ver$tj" : "")) . "'" . target_blank() . ">$wi</a>" : "");
}
function
db_size($k)
{
  if (!connection()->select_db($k)) return "?";
  $J = 0;
  foreach (table_status() as $S) $J += $S["Data_length"] + $S["Index_length"];
  return
    format_number($J);
}
function
set_utf8mb4($i)
{
  static $O = false;
  if (!$O && preg_match('~\butf8mb4~i', $i)) {
    $O = true;
    echo "SET NAMES " . charset(connection()) . ";\n\n";
  }
}
if (isset($_GET["status"])) $_GET["variables"] = $_GET["status"];
if (isset($_GET["import"])) $_GET["sql"] = $_GET["import"];
if (!(DB != "" ? connection()->select_db(DB) : isset($_GET["sql"]) || isset($_GET["dump"]) || isset($_GET["database"]) || isset($_GET["processlist"]) || isset($_GET["privileges"]) || isset($_GET["user"]) || isset($_GET["variables"]) || $_GET["script"] == "connect" || $_GET["script"] == "kill")) {
  if (DB != "" || $_GET["refresh"]) {
    restart_session();
    set_session("dbs", null);
  }
  if (DB != "") {
    header("HTTP/1.1 404 Not Found");
    page_header(lang(36) . ": " . h(DB), lang(113), true);
  } else {
    if ($_POST["db"] && !$m) queries_redirect(substr(ME, 0, -1), lang(114), drop_databases($_POST["db"]));
    page_header(lang(115), $m, false);
    echo "<p class='links'>\n";
    foreach (array('database' => lang(116), 'privileges' => lang(70), 'processlist' => lang(117), 'variables' => lang(118), 'status' => lang(119),) as $y => $X) {
      if (support($y)) echo "<a href='" . h(ME) . "$y='>$X</a>\n";
    }
    echo "<p>" . lang(120, get_driver(DRIVER), "<b>" . h(connection()->server_info) . "</b>", "<b>" . connection()->extension . "</b>") . "\n", "<p>" . lang(121, "<b>" . h(logged_user()) . "</b>") . "\n";
    $j = adminer()->databases();
    if ($j) {
      $sh = support("scheme");
      $jb = collations();
      echo "<form action='' method='post'>\n", "<table class='checkable odds'>\n", script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"), "<thead><tr>" . (support("database") ? "<td>" : "") . "<th>" . lang(36) . (get_session("dbs") !== null ? " - <a href='" . h(ME) . "refresh=1'>" . lang(122) . "</a>" : "") . "<td>" . lang(123) . "<td>" . lang(124) . "<td>" . lang(125) . " - <a href='" . h(ME) . "dbsize=1'>" . lang(126) . "</a>" . script("qsl('a').onclick = partial(ajaxSetHtml, '" . js_escape(ME) . "script=connect');", "") . "</thead>\n";
      $j = ($_GET["dbsize"] ? count_tables($j) : array_flip($j));
      foreach (
        $j
        as $k => $T
      ) {
        $jh = h(ME) . "db=" . urlencode($k);
        $u = h("Db-" . $k);
        echo "<tr>" . (support("database") ? "<td>" . checkbox("db[]", $k, in_array($k, (array)$_POST["db"]), "", "", "", $u) : ""), "<th><a href='$jh' id='$u'>" . h($k) . "</a>";
        $c = h(db_collation($k, $jb));
        echo "<td>" . (support("database") ? "<a href='$jh" . ($sh ? "&amp;ns=" : "") . "&amp;database=' title='" . lang(66) . "'>$c</a>" : $c), "<td align='right'><a href='$jh&amp;schema=' id='tables-" . h($k) . "' title='" . lang(69) . "'>" . ($_GET["dbsize"] ? $T : "?") . "</a>", "<td align='right' id='size-" . h($k) . "'>" . ($_GET["dbsize"] ? db_size($k) : "?"), "\n";
      }
      echo "</table>\n", (support("database") ? "<div class='footer'><div>\n" . "<fieldset><legend>" . lang(127) . " <span id='selected'></span></legend><div>\n" . input_hidden("all") . script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^db/)); };") . "<input type='submit' name='drop' value='" . lang(128) . "'>" . confirm() . "\n" . "</div></fieldset>\n" . "</div></div>\n" : ""), input_token(), "</form>\n", script("tableCheck();");
    }
    if (!empty(adminer()->plugins)) {
      echo "<div class='plugins'>\n", "<h3>" . lang(129) . "</h3>\n<ul>\n";
      foreach (
        adminer()->plugins
        as $wg
      ) {
        $Yb = (method_exists($wg, 'description') ? $wg->description() : "");
        if (!$Yb) {
          $Yg = new
            \ReflectionObject($wg);
          if (preg_match('~^/[\s*]+(.+)~', $Yg->getDocComment(), $B)) $Yb = $B[1];
        }
        $th = (method_exists($wg, 'screenshot') ? $wg->screenshot() : "");
        echo "<li><b>" . get_class($wg) . "</b>" . h($Yb ? ": $Yb" : "") . ($th ? " (<a href='" . h($th) . "'" . target_blank() . ">" . lang(130) . "</a>)" : "") . "\n";
      }
      echo "</ul>\n";
      adminer()->pluginsLinks();
      echo "</div>\n";
    }
  }
  page_footer("db");
  exit;
}
if (support("scheme")) {
  if (DB != "" && $_GET["ns"] !== "") {
    if (!isset($_GET["ns"])) redirect(preg_replace('~ns=[^&]*&~', '', ME) . "ns=" . get_schema());
    if (!set_schema($_GET["ns"])) {
      header("HTTP/1.1 404 Not Found");
      page_header(lang(75) . ": " . h($_GET["ns"]), lang(131), true);
      page_footer("ns");
      exit;
    }
  }
}
class
TmpFile
{
  private $handler;
  var $size;
  function
  __construct()
  {
    $this->handler = tmpfile();
  }
  function
  write($zb)
  {
    $this->size += strlen($zb);
    fwrite($this->handler, $zb);
  }
  function
  send()
  {
    fseek($this->handler, 0);
    fpassthru($this->handler);
    fclose($this->handler);
  }
}
if (isset($_GET["select"]) && ($_POST["edit"] || $_POST["clone"]) && !$_POST["save"]) $_GET["edit"] = $_GET["select"];
if (isset($_GET["callf"])) $_GET["call"] = $_GET["callf"];
if (isset($_GET["function"])) $_GET["procedure"] = $_GET["function"];
if (isset($_GET["download"])) {
  $a = $_GET["download"];
  $o = fields($a);
  header("Content-Type: application/octet-stream");
  header("Content-Disposition: attachment; filename=" . friendly_url("$a-" . implode("_", $_GET["where"])) . "." . friendly_url($_GET["field"]));
  $M = array(idf_escape($_GET["field"]));
  $I = driver()->select($a, $M, array(where($_GET, $o)), $M);
  $K = ($I ? $I->fetch_row() : array());
  echo
  driver()->value($K[0], $o[$_GET["field"]]);
  exit;
} elseif (isset($_GET["table"])) {
  $a = $_GET["table"];
  $o = fields($a);
  if (!$o) $m = error() ?: lang(9);
  $S = table_status1($a);
  $C = adminer()->tableName($S);
  page_header(($o && is_view($S) ? $S['Engine'] == 'materialized view' ? lang(132) : lang(133) : lang(134)) . ": " . ($C != "" ? $C : h($a)), $m);
  $ih = array();
  foreach (
    $o
    as $y => $n
  ) $ih += $n["privileges"];
  adminer()->selectLinks($S, (isset($ih["insert"]) || !support("table") ? "" : null));
  $ob = $S["Comment"];
  if ($ob != "") echo "<p class='nowrap'>" . lang(49) . ": " . h($ob) . "\n";
  if ($o) adminer()->tableStructurePrint($o, $S);
  if (support("indexes") && driver()->supportsIndex($S)) {
    echo "<h3 id='indexes'>" . lang(135) . "</h3>\n";
    $x = indexes($a);
    if ($x) adminer()->tableIndexesPrint($x);
    echo '<p class="links"><a href="' . h(ME) . 'indexes=' . urlencode($a) . '">' . lang(136) . "</a>\n";
  }
  if (!is_view($S)) {
    if (fk_support($S)) {
      echo "<h3 id='foreign-keys'>" . lang(101) . "</h3>\n";
      $kd = foreign_keys($a);
      if ($kd) {
        echo "<table>\n", "<thead><tr><th>" . lang(137) . "<td>" . lang(138) . "<td>" . lang(104) . "<td>" . lang(103) . "<td></thead>\n";
        foreach (
          $kd
          as $C => $q
        ) {
          echo "<tr title='" . h($C) . "'>", "<th><i>" . implode("</i>, <i>", array_map('Adminer\h', $q["source"])) . "</i>";
          $A = ($q["db"] != "" ? preg_replace('~db=[^&]*~', "db=" . urlencode($q["db"]), ME) : ($q["ns"] != "" ? preg_replace('~ns=[^&]*~', "ns=" . urlencode($q["ns"]), ME) : ME));
          echo "<td><a href='" . h($A . "table=" . urlencode($q["table"])) . "'>" . ($q["db"] != "" && $q["db"] != DB ? "<b>" . h($q["db"]) . "</b>." : "") . ($q["ns"] != "" && $q["ns"] != $_GET["ns"] ? "<b>" . h($q["ns"]) . "</b>." : "") . h($q["table"]) . "</a>", "(<i>" . implode("</i>, <i>", array_map('Adminer\h', $q["target"])) . "</i>)", "<td>" . h($q["on_delete"]), "<td>" . h($q["on_update"]), '<td><a href="' . h(ME . 'foreign=' . urlencode($a) . '&name=' . urlencode($C)) . '">' . lang(139) . '</a>', "\n";
        }
        echo "</table>\n";
      }
      echo '<p class="links"><a href="' . h(ME) . 'foreign=' . urlencode($a) . '">' . lang(140) . "</a>\n";
    }
    if (support("check")) {
      echo "<h3 id='checks'>" . lang(141) . "</h3>\n";
      $Ya = driver()->checkConstraints($a);
      if ($Ya) {
        echo "<table>\n";
        foreach (
          $Ya
          as $y => $X
        ) echo "<tr title='" . h($y) . "'>", "<td><code class='jush-" . JUSH . "'>" . h($X), "<td><a href='" . h(ME . 'check=' . urlencode($a) . '&name=' . urlencode($y)) . "'>" . lang(139) . "</a>", "\n";
        echo "</table>\n";
      }
      echo '<p class="links"><a href="' . h(ME) . 'check=' . urlencode($a) . '">' . lang(142) . "</a>\n";
    }
  }
  if (support(is_view($S) ? "view_trigger" : "trigger")) {
    echo "<h3 id='triggers'>" . lang(143) . "</h3>\n";
    $Ti = triggers($a);
    if ($Ti) {
      echo "<table>\n";
      foreach (
        $Ti
        as $y => $X
      ) echo "<tr valign='top'><td>" . h($X[0]) . "<td>" . h($X[1]) . "<th>" . h($y) . "<td><a href='" . h(ME . 'trigger=' . urlencode($a) . '&name=' . urlencode($y)) . "'>" . lang(139) . "</a>\n";
      echo "</table>\n";
    }
    echo '<p class="links"><a href="' . h(ME) . 'trigger=' . urlencode($a) . '">' . lang(144) . "</a>\n";
  }
} elseif (isset($_GET["schema"])) {
  page_header(lang(69), "", array(), h(DB . ($_GET["ns"] ? ".$_GET[ns]" : "")));
  $li = array();
  $mi = array();
  $da = ($_GET["schema"] ?: $_COOKIE["adminer_schema-" . str_replace(".", "_", DB)]);
  preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~', $da, $Re, PREG_SET_ORDER);
  foreach (
    $Re
    as $t => $B
  ) {
    $li[$B[1]] = array($B[2], $B[3]);
    $mi[] = "\n\t'" . js_escape($B[1]) . "': [ $B[2], $B[3] ]";
  }
  $Ii = 0;
  $Ia = -1;
  $qh = array();
  $Xg = array();
  $Ee = array();
  $ua = driver()->allFields();
  foreach (table_status('', true) as $R => $S) {
    if (is_view($S)) continue;
    $zg = 0;
    $qh[$R]["fields"] = array();
    foreach ($ua[$R] as $n) {
      $zg += 1.25;
      $n["pos"] = $zg;
      $qh[$R]["fields"][$n["field"]] = $n;
    }
    $qh[$R]["pos"] = ($li[$R] ?: array($Ii, 0));
    foreach (adminer()->foreignKeys($R) as $X) {
      if (!$X["db"]) {
        $Ce = $Ia;
        if (idx($li[$R], 1) || idx($li[$X["table"]], 1)) $Ce = min(idx($li[$R], 1, 0), idx($li[$X["table"]], 1, 0)) - 1;
        else $Ia -= .1;
        while ($Ee[(string)$Ce]) $Ce -= .0001;
        $qh[$R]["references"][$X["table"]][(string)$Ce] = array($X["source"], $X["target"]);
        $Xg[$X["table"]][$R][(string)$Ce] = $X["target"];
        $Ee[(string)$Ce] = true;
      }
    }
    $Ii = max($Ii, $qh[$R]["pos"][0] + 2.5 + $zg);
  }
  echo '<div id="schema" style="height: ', $Ii, 'em;">
<script', nonce(), '>
qs(\'#schema\').onselectstart = () => false;
const tablePos = {', implode(",", $mi) . "\n", '};
const em = qs(\'#schema\').offsetHeight / ', $Ii, ';
document.onmousemove = schemaMousemove;
document.onmouseup = partialArg(schemaMouseup, \'', js_escape(DB), '\');
</script>
';
  foreach (
    $qh
    as $C => $R
  ) {
    echo "<div class='table' style='top: " . $R["pos"][0] . "em; left: " . $R["pos"][1] . "em;'>", '<a href="' . h(ME) . 'table=' . urlencode($C) . '"><b>' . h($C) . "</b></a>", script("qsl('div').onmousedown = schemaMousedown;");
    foreach ($R["fields"] as $n) {
      $X = '<span' . type_class($n["type"]) . ' title="' . h($n["type"] . ($n["length"] ? "($n[length])" : "") . ($n["null"] ? " NULL" : '')) . '">' . h($n["field"]) . '</span>';
      echo "<br>" . ($n["primary"] ? "<i>$X</i>" : $X);
    }
    foreach ((array)$R["references"] as $ti => $Zg) {
      foreach (
        $Zg
        as $Ce => $Ug
      ) {
        $De = $Ce - idx($li[$C], 1);
        $t = 0;
        foreach ($Ug[0] as $Oh) echo "\n<div class='references' title='" . h($ti) . "' id='refs$Ce-" . ($t++) . "' style='left: $De" . "em; top: " . $R["fields"][$Oh]["pos"] . "em; padding-top: .5em;'>" . "<div style='border-top: 1px solid gray; width: " . (-$De) . "em;'></div></div>";
      }
    }
    foreach ((array)$Xg[$C] as $ti => $Zg) {
      foreach (
        $Zg
        as $Ce => $e
      ) {
        $De = $Ce - idx($li[$C], 1);
        $t = 0;
        foreach (
          $e
          as $si
        ) echo "\n<div class='references arrow' title='" . h($ti) . "' id='refd$Ce-" . ($t++) . "' style='left: $De" . "em; top: " . $R["fields"][$si]["pos"] . "em;'>" . "<div style='height: .5em; border-bottom: 1px solid gray; width: " . (-$De) . "em;'></div>" . "</div>";
      }
    }
    echo "\n</div>\n";
  }
  foreach (
    $qh
    as $C => $R
  ) {
    foreach ((array)$R["references"] as $ti => $Zg) {
      foreach (
        $Zg
        as $Ce => $Ug
      ) {
        $hf = $Ii;
        $Ve = -10;
        foreach ($Ug[0] as $y => $Oh) {
          $_g = $R["pos"][0] + $R["fields"][$Oh]["pos"];
          $Ag = $qh[$ti]["pos"][0] + $qh[$ti]["fields"][$Ug[1][$y]]["pos"];
          $hf = min($hf, $_g, $Ag);
          $Ve = max($Ve, $_g, $Ag);
        }
        echo "<div class='references' id='refl$Ce' style='left: $Ce" . "em; top: $hf" . "em; padding: .5em 0;'><div style='border-right: 1px solid gray; margin-top: 1px; height: " . ($Ve - $hf) . "em;'></div></div>\n";
      }
    }
  }
  echo '</div>
<p class="links"><a href="', h(ME . "schema=" . urlencode($da)), '" id="schema-link">', lang(145), '</a>
';
} elseif (isset($_GET["dump"])) {
  $a = $_GET["dump"];
  if ($_POST && !$m) {
    save_settings(array_intersect_key($_POST, array_flip(array("output", "format", "db_style", "types", "routines", "events", "table_style", "auto_increment", "triggers", "data_style"))), "adminer_export");
    $T = array_flip((array)$_POST["tables"]) + array_flip((array)$_POST["data"]);
    $Nc = dump_headers((count($T) == 1 ? key($T) : DB), (DB == "" || count($T) > 1));
    $ne = preg_match('~sql~', $_POST["format"]);
    if ($ne) {
      echo "-- Adminer " . VERSION . " " . get_driver(DRIVER) . " " . str_replace("\n", " ", connection()->server_info) . " dump\n\n";
      if (JUSH == "sql") {
        echo "SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
" . ($_POST["data_style"] ? "SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
" : "") . "
";
        connection()->query("SET time_zone = '+00:00'");
        connection()->query("SET sql_mode = ''");
      }
    }
    $bi = $_POST["db_style"];
    $j = array(DB);
    if (DB == "") {
      $j = $_POST["databases"];
      if (is_string($j)) $j = explode("\n", rtrim(str_replace("\r", "", $j), "\n"));
    }
    foreach (
      (array)$j
      as $k
    ) {
      adminer()->dumpDatabase($k);
      if (connection()->select_db($k)) {
        if ($ne && preg_match('~CREATE~', $bi) && ($i = get_val("SHOW CREATE DATABASE " . idf_escape($k), 1))) {
          set_utf8mb4($i);
          if ($bi == "DROP+CREATE") echo "DROP DATABASE IF EXISTS " . idf_escape($k) . ";\n";
          echo "$i;\n";
        }
        if ($ne) {
          if ($bi) echo
          use_sql($k) . ";\n\n";
          $dg = "";
          if ($_POST["types"]) {
            foreach (types() as $u => $U) {
              $Bc = type_values($u);
              if ($Bc) $dg
                .= ($bi != 'DROP+CREATE' ? "DROP TYPE IF EXISTS " . idf_escape($U) . ";;\n" : "") . "CREATE TYPE " . idf_escape($U) . " AS ENUM ($Bc);\n\n";
              else $dg
                .= "-- Could not export type $U\n\n";
            }
          }
          if ($_POST["routines"]) {
            foreach (routines() as $K) {
              $C = $K["ROUTINE_NAME"];
              $kh = $K["ROUTINE_TYPE"];
              $i = create_routine($kh, array("name" => $C) + routine($K["SPECIFIC_NAME"], $kh));
              set_utf8mb4($i);
              $dg
                .= ($bi != 'DROP+CREATE' ? "DROP $kh IF EXISTS " . idf_escape($C) . ";;\n" : "") . "$i;\n\n";
            }
          }
          if ($_POST["events"]) {
            foreach (get_rows("SHOW EVENTS", null, "-- ") as $K) {
              $i = remove_definer(get_val("SHOW CREATE EVENT " . idf_escape($K["Name"]), 3));
              set_utf8mb4($i);
              $dg
                .= ($bi != 'DROP+CREATE' ? "DROP EVENT IF EXISTS " . idf_escape($K["Name"]) . ";;\n" : "") . "$i;;\n\n";
            }
          }
          echo ($dg && JUSH == 'sql' ? "DELIMITER ;;\n\n$dg" . "DELIMITER ;\n\n" : $dg);
        }
        if ($_POST["table_style"] || $_POST["data_style"]) {
          $vj = array();
          foreach (table_status('', true) as $C => $S) {
            $R = (DB == "" || in_array($C, (array)$_POST["tables"]));
            $Lb = (DB == "" || in_array($C, (array)$_POST["data"]));
            if ($R || $Lb) {
              $Fi = null;
              if ($Nc == "tar") {
                $Fi = new
                  TmpFile;
                ob_start(array($Fi, 'write'), 1e5);
              }
              adminer()->dumpTable($C, ($R ? $_POST["table_style"] : ""), (is_view($S) ? 2 : 0));
              if (is_view($S)) $vj[] = $C;
              elseif ($Lb) {
                $o = fields($C);
                adminer()->dumpData($C, $_POST["data_style"], "SELECT *" . convert_fields($o, $o) . " FROM " . table($C));
              }
              if ($ne && $_POST["triggers"] && $R && ($Ti = trigger_sql($C))) echo "\nDELIMITER ;;\n$Ti\nDELIMITER ;\n";
              if ($Nc == "tar") {
                ob_end_flush();
                tar_file((DB != "" ? "" : "$k/") . "$C.csv", $Fi);
              } elseif ($ne) echo "\n";
            }
          }
          if (function_exists('Adminer\foreign_keys_sql')) {
            foreach (table_status('', true) as $C => $S) {
              $R = (DB == "" || in_array($C, (array)$_POST["tables"]));
              if ($R && !is_view($S)) echo
              foreign_keys_sql($C);
            }
          }
          foreach (
            $vj
            as $uj
          ) adminer()->dumpTable($uj, $_POST["table_style"], 1);
          if ($Nc == "tar") echo
          pack("x512");
        }
      }
    }
    adminer()->dumpFooter();
    exit;
  }
  page_header(lang(72), $m, ($_GET["export"] != "" ? array("table" => $_GET["export"]) : array()), h(DB));
  echo '
<form action="" method="post">
<table class="layout">
';
  $Pb = array('', 'USE', 'DROP+CREATE', 'CREATE');
  $ni = array('', 'DROP+CREATE', 'CREATE');
  $Mb = array('', 'TRUNCATE+INSERT', 'INSERT');
  if (JUSH == "sql") $Mb[] = 'INSERT+UPDATE';
  $K = get_settings("adminer_export");
  if (!$K) $K = array("output" => "text", "format" => "sql", "db_style" => (DB != "" ? "" : "CREATE"), "table_style" => "DROP+CREATE", "data_style" => "INSERT");
  if (!isset($K["events"])) {
    $K["routines"] = $K["events"] = ($_GET["dump"] == "");
    $K["triggers"] = $K["table_style"];
  }
  echo "<tr><th>" . lang(146) . "<td>" . html_radios("output", adminer()->dumpOutput(), $K["output"]) . "\n", "<tr><th>" . lang(147) . "<td>" . html_radios("format", adminer()->dumpFormat(), $K["format"]) . "\n", (JUSH == "sqlite" ? "" : "<tr><th>" . lang(36) . "<td>" . html_select('db_style', $Pb, $K["db_style"]) . (support("type") ? checkbox("types", 1, $K["types"], lang(31)) : "") . (support("routine") ? checkbox("routines", 1, $K["routines"], lang(148)) : "") . (support("event") ? checkbox("events", 1, $K["events"], lang(149)) : "")), "<tr><th>" . lang(124) . "<td>" . html_select('table_style', $ni, $K["table_style"]) . checkbox("auto_increment", 1, $K["auto_increment"], lang(50)) . (support("trigger") ? checkbox("triggers", 1, $K["triggers"], lang(143)) : ""), "<tr><th>" . lang(150) . "<td>" . html_select('data_style', $Mb, $K["data_style"]), '</table>
<p><input type="submit" value="', lang(72), '">
', input_token(), '
<table>
', script("qsl('table').onclick = dumpClick;");
  $Eg = array();
  if (DB != "") {
    $ab = ($a != "" ? "" : " checked");
    echo "<thead><tr>", "<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$ab>" . lang(124) . "</label>" . script("qs('#check-tables').onclick = partial(formCheck, /^tables\\[/);", ""), "<th style='text-align: right;'><label class='block'>" . lang(150) . "<input type='checkbox' id='check-data'$ab></label>" . script("qs('#check-data').onclick = partial(formCheck, /^data\\[/);", ""), "</thead>\n";
    $vj = "";
    $pi = tables_list();
    foreach (
      $pi
      as $C => $U
    ) {
      $Dg = preg_replace('~_.*~', '', $C);
      $ab = ($a == "" || $a == (substr($a, -1) == "%" ? "$Dg%" : $C));
      $Gg = "<tr><td>" . checkbox("tables[]", $C, $ab, $C, "", "block");
      if ($U !== null && !preg_match('~table~i', $U)) $vj
        .= "$Gg\n";
      else
        echo "$Gg<td align='right'><label class='block'><span id='Rows-" . h($C) . "'></span>" . checkbox("data[]", $C, $ab) . "</label>\n";
      $Eg[$Dg]++;
    }
    echo $vj;
    if ($pi) echo
    script("ajaxSetHtml('" . js_escape(ME) . "script=db');");
  } else {
    echo "<thead><tr><th style='text-align: left;'>", "<label class='block'><input type='checkbox' id='check-databases'" . ($a == "" ? " checked" : "") . ">" . lang(36) . "</label>", script("qs('#check-databases').onclick = partial(formCheck, /^databases\\[/);", ""), "</thead>\n";
    $j = adminer()->databases();
    if ($j) {
      foreach (
        $j
        as $k
      ) {
        if (!information_schema($k)) {
          $Dg = preg_replace('~_.*~', '', $k);
          echo "<tr><td>" . checkbox("databases[]", $k, $a == "" || $a == "$Dg%", $k, "", "block") . "\n";
          $Eg[$Dg]++;
        }
      }
    } else
      echo "<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";
  }
  echo '</table>
</form>
';
  $ad = true;
  foreach (
    $Eg
    as $y => $X
  ) {
    if ($y != "" && $X > 1) {
      echo ($ad ? "<p>" : " ") . "<a href='" . h(ME) . "dump=" . urlencode("$y%") . "'>" . h($y) . "</a>";
      $ad = false;
    }
  }
} elseif (isset($_GET["privileges"])) {
  page_header(lang(70));
  echo '<p class="links"><a href="' . h(ME) . 'user=">' . lang(151) . "</a>";
  $I = connection()->query("SELECT User, Host FROM mysql." . (DB == "" ? "user" : "db WHERE " . q(DB) . " LIKE Db") . " ORDER BY Host, User");
  $td = $I;
  if (!$I) $I = connection()->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");
  echo "<form action=''><p>\n";
  hidden_fields_get();
  echo
  input_hidden("db", DB), ($td ? "" : input_hidden("grant")), "<table class='odds'>\n", "<thead><tr><th>" . lang(34) . "<th>" . lang(33) . "<th></thead>\n";
  while ($K = $I->fetch_assoc()) echo '<tr><td>' . h($K["User"]) . "<td>" . h($K["Host"]) . '<td><a href="' . h(ME . 'user=' . urlencode($K["User"]) . '&host=' . urlencode($K["Host"])) . '">' . lang(10) . "</a>\n";
  if (!$td || DB != "") echo "<tr><td><input name='user' autocapitalize='off'><td><input name='host' value='localhost' autocapitalize='off'><td><input type='submit' value='" . lang(10) . "'>\n";
  echo "</table>\n", "</form>\n";
} elseif (isset($_GET["sql"])) {
  if (!$m && $_POST["export"]) {
    save_settings(array("output" => $_POST["output"], "format" => $_POST["format"]), "adminer_import");
    dump_headers("sql");
    adminer()->dumpTable("", "");
    adminer()->dumpData("", "table", $_POST["query"]);
    adminer()->dumpFooter();
    exit;
  }
  restart_session();
  $Jd = &get_session("queries");
  $Id = &$Jd[DB];
  if (!$m && $_POST["clear"]) {
    $Id = array();
    redirect(remove_from_uri("history"));
  }
  stop_session();
  page_header((isset($_GET["import"]) ? lang(71) : lang(63)), $m);
  $Ie = '--' . (JUSH == 'sql' ? ' ' : '');
  if (!$m && $_POST) {
    $r = false;
    if (!isset($_GET["import"])) $H = $_POST["query"];
    elseif ($_POST["webfile"]) {
      $Sh = adminer()->importServerPath();
      $r = @fopen((file_exists($Sh) ? $Sh : "compress.zlib://$Sh.gz"), "rb");
      $H = ($r ? fread($r, 1e6) : false);
    } else $H = get_file("sql_file", true, ";");
    if (is_string($H)) {
      if (function_exists('memory_get_usage') && ($af = ini_bytes("memory_limit")) != "-1") @ini_set("memory_limit", max($af, strval(2 * strlen($H) + memory_get_usage() + 8e6)));
      if ($H != "" && strlen($H) < 1e6) {
        $Ng = $H . (preg_match("~;[ \t\r\n]*\$~", $H) ? "" : ";");
        if (!$Id || first(end($Id)) != $Ng) {
          restart_session();
          $Id[] = array($Ng, time());
          set_session("queries", $Jd);
          stop_session();
        }
      }
      $Ph = "(?:\\s|/\\*[\s\S]*?\\*/|(?:#|$Ie)[^\n]*\n?|--\r?\n)";
      $Wb = ";";
      $D = 0;
      $wc = true;
      $h = connect();
      if ($h && DB != "") {
        $h->select_db(DB);
        if ($_GET["ns"] != "") set_schema($_GET["ns"], $h);
      }
      $nb = 0;
      $Dc = array();
      $kg = '[\'"' . (JUSH == "sql" ? '`#' : (JUSH == "sqlite" ? '`[' : (JUSH == "mssql" ? '[' : ''))) . ']|/\*|' . $Ie . '|$' . (JUSH == "pgsql" ? '|\$[^$]*\$' : '');
      $Ji = microtime(true);
      $oa = get_settings("adminer_import");
      $nc = adminer()->dumpFormat();
      unset($nc["sql"]);
      while ($H != "") {
        if (!$D && preg_match("~^$Ph*+DELIMITER\\s+(\\S+)~i", $H, $B)) {
          $Wb = preg_quote($B[1]);
          $H = substr($H, strlen($B[0]));
        } elseif (!$D && JUSH == 'pgsql' && preg_match("~^($Ph*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i", $H, $B)) {
          $Wb = "\n\\\\\\.\r?\n";
          $D = strlen($B[0]);
        } else {
          preg_match("($Wb\\s*|$kg)", $H, $B, PREG_OFFSET_CAPTURE, $D);
          list($md, $zg) = $B[0];
          if (!$md && $r && !feof($r)) $H
            .= fread($r, 1e5);
          else {
            if (!$md && rtrim($H) == "") break;
            $D = $zg + strlen($md);
            if ($md && !preg_match("(^$Wb)", $md)) {
              $Sa = driver()->hasCStyleEscapes() || (JUSH == "pgsql" && ($zg > 0 && strtolower($H[$zg - 1]) == "e"));
              $sg = ($md == '/*' ? '\*/' : ($md == '[' ? ']' : (preg_match("~^$Ie|^#~", $md) ? "\n" : preg_quote($md) . ($Sa ? '|\\\\.' : ''))));
              while (preg_match("($sg|\$)s", $H, $B, PREG_OFFSET_CAPTURE, $D)) {
                $oh = $B[0][0];
                if (!$oh && $r && !feof($r)) $H
                  .= fread($r, 1e5);
                else {
                  $D = $B[0][1] + strlen($oh);
                  if (!$oh || $oh[0] != "\\") break;
                }
              }
            } else {
              $wc = false;
              $Ng = substr($H, 0, $zg + ($Wb[0] == "\n" ? 3 : 0));
              $nb++;
              $Gg = "<pre id='sql-$nb'><code class='jush-" . JUSH . "'>" . adminer()->sqlCommandQuery($Ng) . "</code></pre>\n";
              if (JUSH == "sqlite" && preg_match("~^$Ph*+ATTACH\\b~i", $Ng, $B)) {
                echo $Gg, "<p class='error'>" . lang(152) . "\n";
                $Dc[] = " <a href='#sql-$nb'>$nb</a>";
                if ($_POST["error_stops"]) break;
              } else {
                if (!$_POST["only_errors"]) {
                  echo $Gg;
                  ob_flush();
                  flush();
                }
                $Xh = microtime(true);
                if (connection()->multi_query($Ng) && $h && preg_match("~^$Ph*+USE\\b~i", $Ng)) $h->query($Ng);
                do {
                  $I = connection()->store_result();
                  if (connection()->error) {
                    echo ($_POST["only_errors"] ? $Gg : ""), "<p class='error'>" . lang(153) . (connection()->errno ? " (" . connection()->errno . ")" : "") . ": " . error() . "\n";
                    $Dc[] = " <a href='#sql-$nb'>$nb</a>";
                    if ($_POST["error_stops"]) break
                      2;
                  } else {
                    $zi = " <span class='time'>(" . format_time($Xh) . ")</span>" . (strlen($Ng) < 1000 ? " <a href='" . h(ME) . "sql=" . urlencode(trim($Ng)) . "'>" . lang(10) . "</a>" : "");
                    $qa = connection()->affected_rows;
                    $yj = ($_POST["only_errors"] ? "" : driver()->warnings());
                    $zj = "warnings-$nb";
                    if ($yj) $zi
                      .= ", <a href='#$zj'>" . lang(45) . "</a>" . script("qsl('a').onclick = partial(toggle, '$zj');", "");
                    $Lc = null;
                    $Vf = null;
                    $Mc = "explain-$nb";
                    if (is_object($I)) {
                      $_ = $_POST["limit"];
                      $Vf = print_select_result($I, $h, array(), $_);
                      if (!$_POST["only_errors"]) {
                        echo "<form action='' method='post'>\n";
                        $xf = $I->num_rows;
                        echo "<p class='sql-footer'>" . ($xf ? ($_ && $xf > $_ ? lang(154, $_) : "") . lang(155, $xf) : ""), $zi;
                        if ($h && preg_match("~^($Ph|\\()*+SELECT\\b~i", $Ng) && ($Lc = explain($h, $Ng))) echo ", <a href='#$Mc'>Explain</a>" . script("qsl('a').onclick = partial(toggle, '$Mc');", "");
                        $u = "export-$nb";
                        echo ", <a href='#$u'>" . lang(72) . "</a>" . script("qsl('a').onclick = partial(toggle, '$u');", "") . "<span id='$u' class='hidden'>: " . html_select("output", adminer()->dumpOutput(), $oa["output"]) . " " . html_select("format", $nc, $oa["format"]) . input_hidden("query", $Ng) . "<input type='submit' name='export' value='" . lang(72) . "'>" . input_token() . "</span>\n" . "</form>\n";
                      }
                    } else {
                      if (preg_match("~^$Ph*+(CREATE|DROP|ALTER)$Ph++(DATABASE|SCHEMA)\\b~i", $Ng)) {
                        restart_session();
                        set_session("dbs", null);
                        stop_session();
                      }
                      if (!$_POST["only_errors"]) echo "<p class='message' title='" . h(connection()->info) . "'>" . lang(156, $qa) . "$zi\n";
                    }
                    echo ($yj ? "<div id='$zj' class='hidden'>\n$yj</div>\n" : "");
                    if ($Lc) {
                      echo "<div id='$Mc' class='hidden explain'>\n";
                      print_select_result($Lc, $h, $Vf);
                      echo "</div>\n";
                    }
                  }
                  $Xh = microtime(true);
                } while (connection()->next_result());
              }
              $H = substr($H, $D);
              $D = 0;
            }
          }
        }
      }
      if ($wc) echo "<p class='message'>" . lang(157) . "\n";
      elseif ($_POST["only_errors"]) echo "<p class='message'>" . lang(158, $nb - count($Dc)), " <span class='time'>(" . format_time($Ji) . ")</span>\n";
      elseif ($Dc && $nb > 1) echo "<p class='error'>" . lang(153) . ": " . implode("", $Dc) . "\n";
    } else
      echo "<p class='error'>" . upload_error($H) . "\n";
  }
  echo '
<form action="" method="post" enctype="multipart/form-data" id="form">
';
  $Jc = "<input type='submit' value='" . lang(159) . "' title='Ctrl+Enter'>";
  if (!isset($_GET["import"])) {
    $Ng = $_GET["sql"];
    if ($_POST) $Ng = $_POST["query"];
    elseif ($_GET["history"] == "all") $Ng = $Id;
    elseif ($_GET["history"] != "") $Ng = idx($Id[$_GET["history"]], 0);
    echo "<p>";
    textarea("query", $Ng, 20);
    echo
    script(($_POST ? "" : "qs('textarea').focus();\n") . "qs('#form').onsubmit = partial(sqlSubmit, qs('#form'), '" . js_escape(remove_from_uri("sql|limit|error_stops|only_errors|history")) . "');"), "<p>";
    adminer()->sqlPrintAfter();
    echo "$Jc\n", lang(160) . ": <input type='number' name='limit' class='size' value='" . h($_POST ? $_POST["limit"] : $_GET["limit"]) . "'>\n";
  } else {
    echo "<fieldset><legend>" . lang(161) . "</legend><div>";
    $zd = (extension_loaded("zlib") ? "[.gz]" : "");
    echo (ini_bool("file_uploads") ? "SQL$zd (&lt; " . ini_get("upload_max_filesize") . "B): <input type='file' name='sql_file[]' multiple>\n$Jc" : lang(162)), "</div></fieldset>\n";
    $Td = adminer()->importServerPath();
    if ($Td) echo "<fieldset><legend>" . lang(163) . "</legend><div>", lang(164, "<code>" . h($Td) . "$zd</code>"), ' <input type="submit" name="webfile" value="' . lang(165) . '">', "</div></fieldset>\n";
    echo "<p>";
  }
  echo
  checkbox("error_stops", 1, ($_POST ? $_POST["error_stops"] : isset($_GET["import"]) || $_GET["error_stops"]), lang(166)) . "\n", checkbox("only_errors", 1, ($_POST ? $_POST["only_errors"] : isset($_GET["import"]) || $_GET["only_errors"]), lang(167)) . "\n", input_token();
  if (!isset($_GET["import"]) && $Id) {
    print_fieldset("history", lang(168), $_GET["history"] != "");
    for ($X = end($Id); $X; $X = prev($Id)) {
      $y = key($Id);
      list($Ng, $zi, $rc) = $X;
      echo '<a href="' . h(ME . "sql=&history=$y") . '">' . lang(10) . "</a>" . " <span class='time' title='" . @date('Y-m-d', $zi) . "'>" . @date("H:i:s", $zi) . "</span>" . " <code class='jush-" . JUSH . "'>" . shorten_utf8(ltrim(str_replace("\n", " ", str_replace("\r", "", preg_replace("~^(#|$Ie).*~m", '', $Ng)))), 80, "</code>") . ($rc ? " <span class='time'>($rc)</span>" : "") . "<br>\n";
    }
    echo "<input type='submit' name='clear' value='" . lang(169) . "'>\n", "<a href='" . h(ME . "sql=&history=all") . "'>" . lang(170) . "</a>\n", "</div></fieldset>\n";
  }
  echo '</form>
';
} elseif (isset($_GET["edit"])) {
  $a = $_GET["edit"];
  $o = fields($a);
  $Z = (isset($_GET["select"]) ? ($_POST["check"] && count($_POST["check"]) == 1 ? where_check($_POST["check"][0], $o) : "") : where($_GET, $o));
  $fj = (isset($_GET["select"]) ? $_POST["edit"] : $Z);
  foreach (
    $o
    as $C => $n
  ) {
    if (!isset($n["privileges"][$fj ? "update" : "insert"]) || adminer()->fieldName($n) == "" || $n["generated"]) unset($o[$C]);
  }
  if ($_POST && !$m && !isset($_GET["select"])) {
    $Ke = $_POST["referer"];
    if ($_POST["insert"]) $Ke = ($fj ? null : $_SERVER["REQUEST_URI"]);
    elseif (!preg_match('~^.+&select=.+$~', $Ke)) $Ke = ME . "select=" . urlencode($a);
    $x = indexes($a);
    $aj = unique_array($_GET["where"], $x);
    $Qg = "\nWHERE $Z";
    if (isset($_POST["delete"])) queries_redirect($Ke, lang(171), driver()->delete($a, $Qg, $aj ? 0 : 1));
    else {
      $O = array();
      foreach (
        $o
        as $C => $n
      ) {
        $X = process_input($n);
        if ($X !== false && $X !== null) $O[idf_escape($C)] = $X;
      }
      if ($fj) {
        if (!$O) redirect($Ke);
        queries_redirect($Ke, lang(172), driver()->update($a, $O, $Qg, $aj ? 0 : 1));
        if (is_ajax()) {
          page_headers();
          page_messages($m);
          exit;
        }
      } else {
        $I = driver()->insert($a, $O);
        $Be = ($I ? last_id($I) : 0);
        queries_redirect($Ke, lang(173, ($Be ? " $Be" : "")), $I);
      }
    }
  }
  $K = null;
  if ($_POST["save"]) $K = (array)$_POST["fields"];
  elseif ($Z) {
    $M = array();
    foreach (
      $o
      as $C => $n
    ) {
      if (isset($n["privileges"]["select"])) {
        $ya = ($_POST["clone"] && $n["auto_increment"] ? "''" : convert_field($n));
        $M[] = ($ya ? "$ya AS " : "") . idf_escape($C);
      }
    }
    $K = array();
    if (!support("table")) $M = array("*");
    if ($M) {
      $I = driver()->select($a, $M, array($Z), $M, array(), (isset($_GET["select"]) ? 2 : 1));
      if (!$I) $m = error();
      else {
        $K = $I->fetch_assoc();
        if (!$K) $K = false;
      }
      if (isset($_GET["select"]) && (!$K || $I->fetch_assoc())) $K = null;
    }
  }
  if (!support("table") && !$o) {
    if (!$Z) {
      $I = driver()->select($a, array("*"), array(), array("*"));
      $K = ($I ? $I->fetch_assoc() : false);
      if (!$K) $K = array(driver()->primary => "");
    }
    if ($K) {
      foreach (
        $K
        as $y => $X
      ) {
        if (!$Z) $K[$y] = null;
        $o[$y] = array("field" => $y, "null" => ($y != driver()->primary), "auto_increment" => ($y == driver()->primary));
      }
    }
  }
  edit_form($a, $o, $K, $fj, $m);
} elseif (isset($_GET["create"])) {
  $a = $_GET["create"];
  $mg = array();
  foreach (array('HASH', 'LINEAR HASH', 'KEY', 'LINEAR KEY', 'RANGE', 'LIST') as $y) $mg[$y] = $y;
  $Wg = referencable_primary($a);
  $kd = array();
  foreach (
    $Wg
    as $ji => $n
  ) $kd[str_replace("`", "``", $ji) . "`" . str_replace("`", "``", $n["field"])] = $ji;
  $Yf = array();
  $S = array();
  if ($a != "") {
    $Yf = fields($a);
    $S = table_status1($a);
    if (count($S) < 2) $m = lang(9);
  }
  $K = $_POST;
  $K["fields"] = (array)$K["fields"];
  if ($K["auto_increment_col"]) $K["fields"][$K["auto_increment_col"]]["auto_increment"] = true;
  if ($_POST) save_settings(array("comments" => $_POST["comments"], "defaults" => $_POST["defaults"]));
  if ($_POST && !process_fields($K["fields"]) && !$m) {
    if ($_POST["drop"]) queries_redirect(substr(ME, 0, -1), lang(174), drop_tables(array($a)));
    else {
      $o = array();
      $ua = array();
      $jj = false;
      $id = array();
      $Xf = reset($Yf);
      $sa = " FIRST";
      foreach ($K["fields"] as $y => $n) {
        $q = $kd[$n["type"]];
        $Ui = ($q !== null ? $Wg[$q] : $n);
        if ($n["field"] != "") {
          if (!$n["generated"]) $n["default"] = null;
          $Lg = process_field($n, $Ui);
          $ua[] = array($n["orig"], $Lg, $sa);
          if (!$Xf || $Lg !== process_field($Xf, $Xf)) {
            $o[] = array($n["orig"], $Lg, $sa);
            if ($n["orig"] != "" || $sa) $jj = true;
          }
          if ($q !== null) $id[idf_escape($n["field"])] = ($a != "" && JUSH != "sqlite" ? "ADD" : " ") . format_foreign_key(array('table' => $kd[$n["type"]], 'source' => array($n["field"]), 'target' => array($Ui["field"]), 'on_delete' => $n["on_delete"],));
          $sa = " AFTER " . idf_escape($n["field"]);
        } elseif ($n["orig"] != "") {
          $jj = true;
          $o[] = array($n["orig"]);
        }
        if ($n["orig"] != "") {
          $Xf = next($Yf);
          if (!$Xf) $sa = "";
        }
      }
      $og = "";
      if (support("partitioning")) {
        if (isset($mg[$K["partition_by"]])) {
          $jg = array();
          foreach (
            $K
            as $y => $X
          ) {
            if (preg_match('~^partition~', $y)) $jg[$y] = $X;
          }
          foreach ($jg["partition_names"] as $y => $C) {
            if ($C == "") {
              unset($jg["partition_names"][$y]);
              unset($jg["partition_values"][$y]);
            }
          }
          if ($jg != get_partitions_info($a)) {
            $pg = array();
            if ($jg["partition_by"] == 'RANGE' || $jg["partition_by"] == 'LIST') {
              foreach ($jg["partition_names"] as $y => $C) {
                $Y = $jg["partition_values"][$y];
                $pg[] = "\n  PARTITION " . idf_escape($C) . " VALUES " . ($jg["partition_by"] == 'RANGE' ? "LESS THAN" : "IN") . ($Y != "" ? " ($Y)" : " MAXVALUE");
              }
            }
            $og
              .= "\nPARTITION BY $jg[partition_by]($jg[partition])";
            if ($pg) $og
              .= " (" . implode(",", $pg) . "\n)";
            elseif ($jg["partitions"]) $og
              .= " PARTITIONS " . (+$jg["partitions"]);
          }
        } elseif (preg_match("~partitioned~", $S["Create_options"])) $og
          .= "\nREMOVE PARTITIONING";
      }
      $bf = lang(175);
      if ($a == "") {
        cookie("adminer_engine", $K["Engine"]);
        $bf = lang(176);
      }
      $C = trim($K["name"]);
      queries_redirect(ME . (support("table") ? "table=" : "select=") . urlencode($C), $bf, alter_table($a, $C, (JUSH == "sqlite" && ($jj || $id) ? $ua : $o), $id, ($K["Comment"] != $S["Comment"] ? $K["Comment"] : null), ($K["Engine"] && $K["Engine"] != $S["Engine"] ? $K["Engine"] : ""), ($K["Collation"] && $K["Collation"] != $S["Collation"] ? $K["Collation"] : ""), ($K["Auto_increment"] != "" ? number($K["Auto_increment"]) : ""), $og));
    }
  }
  page_header(($a != "" ? lang(43) : lang(73)), $m, array("table" => $a), h($a));
  if (!$_POST) {
    $Wi = driver()->types();
    $K = array("Engine" => $_COOKIE["adminer_engine"], "fields" => array(array("field" => "", "type" => (isset($Wi["int"]) ? "int" : (isset($Wi["integer"]) ? "integer" : "")), "on_update" => "")), "partition_names" => array(""),);
    if ($a != "") {
      $K = $S;
      $K["name"] = $a;
      $K["fields"] = array();
      if (!$_GET["auto_increment"]) $K["Auto_increment"] = "";
      foreach (
        $Yf
        as $n
      ) {
        $n["generated"] = $n["generated"] ?: (isset($n["default"]) ? "DEFAULT" : "");
        $K["fields"][] = $n;
      }
      if (support("partitioning")) {
        $K += get_partitions_info($a);
        $K["partition_names"][] = "";
        $K["partition_values"][] = "";
      }
    }
  }
  $jb = collations();
  if (is_array(reset($jb))) $jb = call_user_func_array('array_merge', array_values($jb));
  $yc = driver()->engines();
  foreach (
    $yc
    as $xc
  ) {
    if (!strcasecmp($xc, $K["Engine"])) {
      $K["Engine"] = $xc;
      break;
    }
  }
  echo '
<form action="" method="post" id="form">
<p>
';
  if (support("columns") || $a == "") {
    echo
    lang(177) . ": <input name='name'" . ($a == "" && !$_POST ? " autofocus" : "") . " data-maxlength='64' value='" . h($K["name"]) . "' autocapitalize='off'>\n", ($yc ? html_select("Engine", array("" => "(" . lang(178) . ")") + $yc, $K["Engine"]) . on_help("event.target.value", 1) . script("qsl('select').onchange = helpClose;") . "\n" : "");
    if ($jb) echo "<datalist id='collations'>" . optionlist($jb) . "</datalist>\n", (preg_match("~sqlite|mssql~", JUSH) ? "" : "<input list='collations' name='Collation' value='" . h($K["Collation"]) . "' placeholder='(" . lang(102) . ")'>\n");
    echo "<input type='submit' value='" . lang(14) . "'>\n";
  }
  if (support("columns")) {
    echo "<div class='scrollable'>\n", "<table id='edit-fields' class='nowrap'>\n";
    edit_fields($K["fields"], $jb, "TABLE", $kd);
    echo "</table>\n", script("editFields();"), "</div>\n<p>\n", lang(50) . ": <input type='number' name='Auto_increment' class='size' value='" . h($K["Auto_increment"]) . "'>\n", checkbox("defaults", 1, ($_POST ? $_POST["defaults"] : get_setting("defaults")), lang(179), "columnShow(this.checked, 5)", "jsonly");
    $qb = ($_POST ? $_POST["comments"] : get_setting("comments"));
    echo (support("comment") ? checkbox("comments", 1, $qb, lang(49), "editingCommentsClick(this, true);", "jsonly") . ' ' . (preg_match('~\n~', $K["Comment"]) ? "<textarea name='Comment' rows='2' cols='20'" . ($qb ? "" : " class='hidden'") . ">" . h($K["Comment"]) . "</textarea>" : '<input name="Comment" value="' . h($K["Comment"]) . '" data-maxlength="' . (min_version(5.5) ? 2048 : 60) . '"' . ($qb ? "" : " class='hidden'") . '>') : ''), '<p>
<input type="submit" value="', lang(14), '">
';
  }
  echo '
';
  if ($a != "") echo '<input type="submit" name="drop" value="', lang(128), '">', confirm(lang(180, $a));
  if (support("partitioning")) {
    $ng = preg_match('~RANGE|LIST~', $K["partition_by"]);
    print_fieldset("partition", lang(181), $K["partition_by"]);
    echo "<p>" . html_select("partition_by", array("" => "") + $mg, $K["partition_by"]) . on_help("event.target.value.replace(/./, 'PARTITION BY \$&')", 1) . script("qsl('select').onchange = partitionByChange;"), "(<input name='partition' value='" . h($K["partition"]) . "'>)\n", lang(182) . ": <input type='number' name='partitions' class='size" . ($ng || !$K["partition_by"] ? " hidden" : "") . "' value='" . h($K["partitions"]) . "'>\n", "<table id='partition-table'" . ($ng ? "" : " class='hidden'") . ">\n", "<thead><tr><th>" . lang(183) . "<th>" . lang(184) . "</thead>\n";
    foreach ($K["partition_names"] as $y => $X) echo '<tr>', '<td><input name="partition_names[]" value="' . h($X) . '" autocapitalize="off">', ($y == count($K["partition_names"]) - 1 ? script("qsl('input').oninput = partitionNameChange;") : ''), '<td><input name="partition_values[]" value="' . h(idx($K["partition_values"], $y)) . '">';
    echo "</table>\n</div></fieldset>\n";
  }
  echo
  input_token(), '</form>
';
} elseif (isset($_GET["indexes"])) {
  $a = $_GET["indexes"];
  $Yd = array("PRIMARY", "UNIQUE", "INDEX");
  $S = table_status1($a, true);
  if (preg_match('~MyISAM|M?aria' . (min_version(5.6, '10.0.5') ? '|InnoDB' : '') . '~i', $S["Engine"])) $Yd[] = "FULLTEXT";
  if (preg_match('~MyISAM|M?aria' . (min_version(5.7, '10.2.2') ? '|InnoDB' : '') . '~i', $S["Engine"])) $Yd[] = "SPATIAL";
  $x = indexes($a);
  $G = array();
  if (JUSH == "mongo") {
    $G = $x["_id_"];
    unset($Yd[0]);
    unset($x["_id_"]);
  }
  $K = $_POST;
  if ($K) save_settings(array("index_options" => $K["options"]));
  if ($_POST && !$m && !$_POST["add"] && !$_POST["drop_col"]) {
    $b = array();
    foreach ($K["indexes"] as $w) {
      $C = $w["name"];
      if (in_array($w["type"], $Yd)) {
        $e = array();
        $Ge = array();
        $Zb = array();
        $O = array();
        ksort($w["columns"]);
        foreach ($w["columns"] as $y => $d) {
          if ($d != "") {
            $z = idx($w["lengths"], $y);
            $Xb = idx($w["descs"], $y);
            $O[] = idf_escape($d) . ($z ? "(" . (+$z) . ")" : "") . ($Xb ? " DESC" : "");
            $e[] = $d;
            $Ge[] = ($z ?: null);
            $Zb[] = $Xb;
          }
        }
        $Kc = $x[$C];
        if ($Kc) {
          ksort($Kc["columns"]);
          ksort($Kc["lengths"]);
          ksort($Kc["descs"]);
          if ($w["type"] == $Kc["type"] && array_values($Kc["columns"]) === $e && (!$Kc["lengths"] || array_values($Kc["lengths"]) === $Ge) && array_values($Kc["descs"]) === $Zb) {
            unset($x[$C]);
            continue;
          }
        }
        if ($e) $b[] = array($w["type"], $C, $O);
      }
    }
    foreach (
      $x
      as $C => $Kc
    ) $b[] = array($Kc["type"], $C, "DROP");
    if (!$b) redirect(ME . "table=" . urlencode($a));
    queries_redirect(ME . "table=" . urlencode($a), lang(185), alter_indexes($a, $b));
  }
  page_header(lang(135), $m, array("table" => $a), h($a));
  $o = array_keys(fields($a));
  if ($_POST["add"]) {
    foreach ($K["indexes"] as $y => $w) {
      if ($w["columns"][count($w["columns"])] != "") $K["indexes"][$y]["columns"][] = "";
    }
    $w = end($K["indexes"]);
    if ($w["type"] || array_filter($w["columns"], 'strlen')) $K["indexes"][] = array("columns" => array(1 => ""));
  }
  if (!$K) {
    foreach (
      $x
      as $y => $w
    ) {
      $x[$y]["name"] = $y;
      $x[$y]["columns"][] = "";
    }
    $x[] = array("columns" => array(1 => ""));
    $K["indexes"] = $x;
  }
  $Ge = (JUSH == "sql" || JUSH == "mssql");
  $Jh = ($_POST ? $_POST["options"] : get_setting("index_options"));
  echo '
<form action="" method="post">
<div class="scrollable">
<table class="nowrap">
<thead><tr>
<th id="label-type">', lang(186), '<th><input type="submit" class="wayoff">', lang(187) . ($Ge ? "<span class='idxopts" . ($Jh ? "" : " hidden") . "'> (" . lang(188) . ")</span>" : "");
  if ($Ge || support("descidx")) echo
  checkbox("options", 1, $Jh, lang(108), "indexOptionsShow(this.checked)", "jsonly") . "\n";
  echo '<th id="label-name">', lang(189), '<th><noscript>', icon("plus", "add[0]", "+", lang(109)), '</noscript>
</thead>
';
  if ($G) {
    echo "<tr><td>PRIMARY<td>";
    foreach ($G["columns"] as $y => $d) echo
    select_input(" disabled", $o, $d), "<label><input disabled type='checkbox'>" . lang(58) . "</label> ";
    echo "<td><td>\n";
  }
  $qe = 1;
  foreach ($K["indexes"] as $w) {
    if (!$_POST["drop_col"] || $qe != key($_POST["drop_col"])) {
      echo "<tr><td>" . html_select("indexes[$qe][type]", array(-1 => "") + $Yd, $w["type"], ($qe == count($K["indexes"]) ? "indexesAddRow.call(this);" : ""), "label-type"), "<td>";
      ksort($w["columns"]);
      $t = 1;
      foreach ($w["columns"] as $y => $d) {
        echo "<span>" . select_input(" name='indexes[$qe][columns][$t]' title='" . lang(47) . "'", ($o ? array_combine($o, $o) : $o), $d, "partial(" . ($t == count($w["columns"]) ? "indexesAddColumn" : "indexesChangeColumn") . ", '" . js_escape(JUSH == "sql" ? "" : $_GET["indexes"] . "_") . "')"), "<span class='idxopts" . ($Jh ? "" : " hidden") . "'>", ($Ge ? "<input type='number' name='indexes[$qe][lengths][$t]' class='size' value='" . h(idx($w["lengths"], $y)) . "' title='" . lang(107) . "'>" : ""), (support("descidx") ? checkbox("indexes[$qe][descs][$t]", 1, idx($w["descs"], $y), lang(58)) : ""), "</span> </span>";
        $t++;
      }
      echo "<td><input name='indexes[$qe][name]' value='" . h($w["name"]) . "' autocapitalize='off' aria-labelledby='label-name'>\n", "<td>" . icon("cross", "drop_col[$qe]", "x", lang(112)) . script("qsl('button').onclick = partial(editingRemoveRow, 'indexes\$1[type]');");
    }
    $qe++;
  }
  echo '</table>
</div>
<p>
<input type="submit" value="', lang(14), '">
', input_token(), '</form>
';
} elseif (isset($_GET["database"])) {
  $K = $_POST;
  if ($_POST && !$m && !$_POST["add"]) {
    $C = trim($K["name"]);
    if ($_POST["drop"]) {
      $_GET["db"] = "";
      queries_redirect(remove_from_uri("db|database"), lang(190), drop_databases(array(DB)));
    } elseif (DB !== $C) {
      if (DB != "") {
        $_GET["db"] = $C;
        queries_redirect(preg_replace('~\bdb=[^&]*&~', '', ME) . "db=" . urlencode($C), lang(191), rename_database($C, $K["collation"]));
      } else {
        $j = explode("\n", str_replace("\r", "", $C));
        $ci = true;
        $Ae = "";
        foreach (
          $j
          as $k
        ) {
          if (count($j) == 1 || $k != "") {
            if (!create_database($k, $K["collation"])) $ci = false;
            $Ae = $k;
          }
        }
        restart_session();
        set_session("dbs", null);
        queries_redirect(ME . "db=" . urlencode($Ae), lang(192), $ci);
      }
    } else {
      if (!$K["collation"]) redirect(substr(ME, 0, -1));
      query_redirect("ALTER DATABASE " . idf_escape($C) . (preg_match('~^[a-z0-9_]+$~i', $K["collation"]) ? " COLLATE $K[collation]" : ""), substr(ME, 0, -1), lang(193));
    }
  }
  page_header(DB != "" ? lang(66) : lang(116), $m, array(), h(DB));
  $jb = collations();
  $C = DB;
  if ($_POST) $C = $K["name"];
  elseif (DB != "") $K["collation"] = db_collation(DB, $jb);
  elseif (JUSH == "sql") {
    foreach (get_vals("SHOW GRANTS") as $td) {
      if (preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~', $td, $B) && $B[1]) {
        $C = stripcslashes(idf_unescape("`$B[2]`"));
        break;
      }
    }
  }
  echo '
<form action="" method="post">
<p>
', ($_POST["add"] || strpos($C, "\n") ? '<textarea autofocus name="name" rows="10" cols="40">' . h($C) . '</textarea><br>' : '<input name="name" autofocus value="' . h($C) . '" data-maxlength="64" autocapitalize="off">') . "\n" . ($jb ? html_select("collation", array("" => "(" . lang(102) . ")") + $jb, $K["collation"]) . doc_link(array('sql' => "charset-charsets.html", 'mariadb' => "supported-character-sets-and-collations/", 'mssql' => "relational-databases/system-functions/sys-fn-helpcollations-transact-sql",)) : ""), '<input type="submit" value="', lang(14), '">
';
  if (DB != "") echo "<input type='submit' name='drop' value='" . lang(128) . "'>" . confirm(lang(180, DB)) . "\n";
  elseif (!$_POST["add"] && $_GET["db"] == "") echo
  icon("plus", "add[0]", "+", lang(109)) . "\n";
  echo
  input_token(), '</form>
';
} elseif (isset($_GET["scheme"])) {
  $K = $_POST;
  if ($_POST && !$m) {
    $A = preg_replace('~ns=[^&]*&~', '', ME) . "ns=";
    if ($_POST["drop"]) query_redirect("DROP SCHEMA " . idf_escape($_GET["ns"]), $A, lang(194));
    else {
      $C = trim($K["name"]);
      $A
        .= urlencode($C);
      if ($_GET["ns"] == "") query_redirect("CREATE SCHEMA " . idf_escape($C), $A, lang(195));
      elseif ($_GET["ns"] != $C) query_redirect("ALTER SCHEMA " . idf_escape($_GET["ns"]) . " RENAME TO " . idf_escape($C), $A, lang(196));
      else
        redirect($A);
    }
  }
  page_header($_GET["ns"] != "" ? lang(67) : lang(68), $m);
  if (!$K) $K["name"] = $_GET["ns"];
  echo '
<form action="" method="post">
<p><input name="name" autofocus value="', h($K["name"]), '" autocapitalize="off">
<input type="submit" value="', lang(14), '">
';
  if ($_GET["ns"] != "") echo "<input type='submit' name='drop' value='" . lang(128) . "'>" . confirm(lang(180, $_GET["ns"])) . "\n";
  echo
  input_token(), '</form>
';
} elseif (isset($_GET["call"])) {
  $ca = ($_GET["name"] ?: $_GET["call"]);
  page_header(lang(197) . ": " . h($ca), $m);
  $kh = routine($_GET["call"], (isset($_GET["callf"]) ? "FUNCTION" : "PROCEDURE"));
  $Ud = array();
  $dg = array();
  foreach ($kh["fields"] as $t => $n) {
    if (substr($n["inout"], -3) == "OUT") $dg[$t] = "@" . idf_escape($n["field"]) . " AS " . idf_escape($n["field"]);
    if (!$n["inout"] || substr($n["inout"], 0, 2) == "IN") $Ud[] = $t;
  }
  if (!$m && $_POST) {
    $Ta = array();
    foreach ($kh["fields"] as $y => $n) {
      $X = "";
      if (in_array($y, $Ud)) {
        $X = process_input($n);
        if ($X === false) $X = "''";
        if (isset($dg[$y])) connection()->query("SET @" . idf_escape($n["field"]) . " = $X");
      }
      $Ta[] = (isset($dg[$y]) ? "@" . idf_escape($n["field"]) : $X);
    }
    $H = (isset($_GET["callf"]) ? "SELECT" : "CALL") . " " . table($ca) . "(" . implode(", ", $Ta) . ")";
    $Xh = microtime(true);
    $I = connection()->multi_query($H);
    $qa = connection()->affected_rows;
    echo
    adminer()->selectQuery($H, $Xh, !$I);
    if (!$I) echo "<p class='error'>" . error() . "\n";
    else {
      $h = connect();
      if ($h) $h->select_db(DB);
      do {
        $I = connection()->store_result();
        if (is_object($I)) print_select_result($I, $h);
        else
          echo "<p class='message'>" . lang(198, $qa) . " <span class='time'>" . @date("H:i:s") . "</span>\n";
      } while (connection()->next_result());
      if ($dg) print_select_result(connection()->query("SELECT " . implode(", ", $dg)));
    }
  }
  echo '
<form action="" method="post">
';
  if ($Ud) {
    echo "<table class='layout'>\n";
    foreach (
      $Ud
      as $y
    ) {
      $n = $kh["fields"][$y];
      $C = $n["field"];
      echo "<tr><th>" . adminer()->fieldName($n);
      $Y = idx($_POST["fields"], $C);
      if ($Y != "") {
        if ($n["type"] == "set") $Y = implode(",", $Y);
      }
      input($n, $Y, idx($_POST["function"], $C, ""));
      echo "\n";
    }
    echo "</table>\n";
  }
  echo '<p>
<input type="submit" value="', lang(197), '">
', input_token(), '</form>

<pre>
';
  function
  pre_tr($oh)
  {
    return
      preg_replace('~^~m', '<tr>', preg_replace('~\|~', '<td>', preg_replace('~\|$~m', "", rtrim($oh))));
  }
  $R = '(\+--[-+]+\+\n)';
  $K = '(\| .* \|\n)';
  echo
  preg_replace_callback("~^$R?$K$R?($K*)$R?~m", function ($B) {
    $bd = pre_tr($B[2]);
    return "<table>\n" . ($B[1] ? "<thead>$bd</thead>\n" : $bd) . pre_tr($B[4]) . "\n</table>";
  }, preg_replace('~(\n(    -|mysql)&gt; )(.+)~', "\\1<code class='jush-sql'>\\3</code>", preg_replace('~(.+)\n---+\n~', "<b>\\1</b>\n", h($kh['comment']))));
  echo '</pre>
';
} elseif (isset($_GET["foreign"])) {
  $a = $_GET["foreign"];
  $C = $_GET["name"];
  $K = $_POST;
  if ($_POST && !$m && !$_POST["add"] && !$_POST["change"] && !$_POST["change-js"]) {
    if (!$_POST["drop"]) {
      $K["source"] = array_filter($K["source"], 'strlen');
      ksort($K["source"]);
      $si = array();
      foreach ($K["source"] as $y => $X) $si[$y] = $K["target"][$y];
      $K["target"] = $si;
    }
    if (JUSH == "sqlite") $I = recreate_table($a, $a, array(), array(), array(" $C" => ($K["drop"] ? "" : " " . format_foreign_key($K))));
    else {
      $b = "ALTER TABLE " . table($a);
      $I = ($C == "" || queries("$b DROP " . (JUSH == "sql" ? "FOREIGN KEY " : "CONSTRAINT ") . idf_escape($C)));
      if (!$K["drop"]) $I = queries("$b ADD" . format_foreign_key($K));
    }
    queries_redirect(ME . "table=" . urlencode($a), ($K["drop"] ? lang(199) : ($C != "" ? lang(200) : lang(201))), $I);
    if (!$K["drop"]) $m = lang(202);
  }
  page_header(lang(203), $m, array("table" => $a), h($a));
  if ($_POST) {
    ksort($K["source"]);
    if ($_POST["add"]) $K["source"][] = "";
    elseif ($_POST["change"] || $_POST["change-js"]) $K["target"] = array();
  } elseif ($C != "") {
    $kd = foreign_keys($a);
    $K = $kd[$C];
    $K["source"][] = "";
  } else {
    $K["table"] = $a;
    $K["source"] = array("");
  }
  echo '
<form action="" method="post">
';
  $Oh = array_keys(fields($a));
  if ($K["db"] != "") connection()->select_db($K["db"]);
  if ($K["ns"] != "") {
    $Zf = get_schema();
    set_schema($K["ns"]);
  }
  $Vg = array_keys(array_filter(table_status('', true), 'Adminer\fk_support'));
  $si = array_keys(fields(in_array($K["table"], $Vg) ? $K["table"] : reset($Vg)));
  $Jf = "this.form['change-js'].value = '1'; this.form.submit();";
  echo "<p><label>" . lang(204) . ": " . html_select("table", $Vg, $K["table"], $Jf) . "</label>\n";
  if (support("scheme")) {
    $rh = array_filter(adminer()->schemas(), function ($qh) {
      return !preg_match('~^information_schema$~i', $qh);
    });
    echo "<label>" . lang(75) . ": " . html_select("ns", $rh, $K["ns"] != "" ? $K["ns"] : $_GET["ns"], $Jf) . "</label>";
    if ($K["ns"] != "") set_schema($Zf);
  } elseif (JUSH != "sqlite") {
    $Qb = array();
    foreach (adminer()->databases() as $k) {
      if (!information_schema($k)) $Qb[] = $k;
    }
    echo "<label>" . lang(74) . ": " . html_select("db", $Qb, $K["db"] != "" ? $K["db"] : $_GET["db"], $Jf) . "</label>";
  }
  echo
  input_hidden("change-js"), '<noscript><p><input type="submit" name="change" value="', lang(205), '"></noscript>
<table>
<thead><tr><th id="label-source">', lang(137), '<th id="label-target">', lang(138), '</thead>
';
  $qe = 0;
  foreach ($K["source"] as $y => $X) {
    echo "<tr>", "<td>" . html_select("source[" . (+$y) . "]", array(-1 => "") + $Oh, $X, ($qe == count($K["source"]) - 1 ? "foreignAddRow.call(this);" : ""), "label-source"), "<td>" . html_select("target[" . (+$y) . "]", $si, idx($K["target"], $y), "", "label-target");
    $qe++;
  }
  echo '</table>
<p>
<label>', lang(104), ': ', html_select("on_delete", array(-1 => "") + explode("|", driver()->onActions), $K["on_delete"]), '</label>
<label>', lang(103), ': ', html_select("on_update", array(-1 => "") + explode("|", driver()->onActions), $K["on_update"]), '</label>
', doc_link(array('sql' => "innodb-foreign-key-constraints.html", 'mariadb' => "foreign-keys/", 'pgsql' => "sql-createtable.html#SQL-CREATETABLE-REFERENCES", 'mssql' => "t-sql/statements/create-table-transact-sql", 'oracle' => "SQLRF01111",)), '<p>
<input type="submit" value="', lang(14), '">
<noscript><p><input type="submit" name="add" value="', lang(206), '"></noscript>
';
  if ($C != "") echo '<input type="submit" name="drop" value="', lang(128), '">', confirm(lang(180, $C));
  echo
  input_token(), '</form>
';
} elseif (isset($_GET["view"])) {
  $a = $_GET["view"];
  $K = $_POST;
  $ag = "VIEW";
  if (JUSH == "pgsql" && $a != "") {
    $P = table_status1($a);
    $ag = strtoupper($P["Engine"]);
  }
  if ($_POST && !$m) {
    $C = trim($K["name"]);
    $ya = " AS\n$K[select]";
    $Ke = ME . "table=" . urlencode($C);
    $bf = lang(207);
    $U = ($_POST["materialized"] ? "MATERIALIZED VIEW" : "VIEW");
    if (!$_POST["drop"] && $a == $C && JUSH != "sqlite" && $U == "VIEW" && $ag == "VIEW") query_redirect((JUSH == "mssql" ? "ALTER" : "CREATE OR REPLACE") . " VIEW " . table($C) . $ya, $Ke, $bf);
    else {
      $ui = $C . "_adminer_" . uniqid();
      drop_create("DROP $ag " . table($a), "CREATE $U " . table($C) . $ya, "DROP $U " . table($C), "CREATE $U " . table($ui) . $ya, "DROP $U " . table($ui), ($_POST["drop"] ? substr(ME, 0, -1) : $Ke), lang(208), $bf, lang(209), $a, $C);
    }
  }
  if (!$_POST && $a != "") {
    $K = view($a);
    $K["name"] = $a;
    $K["materialized"] = ($ag != "VIEW");
    if (!$m) $m = error();
  }
  page_header(($a != "" ? lang(42) : lang(210)), $m, array("table" => $a), h($a));
  echo '
<form action="" method="post">
<p>', lang(189), ': <input name="name" value="', h($K["name"]), '" data-maxlength="64" autocapitalize="off">
', (support("materializedview") ? " " . checkbox("materialized", 1, $K["materialized"], lang(132)) : ""), '<p>';
  textarea("select", $K["select"]);
  echo '<p>
<input type="submit" value="', lang(14), '">
';
  if ($a != "") echo '<input type="submit" name="drop" value="', lang(128), '">', confirm(lang(180, $a));
  echo
  input_token(), '</form>
';
} elseif (isset($_GET["event"])) {
  $aa = $_GET["event"];
  $ie = array("YEAR", "QUARTER", "MONTH", "DAY", "HOUR", "MINUTE", "WEEK", "SECOND", "YEAR_MONTH", "DAY_HOUR", "DAY_MINUTE", "DAY_SECOND", "HOUR_MINUTE", "HOUR_SECOND", "MINUTE_SECOND");
  $Yh = array("ENABLED" => "ENABLE", "DISABLED" => "DISABLE", "SLAVESIDE_DISABLED" => "DISABLE ON SLAVE");
  $K = $_POST;
  if ($_POST && !$m) {
    if ($_POST["drop"]) query_redirect("DROP EVENT " . idf_escape($aa), substr(ME, 0, -1), lang(211));
    elseif (in_array($K["INTERVAL_FIELD"], $ie) && isset($Yh[$K["STATUS"]])) {
      $ph = "\nON SCHEDULE " . ($K["INTERVAL_VALUE"] ? "EVERY " . q($K["INTERVAL_VALUE"]) . " $K[INTERVAL_FIELD]" . ($K["STARTS"] ? " STARTS " . q($K["STARTS"]) : "") . ($K["ENDS"] ? " ENDS " . q($K["ENDS"]) : "") : "AT " . q($K["STARTS"])) . " ON COMPLETION" . ($K["ON_COMPLETION"] ? "" : " NOT") . " PRESERVE";
      queries_redirect(substr(ME, 0, -1), ($aa != "" ? lang(212) : lang(213)), queries(($aa != "" ? "ALTER EVENT " . idf_escape($aa) . $ph . ($aa != $K["EVENT_NAME"] ? "\nRENAME TO " . idf_escape($K["EVENT_NAME"]) : "") : "CREATE EVENT " . idf_escape($K["EVENT_NAME"]) . $ph) . "\n" . $Yh[$K["STATUS"]] . " COMMENT " . q($K["EVENT_COMMENT"]) . rtrim(" DO\n$K[EVENT_DEFINITION]", ";") . ";"));
    }
  }
  page_header(($aa != "" ? lang(214) . ": " . h($aa) : lang(215)), $m);
  if (!$K && $aa != "") {
    $L = get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = " . q(DB) . " AND EVENT_NAME = " . q($aa));
    $K = reset($L);
  }
  echo '
<form action="" method="post">
<table class="layout">
<tr><th>', lang(189), '<td><input name="EVENT_NAME" value="', h($K["EVENT_NAME"]), '" data-maxlength="64" autocapitalize="off">
<tr><th title="datetime">', lang(216), '<td><input name="STARTS" value="', h("$K[EXECUTE_AT]$K[STARTS]"), '">
<tr><th title="datetime">', lang(217), '<td><input name="ENDS" value="', h($K["ENDS"]), '">
<tr><th>', lang(218), '<td><input type="number" name="INTERVAL_VALUE" value="', h($K["INTERVAL_VALUE"]), '" class="size"> ', html_select("INTERVAL_FIELD", $ie, $K["INTERVAL_FIELD"]), '<tr><th>', lang(119), '<td>', html_select("STATUS", $Yh, $K["STATUS"]), '<tr><th>', lang(49), '<td><input name="EVENT_COMMENT" value="', h($K["EVENT_COMMENT"]), '" data-maxlength="64">
<tr><th><td>', checkbox("ON_COMPLETION", "PRESERVE", $K["ON_COMPLETION"] == "PRESERVE", lang(219)), '</table>
<p>';
  textarea("EVENT_DEFINITION", $K["EVENT_DEFINITION"]);
  echo '<p>
<input type="submit" value="', lang(14), '">
';
  if ($aa != "") echo '<input type="submit" name="drop" value="', lang(128), '">', confirm(lang(180, $aa));
  echo
  input_token(), '</form>
';
} elseif (isset($_GET["procedure"])) {
  $ca = ($_GET["name"] ?: $_GET["procedure"]);
  $kh = (isset($_GET["function"]) ? "FUNCTION" : "PROCEDURE");
  $K = $_POST;
  $K["fields"] = (array)$K["fields"];
  if ($_POST && !process_fields($K["fields"]) && !$m) {
    $Wf = routine($_GET["procedure"], $kh);
    $ui = "$K[name]_adminer_" . uniqid();
    foreach ($K["fields"] as $y => $n) {
      if ($n["field"] == "") unset($K["fields"][$y]);
    }
    drop_create("DROP $kh " . routine_id($ca, $Wf), create_routine($kh, $K), "DROP $kh " . routine_id($K["name"], $K), create_routine($kh, array("name" => $ui) + $K), "DROP $kh " . routine_id($ui, $K), substr(ME, 0, -1), lang(220), lang(221), lang(222), $ca, $K["name"]);
  }
  page_header(($ca != "" ? (isset($_GET["function"]) ? lang(223) : lang(224)) . ": " . h($ca) : (isset($_GET["function"]) ? lang(225) : lang(226))), $m);
  if (!$_POST) {
    if ($ca == "") $K["language"] = "sql";
    else {
      $K = routine($_GET["procedure"], $kh);
      $K["name"] = $ca;
    }
  }
  $jb = get_vals("SHOW CHARACTER SET");
  sort($jb);
  $lh = routine_languages();
  echo ($jb ? "<datalist id='collations'>" . optionlist($jb) . "</datalist>" : ""), '
<form action="" method="post" id="form">
<p>', lang(189), ': <input name="name" value="', h($K["name"]), '" data-maxlength="64" autocapitalize="off">
', ($lh ? "<label>" . lang(19) . ": " . html_select("language", $lh, $K["language"]) . "</label>\n" : ""), '<input type="submit" value="', lang(14), '">
<div class="scrollable">
<table class="nowrap">
';
  edit_fields($K["fields"], $jb, $kh);
  if (isset($_GET["function"])) {
    echo "<tr><td>" . lang(227);
    edit_type("returns", $K["returns"], $jb, array(), (JUSH == "pgsql" ? array("void", "trigger") : array()));
  }
  echo '</table>
', script("editFields();"), '</div>
<p>';
  textarea("definition", $K["definition"]);
  echo '<p>
<input type="submit" value="', lang(14), '">
';
  if ($ca != "") echo '<input type="submit" name="drop" value="', lang(128), '">', confirm(lang(180, $ca));
  echo
  input_token(), '</form>
';
} elseif (isset($_GET["sequence"])) {
  $ea = $_GET["sequence"];
  $K = $_POST;
  if ($_POST && !$m) {
    $A = substr(ME, 0, -1);
    $C = trim($K["name"]);
    if ($_POST["drop"]) query_redirect("DROP SEQUENCE " . idf_escape($ea), $A, lang(228));
    elseif ($ea == "") query_redirect("CREATE SEQUENCE " . idf_escape($C), $A, lang(229));
    elseif ($ea != $C) query_redirect("ALTER SEQUENCE " . idf_escape($ea) . " RENAME TO " . idf_escape($C), $A, lang(230));
    else
      redirect($A);
  }
  page_header($ea != "" ? lang(231) . ": " . h($ea) : lang(232), $m);
  if (!$K) $K["name"] = $ea;
  echo '
<form action="" method="post">
<p><input name="name" value="', h($K["name"]), '" autocapitalize="off">
<input type="submit" value="', lang(14), '">
';
  if ($ea != "") echo "<input type='submit' name='drop' value='" . lang(128) . "'>" . confirm(lang(180, $ea)) . "\n";
  echo
  input_token(), '</form>
';
} elseif (isset($_GET["type"])) {
  $fa = $_GET["type"];
  $K = $_POST;
  if ($_POST && !$m) {
    $A = substr(ME, 0, -1);
    if ($_POST["drop"]) query_redirect("DROP TYPE " . idf_escape($fa), $A, lang(233));
    else
      query_redirect("CREATE TYPE " . idf_escape(trim($K["name"])) . " $K[as]", $A, lang(234));
  }
  page_header($fa != "" ? lang(235) . ": " . h($fa) : lang(236), $m);
  if (!$K) $K["as"] = "AS ";
  echo '
<form action="" method="post">
<p>
';
  if ($fa != "") {
    $Wi = driver()->types();
    $Bc = type_values($Wi[$fa]);
    if ($Bc) echo "<code class='jush-" . JUSH . "'>ENUM (" . h($Bc) . ")</code>\n<p>";
    echo "<input type='submit' name='drop' value='" . lang(128) . "'>" . confirm(lang(180, $fa)) . "\n";
  } else {
    echo
    lang(189) . ": <input name='name' value='" . h($K['name']) . "' autocapitalize='off'>\n", doc_link(array('pgsql' => "datatype-enum.html",), "?");
    textarea("as", $K["as"]);
    echo "<p><input type='submit' value='" . lang(14) . "'>\n";
  }
  echo
  input_token(), '</form>
';
} elseif (isset($_GET["check"])) {
  $a = $_GET["check"];
  $C = $_GET["name"];
  $K = $_POST;
  if ($K && !$m) {
    if (JUSH == "sqlite") $I = recreate_table($a, $a, array(), array(), array(), "", array(), "$C", ($K["drop"] ? "" : $K["clause"]));
    else {
      $I = ($C == "" || queries("ALTER TABLE " . table($a) . " DROP CONSTRAINT " . idf_escape($C)));
      if (!$K["drop"]) $I = queries("ALTER TABLE " . table($a) . " ADD" . ($K["name"] != "" ? " CONSTRAINT " . idf_escape($K["name"]) : "") . " CHECK ($K[clause])");
    }
    queries_redirect(ME . "table=" . urlencode($a), ($K["drop"] ? lang(237) : ($C != "" ? lang(238) : lang(239))), $I);
  }
  page_header(($C != "" ? lang(240) . ": " . h($C) : lang(142)), $m, array("table" => $a));
  if (!$K) {
    $bb = driver()->checkConstraints($a);
    $K = array("name" => $C, "clause" => $bb[$C]);
  }
  echo '
<form action="" method="post">
<p>';
  if (JUSH != "sqlite") echo
  lang(189) . ': <input name="name" value="' . h($K["name"]) . '" data-maxlength="64" autocapitalize="off"> ';
  echo
  doc_link(array('sql' => "create-table-check-constraints.html", 'mariadb' => "constraint/", 'pgsql' => "ddl-constraints.html#DDL-CONSTRAINTS-CHECK-CONSTRAINTS", 'mssql' => "relational-databases/tables/create-check-constraints", 'sqlite' => "lang_createtable.html#check_constraints",), "?"), '<p>';
  textarea("clause", $K["clause"]);
  echo '<p><input type="submit" value="', lang(14), '">
';
  if ($C != "") echo '<input type="submit" name="drop" value="', lang(128), '">', confirm(lang(180, $C));
  echo
  input_token(), '</form>
';
} elseif (isset($_GET["trigger"])) {
  $a = $_GET["trigger"];
  $C = "$_GET[name]";
  $Si = trigger_options();
  $K = (array)trigger($C, $a) + array("Trigger" => $a . "_bi");
  if ($_POST) {
    if (!$m && in_array($_POST["Timing"], $Si["Timing"]) && in_array($_POST["Event"], $Si["Event"]) && in_array($_POST["Type"], $Si["Type"])) {
      $Gf = " ON " . table($a);
      $hc = "DROP TRIGGER " . idf_escape($C) . (JUSH == "pgsql" ? $Gf : "");
      $Ke = ME . "table=" . urlencode($a);
      if ($_POST["drop"]) query_redirect($hc, $Ke, lang(241));
      else {
        if ($C != "") queries($hc);
        queries_redirect($Ke, ($C != "" ? lang(242) : lang(243)), queries(create_trigger($Gf, $_POST)));
        if ($C != "") queries(create_trigger($Gf, $K + array("Type" => reset($Si["Type"]))));
      }
    }
    $K = $_POST;
  }
  page_header(($C != "" ? lang(244) . ": " . h($C) : lang(245)), $m, array("table" => $a));
  echo '
<form action="" method="post" id="form">
<table class="layout">
<tr><th>', lang(246), '<td>', html_select("Timing", $Si["Timing"], $K["Timing"], "triggerChange(/^" . preg_quote($a, "/") . "_[ba][iud]$/, '" . js_escape($a) . "', this.form);"), '<tr><th>', lang(247), '<td>', html_select("Event", $Si["Event"], $K["Event"], "this.form['Timing'].onchange();"), (in_array("UPDATE OF", $Si["Event"]) ? " <input name='Of' value='" . h($K["Of"]) . "' class='hidden'>" : ""), '<tr><th>', lang(48), '<td>', html_select("Type", $Si["Type"], $K["Type"]), '</table>
<p>', lang(189), ': <input name="Trigger" value="', h($K["Trigger"]), '" data-maxlength="64" autocapitalize="off">
', script("qs('#form')['Timing'].onchange();"), '<p>';
  textarea("Statement", $K["Statement"]);
  echo '<p>
<input type="submit" value="', lang(14), '">
';
  if ($C != "") echo '<input type="submit" name="drop" value="', lang(128), '">', confirm(lang(180, $C));
  echo
  input_token(), '</form>
';
} elseif (isset($_GET["user"])) {
  $ga = $_GET["user"];
  $Jg = array("" => array("All privileges" => ""));
  foreach (get_rows("SHOW PRIVILEGES") as $K) {
    foreach (explode(",", ($K["Privilege"] == "Grant option" ? "" : $K["Context"])) as $_b) $Jg[$_b][$K["Privilege"]] = $K["Comment"];
  }
  $Jg["Server Admin"] += $Jg["File access on server"];
  $Jg["Databases"]["Create routine"] = $Jg["Procedures"]["Create routine"];
  unset($Jg["Procedures"]["Create routine"]);
  $Jg["Columns"] = array();
  foreach (array("Select", "Insert", "Update", "References") as $X) $Jg["Columns"][$X] = $Jg["Tables"][$X];
  unset($Jg["Server Admin"]["Usage"]);
  foreach ($Jg["Tables"] as $y => $X) unset($Jg["Databases"][$y]);
  $qf = array();
  if ($_POST) {
    foreach ($_POST["objects"] as $y => $X) $qf[$X] = (array)$qf[$X] + idx($_POST["grants"], $y, array());
  }
  $ud = array();
  $Ef = "";
  if (isset($_GET["host"]) && ($I = connection()->query("SHOW GRANTS FOR " . q($ga) . "@" . q($_GET["host"])))) {
    while ($K = $I->fetch_row()) {
      if (preg_match('~GRANT (.*) ON (.*) TO ~', $K[0], $B) && preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~', $B[1], $Re, PREG_SET_ORDER)) {
        foreach (
          $Re
          as $X
        ) {
          if ($X[1] != "USAGE") $ud["$B[2]$X[2]"][$X[1]] = true;
          if (preg_match('~ WITH GRANT OPTION~', $K[0])) $ud["$B[2]$X[2]"]["GRANT OPTION"] = true;
        }
      }
      if (preg_match("~ IDENTIFIED BY PASSWORD '([^']+)~", $K[0], $B)) $Ef = $B[1];
    }
  }
  if ($_POST && !$m) {
    $Ff = (isset($_GET["host"]) ? q($ga) . "@" . q($_GET["host"]) : "''");
    if ($_POST["drop"]) query_redirect("DROP USER $Ff", ME . "privileges=", lang(248));
    else {
      $sf = q($_POST["user"]) . "@" . q($_POST["host"]);
      $qg = $_POST["pass"];
      if ($qg != '' && !$_POST["hashed"] && !min_version(8)) {
        $qg = get_val("SELECT PASSWORD(" . q($qg) . ")");
        $m = !$qg;
      }
      $Eb = false;
      if (!$m) {
        if ($Ff != $sf) {
          $Eb = queries((min_version(5) ? "CREATE USER" : "GRANT USAGE ON *.* TO") . " $sf IDENTIFIED BY " . (min_version(8) ? "" : "PASSWORD ") . q($qg));
          $m = !$Eb;
        } elseif ($qg != $Ef) queries("SET PASSWORD FOR $sf = " . q($qg));
      }
      if (!$m) {
        $hh = array();
        foreach (
          $qf
          as $zf => $td
        ) {
          if (isset($_GET["grant"])) $td = array_filter($td);
          $td = array_keys($td);
          if (isset($_GET["grant"])) $hh = array_diff(array_keys(array_filter($qf[$zf], 'strlen')), $td);
          elseif ($Ff == $sf) {
            $Cf = array_keys((array)$ud[$zf]);
            $hh = array_diff($Cf, $td);
            $td = array_diff($td, $Cf);
            unset($ud[$zf]);
          }
          if (preg_match('~^(.+)\s*(\(.*\))?$~U', $zf, $B) && (!grant("REVOKE", $hh, $B[2], " ON $B[1] FROM $sf") || !grant("GRANT", $td, $B[2], " ON $B[1] TO $sf"))) {
            $m = true;
            break;
          }
        }
      }
      if (!$m && isset($_GET["host"])) {
        if ($Ff != $sf) queries("DROP USER $Ff");
        elseif (!isset($_GET["grant"])) {
          foreach (
            $ud
            as $zf => $hh
          ) {
            if (preg_match('~^(.+)(\(.*\))?$~U', $zf, $B)) grant("REVOKE", array_keys($hh), $B[2], " ON $B[1] FROM $sf");
          }
        }
      }
      queries_redirect(ME . "privileges=", (isset($_GET["host"]) ? lang(249) : lang(250)), !$m);
      if ($Eb) connection()->query("DROP USER $sf");
    }
  }
  page_header((isset($_GET["host"]) ? lang(34) . ": " . h("$ga@$_GET[host]") : lang(151)), $m, array("privileges" => array('', lang(70))));
  $K = $_POST;
  if ($K) $ud = $qf;
  else {
    $K = $_GET + array("host" => get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)"));
    $K["pass"] = $Ef;
    if ($Ef != "") $K["hashed"] = true;
    $ud[(DB == "" || $ud ? "" : idf_escape(addcslashes(DB, "%_\\"))) . ".*"] = array();
  }
  echo '<form action="" method="post">
<table class="layout">
<tr><th>', lang(33), '<td><input name="host" data-maxlength="60" value="', h($K["host"]), '" autocapitalize="off">
<tr><th>', lang(34), '<td><input name="user" data-maxlength="80" value="', h($K["user"]), '" autocapitalize="off">
<tr><th>', lang(35), '<td><input name="pass" id="pass" value="', h($K["pass"]), '" autocomplete="new-password">
', ($K["hashed"] ? "" : script("typePassword(qs('#pass'));")), (min_version(8) ? "" : checkbox("hashed", 1, $K["hashed"], lang(251), "typePassword(this.form['pass'], this.checked);")), '</table>

', "<table class='odds'>\n", "<thead><tr><th colspan='2'>" . lang(70) . doc_link(array('sql' => "grant.html#priv_level"));
  $t = 0;
  foreach (
    $ud
    as $zf => $td
  ) {
    echo '<th>' . ($zf != "*.*" ? "<input name='objects[$t]' value='" . h($zf) . "' size='10' autocapitalize='off'>" : input_hidden("objects[$t]", "*.*") . "*.*");
    $t++;
  }
  echo "</thead>\n";
  foreach (array("" => "", "Server Admin" => lang(33), "Databases" => lang(36), "Tables" => lang(134), "Columns" => lang(47), "Procedures" => lang(252),) as $_b => $Xb) {
    foreach ((array)$Jg[$_b] as $Ig => $ob) {
      echo "<tr><td" . ($Xb ? ">$Xb<td" : " colspan='2'") . ' lang="en" title="' . h($ob) . '">' . h($Ig);
      $t = 0;
      foreach (
        $ud
        as $zf => $td
      ) {
        $C = "'grants[$t][" . h(strtoupper($Ig)) . "]'";
        $Y = $td[strtoupper($Ig)];
        if ($_b == "Server Admin" && $zf != (isset($ud["*.*"]) ? "*.*" : ".*")) echo "<td>";
        elseif (isset($_GET["grant"])) echo "<td><select name=$C><option><option value='1'" . ($Y ? " selected" : "") . ">" . lang(253) . "<option value='0'" . ($Y == "0" ? " selected" : "") . ">" . lang(254) . "</select>";
        else
          echo "<td align='center'><label class='block'>", "<input type='checkbox' name=$C value='1'" . ($Y ? " checked" : "") . ($Ig == "All privileges" ? " id='grants-$t-all'>" : ">" . ($Ig == "Grant option" ? "" : script("qsl('input').onclick = function () { if (this.checked) formUncheck('grants-$t-all'); };"))), "</label>";
        $t++;
      }
    }
  }
  echo "</table>\n", '<p>
<input type="submit" value="', lang(14), '">
';
  if (isset($_GET["host"])) echo '<input type="submit" name="drop" value="', lang(128), '">', confirm(lang(180, "$ga@$_GET[host]"));
  echo
  input_token(), '</form>
';
} elseif (isset($_GET["processlist"])) {
  if (support("kill")) {
    if ($_POST && !$m) {
      $we = 0;
      foreach ((array)$_POST["kill"] as $X) {
        if (kill_process($X)) $we++;
      }
      queries_redirect(ME . "processlist=", lang(255, $we), $we || !$_POST["kill"]);
    }
  }
  page_header(lang(117), $m);
  echo '
<form action="" method="post">
<div class="scrollable">
<table class="nowrap checkable odds">
', script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});");
  $t = -1;
  foreach (process_list() as $t => $K) {
    if (!$t) {
      echo "<thead><tr lang='en'>" . (support("kill") ? "<th>" : "");
      foreach (
        $K
        as $y => $X
      ) echo "<th>$y" . doc_link(array('sql' => "show-processlist.html#processlist_" . strtolower($y), 'pgsql' => "monitoring-stats.html#PG-STAT-ACTIVITY-VIEW", 'oracle' => "REFRN30223",));
      echo "</thead>\n";
    }
    echo "<tr>" . (support("kill") ? "<td>" . checkbox("kill[]", $K[JUSH == "sql" ? "Id" : "pid"], 0) : "");
    foreach (
      $K
      as $y => $X
    ) echo "<td>" . ((JUSH == "sql" && $y == "Info" && preg_match("~Query|Killed~", $K["Command"]) && $X != "") || (JUSH == "pgsql" && $y == "current_query" && $X != "<IDLE>") || (JUSH == "oracle" && $y == "sql_text" && $X != "") ? "<code class='jush-" . JUSH . "'>" . shorten_utf8($X, 100, "</code>") . ' <a href="' . h(ME . ($K["db"] != "" ? "db=" . urlencode($K["db"]) . "&" : "") . "sql=" . urlencode($X)) . '">' . lang(256) . '</a>' : h($X));
    echo "\n";
  }
  echo '</table>
</div>
<p>
';
  if (support("kill")) echo ($t + 1) . "/" . lang(257, max_connections()), "<p><input type='submit' value='" . lang(258) . "'>\n";
  echo
  input_token(), '</form>
', script("tableCheck();");
} elseif (isset($_GET["select"])) {
  $a = $_GET["select"];
  $S = table_status1($a);
  $x = indexes($a);
  $o = fields($a);
  $kd = column_foreign_keys($a);
  $Af = $S["Oid"];
  $pa = get_settings("adminer_import");
  $ih = array();
  $e = array();
  $vh = array();
  $Sf = array();
  $yi = "";
  foreach (
    $o
    as $y => $n
  ) {
    $C = adminer()->fieldName($n);
    $of = html_entity_decode(strip_tags($C), ENT_QUOTES);
    if (isset($n["privileges"]["select"]) && $C != "") {
      $e[$y] = $of;
      if (is_shortable($n)) $yi = adminer()->selectLengthProcess();
    }
    if (isset($n["privileges"]["where"]) && $C != "") $vh[$y] = $of;
    if (isset($n["privileges"]["order"]) && $C != "") $Sf[$y] = $of;
    $ih += $n["privileges"];
  }
  list($M, $vd) = adminer()->selectColumnsProcess($e, $x);
  $M = array_unique($M);
  $vd = array_unique($vd);
  $me = count($vd) < count($M);
  $Z = adminer()->selectSearchProcess($o, $x);
  $Rf = adminer()->selectOrderProcess($o, $x);
  $_ = adminer()->selectLimitProcess();
  if ($_GET["val"] && is_ajax()) {
    header("Content-Type: text/plain; charset=utf-8");
    foreach ($_GET["val"] as $bj => $K) {
      $ya = convert_field($o[key($K)]);
      $M = array($ya ?: idf_escape(key($K)));
      $Z[] = where_check($bj, $o);
      $J = driver()->select($a, $M, $Z, $M);
      if ($J) echo
      first($J->fetch_row());
    }
    exit;
  }
  $G = $dj = array();
  foreach (
    $x
    as $w
  ) {
    if ($w["type"] == "PRIMARY") {
      $G = array_flip($w["columns"]);
      $dj = ($M ? $G : array());
      foreach (
        $dj
        as $y => $X
      ) {
        if (in_array(idf_escape($y), $M)) unset($dj[$y]);
      }
      break;
    }
  }
  if ($Af && !$G) {
    $G = $dj = array($Af => 0);
    $x[] = array("type" => "PRIMARY", "columns" => array($Af));
  }
  if ($_POST && !$m) {
    $Aj = $Z;
    if (!$_POST["all"] && is_array($_POST["check"])) {
      $bb = array();
      foreach ($_POST["check"] as $Xa) $bb[] = where_check($Xa, $o);
      $Aj[] = "((" . implode(") OR (", $bb) . "))";
    }
    $Aj = ($Aj ? "\nWHERE " . implode(" AND ", $Aj) : "");
    if ($_POST["export"]) {
      save_settings(array("output" => $_POST["output"], "format" => $_POST["format"]), "adminer_import");
      dump_headers($a);
      adminer()->dumpTable($a, "");
      $od = ($M ? implode(", ", $M) : "*") . convert_fields($e, $o, $M) . "\nFROM " . table($a);
      $xd = ($vd && $me ? "\nGROUP BY " . implode(", ", $vd) : "") . ($Rf ? "\nORDER BY " . implode(", ", $Rf) : "");
      $H = "SELECT $od$Aj$xd";
      if (is_array($_POST["check"]) && !$G) {
        $Zi = array();
        foreach ($_POST["check"] as $X) $Zi[] = "(SELECT" . limit($od, "\nWHERE " . ($Z ? implode(" AND ", $Z) . " AND " : "") . where_check($X, $o) . $xd, 1) . ")";
        $H = implode(" UNION ALL ", $Zi);
      }
      adminer()->dumpData($a, "table", $H);
      adminer()->dumpFooter();
      exit;
    }
    if (!adminer()->selectEmailProcess($Z, $kd)) {
      if ($_POST["save"] || $_POST["delete"]) {
        $I = true;
        $qa = 0;
        $O = array();
        if (!$_POST["delete"]) {
          foreach ($_POST["fields"] as $C => $X) {
            $X = process_input($o[$C]);
            if ($X !== null && ($_POST["clone"] || $X !== false)) $O[idf_escape($C)] = ($X !== false ? $X : idf_escape($C));
          }
        }
        if ($_POST["delete"] || $O) {
          $H = ($_POST["clone"] ? "INTO " . table($a) . " (" . implode(", ", array_keys($O)) . ")\nSELECT " . implode(", ", $O) . "\nFROM " . table($a) : "");
          if ($_POST["all"] || ($G && is_array($_POST["check"])) || $me) {
            $I = ($_POST["delete"] ? driver()->delete($a, $Aj) : ($_POST["clone"] ? queries("INSERT $H$Aj" . driver()->insertReturning($a)) : driver()->update($a, $O, $Aj)));
            $qa = connection()->affected_rows;
            if (is_object($I)) $qa += $I->num_rows;
          } else {
            foreach ((array)$_POST["check"] as $X) {
              $_j = "\nWHERE " . ($Z ? implode(" AND ", $Z) . " AND " : "") . where_check($X, $o);
              $I = ($_POST["delete"] ? driver()->delete($a, $_j, 1) : ($_POST["clone"] ? queries("INSERT" . limit1($a, $H, $_j)) : driver()->update($a, $O, $_j, 1)));
              if (!$I) break;
              $qa += connection()->affected_rows;
            }
          }
        }
        $bf = lang(259, $qa);
        if ($_POST["clone"] && $I && $qa == 1) {
          $Be = last_id($I);
          if ($Be) $bf = lang(173, " $Be");
        }
        queries_redirect(remove_from_uri($_POST["all"] && $_POST["delete"] ? "page" : ""), $bf, $I);
        if (!$_POST["delete"]) {
          $Bg = (array)$_POST["fields"];
          edit_form($a, array_intersect_key($o, $Bg), $Bg, !$_POST["clone"], $m);
          page_footer();
          exit;
        }
      } elseif (!$_POST["import"]) {
        if (!$_POST["val"]) $m = lang(260);
        else {
          $I = true;
          $qa = 0;
          foreach ($_POST["val"] as $bj => $K) {
            $O = array();
            foreach (
              $K
              as $y => $X
            ) {
              $y = bracket_escape($y, true);
              $O[idf_escape($y)] = (preg_match('~char|text~', $o[$y]["type"]) || $X != "" ? adminer()->processInput($o[$y], $X) : "NULL");
            }
            $I = driver()->update($a, $O, " WHERE " . ($Z ? implode(" AND ", $Z) . " AND " : "") . where_check($bj, $o), ($me || $G ? 0 : 1), " ");
            if (!$I) break;
            $qa += connection()->affected_rows;
          }
          queries_redirect(remove_from_uri(), lang(259, $qa), $I);
        }
      } elseif (!is_string($Yc = get_file("csv_file", true))) $m = upload_error($Yc);
      elseif (!preg_match('~~u', $Yc)) $m = lang(261);
      else {
        save_settings(array("output" => $pa["output"], "format" => $_POST["separator"]), "adminer_import");
        $I = true;
        $kb = array_keys($o);
        preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~', $Yc, $Re);
        $qa = count($Re[0]);
        driver()->begin();
        $Ah = ($_POST["separator"] == "csv" ? "," : ($_POST["separator"] == "tsv" ? "\t" : ";"));
        $L = array();
        foreach ($Re[0] as $y => $X) {
          preg_match_all("~((?>\"[^\"]*\")+|[^$Ah]*)$Ah~", $X . $Ah, $Se);
          if (!$y && !array_diff($Se[1], $kb)) {
            $kb = $Se[1];
            $qa--;
          } else {
            $O = array();
            foreach ($Se[1] as $t => $hb) $O[idf_escape($kb[$t])] = ($hb == "" && $o[$kb[$t]]["null"] ? "NULL" : q(preg_match('~^".*"$~s', $hb) ? str_replace('""', '"', substr($hb, 1, -1)) : $hb));
            $L[] = $O;
          }
        }
        $I = (!$L || driver()->insertUpdate($a, $L, $G));
        if ($I) driver()->commit();
        queries_redirect(remove_from_uri("page"), lang(262, $qa), $I);
        driver()->rollback();
      }
    }
  }
  $ji = adminer()->tableName($S);
  if (is_ajax()) {
    page_headers();
    ob_start();
  } else
    page_header(lang(52) . ": $ji", $m);
  $O = null;
  if (isset($ih["insert"]) || !support("table")) {
    $jg = array();
    foreach ((array)$_GET["where"] as $X) {
      if (isset($kd[$X["col"]]) && count($kd[$X["col"]]) == 1 && ($X["op"] == "=" || (!$X["op"] && (is_array($X["val"]) || !preg_match('~[_%]~', $X["val"]))))) $jg["set" . "[" . bracket_escape($X["col"]) . "]"] = $X["val"];
    }
    $O = $jg ? "&" . http_build_query($jg) : "";
  }
  adminer()->selectLinks($S, $O);
  if (!$e && support("table")) echo "<p class='error'>" . lang(263) . ($o ? "." : ": " . error()) . "\n";
  else {
    echo "<form action='' id='form'>\n", "<div style='display: none;'>";
    hidden_fields_get();
    echo (DB != "" ? input_hidden("db", DB) . (isset($_GET["ns"]) ? input_hidden("ns", $_GET["ns"]) : "") : ""), input_hidden("select", $a), "</div>\n";
    adminer()->selectColumnsPrint($M, $e);
    adminer()->selectSearchPrint($Z, $vh, $x);
    adminer()->selectOrderPrint($Rf, $Sf, $x);
    adminer()->selectLimitPrint($_);
    adminer()->selectLengthPrint($yi);
    adminer()->selectActionPrint($x);
    echo "</form>\n";
    $E = $_GET["page"];
    $nd = null;
    if ($E == "last") {
      $nd = get_val(count_rows($a, $Z, $me, $vd));
      $E = floor(max(0, intval($nd) - 1) / $_);
    }
    $wh = $M;
    $wd = $vd;
    if (!$wh) {
      $wh[] = "*";
      $Ab = convert_fields($e, $o, $M);
      if ($Ab) $wh[] = substr($Ab, 2);
    }
    foreach (
      $M
      as $y => $X
    ) {
      $n = $o[idf_unescape($X)];
      if ($n && ($ya = convert_field($n))) $wh[$y] = "$ya AS $X";
    }
    if (!$me && $dj) {
      foreach (
        $dj
        as $y => $X
      ) {
        $wh[] = idf_escape($y);
        if ($wd) $wd[] = idf_escape($y);
      }
    }
    $I = driver()->select($a, $wh, $Z, $wd, $Rf, $_, $E, true);
    if (!$I) echo "<p class='error'>" . error() . "\n";
    else {
      if (JUSH == "mssql" && $E) $I->seek($_ * $E);
      $vc = array();
      echo "<form action='' method='post' enctype='multipart/form-data'>\n";
      $L = array();
      while ($K = $I->fetch_assoc()) {
        if ($E && JUSH == "oracle") unset($K["RNUM"]);
        $L[] = $K;
      }
      if ($_GET["page"] != "last" && $_ && $vd && $me && JUSH == "sql") $nd = get_val(" SELECT FOUND_ROWS()");
      if (!$L) echo "<p class='message'>" . lang(12) . "\n";
      else {
        $Ga = adminer()->backwardKeys($a, $ji);
        echo "<div class='scrollable'>", "<table id='table' class='nowrap checkable odds'>", script("mixin(qs('#table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true), onkeydown: editingKeydown});"), "<thead><tr>" . (!$vd && $M ? "" : "<td><input type='checkbox' id='all-page' class='jsonly'>" . script("qs('#all-page').onclick = partial(formCheck, /check/);", "") . " <a href='" . h($_GET["modify"] ? remove_from_uri("modify") : $_SERVER["REQUEST_URI"] . "&modify=1") . "'>" . lang(264) . "</a>");
        $pf = array();
        $qd = array();
        reset($M);
        $Sg = 1;
        foreach ($L[0] as $y => $X) {
          if (!isset($dj[$y])) {
            $X = idx($_GET["columns"], key($M)) ?: array();
            $n = $o[$M ? ($X ? $X["col"] : current($M)) : $y];
            $C = ($n ? adminer()->fieldName($n, $Sg) : ($X["fun"] ? "*" : h($y)));
            if ($C != "") {
              $Sg++;
              $pf[$y] = $C;
              $d = idf_escape($y);
              $Md = remove_from_uri('(order|desc)[^=]*|page') . '&order%5B0%5D=' . urlencode($y);
              $Xb = "&desc%5B0%5D=1";
              echo "<th id='th[" . h(bracket_escape($y)) . "]'>" . script("mixin(qsl('th'), {onmouseover: partial(columnMouse), onmouseout: partial(columnMouse, ' hidden')});", "");
              $pd = apply_sql_function($X["fun"], $C);
              $Nh = isset($n["privileges"]["order"]) || $pd;
              echo ($Nh ? '<a href="' . h($Md . ($Rf[0] == $d || $Rf[0] == $y || (!$Rf && $me && $vd[0] == $d) ? $Xb : '')) . '">' . "$pd</a>" : $pd), "<span class='column hidden'>";
              if ($Nh) echo "<a href='" . h($Md . $Xb) . "' title='" . lang(58) . "' class='text'> â</a>";
              if (!$X["fun"] && isset($n["privileges"]["where"])) echo '<a href="#fieldset-search" title="' . lang(55) . '" class="text jsonly"> =</a>', script("qsl('a').onclick = partial(selectSearch, '" . js_escape($y) . "');");
              echo "</span>";
            }
            $qd[$y] = $X["fun"];
            next($M);
          }
        }
        $Ge = array();
        if ($_GET["modify"]) {
          foreach (
            $L
            as $K
          ) {
            foreach (
              $K
              as $y => $X
            ) $Ge[$y] = max($Ge[$y], min(40, strlen(utf8_decode($X))));
          }
        }
        echo ($Ga ? "<th>" . lang(265) : "") . "</thead>\n";
        if (is_ajax()) ob_end_clean();
        foreach (adminer()->rowDescriptions($L, $kd) as $nf => $K) {
          $aj = unique_array($L[$nf], $x);
          if (!$aj) {
            $aj = array();
            foreach ($L[$nf] as $y => $X) {
              if (!preg_match('~^(COUNT\((\*|(DISTINCT )?`(?:[^`]|``)+`)\)|(AVG|GROUP_CONCAT|MAX|MIN|SUM)\(`(?:[^`]|``)+`\))$~', $y)) $aj[$y] = $X;
            }
          }
          $bj = "";
          foreach (
            $aj
            as $y => $X
          ) {
            $n = (array)$o[$y];
            if ((JUSH == "sql" || JUSH == "pgsql") && preg_match('~char|text|enum|set~', $n["type"]) && strlen($X) > 64) {
              $y = (strpos($y, '(') ? $y : idf_escape($y));
              $y = "MD5(" . (JUSH != 'sql' || preg_match("~^utf8~", $n["collation"]) ? $y : "CONVERT($y USING " . charset(connection()) . ")") . ")";
              $X = md5($X);
            }
            $bj
              .= "&" . ($X !== null ? urlencode("where[" . bracket_escape($y) . "]") . "=" . urlencode($X === false ? "f" : $X) : "null%5B%5D=" . urlencode($y));
          }
          echo "<tr>" . (!$vd && $M ? "" : "<td>" . checkbox("check[]", substr($bj, 1), in_array(substr($bj, 1), (array)$_POST["check"])) . ($me || information_schema(DB) ? "" : " <a href='" . h(ME . "edit=" . urlencode($a) . $bj) . "' class='edit'>" . lang(266) . "</a>"));
          foreach (
            $K
            as $y => $X
          ) {
            if (isset($pf[$y])) {
              $n = (array)$o[$y];
              $X = driver()->value($X, $n);
              if ($X != "" && (!isset($vc[$y]) || $vc[$y] != "")) $vc[$y] = (is_mail($X) ? $pf[$y] : "");
              $A = "";
              if (preg_match('~blob|bytea|raw|file~', $n["type"]) && $X != "") $A = ME . 'download=' . urlencode($a) . '&field=' . urlencode($y) . $bj;
              if (!$A && $X !== null) {
                foreach ((array)$kd[$y] as $q) {
                  if (count($kd[$y]) == 1 || end($q["source"]) == $y) {
                    $A = "";
                    foreach ($q["source"] as $t => $Oh) $A
                      .= where_link($t, $q["target"][$t], $L[$nf][$Oh]);
                    $A = ($q["db"] != "" ? preg_replace('~([?&]db=)[^&]+~', '\1' . urlencode($q["db"]), ME) : ME) . 'select=' . urlencode($q["table"]) . $A;
                    if ($q["ns"]) $A = preg_replace('~([?&]ns=)[^&]+~', '\1' . urlencode($q["ns"]), $A);
                    if (count($q["source"]) == 1) break;
                  }
                }
              }
              if ($y == "COUNT(*)") {
                $A = ME . "select=" . urlencode($a);
                $t = 0;
                foreach ((array)$_GET["where"] as $W) {
                  if (!array_key_exists($W["col"], $aj)) $A
                    .= where_link($t++, $W["col"], $W["val"], $W["op"]);
                }
                foreach (
                  $aj
                  as $se => $W
                ) $A
                  .= where_link($t++, $se, $W);
              }
              $Nd = select_value($X, $A, $n, $yi);
              $u = h("val[$bj][" . bracket_escape($y) . "]");
              $Cg = idx(idx($_POST["val"], $bj), bracket_escape($y));
              $qc = !is_array($K[$y]) && is_utf8($Nd) && $L[$nf][$y] == $K[$y] && !$qd[$y] && !$n["generated"];
              $wi = preg_match('~text|json|lob~', $n["type"]);
              echo "<td id='$u'" . (preg_match(number_type(), $n["type"]) && ($X === null || is_numeric(strip_tags($Nd))) ? " class='number'" : "");
              if (($_GET["modify"] && $qc && $X !== null) || $Cg !== null) {
                $_d = h($Cg !== null ? $Cg : $K[$y]);
                echo ">" . ($wi ? "<textarea name='$u' cols='30' rows='" . (substr_count($K[$y], "\n") + 1) . "'>$_d</textarea>" : "<input name='$u' value='$_d' size='$Ge[$y]'>");
              } else {
                $Me = strpos($Nd, "<i>â¦</i>");
                echo " data-text='" . ($Me ? 2 : ($wi ? 1 : 0)) . "'" . ($qc ? "" : " data-warning='" . h(lang(267)) . "'") . ">$Nd";
              }
            }
          }
          if ($Ga) echo "<td>";
          adminer()->backwardKeysPrint($Ga, $L[$nf]);
          echo "</tr>\n";
        }
        if (is_ajax()) exit;
        echo "</table>\n", "</div>\n";
      }
      if (!is_ajax()) {
        if ($L || $E) {
          $Ic = true;
          if ($_GET["page"] != "last") {
            if (!$_ || (count($L) < $_ && ($L || !$E))) $nd = ($E ? $E * $_ : 0) + count($L);
            elseif (JUSH != "sql" || !$me) {
              $nd = ($me ? false : found_rows($S, $Z));
              if (intval($nd) < max(1e4, 2 * ($E + 1) * $_)) $nd = first(slow_query(count_rows($a, $Z, $me, $vd)));
              else $Ic = false;
            }
          }
          $hg = ($_ && ($nd === false || $nd > $_ || $E));
          if ($hg) echo (($nd === false ? count($L) + 1 : $nd - $E * $_) > $_ ? '<p><a href="' . h(remove_from_uri("page") . "&page=" . ($E + 1)) . '" class="loadmore">' . lang(268) . '</a>' . script("qsl('a').onclick = partial(selectLoadMore, $_, '" . lang(269) . "â¦');", "") : ''), "\n";
          echo "<div class='footer'><div>\n";
          if ($hg) {
            $Ue = ($nd === false ? $E + (count($L) >= $_ ? 2 : 1) : floor(($nd - 1) / $_));
            echo "<fieldset>";
            if (JUSH != "simpledb") {
              echo "<legend><a href='" . h(remove_from_uri("page")) . "'>" . lang(270) . "</a></legend>", script("qsl('a').onclick = function () { pageClick(this.href, +prompt('" . lang(270) . "', '" . ($E + 1) . "')); return false; };"), pagination(0, $E) . ($E > 5 ? " â¦" : "");
              for ($t = max(1, $E - 4); $t < min($Ue, $E + 5); $t++) echo
              pagination($t, $E);
              if ($Ue > 0) echo ($E + 5 < $Ue ? " â¦" : ""), ($Ic && $nd !== false ? pagination($Ue, $E) : " <a href='" . h(remove_from_uri("page") . "&page=last") . "' title='~$Ue'>" . lang(271) . "</a>");
            } else
              echo "<legend>" . lang(270) . "</legend>", pagination(0, $E) . ($E > 1 ? " â¦" : ""), ($E ? pagination($E, $E) : ""), ($Ue > $E ? pagination($E + 1, $E) . ($Ue > $E + 1 ? " â¦" : "") : "");
            echo "</fieldset>\n";
          }
          echo "<fieldset>", "<legend>" . lang(272) . "</legend>";
          $ec = ($Ic ? "" : "~ ") . $nd;
          $Kf = "const checked = formChecked(this, /check/); selectCount('selected', this.checked ? '$ec' : checked); selectCount('selected2', this.checked || !checked ? '$ec' : checked);";
          echo
          checkbox("all", 1, 0, ($nd !== false ? ($Ic ? "" : "~ ") . lang(155, $nd) : ""), $Kf) . "\n", "</fieldset>\n";
          if (adminer()->selectCommandPrint()) echo '<fieldset', ($_GET["modify"] ? '' : ' class="jsonly"'), '><legend>', lang(264), '</legend><div>
<input type="submit" value="', lang(14), '"', ($_GET["modify"] ? '' : ' title="' . lang(260) . '"'), '>
</div></fieldset>
<fieldset><legend>', lang(127), ' <span id="selected"></span></legend><div>
<input type="submit" name="edit" value="', lang(10), '">
<input type="submit" name="clone" value="', lang(256), '">
<input type="submit" name="delete" value="', lang(18), '">', confirm(), '</div></fieldset>
';
          $ld = adminer()->dumpFormat();
          foreach ((array)$_GET["columns"] as $d) {
            if ($d["fun"]) {
              unset($ld['sql']);
              break;
            }
          }
          if ($ld) {
            print_fieldset("export", lang(72) . " <span id='selected2'></span>");
            $eg = adminer()->dumpOutput();
            echo ($eg ? html_select("output", $eg, $pa["output"]) . " " : ""), html_select("format", $ld, $pa["format"]), " <input type='submit' name='export' value='" . lang(72) . "'>\n", "</div></fieldset>\n";
          }
          adminer()->selectEmailPrint(array_filter($vc, 'strlen'), $e);
          echo "</div></div>\n";
        }
        if (adminer()->selectImportPrint()) echo "<p>", "<a href='#import'>" . lang(71) . "</a>", script("qsl('a').onclick = partial(toggle, 'import');", ""), "<span id='import'" . ($_POST["import"] ? "" : " class='hidden'") . ">: ", "<input type='file' name='csv_file'> ", html_select("separator", array("csv" => "CSV,", "csv;" => "CSV;", "tsv" => "TSV"), $pa["format"]), " <input type='submit' name='import' value='" . lang(71) . "'>", "</span>";
        echo
        input_token(), "</form>\n", (!$vd && $M ? "" : script("tableCheck();"));
      }
    }
  }
  if (is_ajax()) {
    ob_end_clean();
    exit;
  }
} elseif (isset($_GET["variables"])) {
  $P = isset($_GET["status"]);
  page_header($P ? lang(119) : lang(118));
  $rj = ($P ? show_status() : show_variables());
  if (!$rj) echo "<p class='message'>" . lang(12) . "\n";
  else {
    echo "<table>\n";
    foreach (
      $rj
      as $K
    ) {
      echo "<tr>";
      $y = array_shift($K);
      echo "<th><code class='jush-" . JUSH . ($P ? "status" : "set") . "'>" . h($y) . "</code>";
      foreach (
        $K
        as $X
      ) echo "<td>" . nl_br(h($X));
    }
    echo "</table>\n";
  }
} elseif (isset($_GET["script"])) {
  header("Content-Type: text/javascript; charset=utf-8");
  if ($_GET["script"] == "db") {
    $fi = array("Data_length" => 0, "Index_length" => 0, "Data_free" => 0);
    foreach (table_status() as $C => $S) {
      json_row("Comment-$C", h($S["Comment"]));
      if (!is_view($S)) {
        foreach (array("Engine", "Collation") as $y) json_row("$y-$C", h($S[$y]));
        foreach ($fi + array("Auto_increment" => 0, "Rows" => 0) as $y => $X) {
          if ($S[$y] != "") {
            $X = format_number($S[$y]);
            if ($X >= 0) json_row("$y-$C", ($y == "Rows" && $X && $S["Engine"] == (JUSH == "pgsql" ? "table" : "InnoDB") ? "~ $X" : $X));
            if (isset($fi[$y])) $fi[$y] += ($S["Engine"] != "InnoDB" || $y != "Data_free" ? $S[$y] : 0);
          } elseif (array_key_exists($y, $S)) json_row("$y-$C", "?");
        }
      }
    }
    foreach (
      $fi
      as $y => $X
    ) json_row("sum-$y", format_number($X));
    json_row("");
  } elseif ($_GET["script"] == "kill") connection()->query("KILL " . number($_POST["kill"]));
  else {
    foreach (count_tables(adminer()->databases()) as $k => $X) {
      json_row("tables-$k", $X);
      json_row("size-$k", db_size($k));
    }
    json_row("");
  }
  exit;
} else {
  $qi = array_merge((array)$_POST["tables"], (array)$_POST["views"]);
  if ($qi && !$m && !$_POST["search"]) {
    $I = true;
    $bf = "";
    if (JUSH == "sql" && $_POST["tables"] && count($_POST["tables"]) > 1 && ($_POST["drop"] || $_POST["truncate"] || $_POST["copy"])) queries("SET foreign_key_checks = 0");
    if ($_POST["truncate"]) {
      if ($_POST["tables"]) $I = truncate_tables($_POST["tables"]);
      $bf = lang(273);
    } elseif ($_POST["move"]) {
      $I = move_tables((array)$_POST["tables"], (array)$_POST["views"], $_POST["target"]);
      $bf = lang(274);
    } elseif ($_POST["copy"]) {
      $I = copy_tables((array)$_POST["tables"], (array)$_POST["views"], $_POST["target"]);
      $bf = lang(275);
    } elseif ($_POST["drop"]) {
      if ($_POST["views"]) $I = drop_views($_POST["views"]);
      if ($I && $_POST["tables"]) $I = drop_tables($_POST["tables"]);
      $bf = lang(276);
    } elseif (JUSH == "sqlite" && $_POST["check"]) {
      foreach ((array)$_POST["tables"] as $R) {
        foreach (get_rows("PRAGMA integrity_check(" . q($R) . ")") as $K) $bf
          .= "<b>" . h($R) . "</b>: " . h($K["integrity_check"]) . "<br>";
      }
    } elseif (JUSH != "sql") {
      $I = (JUSH == "sqlite" ? queries("VACUUM") : apply_queries("VACUUM" . ($_POST["optimize"] ? "" : " ANALYZE"), $_POST["tables"]));
      $bf = lang(277);
    } elseif (!$_POST["tables"]) $bf = lang(9);
    elseif ($I = queries(($_POST["optimize"] ? "OPTIMIZE" : ($_POST["check"] ? "CHECK" : ($_POST["repair"] ? "REPAIR" : "ANALYZE"))) . " TABLE " . implode(", ", array_map('Adminer\idf_escape', $_POST["tables"])))) {
      while ($K = $I->fetch_assoc()) $bf
        .= "<b>" . h($K["Table"]) . "</b>: " . h($K["Msg_text"]) . "<br>";
    }
    queries_redirect(substr(ME, 0, -1), $bf, $I);
  }
  page_header(($_GET["ns"] == "" ? lang(36) . ": " . h(DB) : lang(75) . ": " . h($_GET["ns"])), $m, true);
  if (adminer()->homepage()) {
    if ($_GET["ns"] !== "") {
      echo "<h3 id='tables-views'>" . lang(278) . "</h3>\n";
      $pi = tables_list();
      if (!$pi) echo "<p class='message'>" . lang(9) . "\n";
      else {
        echo "<form action='' method='post'>\n";
        if (support("table")) {
          echo "<fieldset><legend>" . lang(279) . " <span id='selected2'></span></legend><div>", "<input type='search' name='query' value='" . h($_POST["query"]) . "'>", script("qsl('input').onkeydown = partialArg(bodyKeydown, 'search');", ""), " <input type='submit' name='search' value='" . lang(55) . "'>\n", "</div></fieldset>\n";
          if ($_POST["search"] && $_POST["query"] != "") {
            $_GET["where"][0]["op"] = driver()->convertOperator("LIKE %%");
            search_tables();
          }
        }
        echo "<div class='scrollable'>\n", "<table class='nowrap checkable odds'>\n", script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"), '<thead><tr class="wrap">', '<td><input id="check-all" type="checkbox" class="jsonly">' . script("qs('#check-all').onclick = partial(formCheck, /^(tables|views)\[/);", ""), '<th>' . lang(134), '<td>' . lang(280) . doc_link(array('sql' => 'storage-engines.html')), '<td>' . lang(123) . doc_link(array('sql' => 'charset-charsets.html', 'mariadb' => 'supported-character-sets-and-collations/')), '<td>' . lang(281) . doc_link(array('sql' => 'show-table-status.html', 'pgsql' => 'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT', 'oracle' => 'REFRN20286')), '<td>' . lang(282) . doc_link(array('sql' => 'show-table-status.html', 'pgsql' => 'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT')), '<td>' . lang(283) . doc_link(array('sql' => 'show-table-status.html')), '<td>' . lang(50) . doc_link(array('sql' => 'example-auto-increment.html', 'mariadb' => 'auto_increment/')), '<td>' . lang(284) . doc_link(array('sql' => 'show-table-status.html', 'pgsql' => 'catalog-pg-class.html#CATALOG-PG-CLASS', 'oracle' => 'REFRN20286')), (support("comment") ? '<td>' . lang(49) . doc_link(array('sql' => 'show-table-status.html', 'pgsql' => 'functions-info.html#FUNCTIONS-INFO-COMMENT-TABLE')) : ''), "</thead>\n";
        $T = 0;
        foreach (
          $pi
          as $C => $U
        ) {
          $uj = ($U !== null && !preg_match('~table|sequence~i', $U));
          $u = h("Table-" . $C);
          echo '<tr><td>' . checkbox(($uj ? "views[]" : "tables[]"), $C, in_array("$C", $qi, true), "", "", "", $u), '<th>' . (support("table") || support("indexes") ? "<a href='" . h(ME) . "table=" . urlencode($C) . "' title='" . lang(41) . "' id='$u'>" . h($C) . '</a>' : h($C));
          if ($uj) echo '<td colspan="6"><a href="' . h(ME) . "view=" . urlencode($C) . '" title="' . lang(42) . '">' . (preg_match('~materialized~i', $U) ? lang(132) : lang(133)) . '</a>', '<td align="right"><a href="' . h(ME) . "select=" . urlencode($C) . '" title="' . lang(40) . '">?</a>';
          else {
            foreach (array("Engine" => array(), "Collation" => array(), "Data_length" => array("create", lang(43)), "Index_length" => array("indexes", lang(136)), "Data_free" => array("edit", lang(44)), "Auto_increment" => array("auto_increment=1&create", lang(43)), "Rows" => array("select", lang(40)),) as $y => $A) {
              $u = " id='$y-" . h($C) . "'";
              echo ($A ? "<td align='right'>" . (support("table") || $y == "Rows" || (support("indexes") && $y != "Data_length") ? "<a href='" . h(ME . "$A[0]=") . urlencode($C) . "'$u title='$A[1]'>?</a>" : "<span$u>?</span>") : "<td id='$y-" . h($C) . "'>");
            }
            $T++;
          }
          echo (support("comment") ? "<td id='Comment-" . h($C) . "'>" : ""), "\n";
        }
        echo "<tr><td><th>" . lang(257, count($pi)), "<td>" . h(JUSH == "sql" ? get_val("SELECT @@default_storage_engine") : ""), "<td>" . h(db_collation(DB, collations()));
        foreach (array("Data_length", "Index_length", "Data_free") as $y) echo "<td align='right' id='sum-$y'>";
        echo "\n", "</table>\n", "</div>\n";
        if (!information_schema(DB)) {
          echo "<div class='footer'><div>\n";
          $oj = "<input type='submit' value='" . lang(285) . "'> " . on_help("'VACUUM'");
          $Nf = "<input type='submit' name='optimize' value='" . lang(286) . "'> " . on_help(JUSH == "sql" ? "'OPTIMIZE TABLE'" : "'VACUUM OPTIMIZE'");
          echo "<fieldset><legend>" . lang(127) . " <span id='selected'></span></legend><div>" . (JUSH == "sqlite" ? $oj . "<input type='submit' name='check' value='" . lang(287) . "'> " . on_help("'PRAGMA integrity_check'") : (JUSH == "pgsql" ? $oj . $Nf : (JUSH == "sql" ? "<input type='submit' value='" . lang(288) . "'> " . on_help("'ANALYZE TABLE'") . $Nf . "<input type='submit' name='check' value='" . lang(287) . "'> " . on_help("'CHECK TABLE'") . "<input type='submit' name='repair' value='" . lang(289) . "'> " . on_help("'REPAIR TABLE'") : ""))) . "<input type='submit' name='truncate' value='" . lang(290) . "'> " . on_help(JUSH == "sqlite" ? "'DELETE'" : "'TRUNCATE" . (JUSH == "pgsql" ? "'" : " TABLE'")) . confirm() . "<input type='submit' name='drop' value='" . lang(128) . "'>" . on_help("'DROP TABLE'") . confirm() . "\n";
          $j = (support("scheme") ? adminer()->schemas() : adminer()->databases());
          if (count($j) != 1 && JUSH != "sqlite") {
            $k = (isset($_POST["target"]) ? $_POST["target"] : (support("scheme") ? $_GET["ns"] : DB));
            echo "<p><label>" . lang(291) . ": ", ($j ? html_select("target", $j, $k) : '<input name="target" value="' . h($k) . '" autocapitalize="off">'), "</label> <input type='submit' name='move' value='" . lang(292) . "'>", (support("copy") ? " <input type='submit' name='copy' value='" . lang(293) . "'> " . checkbox("overwrite", 1, $_POST["overwrite"], lang(294)) : ""), "\n";
          }
          echo "<input type='hidden' name='all' value=''>", script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^(tables|views)\[/));" . (support("table") ? " selectCount('selected2', formChecked(this, /^tables\[/) || $T);" : "") . " }"), input_token(), "</div></fieldset>\n", "</div></div>\n";
        }
        echo "</form>\n", script("tableCheck();");
      }
      echo "<p class='links'><a href='" . h(ME) . "create='>" . lang(73) . "</a>\n", (support("view") ? "<a href='" . h(ME) . "view='>" . lang(210) . "</a>\n" : "");
      if (support("routine")) {
        echo "<h3 id='routines'>" . lang(148) . "</h3>\n";
        $mh = routines();
        if ($mh) {
          echo "<table class='odds'>\n", '<thead><tr><th>' . lang(189) . '<td>' . lang(48) . '<td>' . lang(227) . "<td></thead>\n";
          foreach (
            $mh
            as $K
          ) {
            $C = ($K["SPECIFIC_NAME"] == $K["ROUTINE_NAME"] ? "" : "&name=" . urlencode($K["ROUTINE_NAME"]));
            echo '<tr>', '<th><a href="' . h(ME . ($K["ROUTINE_TYPE"] != "PROCEDURE" ? 'callf=' : 'call=') . urlencode($K["SPECIFIC_NAME"]) . $C) . '">' . h($K["ROUTINE_NAME"]) . '</a>', '<td>' . h($K["ROUTINE_TYPE"]), '<td>' . h($K["DTD_IDENTIFIER"]), '<td><a href="' . h(ME . ($K["ROUTINE_TYPE"] != "PROCEDURE" ? 'function=' : 'procedure=') . urlencode($K["SPECIFIC_NAME"]) . $C) . '">' . lang(139) . "</a>";
          }
          echo "</table>\n";
        }
        echo '<p class="links">' . (support("procedure") ? '<a href="' . h(ME) . 'procedure=">' . lang(226) . '</a>' : '') . '<a href="' . h(ME) . 'function=">' . lang(225) . "</a>\n";
      }
      if (support("sequence")) {
        echo "<h3 id='sequences'>" . lang(295) . "</h3>\n";
        $Dh = get_vals("SELECT sequence_name FROM information_schema.sequences WHERE sequence_schema = current_schema() ORDER BY sequence_name");
        if ($Dh) {
          echo "<table class='odds'>\n", "<thead><tr><th>" . lang(189) . "</thead>\n";
          foreach (
            $Dh
            as $X
          ) echo "<tr><th><a href='" . h(ME) . "sequence=" . urlencode($X) . "'>" . h($X) . "</a>\n";
          echo "</table>\n";
        }
        echo "<p class='links'><a href='" . h(ME) . "sequence='>" . lang(232) . "</a>\n";
      }
      if (support("type")) {
        echo "<h3 id='user-types'>" . lang(31) . "</h3>\n";
        $mj = types();
        if ($mj) {
          echo "<table class='odds'>\n", "<thead><tr><th>" . lang(189) . "</thead>\n";
          foreach (
            $mj
            as $X
          ) echo "<tr><th><a href='" . h(ME) . "type=" . urlencode($X) . "'>" . h($X) . "</a>\n";
          echo "</table>\n";
        }
        echo "<p class='links'><a href='" . h(ME) . "type='>" . lang(236) . "</a>\n";
      }
      if (support("event")) {
        echo "<h3 id='events'>" . lang(149) . "</h3>\n";
        $L = get_rows("SHOW EVENTS");
        if ($L) {
          echo "<table>\n", "<thead><tr><th>" . lang(189) . "<td>" . lang(296) . "<td>" . lang(216) . "<td>" . lang(217) . "<td></thead>\n";
          foreach (
            $L
            as $K
          ) echo "<tr>", "<th>" . h($K["Name"]), "<td>" . ($K["Execute at"] ? lang(297) . "<td>" . $K["Execute at"] : lang(218) . " " . $K["Interval value"] . " " . $K["Interval field"] . "<td>$K[Starts]"), "<td>$K[Ends]", '<td><a href="' . h(ME) . 'event=' . urlencode($K["Name"]) . '">' . lang(139) . '</a>';
          echo "</table>\n";
          $Gc = get_val("SELECT @@event_scheduler");
          if ($Gc && $Gc != "ON") echo "<p class='error'><code class='jush-sqlset'>event_scheduler</code>: " . h($Gc) . "\n";
        }
        echo '<p class="links"><a href="' . h(ME) . 'event=">' . lang(215) . "</a>\n";
      }
      if ($pi) echo
      script("ajaxSetHtml('" . js_escape(ME) . "script=db');");
    }
  }
}
page_footer();
