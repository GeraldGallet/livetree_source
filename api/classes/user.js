/*********** USER **********/
/* La partie de l'API qui touche aux users de la BDD */

class User {
  constructor() { }

  getAll(res) {
    var query = 'SELECT * FROM user;';
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  get(body, res) {
    var query = 'SELECT * FROM user WHERE email = \'' + body.email + '\';';
    var ret = '';
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  add(body, res) {
    res.locals.connection.query('INSERT INTO user SET ?', body, function (error, results, fields) {
      if (error) throw error;
      res.end(JSON.stringify(results));
    });
  }

  delete(body, res) {
    var query = 'DELETE FROM user WHERE email = \'' + body.email + '\'';

    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  change_password(body, res) {
    var query = "UPDATE user SET password = \'" + body.password + '\' WHERE email = \'' + body.email + '\';';

    res.locals.connection.query(query, function (err, result) {
      if (err) throw err;
      console.log(result.affectedRows + " record(s) updated");
    });
  }

}

module.exports = User;
