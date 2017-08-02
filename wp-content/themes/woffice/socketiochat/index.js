var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);

app.get('/', function(req, res){
    res.sendFile(__dirname + '/index.html');
});


io.on('connection', function(socket){
    socket.on('chat message', function(msg,username,roomname,usern){
        io.emit('chat message', msg,username,roomname,usern);
    });
});

// io.on('connection', function(socket){
//
//     socket.join('Project 1 room');
//     socket.on('chat message', function(msg,username,roomname,usern){
//         io.to('Project 1 room').emit('chat message', msg,username,roomname,usern);
//
//     });
//
//
// });

http.listen(3000, function(){
    console.log('listening on *:3000');
});
