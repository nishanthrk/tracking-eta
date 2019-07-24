function empty(str) {
    return (!str || /^\s*$/.test(str) || 0 === str.length);
}

function handelResponseError(error) {
    switch (error.status) {
        case 401:
            alert('Invalid token. Please logout and login again.');
            break;
        case 422:
            alert(error.data.error);
            break;
    }
}

var poly;
var map;

var iconBase =
    'http://maps.google.com/mapfiles/kml/paddle/';

var icons = {
    from: {
        icon: iconBase + 'A.png'
    },
    to: {
        icon: iconBase + 'B.png'
    },
    info: {
        icon: iconBase + 'info-i_maps.png'
    }
};

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 11,
        center: { lat: 28.7041, lng: 77.1025 },
        scrollwheel: false
    });
}

var orderLocationApp = angular.module('order-location', []);
orderLocationApp.run(['$http', function ($http) {
    $http.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';
}]);
orderLocationApp.controller('controller-order-location', function ($rootScope, $scope, $http, $window) {

    $scope.eta = '';
    $scope.showEta = false;
    $scope.current = [];
    $scope.orders = [];
    $scope.polyLineEnable = false;
    $scope.cooridates = [];

    function reInitMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 11,
            center: { lat: 28.7041, lng: 77.1025 },
            scrollwheel: false
        });

        poly = new google.maps.Polyline({
            strokeColor: '#000000',
            strokeOpacity: 1.0,
            strokeWeight: 3
        });

        poly.setMap(map);

        // Add a listener for the click event
        map.addListener('click', addLatLng);
    }

    $scope.calculateEta = function(originLat, originLng, destinationLat, destinationLng) {

        $scope.cooridates.originLat = originLat;
        $scope.cooridates.originLng = originLng;
        $scope.cooridates.destinationLat = destinationLat;
        $scope.cooridates.destinationLng = destinationLng;

        var request = {
            method: 'POST',
            url: baseUrl + '/api/distance.php',
            headers: {'Content-Type': 'application/json'},
            data: {
                originLat: originLat,
                originLng: originLng,
                destinationLat: destinationLat,
                destinationLng: destinationLng
            }
        };
        $http(request)
            .then(
                function (response) {
                    $scope.eta = response.data.data.duration.text;
                    $scope.showEta = true;
                },
                function (error) {
                    alert('Some error. Contact Nishanth.');
                }
            );
    };


    function addLatLng(event) {
        if (!$scope.polyLineEnable) {
            alert('Please click on the start button to travel');
            return;
        }

        var path = poly.getPath();

        path.push(event.latLng);

        var marker = new google.maps.Marker({
            position: event.latLng,
            title: '#' + path.getLength(),
            map: map
        });

        $scope.calculateEta(marker.getPosition().lat(), marker.getPosition().lng(), $scope.current.nextLat, $scope.current.nextLng);
    }

    $scope.fetchOrder = function () {

        var request = {
            method: 'GET',
            url: baseUrl + '/api/order.php'
        };
        $http(request)
            .then(function (response) {
                $scope.orders = response.data.data.orders;
                $scope.current = response.data.data.current;
                if ($scope.current.display === 'Start') {
                    $scope.polyLineEnable = false;
                } else {
                    $scope.polyLineEnable = true;
                }
            }, function (error) {
                handelResponseError(error);
            });

        reInitMap();

        $scope.$watch('current', function () {
            var lat = parseFloat($scope.current.lat);
            var lng = parseFloat($scope.current.lng);
            var nextLat = parseFloat($scope.current.nextLat);
            var nextLng = parseFloat($scope.current.nextLng);
            if (!isNaN(lat) && !isNaN(lng)
                && !isNaN(parseFloat(nextLat)) && !isNaN(nextLng)) {

                var from = new google.maps.LatLng(lat,lng);
                var to = new google.maps.LatLng(nextLat,nextLng);
                new google.maps.Marker({
                    position: from,
                    map: map,
                    icon: icons.from.icon,
                    title: 'From Location'
                });

                new google.maps.Marker({
                    position: to,
                    map: map,
                    icon: icons.to.icon,
                    title: 'To Location'
                });

                var bounds = new google.maps.LatLngBounds();
                bounds.extend(from);
                bounds.extend(to);
                map.fitBounds(bounds);

                $scope.calculateEta(lat, lng, nextLat, nextLng);
            }
        });

        $scope.$watch('eta', function () {
            // console.log($scope.eta);
        });
    };

    $scope.updateStatus = function(id, status) {
        var request = {
            method: 'POST',
            url: baseUrl + '/api/order.php',
            headers: {'Content-Type': 'application/json'},
            data: {
                orderId: id,
                status: status,
                originLat: $scope.cooridates.originLat,
                originLng: $scope.cooridates.originLng,
                destinationLat: $scope.cooridates.destinationLat,
                destinationLng: $scope.cooridates.destinationLng
            }
        };
        $http(request)
            .then(
                function (response) {
                    $scope.orders = response.data.data.orders;
                    $scope.current = response.data.data.current;
                    if ($scope.current.display === 'Start') {
                        reInitMap();
                        $scope.polyLineEnable = false;
                    } else {
                        $scope.polyLineEnable = true;
                    }
                },
                function (error) {
                    handelResponseError(error);
                }
            );
    }
});