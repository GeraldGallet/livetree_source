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

var cmds = ["user", "status", "personal_car", "facility", "place", "company_car", "borne", "work", "domain", "has_domain", "phone_indicative", "has_access", "resa_borne", "reason", "resa_car", "state", "email_validate", "password_recovery"];

// Partie envoi de mail avec Nodemailer et le SMTP de Google
'use strict';
const nodemailer = require('nodemailer');

var transporter = nodemailer.createTransport({
 service: 'gmail',
 auth: {
   user: 'coelablivetree@gmail.com',
   pass: 'livetree@yncrea'
    }
});

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

function get_all(res, table) {
  var query = 'SELECT * FROM ' + table + ';';
  res.locals.connection.query(query, function(error, results, fields) {
    if(error) throw error;
    res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
  });
}

function get(body, res, table) {
  //console.log(body);
  var query = 'SELECT * FROM ' + table + ' WHERE ';
  var placeholder = [];

  for(var prop in body) {
    if(prop != 'options') {
      placeholder.push(body[prop]);
      query += prop + " = ? AND ";
    }
  }
  query += '1 ';

  if(body.options != null) {
    for(var option in body.options)
      query += body.options[option];
  }

  query += ";";
  //console.log(query);
  res.locals.connection.query(query, placeholder, function(error, results, fields) {
    if(error) throw error;
    res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
  });
}

function add(body, res, table) {
  var query = "INSERT INTO " + table + " SET ?";
  res.locals.connection.query(query, body, function (error, results, fields) {
    if (error) throw error;
    res.send(JSON.stringify({"status": 200, "error": null, "response": results}))
    res.end(JSON.stringify(results));
  });
}

function delete_entry(body, res, table) {
  var query = "DELETE FROM " + table + " WHERE ";
  var placeholder = [];

  for(var prop in body) {
    if(prop != 'options') {
      placeholder.push(body[prop]);
      query += prop + " = ? AND ";
    }
  }
  query += '1 ';

  if(body.options != null) {
    for(var option in body.options)
      query += body.options[option];
  }

  query += ";";
  res.locals.connection.query(query, placeholder, function(error, results, fields) {
    if(error) throw error;
    res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
  });
}

function update_table(body, res, table) {
  var query = "UPDATE " + table + " SET ? WHERE ";
  placeholder = [body.set];

  for(var prop in body.where) {
    placeholder.push(body.where[prop]);
    query += prop + " = ? AND ";
  }
  query += '1;';

  res.locals.connection.query(query, placeholder, function(error, results, fields) {
    if(error) throw error;
    res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
  });
}

function custom(body, res) {
  res.locals.connection.query(body.query, function(error, results, fields) {
    if(error) throw error;
    res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
  });
}

function send_mail(body, res) {
  console.log(body);
  let mailOptions = {
    from: 'Live Tree Web <coelablivetree@gmail.com>', // sender address
    to: body.email, // list of receivers
    subject: body.subject, // Subject line
    html: body.html // html body
  };

  transporter.sendMail(mailOptions, function (err, info) {
    if(err) {
      console.log(err)
      res.send(JSON.stringify({"status": 404, "error": "Mail could not be sent", "response": null}));
    } else {
      console.log(info);
      //res.send(JSON.stringify({"status": 200, "error": null, "response": null}));
    }
  });
  res.send(JSON.stringify({"status": 200, "error": null, "response": null}));
  return;
}


// The commands that can be done on a table (get / add)
app.post('/:table/:cmd', jsonParser, function(req, res, next) {
  console.log(req.params.table + "/" + req.params.cmd);
  var passed = false;
  if(req.params.table == "mail") {
    send_mail(req.body, res);
    return;
  }

  if(req.params.table == "custom") {
    custom(req.body, res);
    return;
  }

  for(var i = 0; i < cmds.length; i++) {
    if(req.params.table == cmds[i]) {
      passed = true;
      switch(req.params.cmd) {
        case "get_all":
          get_all(res, cmds[i]);
          break;

        case "get":
          get(req.body, res, cmds[i]);
          break;

        case "add":
          add(req.body, res, cmds[i]);
          break;
      }
    }
  }

  res.locals.connection.end();
  if(!passed)
    res.send(JSON.stringify({"status": 404, "error": "Table not found", "response": null}));
});

app.delete('/:table', jsonParser, function(req, res, next) {
  console.log("delete /" + req.params.table);

  for(var i = 0; i < cmds.length; i++) {
    if(req.params.table == cmds[i]) {
      delete_entry(req.body, res, cmds[i]);
    }
  }
  res.locals.connection.end();
});

app.patch('/:table', jsonParser, function(req, res) {
  console.log("patch /" + req.params.table);

  for(var i = 0; i < cmds.length; i++) {
    if(req.params.table == cmds[i]) {
      update_table(req.body, res, cmds[i]);
    }
  }
  res.locals.connection.end();
});
