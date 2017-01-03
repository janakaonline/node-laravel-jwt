var express = require('express');
var app = express();
var server = require('http').createServer(app);
var io = require('socket.io')(server);
var socketioJwt = require('socketio-jwt');
var myEnv = require('dotenv').config({path: '../.env'});
var moment = require('moment');
var timeSlots = [];

/*

 Accept connection and authorize token code

 */

io.on('connection', socketioJwt.authorize({

    secret: myEnv.JWT_SECRET,

    timeout: 15000

}));

/*

 When authenticated, send back userid + email over socket

 */

io.on('authenticated', function (socket) {

    //var started = moment();
    var endTime = null;
    socket.emit('user-id', socket.decoded_token.userid);

    socket.emit('user-email', socket.decoded_token.email);



    if (timeSlots[socket.decoded_token.userid]) {
        endTime = timeSlots[socket.decoded_token.userid];
    }

    if (!endTime) {
        endTime = moment();
        if (socket.decoded_token.userid == 1) {
            endTime.add(4, 'h')
        } else if (socket.decoded_token.userid == 2) {
            endTime.add(3, 'h')
        }
        else {
            endTime.add(1, 'h')
        }
        timeSlots[socket.decoded_token.userid] = endTime;
    }

    setInterval(function () {
        var ms = endTime.diff(moment());
        var d = moment.duration(ms);
        var s = Math.floor(d.asHours()) + moment.utc(ms).format(":mm:ss");
        socket.emit('tictoc', s);
    }, 500);


    socket.on('public-my-message', function (data) {
        socket.emit('receive-my-message', data.msg);
    });

    socket.on('add-mins', function (data) {
        if (timeSlots[socket.decoded_token.userid]) {
            timeSlots[socket.decoded_token.userid].add(data.mins, 'm');
        }
    });



});

/*

 Start NodeJS server at port 3000

 */
console.log('Serving...');

server.listen(3000);