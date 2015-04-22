'use strict';
(function(){
    var roomsManageApp = angular.module('RoomsManageApp',['ngRoute', 'roomsManageControllers' , 'roomsServices', 'roompricesServices', 'imagesServices']);

    roomsManageApp.config(['$routeProvider', function($routeProvider) {
            $routeProvider
                .when('/', {
                    templateUrl: '/partial/rooms/index.html',
                    controller: 'RoomListCtrl'
                })
                .when('/add', {
                    templateUrl: '/partial/rooms/add.html',
                    controller: 'RoomAddCtrl'
                })
                .when('/edit/:id', {
                    templateUrl: '/partial/rooms/add.html',
                    controller: 'RoomEditCtrl'
                })
                .when('/prices/:id', {
                    templateUrl: '/partial/rooms/prices.html',
                    controller: 'PricesCtrl'
                })
                .when('/images/:id', {
                    templateUrl: '/partial/rooms/images.html',
                    controller: 'ImagesCtrl'
                })
            ;
    }]);
})();