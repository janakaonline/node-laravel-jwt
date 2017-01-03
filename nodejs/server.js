var express = require('express');
var app = express();
var server = require('http').createServer(app);
var io = require('socket.io')(server);
var socketioJwt = require('socketio-jwt');
var myEnv = require('dotenv').config({path:'../.env'});

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


    console.log(socket.decoded_token);

    socket.emit('user-id', socket.decoded_token.userid);

    socket.emit('user-email', socket.decoded_token.email);


    socket.on('public-my-message', function (data) {

        socket.emit('receive-my-message', data.msg);

    });


});


/*

 Start NodeJS server at port 3000

 */
console.log('Serving...');

server.listen(3000);