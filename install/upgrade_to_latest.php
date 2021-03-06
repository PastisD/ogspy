<?php
/**
 * OGSpy installation : Script Upgrade
 * @package OGSpy
 * @subpackage install
 * @author Kyser
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.04
 */

define("IN_SPYOGAME", true);
define("UPGRADE_IN_PROGRESS", true);

require_once("../common.php");

if (!isset($pub_verbose)) {
    $pub_verbose = true;
}


if ($pub_verbose == true) {
?>

<html>
<head>
<title>Mise à jour OGSpy</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="language" content="fr" />
<link rel="stylesheet" type="text/css" href="../skin/OGSpy_skin/formate.css" />
</head>
<body>

<?php
}

// on réinitialise la sequense config
// evite d utiliser le cache ( qui sera périmé ))
$request = "select * from " . TABLE_CONFIG;
$result = $db->sql_query($request);
    while (list($name, $value) = $db->sql_fetch_row($result)) {
        $server_config[$name] = stripslashes($value);
    }


$request = "SELECT config_value FROM " . TABLE_CONFIG . " WHERE config_name = 'version'";
$result = $db->sql_query($request);
list($ogsversion) = $db->sql_fetch_row($result);

$requests = array();
$up_to_date = false;
switch ($ogsversion) {
    case '3.1.0':
        $requests[] = "UPDATE " . TABLE_CONFIG . " SET config_value = '3.1.1' WHERE config_name = 'version'";
        // MODIF TABLE_USER
        $requests[] = "ALTER TABLE `" . TABLE_USER . "` ADD `xtense_type` enum('FF','GM-FF','GM-GC','GM-OP') AFTER `rank_added_ogs`"; // Type de barre utilisée par le user
        $requests[] = "ALTER TABLE `" . TABLE_USER . "` ADD `xtense_version` VARCHAR(10) AFTER `xtense_type`"; // Type de barre utilisée par le user

        // MODIF TABLE_RANK_PLAYER_MILITARY
        $requests[] = "ALTER TABLE `" . TABLE_RANK_PLAYER_MILITARY . "` ADD `nb_spacecraft` int(11) NOT NULL default '0' AFTER `sender_id`"; // Ajout nombre de vaisseaux au classement militaire joueur

        // SUPPRESSIONS ANCIENS CLASSEMENTS : TABLE_RANK_PLAYER_FLEET, TABLE_RANK_PLAYER_RESEARCH, TABLE_RANK_ALLY_FLEET & TABLE_RANK_ALLY_RESEARCH
        $requests[] = "DROP TABLE `" . TABLE_RANK_PLAYER_FLEET . "`"; // ancien classement flotte
        $requests[] = "DROP TABLE `" . TABLE_RANK_PLAYER_RESEARCH . "`"; // ancien classement recherche
        $requests[] = "DROP TABLE `" . TABLE_RANK_ALLY_FLEET . "`"; // ancien classement flotte
        $requests[] = "DROP TABLE `" . TABLE_RANK_ALLY_RESEARCH . "`"; // ancien classement recherche
        $requests[] = "DROP TABLE `" . TABLE_SPY . "`"; // ancienne table des RE
        $requests[] = "DROP TABLE `" . TABLE_UNIVERSE_TEMPORARY . "`"; // ancienne table temporaire univers

        $ogsversion = '3.1.1';
        $up_to_date = true;
        //Pas de break pour faire toutes les mises à jour d'un coup !
    case '3.1.1':
        $requests[] = "UPDATE " . TABLE_CONFIG . " SET config_value = '3.1.2' WHERE config_name = 'version'";
        $ogsversion = '3.1.2';
        $up_to_date = true;
        //Pas de break pour faire toutes les mises à jour d'un coup !
    case '3.1.2':
        $requests[] = "ALTER TABLE `" . TABLE_USER_BUILDING . "` MODIFY `coordinates` VARCHAR(10)";
        $requests[] = "ALTER TABLE `" . TABLE_UNIVERSE . "` MODIFY `phalanx` tinyint(1) NOT NULL default '0'";
        $requests[] = "ALTER TABLE `" . TABLE_USER . "` MODIFY `xtense_type` enum('FF','GM-FF','GM-GC','GM-OP','ANDROID')";
        $requests[] = "ALTER TABLE `" . TABLE_USER . "` ADD `user_email` VARCHAR(50) NOT NULL default '' AFTER `user_password`";
        $requests[] = "ALTER TABLE `" . TABLE_USER . "` ADD `off_commandant` enum('0','1') NOT NULL default '0' AFTER `disable_ip_check`";
        $requests[] = "UPDATE " . TABLE_CONFIG . " SET config_value = '3.1.3' WHERE config_name = 'version'";
        $ogsversion = '3.1.3';
        $up_to_date = true;
    case '3.1.3':
        $requests[] = "CREATE TABLE IF NOT EXISTS `" . TABLE_GCM_USERS . "` ( " .
                        "`user_id` int(11) NOT NULL default '0'," .
                        "`gcm_regid` varchar(255) NOT NULL, " .
                        "`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, " .
                        "`version_android` varchar(50), " .
                        "`version_ogspy` varchar(50), " .
                        "`device` varchar(50), " .
                        "PRIMARY KEY (`gcm_regid`) " .
                        ") ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";

        //Passage des tables en UTF-8
        $requests[] = "ALTER TABLE " . TABLE_CONFIG . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_GROUP . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_SESSIONS . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_STATISTIC . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_UNIVERSE . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_USER . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_USER_BUILDING . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_USER_DEFENCE . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_USER_FAVORITE . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_USER_GROUP . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_USER_SPY . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_USER_TECHNOLOGY . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_MOD . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_MOD_CFG . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_PARSEDSPY . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_PARSEDRC . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_PARSEDRCROUND . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_ROUND_ATTACK . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_ROUND_DEFENSE . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_RANK_PLAYER_POINTS . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_RANK_PLAYER_ECO . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_RANK_PLAYER_TECHNOLOGY . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_RANK_PLAYER_MILITARY . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_RANK_PLAYER_MILITARY_BUILT . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_RANK_PLAYER_MILITARY_LOOSE . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_RANK_PLAYER_MILITARY_DESTRUCT . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_RANK_PLAYER_HONOR . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_RANK_ALLY_POINTS . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_RANK_ALLY_ECO . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_RANK_ALLY_TECHNOLOGY . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_RANK_ALLY_MILITARY . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_RANK_ALLY_MILITARY_BUILT . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_RANK_ALLY_MILITARY_LOOSE . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_RANK_ALLY_MILITARY_DESTRUCT . " CONVERT TO CHARACTER SET utf8";
        $requests[] = "ALTER TABLE " . TABLE_RANK_ALLY_HONOR . " CONVERT TO CHARACTER SET utf8";

        $requests[] = "ALTER TABLE " . TABLE_USER_BUILDING . " ADD `boosters` VARCHAR(64) NOT NULL default 'm:0:0_c:0:0_d:0:0_p:0_m:0' AFTER `fields`";
        $requests[] = "ALTER TABLE " . TABLE_PARSEDRC . " MODIFY `pertes_A` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_PARSEDRC . " MODIFY `pertes_D` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_PARSEDRC . " MODIFY `gain_M` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_PARSEDRC . " MODIFY `gain_C` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_PARSEDRC . " MODIFY `gain_D` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_PARSEDRC . " MODIFY `debris_M` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_PARSEDRC . " MODIFY `debris_C` BIGINT";

        $requests[] = "UPDATE " . TABLE_CONFIG . " SET config_value = '3.2.0' WHERE config_name = 'version'";
        $ogsversion = '3.2.0';
        $up_to_date = true;

    case '3.2.0':
        $requests[] = "UPDATE " . TABLE_CONFIG . " SET config_value = '3.3.0' WHERE config_name = 'version'";
        $requests[] = "ALTER TABLE `" . TABLE_USER_BUILDING . "` DROP `CM`";
        $requests[] = "ALTER TABLE `" . TABLE_USER_BUILDING . "` DROP `CC`";
        $requests[] = "ALTER TABLE `" . TABLE_USER_BUILDING . "` DROP `CD`";
        $requests[] = "ALTER TABLE `" . TABLE_PARSEDSPY . "` DROP `CM`";
        $requests[] = "ALTER TABLE `" . TABLE_PARSEDSPY . "` DROP `CC`";
        $requests[] = "ALTER TABLE `" . TABLE_PARSEDSPY . "` DROP `CD`";
        $ogsversion = '3.3.0';
        $up_to_date = true;

    case '3.3.0':
        $requests[] = "UPDATE " . TABLE_CONFIG . " SET config_value = '3.3.1' WHERE config_name = 'version'";
        $requests[] = "ALTER TABLE `" . TABLE_UNIVERSE . "` MODIFY `galaxy` smallint(2)";
        $requests[] = "ALTER TABLE `" . TABLE_USER . "` MODIFY `user_galaxy` smallint(2)";
        $requests[] = "ALTER TABLE `" . TABLE_USER_FAVORITE . "` MODIFY `galaxy` smallint(2)";
        $ogsversion = '3.3.1';
        $up_to_date = true;

    case '3.3.1':
        $requests[] = "UPDATE " . TABLE_CONFIG . " SET config_value = '3.3.2' WHERE config_name = 'version'";
        $requests[] = "ALTER TABLE `" . TABLE_USER . "` MODIFY `xtense_type` enum('FF','GM-FF','GM-GC','GM-OP','ANDROID')";
        $ogsversion = '3.3.2';
        $up_to_date = true;

    case '3.3.2':
        $requests[] = "UPDATE " . TABLE_CONFIG . " SET config_value = '3.3.3' WHERE config_name = 'version'";
        $ogsversion = '3.3.3';
        $up_to_date = true;

    case '3.3.3':
        $requests[] = "UPDATE " . TABLE_CONFIG . " SET config_value = '0' WHERE config_name = 'mail_active'";
        $requests[] = "UPDATE " . TABLE_CONFIG . " SET config_value = '0' WHERE config_name = 'mail_smtp_use'";
        $requests[] = "UPDATE " . TABLE_CONFIG . " SET config_value = '' WHERE config_name = 'mail_smtp_server'";
        $requests[] = "UPDATE " . TABLE_CONFIG . " SET config_value = '0' WHERE config_name = 'mail_smtp_secure'";
        $requests[] = "UPDATE " . TABLE_CONFIG . " SET config_value = '' WHERE config_name = 'mail_smtp_host'";
        $requests[] = "UPDATE " . TABLE_CONFIG . " SET config_value = '' WHERE config_name = 'mail_smtp_port'";
        $requests[] = "UPDATE " . TABLE_CONFIG . " SET config_value = '' WHERE config_name = 'mail_smtp_username'";
        $requests[] = "UPDATE " . TABLE_CONFIG . " SET config_value = '' WHERE config_name = 'mail_smtp_password'";


        $requests[] = "UPDATE " . TABLE_CONFIG . " SET config_value = '3.3.4' WHERE config_name = 'version'";
        $ogsversion = '3.3.4';
        $up_to_date = true;

    case '3.3.4':

        $requests[] = "ALTER TABLE " . TABLE_RANK_PLAYER_POINTS . " MODIFY `points` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_RANK_PLAYER_ECO . " MODIFY `points` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_RANK_PLAYER_TECHNOLOGY . " MODIFY `points` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_RANK_PLAYER_MILITARY . " MODIFY `points` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_RANK_PLAYER_MILITARY_BUILT . " MODIFY `points` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_RANK_PLAYER_MILITARY_LOOSE . " MODIFY `points` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_RANK_PLAYER_MILITARY_DESTRUCT . " MODIFY `points` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_RANK_PLAYER_HONOR . " MODIFY `points` BIGINT";

        $requests[] = "ALTER TABLE " . TABLE_RANK_ALLY_POINTS . " MODIFY `points` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_RANK_ALLY_POINTS . " MODIFY `points_per_member` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_RANK_ALLY_ECO . " MODIFY `points` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_RANK_ALLY_TECHNOLOGY . " MODIFY `points` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_RANK_ALLY_MILITARY . " MODIFY `points` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_RANK_ALLY_MILITARY_BUILT . " MODIFY `points` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_RANK_ALLY_MILITARY_LOOSE . " MODIFY `points` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_RANK_ALLY_MILITARY_DESTRUCT . " MODIFY `points` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_RANK_ALLY_HONOR . " MODIFY `points` BIGINT";

        $requests[] = "ALTER TABLE `" . TABLE_USER_BUILDING . "` ADD `Dock` SMALLINT(2) NOT NULL DEFAULT '-1' AFTER `Silo`";

        $requests[] = "ALTER TABLE `" . TABLE_MOD . "` MODIFY `version` VARCHAR(10)";

        $requests[] = "ALTER TABLE `" . TABLE_RANK_PLAYER_POINTS . "` ADD `player_id` INT(6) NOT NULL DEFAULT '-1' AFTER `player`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_PLAYER_POINTS . "` ADD `ally_id` INT(6) NOT NULL DEFAULT '-1' AFTER `ally`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_PLAYER_ECO . "` ADD `player_id` INT(6) NOT NULL DEFAULT '-1' AFTER `player`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_PLAYER_ECO . "` ADD `ally_id` INT(6) NOT NULL DEFAULT '-1' AFTER `ally`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_PLAYER_TECHNOLOGY . "` ADD `player_id` INT(6) NOT NULL DEFAULT '-1' AFTER `player`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_PLAYER_TECHNOLOGY . "` ADD `ally_id` INT(6) NOT NULL DEFAULT '-1' AFTER `ally`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_PLAYER_MILITARY . "` ADD `player_id` INT(6) NOT NULL DEFAULT '-1' AFTER `player`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_PLAYER_MILITARY . "` ADD `ally_id` INT(6) NOT NULL DEFAULT '-1' AFTER `ally`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_PLAYER_MILITARY_BUILT . "` ADD `player_id` INT(6) NOT NULL DEFAULT '-1' AFTER `player`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_PLAYER_MILITARY_BUILT . "` ADD `ally_id` INT(6) NOT NULL DEFAULT '-1' AFTER `ally`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_PLAYER_MILITARY_LOOSE . "` ADD `player_id` INT(6) NOT NULL DEFAULT '-1' AFTER `player`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_PLAYER_MILITARY_LOOSE . "` ADD `ally_id` INT(6) NOT NULL DEFAULT '-1' AFTER `ally`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_PLAYER_MILITARY_DESTRUCT . "` ADD `player_id` INT(6) NOT NULL DEFAULT '-1' AFTER `player`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_PLAYER_MILITARY_DESTRUCT . "` ADD `ally_id` INT(6) NOT NULL DEFAULT '-1' AFTER `ally`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_PLAYER_HONOR . "` ADD `player_id` INT(6) NOT NULL DEFAULT '-1' AFTER `player`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_PLAYER_HONOR . "` ADD `ally_id` INT(6) NOT NULL DEFAULT '-1' AFTER `ally`";

        $requests[] = "ALTER TABLE `" . TABLE_RANK_ALLY_POINTS . "` ADD `ally_id` INT(6) NOT NULL DEFAULT '-1' AFTER `ally`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_ALLY_ECO . "` ADD `ally_id` INT(6) NOT NULL DEFAULT '-1' AFTER `ally`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_ALLY_TECHNOLOGY . "` ADD `ally_id` INT(6) NOT NULL DEFAULT '-1' AFTER `ally`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_ALLY_MILITARY . "` ADD `ally_id` INT(6) NOT NULL DEFAULT '-1' AFTER `ally`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_ALLY_MILITARY_BUILT . "` ADD `ally_id` INT(6) NOT NULL DEFAULT '-1' AFTER `ally`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_ALLY_MILITARY_LOOSE . "` ADD `ally_id` INT(6) NOT NULL DEFAULT '-1' AFTER `ally`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_ALLY_MILITARY_DESTRUCT . "` ADD `ally_id` INT(6) NOT NULL DEFAULT '-1' AFTER `ally`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_ALLY_HONOR . "` ADD `ally_id` INT(6) NOT NULL DEFAULT '-1' AFTER `ally`";

        $requests[] = "ALTER TABLE `" . TABLE_RANK_ALLY_ECO . "` ADD `points_per_member` BIGINT AFTER `points`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_ALLY_TECHNOLOGY . "` ADD `points_per_member` BIGINT AFTER `points`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_ALLY_MILITARY . "` ADD `points_per_member` BIGINT AFTER `points`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_ALLY_MILITARY_BUILT . "` ADD `points_per_member` BIGINT AFTER `points`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_ALLY_MILITARY_LOOSE . "` ADD `points_per_member` BIGINT AFTER `points`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_ALLY_MILITARY_DESTRUCT . "` ADD `points_per_member` BIGINT AFTER `points`";
        $requests[] = "ALTER TABLE `" . TABLE_RANK_ALLY_HONOR . "` ADD `points_per_member` BIGINT AFTER `points`";

        if (!defined('TABLE_GAME_ALLY')) {
            define("TABLE_GAME_ALLY", $table_prefix . "game_ally");
        }
        $requests[] = "CREATE TABLE IF NOT EXISTS `" . TABLE_GAME_ALLY . "` ( " .
            "`ally_id` int(6) NOT NULL ," .
            "`ally` varchar(65) NOT NULL, " .
            "`tag` varchar(65)  NOT NULL default '', ".
            "`number_member` int(3) NOT NULL ," .
            "`datadate` INT(11)  NOT NULL default '0', ".
            "PRIMARY KEY (`ally_id`) " .
            ") ENGINE=InnoDB DEFAULT CHARSET=utf8 ";

        if (!defined('TABLE_GAME_PLAYER')) {
            define("TABLE_GAME_PLAYER", $table_prefix . "game_player");
        }

        $requests[] = "CREATE TABLE IF NOT EXISTS `" . TABLE_GAME_PLAYER . "` ( " .
            "`player_id` int(6) NOT NULL ," .
            "`player` varchar(65) NOT NULL, " .
            "`status` varchar(6)  NOT NULL default '', ".
            "`ally_id` int(6) NOT NULL ," .
            "`datadate` INT(11)  NOT NULL default '0', ".
            "PRIMARY KEY (`player_id`) " .
            ") ENGINE=InnoDB DEFAULT CHARSET=utf8 ";


        $requests[] = "ALTER TABLE `" . TABLE_USER . "` ADD `user_password_s` VARCHAR(255)    NOT NULL DEFAULT '' AFTER `user_password`";

        $requests[] = "DROP TABLE `" . TABLE_GCM_USERS . "`";

        if (!defined('TABLE_USER_TOKEN')) {
            define("TABLE_USER_TOKEN", $table_prefix . "user_tokens");
        }
        $requests[] = "CREATE TABLE IF NOT EXISTS `".TABLE_USER_TOKEN."` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `user_id` INT NOT NULL,
	        `name` VARCHAR(100) NOT NULL,
	        `token` VARCHAR(64) NOT NULL,
	        `expiration_date` VARCHAR(15) NOT NULL,
	        PRIMARY KEY (id)
        ) DEFAULT CHARSET = utf8;";

    case '3.3.5-alpha3':

        $requests[] = "UPDATE " . TABLE_CONFIG . " SET config_value = '3.3.5' WHERE config_name = 'version'";
        $ogsversion = '3.3.5';
        $up_to_date = true;

        break;
    default:
    die("Aucune mise … jour n'est disponible");
}


foreach ($requests as $request) {
    $db->sql_query($request);
}

// on supprime tous les fichiers du cache
// pour prendre en compte toutes les modifications
$files = glob('../cache/*.php');
if (count($files) > 0) {
    foreach ($files as $filename) {unlink($filename); }
}

?>
    <h3 align='center'><span style="color: yellow; ">Mise à jour du serveur OGSpy vers la version <?php echo $ogsversion; ?> effectuée avec succès</span></h3>
    <div style="text-align: center;">
    <br>
<?php
if ($pub_verbose == true) {
if ($up_to_date) {
    echo "\t" . "<b><i>Pensez à supprimer le dossier 'install'</i></b><br>" . "\n";
    echo "\t" . "<br><a href='../index.php'>Retour</a>" . "\n";
} else {
    echo "\t" . "<br><span style=\"color: orange; \"><b>Cette version n'est pas la dernière en date, veuillez relancer le script</span><br>" . "\n";
    echo "\t" . "<a href=''>Recommencer l'opération</a>" . "\n";
}
?>
    </div>
</body>
</html>
<?php } ?>
