/*********** REASON **********/
/* Part that touches the reasons of a vehicule reservation */

class Reason {
  constructor() { }

  getAll(res) {
    var query = 'SELECT * FROM reason;';
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Get the line of a specified user
  get(body, res) {
    var query = 'SELECT * FROM reason WHERE id_reason = \'' + body.id_reason + '\';';
    var ret = '';
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Adds a new user in the database
  add(body, res) {
    res.locals.connection.query('INSERT INTO reason SET ?', body, function (error, results, fields) {
      if (error) throw error;
      res.end(JSON.stringify(results));
    });
  }

  // Deletes a user of the database
  delete(body, res) {
    var query = 'DELETE FROM reason WHERE id_reason = \'' + body.id_reason + '\';';

    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }
}

module.exports = Reason;
