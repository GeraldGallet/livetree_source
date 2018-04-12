/*********** HAS_ACCESS **********/
/* Part that touches the accesses of users to places */

class HasAccess {
  constructor() { }

  getAll(res) {
    var query = 'SELECT * FROM has_access;';
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Get the line of a specified user
  get(body, res) {
    var query = 'SELECT * FROM has_access WHERE id_user = \'' + body.id_user + '\';';
    var ret = '';
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Adds a new user in the database
  add(body, res) {
    res.locals.connection.query('INSERT INTO has_access SET ?', body, function (error, results, fields) {
      if (error) throw error;
      res.end(JSON.stringify(results));
    });
  }

  // Deletes a user of the database
  delete(body, res) {
    var query = 'DELETE FROM has_access WHERE id_user = \'' + body.id_user + '\' AND id_place = \'' + body.id_place + '\';';

    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }
}

module.exports = HasAccess;
