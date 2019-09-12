<html lang="en">
<head>
    <title>Location Tracking & ETA System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
    <script src="main.js?<?= strtotime('now') ?>"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=&callback=initMap"></script>
</head>
<body>
<div class="container-fluid" ng-app="order-location" ng-controller="controller-order-location" style="margin-top:10px">
    <div class="row">
        <div class="col-md-8">
            <div id="map" style="width:102%;height:95vh;border:1px solid rgba(0,0,0,.15);"></div>
        </div>
        <div class="col-md-4">
            <span ng-if="orders.length == 0">
                <button type="button" class="btn btn-outline-primary" style="margin-top: 12%;margin-left: 21%;" data-ng-click="fetchOrder()">Generate Multiple Order Delivery</button>
            </span>
            <span ng-if="orders.length != 0">
                <h2 style="text-align: center;">Order Details</h2>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Address</th>
                        <th>ETA</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr data-ng-repeat="order in orders">
                            <td>{{ order.id }}</td>
                            <td>{{ order.address }}</td>
                            <td data-ng-if="current.id == order.id">
                                <span style="position: relative;">
                                    {{ eta }}
                                </span>
                            </td>
                            <td data-ng-if="current.id != order.id">
                                -
                            </td>
                            <td data-ng-if="current.id == order.id">
                                <button type="button" class="btn btn-sm btn-outline-{{ current.display === 'Start' ? 'success' : 'danger' }}" data-ng-click="updateStatus(order.id, current.nextStatus)">{{ current.display }}</button>
                            </td>
                            <td data-ng-if="current.id != order.id">
                                <button type="button" class="btn btn-sm btn-outline-secondary" disabled>{{ order.status === 'FINISHED_DELIVERY' ? 'Completed' : 'Pending' }}</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </span>
        </div>
    </div>
</div>
<script>
    var baseUrl = 'http://localhost:8000';
</script>
</body>
</html>
