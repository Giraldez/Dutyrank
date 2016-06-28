<?php

/**
 * Created by Cluster
 * Date: 12/05/2016
 * Time: 08:04 PM
 */
require_once "config.php";
require_once "loader.php";

class logConverter {
    private $map = "";
    private $gameType = "";
    private $theMatrixReloaded = array();
    private $newMatch = false;

    private function loadLog() {
        return file_get_contents(constant("path"));
    }

    private function logToArray($str) {
        return explode("\r\n", $str);
    }

    private function arrayToDatabase() {
        $zp = gzopen(constant("database"), "w9");
        gzwrite($zp, json_encode($this->theMatrixReloaded));
        gzclose($zp);
    }

    private function truncateLog() {
        if ($fh = fopen(constant("path"), 'w')) {
            fwrite($fh, "");
            fclose($fh);
        }

    }

    private function weaponFilter($weaponString) {
        $weapon = "";
        $weapon_parts = explode("_", trim($weaponString));
        foreach ($weapon_parts as $weapon_part) {
            if ($weapon_part <> "iw5" && $weapon_part <> "mp") {
                $weapon = $weapon_part;
                break;
            }
        }
        return $weapon;
    }

    private function elo_update($assassin, $victim) {

        //Calcular elo
        if (!array_key_exists($assassin, $this->theMatrixReloaded["elo"])) {
            $this->theMatrixReloaded["elo"][$assassin] = 2000;
        }
        if (!array_key_exists($victim, $this->theMatrixReloaded["elo"])) {
            $this->theMatrixReloaded["elo"][$victim] = 2000;
        }
        //elo esperado
        $Ea = 1 / (1 + pow(10, ($this->theMatrixReloaded["elo"][$victim] - $this->theMatrixReloaded["elo"][$assassin]) / 400));
        $Ev = 1 / (1 + pow(10, ($this->theMatrixReloaded["elo"][$assassin] - $this->theMatrixReloaded["elo"][$victim]) / 400));

        ($this->theMatrixReloaded["elo"][$assassin] > 2400) ? $k = 10 : $k = 32;
        //Nuevo elo
        $this->theMatrixReloaded["elo"][$assassin] = round($this->theMatrixReloaded["elo"][$assassin] + $k * (1 - $Ea), 0);

        ($this->theMatrixReloaded["elo"][$victim] > 2400) ? $k = 10 : $k = 32;
        //Nuevo elo
        $this->theMatrixReloaded["elo"][$victim] = round($this->theMatrixReloaded["elo"][$victim] + $k * (0 - $Ev), 0);
    }

