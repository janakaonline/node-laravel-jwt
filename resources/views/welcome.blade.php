@extends('layouts.app')

@section('content')



    <h1>Socket Connection Status: <span id="connection"></span></h1>

    <h1>Login User Id: <span id="userid"></span></h1>

    <h1>Login User Email: <span id="email"></span></h1>

    <h1>Public Message: <span id="receive-my-message"></span></h1>

    <h1>Remaining time: <span id="remaining-time"></span></h1>

    <h1>Add time: <button class="add-mins" data-mins="5">5 mins</button> <button  class="add-mins" data-mins="10">10 mins</button></h1>

    <script src="//code.jquery.com/jquery-1.10.2.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.4.8/socket.io.js"></script>

    <script type="text/javascript">

        $(document).ready(function () {

            socketIOConnectionUpdate('Requesting JWT Token from Laravel');


            $.ajax({

                url: 'http://dev.com/token'

            }).fail(function (jqXHR, textStatus, errorThrown) {

                socketIOConnectionUpdate('Something is wrong on ajax: ' + textStatus);

            }).done(function (result, textStatus, jqXHR) {


                /*

                 make connection with localhost 3000

                 */

                var socket = io.connect('http://dev.com:3000');


                /*

                 connect with socket io

                 */

                socket.on('connect', function () {

                    socketIOConnectionUpdate('Connected to SocketIO, Authenticating')

                    socket.emit('authenticate', {token: result.token});


                    socket.emit('public-my-message', {'msg': 'Hi, Every One.'});

                });




                /*

                 If token authenticated successfully then here will get message

                 */

                socket.on('authenticated', function () {

                    socketIOConnectionUpdate('Authenticated');

                });


                /*

                 If token unauthorized then here will get message

                 */

                socket.on('unauthorized', function (data) {

                    socketIOConnectionUpdate('Unauthorized, error msg: ' + data.message);

                });


                /*

                 If disconnect socketio then here will get message

                 */

                socket.on('disconnect', function () {

                    socketIOConnectionUpdate('Disconnected');

                });


                /*

                 Get Userid by server side emit

                 */

                socket.on('user-id', function (data) {

                    $('#userid').html(data);

                });


                /*

                 Get Email by server side emit

                 */

                socket.on('user-email', function (data) {

                    $('#email').html(data);

                });

                socket.on('tictoc', function (data) {

                    $('#remaining-time').html(data);

                });


                /*

                 Get receive my message by server side emit

                 */

                socket.on('receive-my-message', function (data) {

                    $('#receive-my-message').html(data);

                });

                $('.add-mins').click(function(e){
                    e.preventDefault();
                    socket.emit('add-mins', {mins: $(this).data('mins')});
                });

            });

        });


        /*

         Function for print connection message

         */

        function socketIOConnectionUpdate(str) {

            $('#connection').html(str);

        }

    </script>

@endsection