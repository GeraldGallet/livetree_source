/*********** STATUS **********/
/* Part that touches the statuses of the users */

class Status {
  constructor() { }

  // Gets the line of a specified status
  get(body, res) {
    var query = "SELECT * FROM status WHERE id_status = \'" + body.id_status + "\';";
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Adds a new status
  add(body, res) {
    res.locals.connection.query('INSERT INTO status SET ?', body, function (error, results, fields) {
      if (error) throw error;
      res.end(JSON.stringify(results));
    });
  }

  // Deletes the specified status
  delete(body, res) {
    var query = 'DELETE FROM status WHERE id_status = \'' + body.id_status + '\'';
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

}

module.exports = Status;
