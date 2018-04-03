/*********** FACILITY **********/
/* Part that touches thefacilities */

class Facility {
  constructor() { }

  // Gets all the personal car from a user
  get_all(body, res) {
    var query = "SELECT * FROM facility;";
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Gets a specified personal car
  get(body, res) {
    var query = "SELECT * FROM facility WHERE name = \'" + body.name + "\';";
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Adds a new personal car to the specified user
  add(body, res) {
    res.locals.connection.query('INSERT INTO facility SET ?', body, function (error, results, fields) {
      if (error) throw error;
      res.end(JSON.stringify(results));
    });
  }

  // Removes a specified personal car
  delete(body, res) {
    var query = 'DELETE FROM facility WHERE name = \'' + body.name + '\'';

    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

}

module.exports = Facility;
