/**
 * Created by Cluster on 10/11/15.
 */
angular.module('DutyRankReloaded').controller("mainCtrl", mainCtrl);
mainCtrl.$inject = ['$scope', '$window', "DataService"];

function mainCtrl($scope, $window, DataService) {
    var vm = this;
    //titulo
    vm.title = "DutyRank Reloaded";

    vm.frmInstall = {};
    vm.msgInstall = "";
    vm.frmImport = {};
    vm.msgImport = "";

    vm.msgText = "";

    //altura del banner
    vm.bannerHeight = 46;

    //posición del scroll
    vm.scrollPos = 0;
    vm.maxScroll = 0;
    vm.perCenScroll = 0;
    $window.onscroll = function () {
        vm.scrollPos = document.body.scrollTop || document.documentElement.scrollTop || 0;
        vm.maxScroll = document.body.scrollHeight - window.innerHeight;
        vm.perCenScroll = vm.scrollPos / vm.maxScroll * 100;
        $scope.$apply();
    };

    //tener siempre actualizado el valor si el usuario hace resize : )
    $window.onresize = function () {
        vm.maxScroll = document.body.scrollHeight - window.innerHeight;
        $scope.$apply();
    };
    //Mostrar / ocultar importar / instalar
    vm.viewImport = false;
    vm.viewInstall = false;
    vm.leftMargingWindow = (window.innerWidth - window.innerWidth * 0.33) / 2;


    getData();

    function getData() {
        DataService.getData().then(
            function (response) {
                vm.theMatrix = response.data;
            },
            function (response) {
                vm.msgText = response.statusText;
            }
        );
    }

    vm.install = function (isValid) {
        if (isValid) {
            vm.msgInstall = '';
            DataService.install(vm.frmInstall).then(
                function (response) {
                    if (response.data == 'ok') {
                        vm.viewInstall = false;
                        vm.frmInstall.password = '';
                    } else {
                        vm.msgInstall = response.data;
                    }
                },
                function (response) {
                    vm.msgInstall = response.data + response.statusText;
                }
            );
        }
    };

    vm.import = function (isValid) {
        if (isValid) {
            vm.msgImport = '';
            DataService.importLog(vm.frmImport).then(
                function (response) {
                    if(response.data == 'ok'){
                        vm.viewImport = false;
                        vm.frmImport.password = '';
                        getData();
                    }else{
                        vm.msgImport = response.data;
                    }
                },
                function (response) {
                    vm.msgImport = response.data + response.statusText;
                }
            );
        }
    };
}