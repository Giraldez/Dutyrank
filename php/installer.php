<?php

/**
 * Created by Cluster
 * Date: 12/05/2016
 * Time: 07:29 PM
 */
require_once 'config.php';

class installer {
    public function install($pass) {
        if (md5($pass) == constant("password")) {

            $theMatrixreloaded["countMatch"] = 0;
            $theMatrixreloaded["elo"] = array();
            $theMatrixreloaded["kill_suicida"] = array();
            $theMatrixreloaded["kill"] = array();
            $theMatrixreloaded["death"] = array();
            $theMatrixreloaded["kill_bombCar"] = array();
            $theMatrixreloaded["kill_bouncingbetty"] = array();
            $theMatrixreloaded["kill_claymore"] = array();
            $theMatrixreloaded["kill_c4"] = array();
            $theMatrixreloaded["kill_concussion"] = array();
            $theMatrixreloaded["kill_grenade"] = array();
            $theMatrixreloaded["kill_head"] = array();
            $theMatrixreloaded["kill_knife"] = array();
            $theMatrixreloaded["kill_rpg"] = array();
            $theMatrixreloaded["kill_semtex"] = array();
            $theMatrixreloaded["kill_smaw"] = array();
            $theMatrixreloaded["kill_stinger"] = array();
            $theMatrixreloaded["kill_nuke"] = array();
            $theMatrixreloaded["kill_nuke_game"] = array();
            $theMatrixreloaded["kill_ave_aux"] = array();
            $theMatrixreloaded["matchWin_aux"] = array();
            $theMatrixreloaded["kill_map"] = array();
            $theMatrixreloaded["kill_game_type"] = array();
            $theMatrixreloaded["last_update"] = "";
            $theMatrixreloaded["first_blood"] = array();
            $theMatrixreloaded["first_death"] = array();
            $theMatrixreloaded["racha_aux"] = array();
            $objDateTime = new DateTime('NOW');
            $theMatrixreloaded["last_update"] = $objDateTime->format('l jS \of F Y h:i:s A');

            $zp = gzopen(constant("database"), "w9");
            gzwrite($zp, json_encode($theMatrixreloaded));
            gzclose($zp);

            return "ok";
        }else {
            return "bad password";
        }
    }
}