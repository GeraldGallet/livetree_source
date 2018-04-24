/********** SERVER *********/
/* Le serveur NodeJS utilisé comme API */

// On importe la configuration
const config = require('./config.json');

// On prends les packages dont on a besoin
const mysql = require('mysql'); // Connection a la BDD
const express = require('express'), // Pour recevoir les requetes HTTP
  app = express(),
  port = process.env.PORT || 3000;
const bodyParser = require('body-parser'); // Pour parser le body des requetes
const nodemailer = require('nodemailer'); // Pour envoyer des mails
const jsonParser = bodyParser.json();
app.listen(port);

// La liste des tables de la BDD. Si on recoit une requete avec une autre table, on renverre une erreur 404
var cmds = ["user", "status", "personal_car", "facility", "place", "company_car", "borne", "work", "domain", "has_domain", "phone_indicative", "has_access", "resa_borne", "reason", "resa_car", "state", "email_validate", "password_recovery"];

// Partie envoi de mail avec Nodemailer et le SMTP de Google
'use strict';
var transporter = nodemailer.createTransport({
 service: 'gmail',
 auth: {
   user: 'coelablivetree@gmail.com',
   pass: 'livetree@yncrea'
    }
});

// Connection a la BDD
const connection = mysql.createConnection({
		host     : config.host,
		user     : config.user,
		password : config.password,
		database : config.database
	});
connection.connect();

// Renvoie l'intégralité d'une table
function get_all(res, table) {
  var query = 'SELECT * FROM ' + table + ';';
  connection.query(query, function(error, results, fields) {
    if(error) throw error;
    res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
  });
}

// Renvoie certaines lignes d'une table
function get(body, res, table) {
  var query = 'SELECT * FROM ' + table + ' WHERE ';
  var placeholder = [];

  for(var prop in body) { // On ajoute tous les filtres envoyés dans le body
    if(prop != 'options') {
      placeholder.push(body[prop]);
      query += prop + " = ? AND ";
    }
  }
  query += '1 '; // Pour ne pas finir sur un "AND"

  if(body.options != null) { // On ajoute les options (GROUP BY / ORDER BY / LIMIT / ...)
    for(var option in body.options)
      query += body.options[option];
  }

  query += ";";
  connection.query(query, placeholder, function(error, results, fields) {
    if(error) throw error;
    res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
  });
}

// Ajoute une ligne dans la table
// On utilise le placeholder (?) avec le body en json
// Renvoie l'id de la ligne créée
function add(body, res, table) {
  var query = "INSERT INTO " + table + " SET ?";
  connection.query(query, body, function (error, results, fields) {
    if (error) throw error;
    res.send(JSON.stringify({"status": 200, "error": null, "response": results}))
    res.end(JSON.stringify(results));
  });
}

// Supprime une ligne
function delete_entry(body, res, table) {
  var query = "DELETE FROM " + table + " WHERE ";
  var placeholder = [];

  for(var prop in body) { // On ajoute les filtres
    if(prop != 'options') {
      placeholder.push(body[prop]);
      query += prop + " = ? AND ";
    }
  }
  query += '1 ';

  if(body.options != null) { // On ajoute les options
    for(var option in body.options)
      query += body.options[option];
  }

  query += ";";
  connection.query(query, placeholder, function(error, results, fields) {
    if(error) throw error;
    res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
  });
}

// Modifie une ligne
// placeholder pour les valeurs à changer
function update_table(body, res, table) {
  var query = "UPDATE " + table + " SET ? WHERE ";
  placeholder = [body.set];

  for(var prop in body.where) { // On ajoute les filtres
    placeholder.push(body.where[prop]);
    query += prop + " = ? AND ";
  }
  query += '1;';

  connection.query(query, placeholder, function(error, results, fields) {
    if(error) throw error;
    res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
  });
}

// Applique une requête directement créée par l'utilisateur
function custom(body, res) {
  connection.query(body.query, function(error, results, fields) {
    if(error) throw error;
    res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
  });
}

// Envoie un e-mail
function send_mail(body, res) {
  let mailOptions = {
    from: 'Live Tree Web <coelablivetree@gmail.com>', // Adresse avec laquelle on envoie l'e-mail
    to: body.email, // Liste des receveurs
    subject: body.subject, // Objet du mail
    html: body.html // Corps du mail en HTML
  };

  transporter.sendMail(mailOptions, function (err, info) {
    if(err) {
      console.log(err)
      res.send(JSON.stringify({"status": 404, "error": "Mail could not be sent", "response": null}));
    } else {
      //console.log(info);
    }
  });
  res.send(JSON.stringify({"status": 200, "error": null, "response": null}));
  return;
}

/* Fonction qui recoivent les requêtes */
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
});

app.patch('/:table', jsonParser, function(req, res) {
  console.log("patch /" + req.params.table);

  for(var i = 0; i < cmds.length; i++) {
    if(req.params.table == cmds[i]) {
      update_table(req.body, res, cmds[i]);
    }
  }
});
