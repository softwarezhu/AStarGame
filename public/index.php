<!DOCTYPE html>
<html lang="en" ng-app="app">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="//cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .map {
            display: table;
            text-align: center;
            border-collapse: collapse;
        }
        .grid-row {
            display: table-row;
            
        }
        .grid {
            display: table-cell;
            border: 1px solid #f60;
            width: 20px;
            height: 20px;
        }

        .on {
            background: #ff6;
        }
    </style>
</head>

<body ng-controller="MyCtrl">

<div>
    <button class="btn btn-default" ng-click="generateMap()">Generate Map</button>
    <button class="btn btn-default" ng-click="start()">Star Game</button>
</div>
<div>
    <div class="input-group">
        Map Size：<input type="text" name="" id="" ng-model="width"> * <input type="text" ng-model="height">
    </div>
    <textarea name="" id="" cols="30" rows="10" ng-model="mapsRaw"></textarea>

</div>

<section id="summary">
    Map Size：{{width}}*{{height}}
    Memory：{{memory}}
    Time：{{elapsed}}
    Used Time：{{time}}

</section>
<section class="map" id="map">
    <div class="grid-row" ng-repeat="row in maps">
        <div class="grid" ng-class="isOn(col)" ng-repeat="col in row track by $index">{{col.text}}</div>
    </div>
</section>
<script src="//cdn.bootcss.com/jquery/2.2.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="//cdn.bootcss.com/angular.js/1.5.8/angular.min.js"></script>
<script>
    var app = angular.module('app', []);
    app.controller('MyCtrl', function ($scope) {
        $scope.maps = [];
        $scope.mapsRaw = '';
        $scope.time = 0;
        $scope.width = 20;
        $scope.height = 20;
        $scope.memory = 0;
        $scope.elapsed = 0;

        $scope.generateMap = function () {
            var maps = [];
            var chars = ['.', '*', 'X'];
            for (var i = 0; i < $scope.height; i++) {
                maps.push([]);
                for (var j = 0; j < $scope.width; j++) {
                    var rand = parseInt((Math.random() * 4) % 4);
                    if (rand == 0) {
                        maps[i].push({
                            'text': 'X',
                            'on': false
                        });
                    } else if (rand == 1) {
                        maps[i].push({
                            'text': parseInt((Math.random() * 9) % 9)+1,
                            'on': false
                        });
                    } else {
                        maps[i].push({
                            'text': '.',
                            'on': false
                        });
                    }
                }
                maps[0][0].text = '.';
            }

            $scope.maps = maps;

            var texts = $scope.height + ' ' + $scope.width + '\n';
            maps.forEach(function (item1) {
                item1.forEach(function (node) {
                    texts += node.text;
                });
                texts += "\n";
            });
            $scope.mapsRaw = texts;
        };

        $scope.getMapRaw = function () {
            var texts = '';
            $scope.maps.forEach(function (item1) {
                texts += item1.join('')+"\n";
            });

            return texts;
        };

        var timeArr = [];
        $scope.start = function () {
            $scope.time = 0;
            $scope.memory = 0;
            $scope.elapsed = 0;
            // 清空状态
            for (var h = 0; h < $scope.height; h++) {
                for (var w = 0; w < $scope.width; w++) {
                    $scope.maps[h][w].on = false;
                }
            }
            for (var i = 0; i < timeArr.length; i++) {
                clearTimeout(timeArr[i]);
            }
            timeArr = [];

            $.post('/search.php', {
                map: $scope.mapsRaw
            }, function (data) {
                $scope.memory = data.memory;
                $scope.elapsed = data.elapsed;
                if (!data.status) {
                    $scope.$digest();
                    return alert('God please help our poor hero. ');
                }
                var result = data.data;
                var t = 0;
                $scope.time = -1;
                for (var i = 0; i < result.length; i++) {
                    (function(node){
                        var timeId = setTimeout(function(){
                            $scope.maps[node.y][node.x].on = true;
                            $scope.time += 1 + node.cost;
                            $scope.$digest();
                        }, t+=1000);
                        timeArr.push(timeId);
                    })(result[i]);
                }
            }, 'json');
        };

        $scope.isOn = function (node) {
            return node.on ? 'on' : '';
        }
    });
</script>
</body>

</html>
