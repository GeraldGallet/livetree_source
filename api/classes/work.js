/*********** WORK **********/
/* Part that touches the works */

class Work {
  constructor() { }

  getAll(res) {
    var query = 'SELECT * FROM work;';
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Get the line of a specified user
  get(body, res) {
    var query = 'SELECT * FROM work WHERE id_user = \'' + body.id_user + '\';';
    var ret = '';
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Adds a new user in the database
  add(body, res) {
    res.locals.connection.query('INSERT INTO work SET ?', body, function (error, results, fields) {
      if (error) throw error;
      res.end(JSON.stringify(results));
    });
  }

  // Deletes a user of the database
  delete(body, res) {
    var query = 'DELETE FROM work WHERE id_user = \'' + body.id_user + '\' AND id_facility = \'' + body.id_facility + '\';';

    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }
}

module.exports = Work;
