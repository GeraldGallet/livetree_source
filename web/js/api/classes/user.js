/*********** USER **********/
/* Part that touches the users */

class User {
  constructor() { }

  getAll(res) {
    var query = 'SELECT * FROM user;';
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Get the line of a specified user
  get(body, res) {
    var query = 'SELECT * FROM user WHERE email = \'' + body.email + '\';';
    var ret = '';
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Adds a new user in the database
  add(body, res) {
    res.locals.connection.query('INSERT INTO user SET ?', body, function (error, results, fields) {
      if (error) throw error;
      res.end(JSON.stringify(results));
    });
  }

  // Deletes a user of the database
  delete(body, res) {
    var query = 'DELETE FROM user WHERE email = \'' + body.email + '\'';

    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Changes the password of a user
  change_password(body, res) {
    var query = "UPDATE user SET password = \'" + body.password + '\' WHERE email = \'' + body.email + '\';';

    res.locals.connection.query(query, function (err, result) {
      if (err) throw err;
      console.log(result.affectedRows + " record(s) updated");
    });
  }

}

module.exports = User;
