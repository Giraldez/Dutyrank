/**
 * Created by Cluster
 * Date: 06/05/2016
 * Time: 05:14 PM
 */
angular.module("DutyRankReloaded").service("DataService", DataService);
DataService.$inject = ["$http"];

function DataService($http) {

    return {
        getData: getData,
        install: install,
        importLog: importLog
    };

    function getData() {
        return $http.get('/php/index.php/load');
    }

    function install(password) {
        return $http.post('/php/index.php/install', password);
    }

    function importLog(password) {
        return $http.post('/php/index.php/converter', password);
    }
}