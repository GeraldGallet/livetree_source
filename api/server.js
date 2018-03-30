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
const Status = require('./classes/status.js');
const status = new Status();
const PersonalCar = require('./classes/personalcar.js');
const personal_car = new PersonalCar();

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

// The commands that can be done on a table (get / add)
app.post('/:table/:cmd', jsonParser, function(req, res, next) {
  console.log(req.params.table + "/" + req.params.cmd);

  switch(req.params.table) {
    case "user":
      switch(req.params.cmd) {
          case "get":
            user.get(req.body, res);
            break;

          case "add":
            user.add(req.body, res);
            break;

          default:
            console.log("UNKNOWN POST COMMAND");
            break;
      }
      break;

    case "status":
      switch(req.params.cmd) {
        case "get":
          status.get(req.body, res);
          break;

        case "add":
          status.add(req.body, res);
          break;

        default:
          console.log("UNKNOWN POST COMMAND");
          break;
      }
      break;

    case "personal_car":
      switch(req.params.cmd) {
        case "get_all":
          personal_car.get_all(req.body, res);
          break;
          
        case "get":
          personal_car.get(req.body, res);
          break;

        case "add":
          personal_car.add(req.body, res);
          break;

        default:
          console.log("UNKNOWN POST COMMAND");
          break;
      }
      break;


    default:
      console.log("UNKNOWN TABLE POST /" + req.params.table + "/");
      break;
  }
});

app.delete('/:table', jsonParser, function(req, res, next) {
  console.log("delete /" + req.params.table);

  switch(req.params.table) {
    case "status":
      status.delete(req.body, res);
      break;

    case "user":
      user.delete(req.body, res);
      break;

    default:
      console.log("UNKNOWN COMMAND DELETE /" + req.params.table);
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
