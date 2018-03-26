const mysql = require('mysql');
const config = require('./config.json');

var express = require('express'),
  app = express(),
  port = process.env.PORT || 3000;

app.listen(port);

//Database connection
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

app.get('/user/all', function(req, res, next) {
  console.log('/user/all');
  var query = 'SELECT * FROM user;';

  res.locals.connection.query(query, function(error, results, fields) {
    if(error) throw error;
    res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
  });
});

app.get('/user/:mail', function(req, res, next) {
  console.log('/user/' + req.params.mail);
  var query = 'SELECT * FROM user WHERE email = \'' + req.params.mail + '\';';
  
  res.locals.connection.query(query, function(error, results, fields) {
    if(error) throw error;
    res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
  });
});
