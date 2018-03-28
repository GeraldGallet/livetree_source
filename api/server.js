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
});

// Get the DB line of specified user
app.post('/get_user', jsonParser, function(req, res, next) {
  user.get(req.body, res);
});

app.post('/user', jsonParser, function (req, res) {
  console.log('POST /user');
  var postData  = req.body;

  res.locals.connection.query('INSERT INTO user SET ?', postData, function (error, results, fields) {
    if (error) throw error;
    res.end(JSON.stringify(results));
  });
});

app.patch('/user/password', jsonParser, function(req, res) {
  console.log("PATCH /user/password");
  var data = req.body;
  var query = "UPDATE user SET password = \'" + data.password + '\' WHERE email = \'' + data.email + '\';';

  res.locals.connection.query(query, function (err, result) {
    if (err) throw err;
    console.log(result.affectedRows + " record(s) updated");
  });
});

app.delete('/user', jsonParser, function(req, res, next) {
  console.log("DELETE /user");
  var data = req.body;
  var query = 'DELETE FROM user WHERE email = \'' + data.email + '\'';

  res.locals.connection.query(query, function(error, results, fields) {
    if(error) throw error;
    res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
  });

});