    public function convert($pass) {
        if (md5($pass) == constant("password")) {
            $log = $this->logToArray($this->loadLog());
            $loader = new loader();
            $this->theMatrixReloaded = $loader->load();
            if (is_array($log)) {
                if (count($log) > 0) {
                    foreach ($log as $line) {
                        if (strpos($line, "0:00 ------------------------------------------------------------") !== false) {
                            $this->theMatrixReloaded["countMatch"]++;
                            $this->newMatch = true;
                            continue;
                        }
                        if (strpos($line, "0:00 InitGame: \\g_gametype\\") !== false) {
                            $data = explode("\\", $line);
                            $this->gameType = $data[2];
                            $this->map = explode("_", $data[8])[1];
                            continue;
                        }
                        $data = explode(";", $line);
                        $action = strstr($data[0], "K");
                        $assassin = $data[8];
                        $victim = $data[4];
                        //para evitar que alguien suba en el ranking porque tenga de nombre un número
                        if (is_numeric($assassin)) $assassin = "_" . $assassin;
                        if (is_numeric($victim)) $victim = "_" . $victim;
                        $bodyPart = $data[12];
                        if ($action == "K" && $assassin <> "world") { //si hay kill y no lo mató el "mapa"
                            if ($assassin <> $victim) {//si no fue un suicidio
                                $weapon = $this->weaponFilter($data[9]);

                                if ($this->newMatch == true) {//primera sangre. solo cuento si hay juego nuevo
                                    $this->theMatrixReloaded["first_blood"][$assassin]++;
                                    $this->theMatrixReloaded["first_death"][$victim]++;
                                    $this->newMatch = false;
                                    //aprovecho para ponerle a todos la racha en cero! hay juego nuevo
                                    foreach ($this->theMatrixReloaded["racha_aux"] as $player => $value) {
                                        $this->theMatrixReloaded["racha_aux"][$player]["racha_actual"] = 0;
                                    }
                                }
                                //si no está la victima la creo y con valor cero!
                                if (!array_key_exists($victim, $this->theMatrixReloaded["racha_aux"])) $this->theMatrixReloaded["racha_aux"][$victim]["racha"] = 0;
                                //incremento la racha del asesino
                                $this->theMatrixReloaded["racha_aux"][$assassin]["racha_actual"]++;
                                //Actualizo la racha en caso de que la actual sea mayor que la anterior
                                if ($this->theMatrixReloaded["racha_aux"][$assassin]["racha_actual"] > $this->theMatrixReloaded["racha_aux"][$assassin]["racha"]) {
                                    $this->theMatrixReloaded["racha_aux"][$assassin]["racha"] = $this->theMatrixReloaded["racha_aux"][$assassin]["racha_actual"];
                                }
                                //reseteo la racha a cero si "te matan".
                                $this->theMatrixReloaded["racha_aux"][$victim]["racha_actual"] = 0;

                                $this->theMatrixReloaded["kill"][$assassin]++;
                                $this->theMatrixReloaded["death"][$victim]++;
                                if ($bodyPart == "head") $this->theMatrixReloaded["kill_head"][$assassin]++;
                                if ($weapon == "throwingknife") $this->theMatrixReloaded["kill_knife"][$assassin]++;
                                if ($weapon == "destructible") $this->theMatrixReloaded["kill_bombCar"][$assassin]++;
                                if ($weapon == "frag") $this->theMatrixReloaded["kill_grenade"][$assassin]++;
                                if ($weapon == "semtex") $this->theMatrixReloaded["kill_semtex"][$assassin]++;
                                if ($weapon == "concussion") $this->theMatrixReloaded["kill_concussion"][$assassin]++;
                                if ($weapon == "smaw") $this->theMatrixReloaded["kill_smaw"][$assassin]++;
                                if ($weapon == "rpg") $this->theMatrixReloaded["kill_rpg"][$assassin]++;
                                if ($weapon == "stinger") $this->theMatrixReloaded["kill_stinger"][$assassin]++;
                                if ($weapon == "claymore") $this->theMatrixReloaded["kill_claymore"][$assassin]++;
                                if ($weapon == "c4") $this->theMatrixReloaded["kill_c4"][$assassin]++;
                                if ($weapon == "bouncingbetty") $this->theMatrixReloaded["kill_bouncingbetty"][$assassin]++;
                                if ($weapon == "nuke") {
                                    $this->theMatrixReloaded["kill_nuke"][$assassin]++;
                                    $this->theMatrixReloaded["kill_nuke_game"][$assassin][$this->theMatrixReloaded["countMatch"]] = 0;
                                }
                                $this->theMatrixReloaded["kill_map"][$this->map][$assassin]++;
                                $this->theMatrixReloaded["kill_game_type"][$this->gameType][$assassin]++;

                                $this->theMatrixReloaded["kill_ave_aux"][$assassin][$this->theMatrixReloaded["countMatch"]]++;
                                $this->theMatrixReloaded["matchWin_aux"][$this->theMatrixReloaded["countMatch"]][$assassin]++;

                                $this->elo_update($assassin, $victim);
                            } else {//kill suicida
                                $this->theMatrixReloaded["kill_suicida"][$assassin]++;
                            }
                        }
                    }
                    //fecha de la actualizacion
                    $objDateTime = new DateTime('NOW');
                    $this->theMatrixReloaded["last_update"] = $objDateTime->format('l jS \of F Y h:i:s A');
                    $this->arrayToDatabase();
                    $this->truncateLog();
                    return "ok";
                } else {
                    return "log vacío";
                }
            } else {
                return "Error cargando el log";
            }
        } else {
            return "bad password";
        }

    }
}