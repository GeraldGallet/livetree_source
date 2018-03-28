/********** SERVER *********/
/* The server we use as an API */

// Configuration import
const config = require('./config.json');

// Needed packages
const mysql = require('mysql');
var express = require('express'),
  app = express(),
  port = process.env.PORT || 3000;
app.listen(port);
var bodyParser = require('body-parser');
var jsonParser = bodyParser.json();

// Import our own classes
const User = require('./classes/user.js');
const user = new User();

// Connecting to DB
app.use(function(req, res, next){
	res.locals.connection = mysql.createConnection({
		host     : config.host,
		user     : config.user,
		password : config.password,
		database : config.database
	});
	res.locals.connection.connect();
	next();
});

app.get('/', function(req, res, next) {
  res.send('hello world :)');
});

// Get all users
app.get('/user', function(req, res, next) {
  console.log('GET /user');
});

// The commands that can be done on a user
app.post('/user/:cmd', jsonParser, function(req, res, next) {
  switch(req.params.cmd) {
      case "get":
        console.log("/user/get");
        user.get(req.body, res);
        break;

      case "add":
        console.log("/user/add");
        user.add(req.body, res);
        break;

      default:
        console.log("UNKNOWN COMMAND POST /user/:cmd");
        break;
  }
});

app.delete('/:cmd', jsonParser, function(req, res, next) {
  switch(req.params.cmd) {
    case "user":
      console.log("delete /user");
      user.delete(req.body, res);
      break;

    default:
      console.log("UNKNOWN COMMAND DELETE /:cmd");
      break;
  }
});

app.patch('/user/:cmd', jsonParser, function(req, res) {
  switch(req.params.cmd) {
    case "password":
      console.log("patch /user/password");
      user.change_password(req.body, res);
      break;

    default:
      console.log("UNKNOWN COMMAND PATCH /user/:cmd");
  }
});
