<?php

/**
 * Created by Cluster
 * Date: 12/05/2016
 * Time: 07:50 PM
 */
require_once "config.php";

class loader {
    private $theMatrixReloaded = array();

    public function load() {
        $zp = gzopen(constant("database"), "r");
        $theMatrixreloaded = json_decode(gzread($zp, 524288000), true);
        gzclose($zp);
        return (array)$theMatrixreloaded;
    }

    private function orderRacha() {
        foreach ($this->theMatrixReloaded["racha_aux"] as $assassin => $value) {
            $this->theMatrixReloaded["racha_aux"][$assassin] = $value["racha"];
        }
    }

    private function orderRanking() {
        array_multisort($this->theMatrixReloaded["elo"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["kill_suicida"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["kill"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["death"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["kill_bombCar"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["kill_bouncingbetty"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["kill_claymore"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["kill_c4"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["kill_concussion"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["kill_grenade"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["kill_head"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["kill_knife"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["kill_rpg"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["kill_semtex"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["kill_smaw"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["kill_stinger"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["kill_nuke"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["first_blood"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["first_death"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["racha_aux"], SORT_DESC, SORT_NUMERIC);

    }

    public function orderRankingOthers() {
        array_multisort($this->theMatrixReloaded["ratio"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["kill_avg"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["match_win"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["play_games"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["ratio_match_wins"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["majorKillOneGame"], SORT_DESC, SORT_NUMERIC);
        array_multisort($this->theMatrixReloaded["kill_moab_game"], SORT_DESC, SORT_NUMERIC);

        foreach ($this->theMatrixReloaded["kill_game_type"] as $gameType => $value) {
            array_multisort($this->theMatrixReloaded["kill_game_type"][$gameType], SORT_DESC, SORT_NUMERIC);
        }
        foreach ($this->theMatrixReloaded["kill_map"] as $gameType => $value) {
            array_multisort($this->theMatrixReloaded["kill_map"][$gameType], SORT_DESC, SORT_NUMERIC);
        }
    }

    public function fullLoad() {
        $this->theMatrixReloaded = $this->load();

        //ordenar descendente
        $this->orderRacha();//preparar el array para ordenarlo con orderRanking
        $this->orderRanking();

        //limites
        $this->firstPositionsRankingKillDeath();
        $this->firstPositionsRanking($this->theMatrixReloaded["death"]);
        $this->firstPositionsRanking($this->theMatrixReloaded["elo"]);
        $this->firstPositionsRanking($this->theMatrixReloaded["kill_suicida"]);
        $this->firstPositionsRanking($this->theMatrixReloaded["kill_bombCar"]);
        $this->firstPositionsRanking($this->theMatrixReloaded["kill_bouncingbetty"]);
        $this->firstPositionsRanking($this->theMatrixReloaded["kill_claymore"]);
        $this->firstPositionsRanking($this->theMatrixReloaded["kill_c4"]);
        $this->firstPositionsRanking($this->theMatrixReloaded["kill_concussion"]);
        $this->firstPositionsRanking($this->theMatrixReloaded["kill_grenade"]);
        $this->firstPositionsRanking($this->theMatrixReloaded["kill_head"]);
        $this->firstPositionsRanking($this->theMatrixReloaded["kill_knife"]);
        $this->firstPositionsRanking($this->theMatrixReloaded["kill_rpg"]);
        $this->firstPositionsRanking($this->theMatrixReloaded["kill_semtex"]);
        $this->firstPositionsRanking($this->theMatrixReloaded["kill_smaw"]);
        $this->firstPositionsRanking($this->theMatrixReloaded["kill_stinger"]);
        $this->firstPositionsRanking($this->theMatrixReloaded["kill_nuke"]);
        $this->firstPositionsRanking($this->theMatrixReloaded["first_blood"]);
        $this->firstPositionsRanking($this->theMatrixReloaded["first_death"]);
        $this->firstPositionsRanking($this->theMatrixReloaded["racha_aux"]);
        $this->killsGameType();
        $this->killsMap();

        //Otros cálculos
        $this->ratio();
        $this->killAvg();
        $this->matchWins();
        $this->playGames();
        $this->ratioMatchWins();
        $this->majorKillsOneMatch();
        $this->killMoabGame();

        //ordenar descendente
        $this->orderRankingOthers();
        //eliminar los datos ajenos al cliente
        $this->clear();
        return $this->theMatrixReloaded;
    }

    private function firstPositionsRankingKillDeath() {
        if (count($this->theMatrixReloaded["kill"]) > constant("limite")) {
            $i = 0;
            foreach ($this->theMatrixReloaded["kill"] as $assassin => $quantity) {
                $i++;
                if ($i > constant("limite")) {
                    unset($this->theMatrixReloaded["kill"][$assassin]);
                    unset($this->theMatrixReloaded["death"][$assassin]);
                }
            }
        }
    }

    private function firstPositionsRanking(&$arrayCopy) {
        $arrayCopy = (array)$arrayCopy;
        if (count($arrayCopy) > constant("limite")) {
            $i = 0;
            foreach ($arrayCopy as $assassin => $quantity) {
                $i++;
                if ($i > constant("limite")) {
                    unset($arrayCopy[$assassin]);
                }
            }
        }
    }

    private function ratio() {
        foreach ($this->theMatrixReloaded["kill"] as $assassin => $quantity) {
            if ($this->theMatrixReloaded["death"][$assassin] == 0) {
                $this->theMatrixReloaded["death"][$assassin] = 0.0001;
            }
            $this->theMatrixReloaded["ratio"][$assassin] = round($quantity / $this->theMatrixReloaded["death"][$assassin], 3);
            if ($this->theMatrixReloaded["death"][$assassin] == 0.0001) {
                $this->theMatrixReloaded["death"][$assassin] = 0;
            }
        }
    }

    private function killAvg() {
        foreach ($this->theMatrixReloaded["kill_ave_aux"] as $assassin => $countMatch) {
            $avg = 0;
            $count = 0;
            foreach ($countMatch as $quantuty) {
                $count++;
                $avg += $quantuty;
            }
            $this->theMatrixReloaded["kill_avg"][$assassin] = round($avg / $count, 0);
        }
        $this->firstPositionsRanking($this->theMatrixReloaded["kill_avg"]);
    }

    private function matchWins() {
        $pga = array();
        $pga2 = array();
        foreach ($this->theMatrixReloaded["matchWin_aux"] as $countMatch => $value1) {
            foreach ($value1 as $assassin => $quantity) {
                if ($pga[$countMatch] < $quantity) {
                    $pga[$countMatch] = $quantity;
                    $pga2[$countMatch] = $assassin;
                }
            }

        }
        foreach ($pga2 as $countMatch => $assassin) {
            $this->theMatrixReloaded["match_win"][$assassin] += 1;
        }
        $this->firstPositionsRanking($this->theMatrixReloaded["match_win"]);
    }

    private function playGames() {
        foreach ($this->theMatrixReloaded["kill_ave_aux"] as $assassin => $quantity) {
            $this->theMatrixReloaded["play_games"][$assassin] = count((array)$this->theMatrixReloaded["kill_ave_aux"][$assassin]);
        }
        $this->firstPositionsRanking($this->theMatrixReloaded["play_games"]);
    }

    private function ratioMatchWins() {
        foreach ($this->theMatrixReloaded["match_win"] as $assassin => $quantity) {
            $this->theMatrixReloaded["ratio_match_wins"][$assassin] = round($this->theMatrixReloaded["match_win"][$assassin] / count((array)$this->theMatrixReloaded["kill_ave_aux"][$assassin]), 3);

        }
    }

    private function majorKillsOneMatch() {
        foreach ($this->theMatrixReloaded["kill_ave_aux"] as $assassin => $matchNumbers) {
            $aux = 0;
            foreach ($matchNumbers as $matchNumber => $quantity) {
                if ($quantity > $aux) {
                    $aux = $quantity;
                }
            }
            $this->theMatrixReloaded["majorKillOneGame"][$assassin] = $aux;
        }
        $this->firstPositionsRanking($this->theMatrixReloaded["majorKillOneGame"]);
    }

    private function killsGameType() {
        foreach ($this->theMatrixReloaded["kill_game_type"] as $gameType => $value) {
            $this->firstPositionsRanking($this->theMatrixReloaded["kill_game_type"][$gameType]);
        }

    }

    private function killsMap() {
        foreach ($this->theMatrixReloaded["kill_map"] as $map => $value) {
            $this->firstPositionsRanking($this->theMatrixReloaded["kill_map"][$map]);
        }

    }

    private function killMoabGame() {
        foreach ($this->theMatrixReloaded["kill_nuke_game"] as $assassin => $matchNumber) {
            $this->theMatrixReloaded["kill_moab_game"][$assassin] = count((array)$this->theMatrixReloaded["kill_nuke_game"][$assassin]);

        }

    }

    private function clear() {
        unset($this->theMatrixReloaded["kill_nuke_game"]);
        unset($this->theMatrixReloaded["kill_ave_aux"]);
        unset($this->theMatrixReloaded["matchWin_aux"]);
        unset($this->theMatrixReloaded["countMatch"]);
    }
}